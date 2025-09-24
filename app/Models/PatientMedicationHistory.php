<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PatientMedicationHistory extends Model
{
    protected $table = 'patient_medication_history';
    
    protected $fillable = [
        'patient_visit_id',
        'medicine_details_id',
        'quantity',
        'dosage',
        'frequency',
        'instructions',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'dosage' => 'decimal:2',
    ];
    
    // Relationships
    public function patientVisit(): BelongsTo
    {
        return $this->belongsTo(PatientVisit::class);
    }
    
    public function medicineDetails(): BelongsTo
    {
        return $this->belongsTo(MedicineDetail::class);
    }
    
    // Through relationships
    public function patient()
    {
        return $this->hasOneThrough(
            Patient::class,
            PatientVisit::class,
            'id', // Foreign key on patient_visits table
            'id', // Foreign key on patients table
            'patient_visit_id', // Local key on patient_medication_history table
            'patient_id' // Local key on patient_visits table
        );
    }
    
    // Accessors
    public function getFormattedDosageAttribute(): string
    {
        return $this->dosage . 'mg';
    }
    
    public function getFormattedFrequencyAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->frequency));
    }
    
    public function getMedicineNameAttribute(): string
    {
        return $this->medicineDetails ? $this->medicineDetails->medicine_name : 'Unknown Medicine';
    }
    
    public function getVisitDateAttribute()
    {
        return $this->patientVisit ? $this->patientVisit->visit_date : null;
    }
    
    // Scopes
    public function scopeForPatientVisit($query, $patientVisitId)
    {
        return $query->where('patient_visit_id', $patientVisitId);
    }
    
    public function scopeForMedicine($query, $medicineDetailsId)
    {
        return $query->where('medicine_details_id', $medicineDetailsId);
    }
    
    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }
    
    public function scopeHighDosage($query, $threshold = 100)
    {
        return $query->where('dosage', '>=', $threshold);
    }
    
    public function scopeRecent($query, $days = 30)
    {
        return $query->whereHas('patientVisit', function($q) use ($days) {
            $q->where('visit_date', '>=', now()->subDays($days));
        });
    }
    
    // Helper methods
    public function getTotalDosage(): float
    {
        return $this->quantity * $this->dosage;
    }
    
    public function getDailyDosage(): float
    {
        $frequencyMultiplier = $this->getFrequencyMultiplier();
        return $this->dosage * $frequencyMultiplier;
    }
    
    private function getFrequencyMultiplier(): int
    {
        switch (strtolower($this->frequency)) {
            case 'once daily':
            case '1x daily':
                return 1;
            case 'twice daily':
            case '2x daily':
            case 'bid':
                return 2;
            case 'three times daily':
            case '3x daily':
            case 'tid':
                return 3;
            case 'four times daily':
            case '4x daily':
            case 'qid':
                return 4;
            case 'every 6 hours':
                return 4;
            case 'every 8 hours':
                return 3;
            case 'every 12 hours':
                return 2;
            default:
                return 1;
        }
    }
}
