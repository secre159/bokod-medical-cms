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
        'profile_picture',
        'password',
        'role',
        'status',
        'registration_status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'registration_source',
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
     * Get user initials
     */
    public function getInitials()
    {
        if (!$this->name || trim($this->name) === '') {
            // Use email as fallback if available
            if ($this->email) {
                $emailParts = explode('@', $this->email);
                $username = $emailParts[0];
                return strtoupper(substr($username, 0, 2));
            }
            return 'UN'; // Unknown User
        }
        
        $names = explode(' ', trim($this->name));
        $names = array_filter($names); // Remove empty elements
        
        if (count($names) >= 2) {
            return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
        } elseif (count($names) === 1 && strlen($names[0]) >= 2) {
            return strtoupper(substr($names[0], 0, 2));
        } elseif (count($names) === 1 && strlen($names[0]) === 1) {
            return strtoupper($names[0] . $names[0]);
        }
        
        return 'UN'; // Fallback for edge cases
    }
    
    /**
     * Get background color for initials avatar based on user's name
     */
    public function getInitialsColor()
    {
        $colors = [
            '#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe', 
            '#43e97b', '#38f9d7', '#ffecd2', '#fcb69f', '#a8edea', '#fed6e3',
            '#ff9a9e', '#fecfef', '#ffeaa7', '#fab1a0', '#fd79a8', '#fdcb6e',
            '#6c5ce7', '#a29bfe', '#74b9ff', '#0984e3', '#00b894', '#00cec9'
        ];
        
        $initials = $this->getInitials();
        $colorIndex = array_sum(array_map('ord', str_split($initials))) % count($colors);
        
        return $colors[$colorIndex];
    }
    
    /**
     * Generate SVG initials avatar as data URL
     */
    public function generateInitialsAvatar($size = 128)
    {
        $initials = $this->getInitials();
        $backgroundColor = $this->getInitialsColor();
        $fontSize = $size * 0.4; // Font size is 40% of the avatar size
        
        $svg = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<circle cx="' . ($size / 2) . '" cy="' . ($size / 2) . '" r="' . ($size / 2) . '" fill="' . $backgroundColor . '"/>';
        $svg .= '<text x="50%" y="50%" font-family="Arial, sans-serif" font-size="' . $fontSize . '" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="central">' . htmlspecialchars($initials) . '</text>';
        $svg .= '</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
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
     * Get user image for AdminLTE
     */
    public function adminlte_image()
    {
        if ($this->profile_picture) {
            return $this->profile_picture;
        }
        
        // Return SVG initials avatar as fallback
        return $this->generateInitialsAvatar(64);
    }
    
    /**
     * Get profile picture URL attribute
     */
    public function getProfilePictureUrlAttribute()
    {
        return $this->profile_picture;
    }
    
    /**
     * Check if user has a profile picture
     */
    public function hasProfilePicture()
    {
        return !empty($this->profile_picture);
    }
    
    /**
     * Get the avatar URL - either profile picture or initials SVG
     */
    public function getAvatarUrl($size = 64)
    {
        if ($this->profile_picture) {
            return $this->profile_picture;
        }
        
        return $this->generateInitialsAvatar($size);
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
