<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Traits\PhilippineTimezone;
use App\Helpers\TimezoneHelper;

class Appointment extends Model
{
    use PhilippineTimezone;
    
    protected $primaryKey = 'appointment_id';
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'appointment_id';
    }
    
    protected $fillable = [
        'patient_id',
        'appointment_date',
        'appointment_time',
        'reason',
        'status',
        'approval_status',
        'reschedule_status',
        'requested_date',
        'requested_time',
        'reschedule_reason',
        'cancellation_reason',
        'cancelled_at',
        'notes',
        'diagnosis',
        'treatment_notes',
    ];
    
    protected $casts = [
        'appointment_date' => 'date',
        'requested_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'requested_time' => 'datetime:H:i',
        'cancelled_at' => 'datetime',
    ];
    
    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    
    const APPROVAL_PENDING = 'pending';
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_REJECTED = 'rejected';
    
    const RESCHEDULE_NONE = 'none';
    const RESCHEDULE_PENDING = 'pending';
    
    /**
     * Get the patient that owns the appointment
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    
    /**
     * Get the prescriptions for this appointment
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'appointment_id', 'appointment_id');
    }
    
    /**
     * Check if appointment is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * Check if appointment is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }
    
    /**
     * Check if appointment has pending reschedule
     */
    public function hasPendingReschedule(): bool
    {
        return $this->reschedule_status === self::RESCHEDULE_PENDING;
    }
    
    /**
     * Check if appointment is overdue
     */
    public function isOverdue(): bool
    {
        // Compare with Philippine timezone date
        $todayInPhilippines = TimezoneHelper::now()->toDateString();
        return $this->appointment_date->toDateString() < $todayInPhilippines;
    }
    
    /**
     * Check if appointment is today
     */
    public function isToday(): bool
    {
        // Compare with Philippine timezone date
        $todayInPhilippines = TimezoneHelper::now()->toDateString();
        return $this->appointment_date->toDateString() === $todayInPhilippines;
    }
    
    /**
     * Check if the appointment can be approved
     */
    public function canApproveAppointment(): bool
    {
        return $this->approval_status === self::APPROVAL_PENDING && 
               $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * Check if the appointment can be edited
     */
    public function canEditAppointment(): bool
    {
        return $this->status !== self::STATUS_COMPLETED && 
               $this->status !== self::STATUS_CANCELLED;
    }
    
    /**
     * Check if the appointment can be cancelled
     */
    public function canCancelAppointment(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * Check if the appointment can be completed
     */
    public function canCompleteAppointment(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->approval_status === self::APPROVAL_APPROVED;
    }
    
    /**
     * Check if the appointment can be rescheduled
     */
    public function canRescheduleAppointment(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->reschedule_status !== self::RESCHEDULE_PENDING;
    }
    
    /**
     * Get formatted appointment datetime
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->appointment_date->format('M d, Y') . ' at ' . $this->appointment_time->format('g:i A');
    }
    
    /**
     * Scope for active appointments
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    
    /**
     * Scope for approved appointments
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }
    
    /**
     * Scope for pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', self::APPROVAL_PENDING);
    }
    
    /**
     * Scope for pending reschedule
     */
    public function scopePendingReschedule($query)
    {
        return $query->where('reschedule_status', self::RESCHEDULE_PENDING);
    }
    
    /**
     * Scope for upcoming appointments
     */
    public function scopeUpcoming($query)
    {
        // Use Philippine timezone for consistent date comparison
        $todayInPhilippines = TimezoneHelper::now()->toDateString();
        return $query->where('appointment_date', '>=', $todayInPhilippines);
    }
    
    /**
     * Scope for today's appointments
     */
    public function scopeToday($query)
    {
        // Use Philippine timezone for consistent date comparison
        $todayInPhilippines = TimezoneHelper::now()->toDateString();
        return $query->whereDate('appointment_date', $todayInPhilippines);
    }
    
    /**
     * Scope for this week's appointments
     */
    public function scopeThisWeek($query)
    {
        // Use Philippine timezone for consistent week calculation
        $nowInPhilippines = TimezoneHelper::now();
        return $query->whereBetween('appointment_date', [
            $nowInPhilippines->startOfWeek()->toDateString(),
            $nowInPhilippines->endOfWeek()->toDateString()
        ]);
    }
    
    /**
     * Scope for overdue appointments
     */
    public function scopeOverdue($query)
    {
        // Use Philippine timezone for consistent date comparison
        $todayInPhilippines = TimezoneHelper::now()->toDateString();
        return $query->where('appointment_date', '<', $todayInPhilippines)
                     ->where('status', self::STATUS_ACTIVE); // Only active appointments can be overdue
    }
}
