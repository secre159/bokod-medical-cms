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
            $table->foreignId('prescribed_by')->nullable()->constrained('users')->onDelete('set null')->comment('Doctor who prescribed');
            $table->decimal('quantity', 8, 2)->comment('Quantity prescribed');
            $table->decimal('dosage', 8, 2)->comment('Dosage amount');
            $table->string('dosage_unit')->default('mg')->comment('mg, ml, tablets, etc.');
            $table->string('frequency')->nullable()->comment('e.g., 3 times a day, twice daily');
            $table->string('duration')->nullable()->comment('e.g., 7 days, 2 weeks');
            $table->text('instructions')->nullable()->comment('Special instructions for medication');
            $table->enum('status', ['Prescribed', 'Dispensed', 'Completed', 'Discontinued'])->default('Prescribed');
            $table->timestamp('dispensed_at')->nullable();
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->onDelete('set null')->comment('Pharmacist who dispensed');
            $table->text('pharmacist_notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('patient_visit_id');
            $table->index('medicine_details_id');
            $table->index('prescribed_by');
            $table->index('status');
            $table->index('dispensed_at');
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
