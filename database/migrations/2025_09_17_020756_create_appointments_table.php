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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->text('reason')->nullable();
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->enum('reschedule_status', ['none', 'pending'])->default('none');
            $table->date('requested_date')->nullable();
            $table->time('requested_time')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['appointment_date']);
            $table->index(['status']);
            $table->index(['approval_status']);
            $table->index(['patient_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
