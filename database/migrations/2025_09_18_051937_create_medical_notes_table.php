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
        Schema::create('medical_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments', 'appointment_id')->onDelete('cascade');
            $table->foreignId('patient_visit_id')->nullable()->constrained('patient_visits')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('note_type')->default('general'); // general, treatment, observation, diagnosis
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('note_date')->useCurrent();
            $table->boolean('is_private')->default(false);
            $table->json('tags')->nullable(); // For categorization
            $table->timestamps();
            
            // Indexes
            $table->index(['patient_id']);
            $table->index(['appointment_id']);
            $table->index(['patient_visit_id']);
            $table->index(['note_type']);
            $table->index(['priority']);
            $table->index(['note_date']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_notes');
    }
};
