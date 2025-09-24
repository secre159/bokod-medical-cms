<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the fields already exist before adding them
        if (!Schema::hasColumn('medicines', 'generic_name')) {
            Schema::table('medicines', function (Blueprint $table) {
                // Add new comprehensive fields
                $table->string('generic_name')->nullable()->after('medicine_name');
                $table->string('brand_name')->nullable()->after('generic_name');
                $table->string('manufacturer')->nullable()->after('brand_name');
                $table->string('category')->default('General')->after('manufacturer');
                $table->string('dosage_form')->nullable()->after('description'); // Tablet, Capsule, Syrup, etc.
                $table->string('strength')->nullable()->after('dosage_form'); // 500mg, 250ml, etc.
                $table->string('unit')->default('pieces')->after('strength'); // pieces, bottles, vials, etc.
                $table->integer('stock_quantity')->default(0)->after('unit');
                $table->integer('minimum_stock')->default(10)->after('stock_quantity');
                $table->decimal('unit_price', 10, 2)->default(0.00)->after('minimum_stock');
                $table->decimal('selling_price', 10, 2)->default(0.00)->after('unit_price');
                $table->string('supplier')->nullable()->after('selling_price');
                $table->string('batch_number')->nullable()->after('supplier');
                $table->date('manufacturing_date')->nullable()->after('batch_number');
                $table->date('expiry_date')->nullable()->after('manufacturing_date');
                $table->string('storage_conditions')->nullable()->after('expiry_date');
                $table->text('side_effects')->nullable()->after('storage_conditions');
                $table->text('contraindications')->nullable()->after('side_effects');
                $table->boolean('requires_prescription')->default(true)->after('contraindications');
                $table->enum('status', ['active', 'inactive', 'expired', 'discontinued'])->default('active')->after('requires_prescription');
                $table->text('notes')->nullable()->after('status');
                
                // Add new indexes
                $table->index(['medicine_name', 'status']);
                $table->index(['category', 'status']);
                $table->index(['stock_quantity', 'minimum_stock']);
                $table->index('expiry_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Drop added columns
            $table->dropColumn([
                'generic_name', 'brand_name', 'manufacturer', 'category',
                'dosage_form', 'strength', 'unit', 'stock_quantity', 'minimum_stock',
                'unit_price', 'selling_price', 'supplier', 'batch_number',
                'manufacturing_date', 'expiry_date', 'storage_conditions',
                'side_effects', 'contraindications', 'requires_prescription',
                'status', 'notes'
            ]);
            
            // Re-add the unique constraint
            $table->unique('medicine_name');
        });
    }
};
