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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('medicine_name');
            $table->string('generic_name')->nullable();
            $table->text('description')->nullable();
            $table->enum('medicine_type', ['Tablet', 'Capsule', 'Syrup', 'Injection', 'Ointment', 'Drops', 'Other']);
            $table->string('manufacturer')->nullable();
            $table->text('indications')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('side_effects')->nullable();
            $table->decimal('price_per_unit', 10, 2)->nullable();
            $table->boolean('requires_prescription')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('medicine_name');
            $table->index('generic_name');
            $table->index('medicine_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
