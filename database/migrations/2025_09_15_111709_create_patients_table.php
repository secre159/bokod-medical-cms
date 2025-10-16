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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('patient_lname')->nullable();
            $table->string('patient_mname')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->date('birthday');
            $table->string('course')->nullable();
            $table->string('student_number')->nullable();
            $table->enum('civil_status', ['Single', 'Married', 'Divorced', 'Widowed']);
            $table->string('contact_person')->nullable();
            $table->string('contact_person_number')->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
            $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kg');
            $table->decimal('bmi', 5, 2)->nullable()->comment('Body Mass Index');
            $table->string('blood_type')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Patient account user ID');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['patient_name', 'patient_lname']);
            $table->index('student_number');
            $table->index('email');
            $table->index('archived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
