<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'scheduled_for',
        'is_read',
        'is_sent',
        'priority'
    ];

    protected $casts = [
        'data' => 'array',
        'scheduled_for' => 'datetime',
        'is_read' => 'boolean',
        'is_sent' => 'boolean',
    ];

    // Notification types
    const TYPE_APPOINTMENT_REMINDER = 'appointment_reminder';
    const TYPE_MEDICATION_REMINDER = 'medication_reminder';
    const TYPE_LAB_RESULTS = 'lab_results';
    const TYPE_PRESCRIPTION_REFILL = 'prescription_refill';
    const TYPE_HEALTH_CHECKUP = 'health_checkup';
    const TYPE_SYSTEM_ALERT = 'system_alert';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for notifications ready to be sent
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('is_sent', false)
                    ->where(function($q) {
                        $q->whereNull('scheduled_for')
                          ->orWhere('scheduled_for', '<=', now());
                    });
    }

    /**
     * Scope for specific notification type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for priority level
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent()
    {
        $this->update(['is_sent' => true]);
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            self::TYPE_APPOINTMENT_REMINDER => 'fas fa-calendar-alt',
            self::TYPE_MEDICATION_REMINDER => 'fas fa-pills',
            self::TYPE_LAB_RESULTS => 'fas fa-vials',
            self::TYPE_PRESCRIPTION_REFILL => 'fas fa-prescription-bottle',
            self::TYPE_HEALTH_CHECKUP => 'fas fa-heartbeat',
            self::TYPE_SYSTEM_ALERT => 'fas fa-exclamation-triangle',
            default => 'fas fa-bell',
        };
    }

    /**
     * Get notification color based on priority
     */
    public function getColorAttribute()
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'text-muted',
            self::PRIORITY_NORMAL => 'text-info',
            self::PRIORITY_HIGH => 'text-warning',
            self::PRIORITY_URGENT => 'text-danger',
            default => 'text-info',
        };
    }
}
