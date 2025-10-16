<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// TEMPORARY ROUTE - REMOVE AFTER FIXING PRODUCTION
Route::get('/fix-prescriptions-migration', function () {
    try {
        // Run prescriptions table creation
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_09_17_024402_create_prescriptions_table.php'
        ]);
        
        // Add comprehensive fields
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_09_17_030723_add_comprehensive_fields_to_prescriptions_table.php'
        ]);
        
        // Add generic_name column
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_09_18_080718_add_generic_name_to_prescriptions_table.php'
        ]);
        
        // Add appointment_id column
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_09_17_125546_add_appointment_id_to_prescriptions_table.php'
        ]);
        
        // Add consultation_type column
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_09_18_084419_add_consultation_type_to_prescriptions_table.php'
        ]);
        
        // Add prescribed_by column using raw SQL since we don't have a specific migration
        DB::statement('ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS prescribed_by BIGINT');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Prescriptions table migrations completed successfully! Added prescribed_by column.',
            'output' => Artisan::output()
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
})->middleware('auth'); // Only allow authenticated users