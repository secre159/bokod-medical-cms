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
        Schema::create('patient_medication_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_visit_id')->constrained('patient_visits')->onDelete('cascade');
            $table->foreignId('medicine_details_id')->constrained('medicine_details')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('dosage', 8, 2)->default(1.0);
            $table->string('frequency')->nullable(); // e.g., "3 times daily"
            $table->text('instructions')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['patient_visit_id']);
            $table->index(['medicine_details_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_medication_history');
    }
};
