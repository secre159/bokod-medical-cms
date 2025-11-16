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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->unsignedBigInteger('medicine_id')->nullable(); // Will add foreign key when medicines table exists
            $table->string('medicine_name'); // Store medicine name in case medicine is deleted
            $table->integer('quantity');
            $table->string('dosage');
            $table->text('instructions')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled', 'expired'])->default('active');
            $table->date('prescribed_date');
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'status']);
            $table->index('prescribed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
