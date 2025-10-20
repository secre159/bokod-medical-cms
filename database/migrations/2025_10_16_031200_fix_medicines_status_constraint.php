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
        // Drop existing check constraint if it exists
        try {
            DB::statement('ALTER TABLE medicines DROP CONSTRAINT IF EXISTS medicines_status_check');
        } catch (\Exception $e) {
            // Constraint may not exist, continue
        }
        
        // Recreate the status column with correct enum values
        DB::statement("ALTER TABLE medicines DROP CONSTRAINT IF EXISTS medicines_status_check");
        DB::statement("ALTER TABLE medicines ADD CONSTRAINT medicines_status_check CHECK (status IN ('active', 'inactive', 'expired', 'discontinued'))");
        
        // Update any invalid status values to 'active'
        DB::statement("UPDATE medicines SET status = 'active' WHERE status NOT IN ('active', 'inactive', 'expired', 'discontinued')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint
        DB::statement('ALTER TABLE medicines DROP CONSTRAINT IF EXISTS medicines_status_check');
    }
};