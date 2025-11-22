<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * EMERGENCY SUPER ADMIN FIX ROUTE
 * 
 * This route is used to restore super admin access after a database restore
 * that wiped the is_super_admin flag.
 * 
 * Security: Requires authentication and specific query parameter
 */
Route::get('/emergency-restore-superadmin', function () {
    // Must be authenticated
    if (!auth()->check()) {
        abort(403, 'Authentication required');
    }
    
    // Require secret parameter for additional security
    if (request()->input('confirm') !== 'restore-admin-access') {
        abort(403, 'Invalid confirmation');
    }
    
    try {
        $userId = auth()->id();
        
        // Update current user to be super admin
        DB::table('users')
            ->where('id', $userId)
            ->update([
                'is_super_admin' => true,
                'updated_at' => now()
            ]);
        
        Log::info('Emergency super admin access restored', [
            'user_id' => $userId,
            'email' => auth()->user()->email,
            'ip' => request()->ip()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Super admin access restored successfully!',
            'user_id' => $userId,
            'email' => auth()->user()->email,
            'next_step' => 'You can now access /settings'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Emergency super admin fix failed', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to restore super admin access',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth')->name('emergency.superadmin.fix');
