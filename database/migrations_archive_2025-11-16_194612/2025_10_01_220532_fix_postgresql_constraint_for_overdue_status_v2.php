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
        // Only run this if we're using PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            // First, drop the existing constraint if it exists
            try {
                DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check');
                echo "Dropped existing constraint\n";
            } catch (Exception $e) {
                echo "No existing constraint to drop or error: " . $e->getMessage() . "\n";
            }
            
            // Now add the new constraint with all valid status values
            try {
                DB::statement(
                    "ALTER TABLE appointments ADD CONSTRAINT appointments_status_check 
                     CHECK (status IN ('pending', 'active', 'completed', 'cancelled', 'overdue'))"
                );
                echo "Successfully added new constraint with overdue status\n";
            } catch (Exception $e) {
                echo "Error adding constraint: " . $e->getMessage() . "\n";
                // If we can't add the constraint, let's try to see what values exist
                $statuses = DB::table('appointments')->distinct()->pluck('status');
                echo "Current status values in database: " . implode(', ', $statuses->toArray()) . "\n";
                throw $e;
            }
        } else {
            echo "Not PostgreSQL, skipping constraint fix\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run this if we're using PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            // Drop the constraint with overdue
            DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check');
            
            // Re-add the original constraint without overdue
            DB::statement(
                "ALTER TABLE appointments ADD CONSTRAINT appointments_status_check 
                 CHECK (status IN ('pending', 'active', 'completed', 'cancelled'))"
            );
        }
    }
};
