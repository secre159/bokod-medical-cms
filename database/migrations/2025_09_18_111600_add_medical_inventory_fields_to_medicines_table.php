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
        Schema::table('medicines', function (Blueprint $table) {
            // Medical inventory tracking fields
            $table->string('stock_number')->nullable()->after('id'); // Article/Stock Number
            $table->string('unit_measure')->nullable()->after('unit'); // Unit of Measure
            $table->decimal('unit_value', 10, 2)->nullable()->after('unit_measure'); // Unit Value
            $table->integer('balance_per_card')->nullable()->after('stock_quantity'); // Balance per Card
            $table->integer('on_hand_per_count')->nullable()->after('balance_per_card'); // On Hand per Count
            $table->integer('shortage_overage')->default(0)->after('on_hand_per_count'); // Shortage/Overage
            $table->decimal('total_value', 12, 2)->nullable()->after('selling_price'); // Total Value
            $table->text('inventory_remarks')->nullable()->after('notes'); // Remarks
            
            // Add indexes for inventory tracking
            $table->index('stock_number');
            $table->index(['balance_per_card', 'on_hand_per_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn([
                'stock_number', 'unit_measure', 'unit_value', 
                'balance_per_card', 'on_hand_per_count', 'shortage_overage',
                'total_value', 'inventory_remarks'
            ]);
        });
    }
};
