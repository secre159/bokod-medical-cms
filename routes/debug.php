<?php

use Illuminate\Support\Facades\Route;

// Basic debug routes without middleware
Route::get('/debug/test-basic', function () {
    return response()->json([
        'message' => 'Basic debug route working',
        'timestamp' => now(),
        'app_env' => app()->environment(),
        'middleware_test' => 'passed'
    ]);
});

// Simple authentication testing route (no middleware)
Route::get('/debug/auth-test-simple', function () {
    return response()->json([
        'message' => 'Debug route working',
        'authenticated' => auth()->check(),
        'user' => auth()->check() ? auth()->user()->only('id', 'name', 'email', 'role', 'status') : null,
        'timestamp' => now()
    ], 200, [], JSON_PRETTY_PRINT);
});

// Authentication testing routes (no middleware to avoid session issues)
Route::get('/debug/auth-test', function () {
    try {
        return view('debug.auth-test');
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to load debug view',
            'message' => $e->getMessage(),
            'authenticated' => auth()->check(),
            'session_available' => request()->hasSession()
        ]);
    }
});

// Test user management routes (no middleware for debugging)
Route::get('/debug/create-test-users', function () {
    try {
        $testUsers = [
            [
                'name' => 'Pending Patient',
                'email' => 'pending@test.com',
                'role' => 'patient',
                'status' => 'inactive',
                'registration_status' => \App\Models\User::REGISTRATION_PENDING,
            ],
            [
                'name' => 'Approved Patient',
                'email' => 'approved@test.com',
                'role' => 'patient',
                'status' => 'active',
                'registration_status' => \App\Models\User::REGISTRATION_APPROVED,
                'approved_at' => now(),
            ],
            [
                'name' => 'Rejected Patient',
                'email' => 'rejected@test.com',
                'role' => 'patient',
                'status' => 'inactive',
                'registration_status' => \App\Models\User::REGISTRATION_REJECTED,
                'rejection_reason' => 'Test rejection reason',
            ],
            [
                'name' => 'Deactivated Patient',
                'email' => 'deactivated@test.com',
                'role' => 'patient',
                'status' => 'inactive',
                'registration_status' => \App\Models\User::REGISTRATION_APPROVED,
                'approved_at' => now(),
            ]
        ];
        
        $created = [];
        
        foreach ($testUsers as $userData) {
            // Skip if user already exists
            if (\App\Models\User::where('email', $userData['email'])->exists()) {
                continue;
            }
            
            $userData['password'] = \Hash::make('password123');
            $userData['registration_source'] = \App\Models\User::SOURCE_ADMIN;
            
            $user = \App\Models\User::create($userData);
            $created[] = $user;
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Test users created successfully',
            'users' => $created,
            'created_count' => count($created)
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

Route::delete('/debug/cleanup-test-users', function () {
    try {
        $testEmails = [
            'pending@test.com',
            'approved@test.com', 
            'rejected@test.com',
            'deactivated@test.com'
        ];
        
        $deleted = \App\Models\User::whereIn('email', $testEmails)->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} test users"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/debug/user-status/{userId}', function ($userId) {
    try {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'registration_status' => $user->registration_status,
            'approved_at' => $user->approved_at,
            'rejection_reason' => $user->rejection_reason,
            'checks' => [
                'isActive' => $user->isActive(),
                'isAdmin' => $user->isAdmin(),
                'isPatient' => $user->isPatient(),
                'isRegistrationPending' => $user->isRegistrationPending(),
                'isRegistrationApproved' => $user->isRegistrationApproved(),
                'isRegistrationRejected' => $user->isRegistrationRejected(),
                'canLogin' => $user->isActive() && ($user->isAdmin() || $user->isRegistrationApproved()),
            ],
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ], 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Routes that need authentication
Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/debug-patient-profile', function () {
        $patient = \App\Models\Patient::where('user_id', auth()->id())->first();
        
        if (!$patient) {
            return response()->json(['error' => 'Patient not found']);
        }
        
        return response()->json([
            'patient_id' => $patient->id,
            'user_id' => $patient->user_id,
            'user_name' => $patient->user->name,
            'current_profile_picture' => $patient->user->profile_picture,
            'has_profile_picture' => $patient->user->hasProfilePicture(),
            'patient_data' => $patient->toArray(),
            'user_data' => $patient->user->toArray()
        ], 200, [], JSON_PRETTY_PRINT);
    });
});
