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
        // Modify the enum column to add new types for physical count and adjustments
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('add', 'subtract', 'adjust', 'bulk_add', 'bulk_subtract', 'physical_count', 'adjustment_add', 'adjustment_subtract')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('add', 'subtract', 'adjust', 'bulk_add', 'bulk_subtract')");
    }
};
