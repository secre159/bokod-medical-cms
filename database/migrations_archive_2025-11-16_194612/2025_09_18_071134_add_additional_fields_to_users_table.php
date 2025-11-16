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
            // Add avatar field (separate from profile_picture)
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            
            // Add personal information fields
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
            
            // Add emergency contact information
            if (!Schema::hasColumn('users', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable();
            }
            if (!Schema::hasColumn('users', 'emergency_phone')) {
                $table->string('emergency_phone', 20)->nullable();
            }
            
            // Add medical information (for patients)
            if (!Schema::hasColumn('users', 'medical_history')) {
                $table->text('medical_history')->nullable();
            }
            if (!Schema::hasColumn('users', 'allergies')) {
                $table->text('allergies')->nullable();
            }
            if (!Schema::hasColumn('users', 'notes')) {
                $table->text('notes')->nullable();
            }
            
            // Add audit fields
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
                // Add foreign key constraint only if column was just created
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['created_by']);
            
            // Drop the added columns
            $table->dropColumn([
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
                'created_by',
                'last_login_at',
            ]);
        });
    }
};
