<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if appointment_id column exists and fix it
        if (Schema::hasTable('prescriptions')) {
            // First, check if the column exists
            if (!Schema::hasColumn('prescriptions', 'appointment_id')) {
                Schema::table('prescriptions', function (Blueprint $table) {
                    $table->unsignedBigInteger('appointment_id')->nullable()->after('patient_id');
                    $table->foreign('appointment_id')->references('appointment_id')->on('appointments')->onDelete('set null');
                });
            } else {
                // Column exists, but let's ensure proper foreign key constraint
                try {
                    // Drop existing foreign key if it exists
                    DB::statement('ALTER TABLE prescriptions DROP CONSTRAINT IF EXISTS prescriptions_appointment_id_foreign');
                    
                    // Recreate the foreign key constraint with proper reference
                    Schema::table('prescriptions', function (Blueprint $table) {
                        $table->foreign('appointment_id')->references('appointment_id')->on('appointments')->onDelete('set null');
                    });
                } catch (Exception $e) {
                    // If foreign key creation fails, that's okay - the column exists
                }
            }
        }
        
        // Ensure there are no orphaned prescriptions with invalid appointment_ids
        DB::statement('
            UPDATE prescriptions 
            SET appointment_id = NULL 
            WHERE appointment_id IS NOT NULL 
            AND appointment_id NOT IN (SELECT appointment_id FROM appointments)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop foreign key, not the column itself
        if (Schema::hasTable('prescriptions')) {
            try {
                Schema::table('prescriptions', function (Blueprint $table) {
                    $table->dropForeign(['appointment_id']);
                });
            } catch (Exception $e) {
                // Foreign key might not exist
            }
        }
    }
};
