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
        // Update the status column to include 'overdue' option
        \DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('active', 'cancelled', 'completed', 'overdue') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert overdue appointments back to active before removing the enum value
        \DB::statement("UPDATE appointments SET status = 'active' WHERE status = 'overdue'");
        
        // Revert the status column to original enum values
        \DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('active', 'cancelled', 'completed') NOT NULL DEFAULT 'active'");
    }
};
