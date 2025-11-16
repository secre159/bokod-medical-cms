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
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('appointment_type', ['Regular', 'Follow-up', 'Emergency', 'Consultation']);
            $table->text('reason_for_visit')->nullable();
            $table->text('symptoms')->nullable();
            $table->enum('status', ['Scheduled', 'Confirmed', 'In Progress', 'Completed', 'Cancelled', 'No Show'])->default('Scheduled');
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_emergency')->default(false);
            $table->integer('priority')->default(1)->comment('1 = Low, 2 = Normal, 3 = High, 4 = Emergency');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('email_sent')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['appointment_date', 'appointment_time']);
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('status');
            $table->index('approval_status');
            $table->index('is_emergency');
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
