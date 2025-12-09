<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update the role column to ensure it can accept 'it' role value
     * This migration is only needed for databases that existed before IT role was added
     */
    public function up(): void
    {
        // Only run if users table exists and role column exists
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            // Use Laravel's Schema builder which handles both MySQL and PostgreSQL
            Schema::table('users', function (Blueprint $table) {
                // Change the role column to varchar to accept 'it' role
                $table->string('role', 50)->default('user')->change();
            });
        }
        // If users table doesn't exist yet, the comprehensive migration will create it with the correct type
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - role column remains as VARCHAR
    }
};
