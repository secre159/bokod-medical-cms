<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'patient_name',
        'address',
        'position',
        'civil_status',
        'course',
        'height',
        'weight',
        'bmi',
        'systolic_bp',
        'diastolic_bp',
        'blood_pressure',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_address',
        'date_of_birth',
        'phone_number',
        'phone', // Additional phone field used by UserController
        'email',
        'gender',
        'user_id',
        'archived',
        'emergency_contact', // Used by UserController
        'emergency_phone', // Used by UserController
        'medical_history',
        'allergies', 
        'notes',
        'status',
        'updated_by',
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
        'archived' => 'boolean',
        'height' => 'decimal:1',
        'weight' => 'decimal:1',
        'bmi' => 'decimal:1',
        'systolic_bp' => 'integer',
        'diastolic_bp' => 'integer',
    ];
    
    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
    
    public function visits(): HasMany
    {
        return $this->hasMany(PatientVisit::class);
    }
    
    public function medicationHistory(): HasMany
    {
        return $this->hasMany(PatientMedicationHistory::class);
    }
    
    public function medicalNotes(): HasMany
    {
        return $this->hasMany(MedicalNote::class);
    }
    
    // Accessors
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }
    
    public function getBmiStatusAttribute()
    {
        if (!$this->bmi) return null;
        
        $bmi = (float) $this->bmi;
        if ($bmi < 18.5) return 'Underweight';
        if ($bmi < 25) return 'Normal weight';
        if ($bmi < 30) return 'Overweight';
        return 'Obese';
    }
    
    public function getBpStatusAttribute()
    {
        if (!$this->systolic_bp || !$this->diastolic_bp) {
            // Try to parse from blood_pressure string
            if ($this->blood_pressure && str_contains($this->blood_pressure, '/')) {
                $parts = explode('/', $this->blood_pressure);
                $systolic = (int) $parts[0];
                $diastolic = (int) $parts[1];
            } else {
                return null;
            }
        } else {
            $systolic = $this->systolic_bp;
            $diastolic = $this->diastolic_bp;
        }
        
        if ($systolic < 120 && $diastolic < 80) return 'Normal';
        if ($systolic <= 129 && $diastolic < 80) return 'Elevated';
        if ($systolic <= 139 || $diastolic <= 89) return 'High Blood Pressure Stage 1';
        if ($systolic >= 140 || $diastolic >= 90) return 'High Blood Pressure Stage 2';
        if ($systolic > 180 || $diastolic > 120) return 'Hypertensive Crisis';
        
        return 'Unknown';
    }
    
    /**
     * Get patient initials from patient_name
     */
    public function getInitials()
    {
        if (!$this->patient_name) {
            return '??';
        }
        
        $names = explode(' ', trim($this->patient_name));
        if (count($names) >= 2) {
            return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
        }
        
        return strtoupper(substr($names[0], 0, 2));
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('archived', false);
    }
    
    public function scopeArchived($query)
    {
        return $query->where('archived', true);
    }
}
