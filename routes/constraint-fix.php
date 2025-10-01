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

/**
 * MANUAL Appointment Status Update Route
 * Visit: https://bokod-medical-cms.onrender.com/update-appointment-status/1
 */
Route::get('/update-appointment-status/{id}', function ($id) {
    try {
        // Find the appointment
        $appointment = DB::table('appointments')->where('appointment_id', $id)->first();
        
        if (!$appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Appointment not found',
                'appointment_id' => $id
            ]);
        }
        
        // Check if appointment is overdue
        $appointmentDateTime = $appointment->appointment_date . ' ' . $appointment->appointment_time;
        $isOverdue = now()->greaterThan($appointmentDateTime);
        
        $results = [];
        $results[] = "Appointment ID: {$appointment->appointment_id}";
        $results[] = "Current status: {$appointment->status}";
        $results[] = "Appointment date/time: {$appointmentDateTime}";
        $results[] = "Current time: " . now()->toDateTimeString();
        $results[] = "Is overdue: " . ($isOverdue ? 'Yes' : 'No');
        
        if ($isOverdue && $appointment->status !== 'overdue') {
            // First try to fix constraint if needed
            try {
                DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check');
                DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ('pending', 'active', 'completed', 'cancelled', 'overdue'))");
                $results[] = "✓ Constraint updated";
            } catch (Exception $e) {
                $results[] = "⚠ Constraint update: " . $e->getMessage();
            }
            
            // Update the appointment status
            try {
                DB::table('appointments')
                    ->where('appointment_id', $id)
                    ->update([
                        'status' => 'overdue',
                        'updated_at' => now()
                    ]);
                
                $results[] = "✅ Successfully updated appointment status to 'overdue'";
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Appointment status updated successfully!',
                    'details' => $results,
                    'appointment_id' => $id,
                    'new_status' => 'overdue'
                ]);
                
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update appointment status: ' . $e->getMessage(),
                    'details' => $results,
                    'suggestion' => 'Try visiting /emergency-fix-constraint first'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'no_update_needed',
                'message' => $isOverdue ? 'Appointment is already marked as overdue' : 'Appointment is not overdue yet',
                'details' => $results,
                'current_status' => $appointment->status
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error checking appointment: ' . $e->getMessage(),
            'appointment_id' => $id
        ], 500);
    }
});
