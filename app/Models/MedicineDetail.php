<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineDetail extends Model
{
    protected $fillable = [
        'medicine_name',
        'description',
        'dosage_form',
        'strength',
        'manufacturer',
    ];
    
    public function medicationHistory(): HasMany
    {
        return $this->hasMany(PatientMedicationHistory::class);
    }
}
