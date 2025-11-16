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
        // First, update any existing 'archived' values to 'inactive' to maintain data consistency
        DB::statement("UPDATE users SET status = 'inactive' WHERE status = 'archived'");
        
        // Now modify the enum to include 'inactive' and remove 'archived'
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: update 'inactive' back to 'archived' and restore original enum
        DB::statement("UPDATE users SET status = 'archived' WHERE status = 'inactive'");
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'archived') NOT NULL DEFAULT 'active'");
    }
};
