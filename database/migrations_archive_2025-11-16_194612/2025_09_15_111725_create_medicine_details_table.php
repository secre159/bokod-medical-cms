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
        Schema::create('medicine_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->onDelete('cascade');
            $table->string('unit_measure'); // e.g., 'mg', 'ml', 'tablet', 'capsule'
            $table->string('unit_value'); // e.g., '500', '250', '1'
            $table->string('packing')->nullable(); // e.g., 'Bottle of 100 tablets', 'Box of 10 vials'
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->decimal('minimum_stock', 10, 2)->default(10);
            $table->decimal('maximum_stock', 10, 2)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('supplier')->nullable();
            $table->date('date_received')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('medicine_id');
            $table->index(['unit_measure', 'unit_value']);
            $table->index('stock_quantity');
            $table->index('expiry_date');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_details');
    }
};
