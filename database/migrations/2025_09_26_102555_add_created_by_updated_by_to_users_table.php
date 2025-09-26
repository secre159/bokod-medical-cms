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
        Schema::table('users', function (Blueprint $table) {
            // Add created_by and updated_by columns if they don't exist
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('users', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
            
            // Add missing columns that might not exist
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('profile_picture');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('emergency_contact');
            }
            if (!Schema::hasColumn('users', 'medical_history')) {
                $table->text('medical_history')->nullable()->after('emergency_phone');
            }
            if (!Schema::hasColumn('users', 'allergies')) {
                $table->text('allergies')->nullable()->after('medical_history');
            }
            if (!Schema::hasColumn('users', 'notes')) {
                $table->text('notes')->nullable()->after('allergies');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('updated_by');
            }
        });
        
        // Add foreign key constraints if they don't exist
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign keys might already exist, ignore error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first
            try {
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
            } catch (\Exception $e) {
                // Foreign keys might not exist, ignore error
            }
            
            // Drop the columns
            $table->dropColumn([
                'created_by',
                'updated_by',
                'avatar',
                'phone',
                'date_of_birth',
                'gender',
                'address',
                'emergency_contact',
                'emergency_phone',
                'medical_history',
                'allergies',
                'notes',
                'last_login_at'
            ]);
        });
    }
};
