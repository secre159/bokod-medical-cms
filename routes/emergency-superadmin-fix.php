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

/**
 * EMERGENCY DATABASE MIGRATION ROUTE
 * 
 * Run pending database migrations after a restore
 * Security: Requires authentication and super admin status
 */
Route::get('/emergency-run-migrations', function () {
    // Must be authenticated
    if (!auth()->check()) {
        abort(403, 'Authentication required');
    }
    
    // Must be super admin
    $user = auth()->user();
    if (!$user->is_super_admin) {
        abort(403, 'Super admin access required');
    }
    
    // Require confirmation parameter
    if (request()->input('confirm') !== 'run-migrations-now') {
        abort(403, 'Invalid confirmation');
    }
    
    try {
        // Run migrations
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        
        $output = \Illuminate\Support\Facades\Artisan::output();
        
        Log::info('Emergency migrations executed', [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'output' => $output
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Migrations executed successfully!',
            'output' => $output,
            'user' => auth()->user()->email
        ]);
        
    } catch (\Exception $e) {
        Log::error('Emergency migrations failed', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to run migrations',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth')->name('emergency.migrations');

/**
 * EMERGENCY TABLE CREATION ROUTE
 * 
 * Manually create missing tables after a restore
 * Security: Requires authentication and super admin status
 */
Route::get('/emergency-create-tables', function () {
    // Must be authenticated
    if (!auth()->check()) {
        abort(403, 'Authentication required');
    }
    
    // Must be super admin
    $user = auth()->user();
    if (!$user->is_super_admin) {
        abort(403, 'Super admin access required');
    }
    
    // Require confirmation parameter
    if (request()->input('confirm') !== 'create-tables-now') {
        abort(403, 'Invalid confirmation');
    }
    
    try {
        $results = [];
        
        // Check if stock_movements table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('stock_movements')) {
            // Create stock_movements table
            \Illuminate\Support\Facades\Schema::create('stock_movements', function ($table) {
                $table->id();
                $table->unsignedBigInteger('medicine_id');
                $table->unsignedBigInteger('user_id');
                $table->string('type'); // 'add', 'subtract', 'adjust', 'bulk_add', 'bulk_subtract'
                $table->integer('quantity_changed');
                $table->integer('quantity_before');
                $table->integer('quantity_after');
                $table->string('reason');
                $table->text('notes')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();
                
                $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['medicine_id', 'created_at']);
            });
            
            $results[] = 'stock_movements table created successfully';
        } else {
            $results[] = 'stock_movements table already exists';
        }
        
        // Check if departments table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('departments')) {
            // Create departments table
            \Illuminate\Support\Facades\Schema::create('departments', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
            
            $results[] = 'departments table created successfully';
        } else {
            $results[] = 'departments table already exists';
        }
        
        Log::info('Emergency table creation executed', [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'results' => $results
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Table creation completed!',
            'results' => $results,
            'user' => auth()->user()->email
        ]);
        
    } catch (\Exception $e) {
        Log::error('Emergency table creation failed', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to create tables',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth')->name('emergency.create.tables');
