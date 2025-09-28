<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Medicine extends Model
{
    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';
    const STATUS_DISCONTINUED = 'discontinued';
    
    // Category constants
    const CATEGORIES = [
        'Pain Relief' => 'Pain Relief',
        'Antibiotics' => 'Antibiotics',
        'Vitamins & Supplements' => 'Vitamins & Supplements',
        'Cold & Flu' => 'Cold & Flu',
        'Digestive Health' => 'Digestive Health',
        'Heart & Blood Pressure' => 'Heart & Blood Pressure',
        'Diabetes' => 'Diabetes',
        'Skin Care' => 'Skin Care',
        'Eye Care' => 'Eye Care',
        'Mental Health' => 'Mental Health',
        'General' => 'General',
    ];
    
    // Dosage form constants
    const DOSAGE_FORMS = [
        'Tablet' => 'Tablet',
        'Capsule' => 'Capsule',
        'Syrup' => 'Syrup',
        'Injection' => 'Injection',
        'Cream' => 'Cream',
        'Ointment' => 'Ointment',
        'Drops' => 'Drops',
        'Inhaler' => 'Inhaler',
        'Patch' => 'Patch',
    ];
    
    // Therapeutic class constants
    const THERAPEUTIC_CLASSES = [
        'Analgesic' => 'Analgesic',
        'Antibiotic' => 'Antibiotic',
        'Antiviral' => 'Antiviral',
        'Antihistamine' => 'Antihistamine',
        'Antacid' => 'Antacid',
        'Antipyretic' => 'Antipyretic',
        'Anti-inflammatory' => 'Anti-inflammatory',
        'Bronchodilator' => 'Bronchodilator',
        'Diuretic' => 'Diuretic',
        'Anticoagulant' => 'Anticoagulant',
        'Antihypertensive' => 'Antihypertensive',
        'Antidiabetic' => 'Antidiabetic',
        'Vitamin/Supplement' => 'Vitamin/Supplement',
    ];
    
    // Pregnancy category constants
    const PREGNANCY_CATEGORIES = [
        'A' => 'Category A - Safe',
        'B' => 'Category B - Probably Safe',
        'C' => 'Category C - Use with Caution',
        'D' => 'Category D - Use Only if Necessary',
        'X' => 'Category X - Contraindicated',
    ];
    
    protected $fillable = [
        'stock_number',
        'medicine_name',
        'generic_name',
        'brand_name',
        'manufacturer',
        'category',
        'therapeutic_class',
        'description',
        'indication',
        'dosage_form',
        'strength',
        'dosage_instructions',
        'age_restrictions',
        'unit',
        'unit_measure',
        'stock_quantity',
        'balance_per_card',
        'on_hand_per_count',
        'shortage_overage',
        'minimum_stock',
        'supplier',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'storage_conditions',
        'side_effects',
        'contraindications',
        'drug_interactions',
        'pregnancy_category',
        'warnings',
        'requires_prescription',
        'status',
        'notes',
        'inventory_remarks',
        'medicine_image',
    ];
    
    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'stock_quantity' => 'integer',
        'balance_per_card' => 'integer',
        'on_hand_per_count' => 'integer',
        'shortage_overage' => 'integer',
        'minimum_stock' => 'integer',
        'requires_prescription' => 'boolean',
    ];
    
    // Relationships
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
    
    public function medicineDetails(): HasMany
    {
        return $this->hasMany(MedicineDetail::class);
    }
    
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
    
    // Accessors
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->minimum_stock;
    }
    
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
    
    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) return false;
        
        return $this->expiry_date->diffInDays(now()) <= 30;
    }
    
    public function getStockStatusAttribute(): string
    {
        if ($this->is_expired) return 'Expired';
        if ($this->stock_quantity <= 0) return 'Out of Stock';
        if ($this->is_low_stock) return 'Low Stock';
        return 'In Stock';
    }
    
    public function getStockStatusColorAttribute(): string
    {
        if ($this->is_expired) return 'danger';
        if ($this->stock_quantity <= 0) return 'danger';
        if ($this->is_low_stock) return 'warning';
        return 'success';
    }
    
    public function getCalculatedShortageOverageAttribute(): int
    {
        if ($this->balance_per_card && $this->on_hand_per_count) {
            return $this->on_hand_per_count - $this->balance_per_card;
        }
        return $this->shortage_overage ?? 0;
    }
    
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
    
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= minimum_stock');
    }
    
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }
    
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }
    
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
    
    public function scopeRequiringPrescription($query)
    {
        return $query->where('requires_prescription', true);
    }
    
    // Helper methods
    public static function getTherapeuticClasses(): array
    {
        return self::THERAPEUTIC_CLASSES;
    }
    
    public static function getPregnancyCategories(): array
    {
        return self::PREGNANCY_CATEGORIES;
    }
    
    public static function getCategories(): array
    {
        return self::CATEGORIES;
    }
    
    public static function getDosageForms(): array
    {
        return self::DOSAGE_FORMS;
    }
    
    public function updateStock($quantity, $operation = 'subtract')
    {
        if ($operation === 'add') {
            $this->increment('stock_quantity', $quantity);
        } else {
            $this->decrement('stock_quantity', $quantity);
        }
        
        // Update status if expired or out of stock
        if ($this->is_expired && $this->status !== self::STATUS_EXPIRED) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
        
        return $this;
    }
    
    public function addStock($quantity)
    {
        return $this->updateStock($quantity, 'add');
    }
    
    public function subtractStock($quantity)
    {
        return $this->updateStock($quantity, 'subtract');
    }
    
    public static function getCategoriesList()
    {
        return self::CATEGORIES;
    }
    
    public static function getDosageFormsList()
    {
        return self::DOSAGE_FORMS;
    }
    
    public static function getTherapeuticClassesList()
    {
        return self::THERAPEUTIC_CLASSES;
    }
    
    public static function getPregnancyCategoriesList()
    {
        return self::PREGNANCY_CATEGORIES;
    }
}
