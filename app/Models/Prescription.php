<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Prescription extends Model
{
    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    
    // Dosage frequency constants
    const FREQUENCIES = [
        'once_daily' => 'Once Daily',
        'twice_daily' => 'Twice Daily (BID)',
        'three_times_daily' => 'Three Times Daily (TID)',
        'four_times_daily' => 'Four Times Daily (QID)',
        'every_6_hours' => 'Every 6 Hours',
        'every_8_hours' => 'Every 8 Hours',
        'every_12_hours' => 'Every 12 Hours',
        'as_needed' => 'As Needed (PRN)',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
    ];
    
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'medicine_id',
        'medicine_name',
        'generic_name',
        'quantity',
        'dosage',
        'frequency',
        'duration_days',
        'instructions',
        'status',
        'prescribed_date',
        'expiry_date',
        'dispensed_quantity',
        'remaining_quantity',
        'prescribed_by',
        'notes',
        'consultation_type',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'dispensed_quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'duration_days' => 'integer',
        'prescribed_date' => 'date',
        'expiry_date' => 'date',
    ];
    
    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }
    
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
    
    public function prescribedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prescribed_by');
    }
    
    // Accessors
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
    
    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) return false;
        
        return $this->expiry_date->diffInDays(now()) <= 7;
    }
    
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->quantity <= 0) return 0;
        
        $dispensed = $this->dispensed_quantity ?? 0;
        return round(($dispensed / $this->quantity) * 100, 1);
    }
    
    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return $this->is_expired ? 'danger' : ($this->is_expiring_soon ? 'warning' : 'success');
            case self::STATUS_COMPLETED:
                return 'primary';
            case self::STATUS_CANCELLED:
                return 'secondary';
            case self::STATUS_EXPIRED:
                return 'danger';
            default:
                return 'info';
        }
    }
    
    public function getStatusBadgeAttribute(): string
    {
        $color = $this->status_color;
        $text = ucfirst($this->status);
        
        if ($this->is_expired && $this->status === self::STATUS_ACTIVE) {
            $text = 'Expired';
        } elseif ($this->is_expiring_soon && $this->status === self::STATUS_ACTIVE) {
            $text = 'Expiring Soon';
        }
        
        return "<span class='badge badge-{$color}'>{$text}</span>";
    }
    
    public function getFrequencyTextAttribute(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? ucfirst(str_replace('_', ' ', $this->frequency));
    }
    
    public function getDurationTextAttribute(): string
    {
        if (!$this->duration_days) return 'Ongoing';
        
        if ($this->duration_days == 1) {
            return '1 day';
        } elseif ($this->duration_days < 7) {
            return $this->duration_days . ' days';
        } elseif ($this->duration_days == 7) {
            return '1 week';
        } elseif ($this->duration_days < 30) {
            return round($this->duration_days / 7, 1) . ' weeks';
        } else {
            return round($this->duration_days / 30, 1) . ' months';
        }
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
    
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }
    
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays($days)])
                    ->where('status', self::STATUS_ACTIVE);
    }
    
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }
    
    public function scopeForMedicine($query, $medicineId)
    {
        return $query->where('medicine_id', $medicineId);
    }
    
    public function scopePrescribedBy($query, $userId)
    {
        return $query->where('prescribed_by', $userId);
    }
    
    // Helper methods
    public function markAsCompleted()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'dispensed_quantity' => $this->quantity,
            'remaining_quantity' => 0,
        ]);
        
        return $this;
    }
    
    public function markAsCancelled($reason = null)
    {
        $notes = $this->notes;
        if ($reason) {
            $notes .= "\nCancelled: " . $reason;
        }
        
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $notes,
        ]);
        
        return $this;
    }
    
    public function updateDispensed($quantity, $notes = null)
    {
        $newDispensed = min($this->quantity, $quantity);
        $remaining = max(0, $this->quantity - $newDispensed);
        
        $updateData = [
            'dispensed_quantity' => $newDispensed,
            'remaining_quantity' => $remaining,
        ];
        
        if ($remaining == 0) {
            $updateData['status'] = self::STATUS_COMPLETED;
        }
        
        if ($notes) {
            $updateData['notes'] = $this->notes . "\n" . $notes;
        }
        
        $this->update($updateData);
        
        // Update medicine stock if linked
        if ($this->medicine) {
            $this->medicine->subtractStock($newDispensed - ($this->dispensed_quantity ?? 0));
        }
        
        return $this;
    }
    
    public static function getFrequencies()
    {
        return self::FREQUENCIES;
    }
    
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }
}
