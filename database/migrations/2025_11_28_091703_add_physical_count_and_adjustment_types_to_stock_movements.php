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
        // Only run if stock_movements table exists
        if (!Schema::hasTable('stock_movements')) {
            return;
        }

        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            // MySQL: Use ALTER TABLE MODIFY for ENUM
            DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('add', 'subtract', 'adjust', 'bulk_add', 'bulk_subtract', 'physical_count', 'adjustment_add', 'adjustment_subtract')");
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Use varchar and check constraint instead of enum
            // First, convert existing ENUM to VARCHAR if it exists
            DB::statement("ALTER TABLE stock_movements ALTER COLUMN type TYPE VARCHAR(50)");
            
            // Add check constraint for allowed values
            DB::statement("
                ALTER TABLE stock_movements 
                ADD CONSTRAINT stock_movements_type_check 
                CHECK (type IN ('add', 'subtract', 'adjust', 'bulk_add', 'bulk_subtract', 'physical_count', 'adjustment_add', 'adjustment_subtract'))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('stock_movements')) {
            return;
        }

        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            // Revert back to original enum values
            DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('add', 'subtract', 'adjust', 'bulk_add', 'bulk_subtract')");
        } elseif ($driver === 'pgsql') {
            // Drop the check constraint
            DB::statement("ALTER TABLE stock_movements DROP CONSTRAINT IF EXISTS stock_movements_type_check");
            
            // Re-add constraint with original values
            DB::statement("
                ALTER TABLE stock_movements 
                ADD CONSTRAINT stock_movements_type_check 
                CHECK (type IN ('add', 'subtract', 'adjust', 'bulk_add', 'bulk_subtract'))
            ");
        }
    }
};
