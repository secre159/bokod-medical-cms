<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensure all optional fields across tables are properly marked as nullable
     */
    public function up(): void
    {
        // Appointments table - add missing nullable fields
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('reschedule_reason');
            }
            if (!Schema::hasColumn('appointments', 'treatment_notes')) {
                $table->text('treatment_notes')->nullable()->after('diagnosis');
            }
        });
        
        // Ensure existing columns that should be nullable are updated
        // For PostgreSQL, we need to use raw SQL for ALTER COLUMN
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Patients table - blood pressure columns
            if (Schema::hasColumn('patients', 'systolic_bp')) {
                DB::statement('ALTER TABLE patients ALTER COLUMN systolic_bp DROP NOT NULL');
            }
            if (Schema::hasColumn('patients', 'diastolic_bp')) {
                DB::statement('ALTER TABLE patients ALTER COLUMN diastolic_bp DROP NOT NULL');
            }
            
            // Users table - ensure created_by is nullable
            if (Schema::hasColumn('users', 'created_by')) {
                DB::statement('ALTER TABLE users ALTER COLUMN created_by DROP NOT NULL');
            }
            
            // Medicines table - ensure unit_measure is properly nullable
            if (Schema::hasColumn('medicines', 'unit_measure')) {
                DB::statement('ALTER TABLE medicines ALTER COLUMN unit_measure DROP NOT NULL');
            }
        }
        
        // Add any missing columns to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('updated_by');
            }
            if (!Schema::hasColumn('users', 'profile_picture_public_id')) {
                $table->string('profile_picture_public_id')->nullable()->after('profile_picture');
            }
        });
        
        // Add missing blood pressure columns to patients if needed
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'systolic_bp')) {
                $table->integer('systolic_bp')->nullable()->after('bmi');
            }
            if (!Schema::hasColumn('patients', 'diastolic_bp')) {
                $table->integer('diastolic_bp')->nullable()->after('systolic_bp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
            if (Schema::hasColumn('appointments', 'treatment_notes')) {
                $table->dropColumn('treatment_notes');
            }
        });
        
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('users', 'profile_picture_public_id')) {
                $table->dropColumn('profile_picture_public_id');
            }
        });
        
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'systolic_bp')) {
                $table->dropColumn('systolic_bp');
            }
            if (Schema::hasColumn('patients', 'diastolic_bp')) {
                $table->dropColumn('diastolic_bp');
            }
        });
    }
};
