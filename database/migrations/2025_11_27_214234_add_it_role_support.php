<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration documents the addition of the 'it' role to the system.
     * No database schema changes are required as the 'role' column already
     * exists as a string type and can accommodate any role value.
     * 
     * Available roles:
     * - 'admin': Full system access
     * - 'patient': Patient portal access
     * - 'it': Settings-only access for IT personnel
     */
    public function up(): void
    {
        // No schema changes needed
        // The 'role' column in users table already supports string values
        // This migration exists for documentation purposes only
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No schema changes to reverse
    }
};
