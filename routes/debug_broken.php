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

// Test routes for profile picture upload (no auth needed for testing)
Route::get('/test-imgbb-config', function () {
    try {
        $imgbbService = new \App\Services\ImgBBService();
        
        return response()->json([
            'configured' => $imgbbService->isConfigured(),
            'api_key_set' => env('IMGBB_API_KEY') ? 'YES' : 'NO',
            'api_key_preview' => env('IMGBB_API_KEY') ? substr(env('IMGBB_API_KEY'), 0, 8) . '...' : 'NOT SET',
            'config_value' => config('services.imgbb.key') ? 'SET' : 'NOT SET',
            'connection_test' => $imgbbService->testConnection()
        ], 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

Route::get('/test-upload-form', function () {
    $users = \App\Models\User::select('id', 'name', 'email')->get();
    
    return view('test-upload', ['users' => $users]);
});

Route::post('/test-upload-submit', function (\Illuminate\Http\Request $request) {
    try {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'user_id' => 'required|exists:users,id'
        ]);
        
        $user = \App\Models\User::findOrFail($request->user_id);
        $file = $request->file('profile_picture');
        
        // Try ImgBB upload first
        $imgbbService = new \App\Services\ImgBBService();
        $result = $imgbbService->uploadProfilePicture($file, $user->id);
        
        if ($result['success']) {
            // Update user profile picture
            $user->update(['profile_picture' => $result['url']]);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile picture uploaded successfully!',
                'imgbb_result' => $result,
                'user_id' => $user->id,
                'updated_user' => $user->fresh()
            ], 200, [], JSON_PRETTY_PRINT);
        } else {
            // Fallback to local storage
            $path = $file->store('profile_pictures', 'public');
            $url = \Storage::disk('public')->url($path);
            $user->update(['profile_picture' => $url]);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile picture uploaded successfully (local fallback)!',
                'fallback_used' => true,
                'imgbb_error' => $result['error'],
                'local_url' => $url,
                'user_id' => $user->id,
                'updated_user' => $user->fresh()
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 422);
    }
});

Route::get('/debug/profile-picture', function () {
    $users = \App\Models\User::select('id', 'name', 'email', 'profile_picture', 'created_at', 'updated_at')
                             ->orderBy('updated_at', 'desc')
                             ->limit(10)
                             ->get();
    
    return response()->json([
        'recent_users' => $users,
        'total_users_with_pictures' => \App\Models\User::whereNotNull('profile_picture')->count(),
        'total_users' => \App\Models\User::count(),
        'imgbb_configured' => config('services.imgbb.key') ? true : false
    ], 200, [], JSON_PRETTY_PRINT);
});

// Debug route to test patient profile update flow
Route::get('/test-patient-profile', function () {
    return view('test-patient-profile');
});

// Quick login as patient for testing
Route::get('/test-login-patient', function () {
    $user = \App\Models\User::find(12); // secre - patient user
    if ($user) {
        \Auth::login($user);
        return redirect('/my-profile/edit')->with('success', 'Logged in as patient user for testing');
    }
    return response('Patient user not found', 404);
});

// Debug route to intercept patient profile form submission
Route::get('/debug-patient-data/{userId}', function ($userId) {
    $user = \App\Models\User::find($userId);
    if (!$user) {
        return response()->json(['error' => 'User not found']);
    }
    
    $patient = \App\Models\Patient::where('user_id', $userId)->first();
    if (!$patient) {
        return response()->json(['error' => 'Patient not found']);
    }
    
    return response()->json([
        'user' => $user->toArray(),
        'patient' => $patient->toArray(),
        'validation_data' => [
            'user_email' => $user->email,
            'patient_email' => $patient->email,
            'phone_number' => $patient->phone_number,
            'address' => $patient->address,
            'course' => $patient->course,
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});

Route::patch('/debug-my-profile', function (\Illuminate\Http\Request $request) {
    \Log::info('DEBUG INTERCEPT: Patient profile form submitted', [
        'user_id' => auth()->id(),
        'user_role' => auth()->user()->role ?? 'not logged in',
        'request_method' => $request->method(),
        'all_data' => $request->all(),
        'has_files' => $request->hasFile('profile_picture'),
        'file_info' => $request->hasFile('profile_picture') ? [
            'name' => $request->file('profile_picture')->getClientOriginalName(),
            'size' => $request->file('profile_picture')->getSize(),
            'mime' => $request->file('profile_picture')->getMimeType()
        ] : null,
        'headers' => $request->headers->all()
    ]);
    
    return response()->json([
        'message' => 'Form submission intercepted for debugging',
        'user' => auth()->user()->only('id', 'name', 'email', 'role'),
        'has_profile_picture' => $request->hasFile('profile_picture'),
        'data_received' => $request->all()
    ]);
})->middleware('auth');

// Route to check current authentication state and patient data
Route::get('/debug-auth-state', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $patient = \App\Models\Patient::where('user_id', $user->id)->first();
        
        return response()->json([
            'authenticated' => true,
            'user' => $user->only('id', 'name', 'email', 'role', 'profile_picture', 'updated_at'),
            'patient_exists' => $patient ? true : false,
            'patient_data' => $patient ? $patient->only('id', 'email', 'phone_number', 'address') : null,
            'session_data' => [
                'session_id' => session()->getId(),
                'csrf_token' => csrf_token(),
                'profile_updated_flags' => session()->all()
            ]
        ], 200, [], JSON_PRETTY_PRINT);
    } else {
        return response()->json([
            'authenticated' => false,
            'message' => 'User not logged in'
        ], 401);
    }
})->middleware('web');



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
            $userData['password'] = \Hash::make('password123');
            $userData['registration_source'] = \App\Models\User::SOURCE_ADMIN;
            
            $user = \App\Models\User::create($userData);
            $created[] = $user;
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Test users created successfully',
            'users' => $created
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
});

Route::post('/debug/patient-profile-update/{userId}', function (\Illuminate\Http\Request $request, $userId) {
    try {
        $user = \App\Models\User::findOrFail($userId);
        $patient = \App\Models\Patient::where('user_id', $userId)->first();
        
        if (!$patient) {
            return response()->json(['error' => 'Patient not found for user'], 404);
        }
        
        \Log::info('DEBUG: Patient profile update started', [
            'user_id' => $userId,
            'has_profile_picture' => $request->hasFile('profile_picture'),
            'request_data' => $request->all(),
            'current_profile_picture' => $user->profile_picture
        ]);
        
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            
            \Log::info('DEBUG: File upload details', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'is_valid' => $file->isValid()
            ]);
            
            // Use the same logic as PatientProfileController
            $uploadSuccess = false;
            $profilePictureUrl = null;
            
            // Try ImgBB first
            try {
                $imgbbService = new \App\Services\ImgBBService();
                $uploadResult = $imgbbService->uploadProfilePicture($file, $user->id);
                
                if ($uploadResult['success']) {
                    $profilePictureUrl = $uploadResult['url'];
                    $uploadSuccess = true;
                    \Log::info('DEBUG: ImgBB upload successful', ['url' => $profilePictureUrl]);
                }
            } catch (\Exception $e) {
                \Log::warning('DEBUG: ImgBB upload failed', ['error' => $e->getMessage()]);
            }
            
            // Fallback to local storage if ImgBB failed
            if (!$uploadSuccess) {
                $localService = new \App\Services\LocalProfilePictureService();
                $localResult = $localService->uploadProfilePicture($file, $user->id);
                
                if ($localResult['success']) {
                    $profilePictureUrl = $localResult['url'];
                    \Log::info('DEBUG: Local storage upload successful', ['url' => $profilePictureUrl]);
                } else {
                    throw new \Exception('Profile picture upload failed: ' . $localResult['error']);
                }
            }
            
            // Update the user's profile picture
            if ($profilePictureUrl) {
                \Log::info('DEBUG: Updating user profile picture', [
                    'user_id' => $user->id,
                    'old_profile_picture' => $user->profile_picture,
                    'new_profile_picture' => $profilePictureUrl
                ]);
                
                $user->update(['profile_picture' => $profilePictureUrl]);
                
                // Verify the update
                $user->refresh();
                \Log::info('DEBUG: Profile picture update verification', [
                    'user_id' => $user->id,
                    'current_profile_picture' => $user->profile_picture,
                    'update_successful' => $user->profile_picture === $profilePictureUrl
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully!',
                    'old_url' => $request->get('old_profile_picture'),
                    'new_url' => $profilePictureUrl,
                    'user' => $user->fresh(),
                    'upload_method' => $uploadSuccess ? 'imgbb' : 'local'
                ]);
            }
        }
        
        return response()->json(['error' => 'No file uploaded'], 400);
        
    } catch (\Exception $e) {
        \Log::error('DEBUG: Patient profile update failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
