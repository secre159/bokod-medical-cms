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
            $table->text('address')->nullable();
            $table->string('position')->nullable(); // ID/Position number
            $table->enum('civil_status', ['Single', 'Married', 'Divorced', 'Widowed'])->default('Single');
            $table->string('course')->nullable();
            $table->decimal('bmi', 5, 2)->nullable();
            $table->string('blood_pressure')->nullable();
            $table->string('contact_person')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('archived')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index(['patient_name']);
            $table->index(['email']);
            $table->index(['archived']);
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
