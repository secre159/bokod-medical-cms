<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PatientVisit extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_date',
        'next_visit_date',
        'bp', // Blood pressure
        'temperature',
        'pulse_rate',
        'rr', // Respiratory rate
        'spo2', // Oxygen saturation
        'disease',
        'symptoms',
        'notes',
    ];
    
    protected $casts = [
        'visit_date' => 'date',
        'next_visit_date' => 'date',
        'temperature' => 'decimal:2',
        'pulse_rate' => 'integer',
        'rr' => 'integer',
        'spo2' => 'integer',
    ];
    
    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
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
    public function getFormattedVisitDateAttribute(): string
    {
        return $this->visit_date->format('M d, Y');
    }
    
    public function getFormattedNextVisitDateAttribute(): string
    {
        return $this->next_visit_date ? $this->next_visit_date->format('M d, Y') : 'Not scheduled';
    }
    
    public function getVitalSignsAttribute(): array
    {
        return [
            'Blood Pressure' => $this->bp ?: 'Not recorded',
            'Temperature' => $this->temperature ? $this->temperature . '°C' : 'Not recorded',
            'Pulse Rate' => $this->pulse_rate ? $this->pulse_rate . ' bpm' : 'Not recorded',
            'Respiratory Rate' => $this->rr ? $this->rr . ' breaths/min' : 'Not recorded',
            'Oxygen Saturation' => $this->spo2 ? $this->spo2 . '%' : 'Not recorded',
        ];
    }
    
    public function getIsRecentAttribute(): bool
    {
        return $this->visit_date->diffInDays(now()) <= 7;
    }
    
    public function getHasNextVisitAttribute(): bool
    {
        return $this->next_visit_date && $this->next_visit_date->isFuture();
    }
    
    public function getIsOverdueForNextVisitAttribute(): bool
    {
        return $this->next_visit_date && $this->next_visit_date->isPast();
    }
    
    // Helper methods
    public function hasVitalSigns(): bool
    {
        return $this->bp || $this->temperature || $this->pulse_rate || $this->rr || $this->spo2;
    }
    
    public function getVitalSignsStatus(): string
    {
        if (!$this->hasVitalSigns()) {
            return 'incomplete';
        }
        
        // Check for abnormal values
        $abnormal = false;
        
        // Temperature check (normal range 36.1-37.2°C)
        if ($this->temperature && ($this->temperature < 36.1 || $this->temperature > 37.2)) {
            $abnormal = true;
        }
        
        // Pulse rate check (normal range 60-100 bpm)
        if ($this->pulse_rate && ($this->pulse_rate < 60 || $this->pulse_rate > 100)) {
            $abnormal = true;
        }
        
        // Respiratory rate check (normal range 12-20 breaths/min)
        if ($this->rr && ($this->rr < 12 || $this->rr > 20)) {
            $abnormal = true;
        }
        
        // Oxygen saturation check (normal > 95%)
        if ($this->spo2 && $this->spo2 < 95) {
            $abnormal = true;
        }
        
        return $abnormal ? 'abnormal' : 'normal';
    }
    
    // Scopes
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }
    
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('visit_date', '>=', now()->subDays($days));
    }
    
    public function scopeWithNextVisit($query)
    {
        return $query->whereNotNull('next_visit_date');
    }
    
    public function scopeOverdueForNextVisit($query)
    {
        return $query->where('next_visit_date', '<', now());
    }
    
    public function scopeWithVitalSigns($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('bp')
              ->orWhereNotNull('temperature')
              ->orWhereNotNull('pulse_rate')
              ->orWhereNotNull('rr')
              ->orWhereNotNull('spo2');
        });
    }
    
    public function scopeByDisease($query, $disease)
    {
        return $query->where('disease', 'like', '%' . $disease . '%');
    }
}
