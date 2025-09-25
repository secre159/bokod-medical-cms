<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Conversation extends Model
{
    protected $fillable = [
        'title',
        'type',
        'patient_id',
        'admin_id',
        'is_active',
        'last_message_at',
        'is_archived',
        'archived_at',
        'archived_by',
        'admin_archived',
        'patient_archived',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'admin_archived' => 'boolean',
        'patient_archived' => 'boolean',
        'last_message_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Get the patient record
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patient_id');
    }
    
    /**
     * Get the patient user (through patient record)
     */
    public function patientUser()
    {
        return $this->patient->user ?? null;
    }

    /**
     * Get the admin user
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get all messages for this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get unread messages for a specific user
     */
    public function unreadMessagesFor($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false);
    }

    /**
     * Mark all messages as read for a specific user
     */
    public function markAsReadFor($userId)
    {
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Get conversation title for display
     */
    public function getDisplayTitleAttribute()
    {
        if ($this->title) {
            return $this->title;
        }

        // Generate title based on participants
        if (auth()->user()->role === 'admin') {
            return $this->patient->patient_name ?? 'Unknown Patient';
        } else {
            return $this->admin->name ?? 'Medical Staff';
        }
    }

    /**
     * Get the other participant (not the current user)
     */
    public function getOtherParticipantAttribute()
    {
        $currentUserId = auth()->id();
        
        if ($this->patient_id === $currentUserId) {
            return $this->admin;
        } else {
            return $this->patient;
        }
    }

    /**
     * Scope for active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for conversations involving a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('patient_id', $userId)
              ->orWhere('admin_id', $userId);
        });
    }

    /**
     * Create or get conversation between patient and admin
     * @param int $patientUserId - The user.id of the patient
     * @param int $adminId - The admin user.id (optional)
     */
    public static function findOrCreateBetween($patientUserId, $adminId = null)
    {
        // First, find the patient record from patients table using user_id
        $patient = \App\Models\Patient::where('user_id', $patientUserId)->first();
        
        if (!$patient) {
            throw new \Exception('Patient record not found for user ID: ' . $patientUserId);
        }
        
        // If no admin specified, find any available admin
        if (!$adminId) {
            $adminUser = User::where('role', 'admin')->where('status', 'active')->first();
            
            if (!$adminUser) {
                throw new \Exception('No admin users available to start conversation.');
            }
            
            $adminId = $adminUser->id;
        }

        return static::firstOrCreate(
            [
                'patient_id' => $patient->id, // Use patients.id instead of users.id
                'admin_id' => $adminId,
                'type' => 'patient_admin'
            ],
            [
                'is_active' => true,
                'last_message_at' => now()
            ]
        );
    }
    
    /**
     * Archive conversation for current user
     */
    public function archiveFor($userId)
    {
        $user = User::find($userId);
        
        if ($user->role === 'admin') {
            $this->update([
                'admin_archived' => true,
                'archived_at' => now(),
                'archived_by' => $userId
            ]);
        } else {
            $this->update([
                'patient_archived' => true,
                'archived_at' => now(),
                'archived_by' => $userId
            ]);
        }
        
        // If both parties archived, mark conversation as archived
        if ($this->admin_archived && $this->patient_archived) {
            $this->update(['is_archived' => true]);
        }
    }
    
    /**
     * Unarchive conversation for current user
     */
    public function unarchiveFor($userId)
    {
        $user = User::find($userId);
        
        if ($user->role === 'admin') {
            $this->update(['admin_archived' => false]);
        } else {
            $this->update(['patient_archived' => false]);
        }
        
        // If unarchived by any party, conversation is active
        $this->update(['is_archived' => false]);
    }
    
    /**
     * Check if conversation is archived for specific user
     */
    public function isArchivedFor($userId)
    {
        $user = User::find($userId);
        
        if ($user->role === 'admin') {
            return $this->admin_archived;
        } else {
            return $this->patient_archived;
        }
    }
    
    /**
     * Scope for non-archived conversations for specific user
     */
    public function scopeNotArchivedFor($query, $userId)
    {
        $user = User::find($userId);
        
        if ($user->role === 'admin') {
            return $query->where('admin_archived', false);
        } else {
            return $query->where('patient_archived', false);
        }
    }
    
    /**
     * Scope for archived conversations for specific user
     */
    public function scopeArchivedFor($query, $userId)
    {
        $user = User::find($userId);
        
        if ($user->role === 'admin') {
            return $query->where('admin_archived', true);
        } else {
            return $query->where('patient_archived', true);
        }
    }
}
