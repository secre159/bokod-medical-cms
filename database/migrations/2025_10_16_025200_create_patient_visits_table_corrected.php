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
        // Only create if table doesn't exist
        if (!Schema::hasTable('patient_visits')) {
            Schema::create('patient_visits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                
                // Use appointment_id instead of id for appointments table reference
                $table->unsignedBigInteger('appointment_id')->nullable();
                $table->foreign('appointment_id')->references('appointment_id')->on('appointments')->onDelete('set null');
                
                $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('nurse_id')->nullable()->constrained('users')->onDelete('set null');
                $table->date('visit_date');
                $table->date('next_visit_date')->nullable();
                
                // Vital Signs
                $table->string('bp')->nullable()->comment('Blood pressure');
                $table->decimal('temperature', 5, 2)->nullable()->comment('Temperature in Celsius');
                $table->integer('pulse_rate')->nullable()->comment('Pulse rate per minute');
                $table->integer('rr')->nullable()->comment('Respiratory rate per minute');
                $table->integer('spo2')->nullable()->comment('Oxygen saturation percentage');
                $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
                $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kg');
                $table->decimal('bmi', 5, 2)->nullable()->comment('Body Mass Index');
                
                // Medical Information
                $table->string('disease')->nullable();
                $table->text('symptoms')->nullable();
                $table->text('diagnosis')->nullable();
                $table->text('treatment_plan')->nullable();
                $table->text('notes')->nullable();
                $table->text('doctor_observations')->nullable();
                $table->text('recommendations')->nullable();
                
                // Visit Status
                $table->enum('visit_type', ['Regular', 'Follow-up', 'Emergency', 'Consultation'])->default('Regular');
                $table->enum('status', ['In Progress', 'Completed', 'Cancelled'])->default('In Progress');
                $table->timestamp('completed_at')->nullable();
                
                $table->timestamps();

                // Indexes
                $table->index('patient_id');
                $table->index('visit_date');
                $table->index('appointment_id');
                $table->index('doctor_id');
                $table->index('status');
                $table->index('visit_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_visits');
    }
};