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
            $table->string('packing')->nullable(); // e.g., "Strip", "Bottle"
            $table->integer('unit_value')->default(1); // e.g., 10 (tablets per strip)
            $table->string('unit_measure')->nullable(); // e.g., "tablets", "ml"
            $table->integer('stock_quantity')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['medicine_id']);
            $table->index(['stock_quantity']);
            $table->index(['expiry_date']);
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
