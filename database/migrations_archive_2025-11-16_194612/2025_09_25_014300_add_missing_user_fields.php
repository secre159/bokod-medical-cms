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
            // Only add if column doesn't exist to prevent duplicate column errors
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 10)->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'emergency_phone')) {
                $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'date_of_birth', 
                'gender',
                'address',
                'emergency_contact',
                'emergency_phone',
                'medical_history',
                'allergies',
                'notes'
            ]);
        });
    }
};