<?php

namespace App\Models;

// Email verification removed - users receive credentials via email anyway
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'email',
        'password',
        'role',
        'status',
        'registration_status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'registration_source',
        'profile_picture',
        'avatar',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact',
        'emergency_phone',
        'medical_history',
        'allergies',
        'notes',
        'created_by',
        'updated_by',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }
    
    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_PATIENT = 'patient';
    
    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';
    
    // Legacy constant for backward compatibility
    const STATUS_ARCHIVED = 'inactive'; // Maps to inactive in database
    
    /**
     * Registration status constants
     */
    const REGISTRATION_PENDING = 'pending';
    const REGISTRATION_APPROVED = 'approved';
    const REGISTRATION_REJECTED = 'rejected';
    
    /**
     * Registration source constants
     */
    const SOURCE_ADMIN = 'admin';
    const SOURCE_SELF = 'self';
    const SOURCE_IMPORT = 'import';
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    
    /**
     * Check if user is patient
     */
    public function isPatient(): bool
    {
        return $this->role === self::ROLE_PATIENT;
    }
    
    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * Check if registration is pending
     */
    public function isRegistrationPending(): bool
    {
        return $this->registration_status === self::REGISTRATION_PENDING;
    }
    
    /**
     * Check if registration is approved
     */
    public function isRegistrationApproved(): bool
    {
        return $this->registration_status === self::REGISTRATION_APPROVED;
    }
    
    /**
     * Check if registration is rejected
     */
    public function isRegistrationRejected(): bool
    {
        return $this->registration_status === self::REGISTRATION_REJECTED;
    }
    
    /**
     * Check if user self-registered
     */
    public function isSelfRegistered(): bool
    {
        return $this->registration_source === self::SOURCE_SELF;
    }
    
    
    /**
     * Get the patient record associated with the user
     */
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }
    
    /**
     * Get prescriptions associated with the user (through appointments)
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'prescribed_by');
    }
    
    /**
     * Get appointments associated with the user (if they're a patient)
     */
    public function appointments(): HasManyThrough
    {
        // This relationship assumes patients have appointments linked through the patient record
        return $this->hasManyThrough(
            Appointment::class,
            Patient::class,
            'user_id',      // Foreign key on patients table
            'patient_id',   // Foreign key on appointments table
            'id',           // Local key on users table
            'id'            // Local key on patients table
        );
    }
    
    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute(): string
    {
        return \App\Services\ProfilePictureService::getProfilePictureUrl($this);
    }
    
    /**
     * Get medical notes created by this user
     */
    public function createdMedicalNotes(): HasMany
    {
        return $this->hasMany(MedicalNote::class, 'created_by');
    }
    
    /**
     * Get the display name or fallback to name
     */
    public function getDisplayNameAttribute($value)
    {
        return $value ?: $this->name;
    }
    
    /**
     * Get the profile picture URL for AdminLTE
     */
    public function adminlte_image()
    {
        return $this->getProfilePictureUrlAttribute();
    }
    
    /**
     * Get avatar HTML with initials fallback
     */
    public function getAvatarHtml($size = 'default', $cssClasses = '')
    {
        return \App\Services\ProfilePictureService::getAvatarHtml($this, $size, $cssClasses);
    }
    
    /**
     * Get user initials
     */
    public function getInitials()
    {
        if (!$this->name) {
            return '??';
        }
        
        $names = explode(' ', trim($this->name));
        if (count($names) >= 2) {
            return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
        }
        
        return strtoupper(substr($names[0], 0, 2));
    }
    
    /**
     * Get the profile URL for AdminLTE user menu
     */
    public function adminlte_profile_url()
    {
        if ($this->role === 'patient') {
            return route('patient.profile.edit');
        }
        
        // For admin users, return a default profile route or null
        return route('dashboard.index');
    }
    
    /**
     * Get user description for AdminLTE
     */
    public function adminlte_desc()
    {
        return ucfirst($this->role);
    }
    
    /**
     * Get user full name for AdminLTE
     */
    public function adminlte_full_name()
    {
        return $this->name;
    }
    
    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    
    /**
     * Scope to get only admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }
    
    /**
     * Scope to get only patient users
     */
    public function scopePatients($query)
    {
        return $query->where('role', self::ROLE_PATIENT);
    }
    
    /**
     * Get notifications for the user
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Models\Notification::class);
    }
    
    /**
     * Get the user who approved this registration
     */
    public function approvedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }
}
