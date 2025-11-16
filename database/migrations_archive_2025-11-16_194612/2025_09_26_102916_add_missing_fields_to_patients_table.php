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
        Schema::table('patients', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('patients', 'phone')) {
                $table->string('phone')->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('patients', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('emergency_contact_address');
            }
            if (!Schema::hasColumn('patients', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('emergency_contact');
            }
            if (!Schema::hasColumn('patients', 'medical_history')) {
                $table->text('medical_history')->nullable()->after('emergency_phone');
            }
            if (!Schema::hasColumn('patients', 'allergies')) {
                $table->text('allergies')->nullable()->after('medical_history');
            }
            if (!Schema::hasColumn('patients', 'notes')) {
                $table->text('notes')->nullable()->after('allergies');
            }
            if (!Schema::hasColumn('patients', 'status')) {
                $table->string('status')->default('active')->after('notes');
            }
            if (!Schema::hasColumn('patients', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('status');
            }
        });
        
        // Add foreign key constraint for updated_by
        try {
            Schema::table('patients', function (Blueprint $table) {
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Drop foreign key first
            try {
                $table->dropForeign(['updated_by']);
            } catch (\Exception $e) {
                // Foreign key might not exist, ignore error
            }
            
            // Drop the columns
            $table->dropColumn([
                'phone',
                'emergency_contact',
                'emergency_phone',
                'medical_history',
                'allergies',
                'notes',
                'status',
                'updated_by'
            ]);
        });
    }
};
