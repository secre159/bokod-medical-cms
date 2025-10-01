<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/**
 * EMERGENCY PostgreSQL Constraint Fix Route
 * Visit: https://bokod-medical-cms.onrender.com/emergency-fix-constraint
 */
Route::get('/emergency-fix-constraint', function () {
    try {
        // Check if we're using PostgreSQL
        if (DB::getDriverName() !== 'pgsql') {
            return response()->json([
                'status' => 'skipped',
                'message' => 'Not using PostgreSQL database',
                'driver' => DB::getDriverName()
            ]);
        }

        $results = [];
        
        // Show current appointment statuses
        $currentStatuses = DB::table('appointments')->distinct()->pluck('status');
        $results[] = "Current appointment status values: " . $currentStatuses->implode(', ');
        
        // Drop existing constraint
        try {
            DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check');
            $results[] = "✓ Successfully dropped existing constraint";
        } catch (Exception $e) {
            $results[] = "⚠ Drop constraint failed: " . $e->getMessage();
        }
        
        // Add new constraint with overdue status
        try {
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ('pending', 'active', 'completed', 'cancelled', 'overdue'))");
            $results[] = "✅ Successfully added new constraint with 'overdue' status";
        } catch (Exception $e) {
            $results[] = "❌ Add constraint failed: " . $e->getMessage();
            throw $e;
        }
        
        // Verify constraint exists
        $constraintExists = DB::select("SELECT conname FROM pg_constraint WHERE conname = 'appointments_status_check'");
        if (!empty($constraintExists)) {
            $results[] = "✅ Constraint verification successful";
        } else {
            $results[] = "❌ Constraint verification failed";
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'PostgreSQL constraint fixed successfully!',
            'details' => $results,
            'next_step' => 'The website should now work properly. You can enable auto-updates again.'
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fix constraint: ' . $e->getMessage(),
            'details' => $results ?? [],
            'suggestion' => 'Contact support or check database permissions'
        ], 500);
    }
});