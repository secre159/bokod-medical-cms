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
        Schema::create('patient_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->date('visit_date');
            $table->date('next_visit_date')->nullable();
            $table->string('bp')->nullable(); // Blood pressure
            $table->decimal('temperature', 5, 2)->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('rr')->nullable(); // Respiratory rate
            $table->integer('spo2')->nullable(); // Oxygen saturation
            $table->string('disease')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['patient_id']);
            $table->index(['visit_date']);
            $table->index(['disease']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_visits');
    }
};
