<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('prescriptions') && !Schema::hasColumn('prescriptions', 'appointment_id')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                // Add nullable foreign key to appointments.appointment_id
                $table->unsignedBigInteger('appointment_id')->nullable()->after('patient_id');

                // Create the foreign key constraint safely if appointments table exists
                if (Schema::hasTable('appointments')) {
                    // Use raw foreign to specify non-standard PK column name
                    $table->foreign('appointment_id')
                          ->references('appointment_id')
                          ->on('appointments')
                          ->onDelete('set null');
                }

                // Index for performance
                $table->index('appointment_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('prescriptions') && Schema::hasColumn('prescriptions', 'appointment_id')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                // Drop FK if exists (ignore errors if name differs)
                try {
                    $table->dropForeign(['appointment_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                try {
                    $table->dropIndex(['appointment_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('appointment_id');
            });
        }
    }
};