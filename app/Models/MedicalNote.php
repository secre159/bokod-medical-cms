<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MedicalNote extends Model
{
    // Note type constants
    const TYPE_GENERAL = 'general';
    const TYPE_TREATMENT = 'treatment';
    const TYPE_OBSERVATION = 'observation';
    const TYPE_DIAGNOSIS = 'diagnosis';
    
    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';
    
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'patient_visit_id',
        'title',
        'content',
        'note_type',
        'priority',
        'created_by',
        'note_date',
        'is_private',
        'tags',
    ];
    
    protected $casts = [
        'note_date' => 'datetime',
        'is_private' => 'boolean',
        'tags' => 'array',
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
    
    public function patientVisit(): BelongsTo
    {
        return $this->belongsTo(PatientVisit::class);
    }
    
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Accessors
    public function getPriorityColorAttribute(): string
    {
        switch ($this->priority) {
            case self::PRIORITY_LOW:
                return 'secondary';
            case self::PRIORITY_NORMAL:
                return 'info';
            case self::PRIORITY_HIGH:
                return 'warning';
            case self::PRIORITY_URGENT:
                return 'danger';
            default:
                return 'info';
        }
    }
    
    public function getPriorityBadgeAttribute(): string
    {
        $color = $this->priority_color;
        $text = ucfirst($this->priority);
        
        return "<span class='badge badge-{$color}'>{$text}</span>";
    }
    
    public function getTypeColorAttribute(): string
    {
        switch ($this->note_type) {
            case self::TYPE_GENERAL:
                return 'primary';
            case self::TYPE_TREATMENT:
                return 'success';
            case self::TYPE_OBSERVATION:
                return 'info';
            case self::TYPE_DIAGNOSIS:
                return 'warning';
            default:
                return 'primary';
        }
    }
    
    public function getTypeBadgeAttribute(): string
    {
        $color = $this->type_color;
        $text = ucfirst(str_replace('_', ' ', $this->note_type));
        
        return "<span class='badge badge-{$color}'>{$text}</span>";
    }
    
    public function getFormattedDateAttribute(): string
    {
        return $this->note_date->format('M d, Y g:i A');
    }
    
    public function getShortContentAttribute(): string
    {
        return strlen($this->content) > 100 ? substr($this->content, 0, 100) . '...' : $this->content;
    }
    
    // Scopes
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('note_type', $type);
    }
    
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
    
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }
    
    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }
    
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('note_date', '>=', now()->subDays($days));
    }
    
    public function scopeOrderByPriority($query)
    {
        return $query->orderByRaw(
            "CASE priority 
            WHEN 'urgent' THEN 1 
            WHEN 'high' THEN 2 
            WHEN 'normal' THEN 3 
            WHEN 'low' THEN 4 
            ELSE 5 END"
        );
    }
    
    // Static methods
    public static function getTypes()
    {
        return [
            self::TYPE_GENERAL => 'General',
            self::TYPE_TREATMENT => 'Treatment',
            self::TYPE_OBSERVATION => 'Observation',
            self::TYPE_DIAGNOSIS => 'Diagnosis',
        ];
    }
    
    public static function getPriorities()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }
}
