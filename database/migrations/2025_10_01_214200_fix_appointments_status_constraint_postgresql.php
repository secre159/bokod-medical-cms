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
        // For PostgreSQL, we need to drop and recreate the check constraint
        DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check');
        DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ('active', 'cancelled', 'completed', 'overdue'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any overdue appointments back to active
        DB::statement("UPDATE appointments SET status = 'active' WHERE status = 'overdue'");
        
        // Recreate the original constraint
        DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check');
        DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ('active', 'cancelled', 'completed'))");
    }
};