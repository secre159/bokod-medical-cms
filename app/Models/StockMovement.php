<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'medicine_id',
        'user_id',
        'type',
        'quantity_changed',
        'quantity_before',
        'quantity_after',
        'reason',
        'notes',
        'reference_type',
        'reference_id'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the medicine that this movement belongs to
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
    
    /**
     * Get the user who made this movement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get formatted type for display
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->type) {
            'add' => 'Stock Added',
            'subtract' => 'Stock Removed',
            'adjust' => 'Stock Adjusted',
            'bulk_add' => 'Bulk Addition',
            'bulk_subtract' => 'Bulk Removal',
            'physical_count' => 'Physical Count',
            'adjustment_add' => 'Adjustment (Added)',
            'adjustment_subtract' => 'Adjustment (Removed)',
            default => ucfirst($this->type)
        };
    }
    
    /**
     * Get the icon class for the movement type
     */
    public function getIconClassAttribute(): string
    {
        return match($this->type) {
            'add', 'bulk_add', 'adjustment_add' => 'fas fa-plus-circle text-success',
            'subtract', 'bulk_subtract', 'adjustment_subtract' => 'fas fa-minus-circle text-danger',
            'adjust' => 'fas fa-edit text-warning',
            'physical_count' => 'fas fa-clipboard-check text-info',
            default => 'fas fa-circle text-info'
        };
    }
}
