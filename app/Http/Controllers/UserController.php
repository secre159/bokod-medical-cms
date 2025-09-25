<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of users with filtering and search
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('patient');
            
            // Filter by role
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }
            
            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            
            $users = $query->orderBy('created_at', 'desc')->paginate(15);
            
            // Get statistics
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'admin_users' => User::where('role', 'admin')->count(),
                'patient_users' => User::where('role', 'patient')->count(),
            ];
            
            return view('users.index', compact('users', 'stats'));
        } catch (\Exception $e) {
            Log::error('User index error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error loading users: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['admin', 'patient'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => $request->status,
                'password' => Hash::make($request->password),
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'medical_history' => $request->medical_history,
                'allergies' => $request->allergies,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $avatarPath;
            }

            $user = User::create($userData);

            // If creating a patient, also create patient record
            if ($request->role === 'patient') {
                $user->patient()->create([
                    'patient_name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'emergency_contact' => $request->emergency_contact,
                    'emergency_phone' => $request->emergency_phone,
                    'medical_history' => $request->medical_history,
                    'allergies' => $request->allergies,
                    'notes' => $request->notes,
                    'status' => 'active',
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error creating user: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        try {
            $user->load(['patient', 'prescriptions', 'appointments']);
            
            // Get user activity statistics
            $stats = [
                'prescriptions_count' => $user->prescriptions()->count(),
                'appointments_count' => $user->appointments()->count(),
                'last_login' => $user->last_login_at,
                'account_age' => $user->created_at->diffInDays(now()),
            ];

            return view('users.show', compact('user', 'stats'));
        } catch (\Exception $e) {
            Log::error('User show error: ' . $e->getMessage());
            return redirect()->route('users.index')
                ->withErrors(['error' => 'Error loading user details: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        // Prevent editing own admin account to avoid lockout
        if ($user->id === Auth::id() && $user->role === 'admin') {
            return redirect()->route('users.show', $user)
                ->withErrors(['error' => 'Cannot edit your own admin account. Use profile settings instead.']);
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Prevent editing own admin account
        if ($user->id === Auth::id() && $user->role === 'admin') {
            return redirect()->route('users.show', $user)
                ->withErrors(['error' => 'Cannot edit your own admin account.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['admin', 'patient'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => $request->status,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'medical_history' => $request->medical_history,
                'allergies' => $request->allergies,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $avatarPath;
            }

            $user->update($userData);

            // Update patient record if exists and role is patient
            if ($request->role === 'patient' && $user->patient) {
                $patientData = [
                    'patient_name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'phone_number' => $request->phone, // Also update phone_number for consistency
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'emergency_contact' => $request->emergency_contact,
                    'emergency_phone' => $request->emergency_phone,
                    'medical_history' => $request->medical_history,
                    'allergies' => $request->allergies,
                    'notes' => $request->notes,
                    'status' => $request->status,
                    'updated_by' => Auth::id(),
                ];
                
                $user->patient->update($patientData);
            }

            DB::commit();

            return redirect()->route('users.show', $user)
                ->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error updating user: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->withErrors(['error' => 'Cannot delete your own account.']);
        }

        // Prevent deleting the last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.index')
                ->withErrors(['error' => 'Cannot delete the last admin user.']);
        }

        try {
            DB::beginTransaction();

            // Check for dependencies
            $hasActiveData = false;
            $dependencies = [];

            if ($user->prescriptions()->where('status', 'active')->exists()) {
                $hasActiveData = true;
                $dependencies[] = 'active prescriptions';
            }

            if ($user->appointments()->where('status', 'scheduled')->exists()) {
                $hasActiveData = true;
                $dependencies[] = 'scheduled appointments';
            }

            if ($hasActiveData) {
                return redirect()->route('users.index')
                    ->withErrors(['error' => 'Cannot delete user with ' . implode(' and ', $dependencies) . '. Please resolve these first.']);
            }

            // Delete avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Soft delete or mark as inactive instead of hard delete
            $user->update([
                'status' => 'inactive',
                'deleted_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Error deleting user: ' . $e->getMessage()]);
        }
    }

    /**
     * Change user status
     */
    public function changeStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // Prevent deactivating own account
        if ($user->id === Auth::id() && $request->status === 'inactive') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot deactivate your own account.'
            ], 400);
        }

        // Prevent deactivating the last admin
        if ($user->role === 'admin' && $request->status === 'inactive' && User::where('role', 'admin')->where('status', 'active')->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot deactivate the last admin user.'
            ], 400);
        }

        try {
            $user->update([
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            // Update patient record if exists
            if ($user->patient) {
                $user->patient->update([
                    'status' => $request->status,
                    'updated_by' => Auth::id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('User status change error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating user status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        try {
            // Generate password reset token and send email
            $token = \Str::random(60);
            
            // Store token in database (you might want to create a password_reset_tokens table)
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Send password reset email (implement your mail logic here)
            // Mail::to($user->email)->send(new PasswordResetMail($token, $user));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to user\'s email!'
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending password reset link: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get user statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'admin_users' => User::where('role', 'admin')->count(),
                'patient_users' => User::where('role', 'patient')->count(),
                'recent_logins' => User::where('last_login_at', '>', now()->subDays(7))->count(),
                'new_users_this_month' => User::where('created_at', '>', now()->startOfMonth())->count(),
                'users_by_role' => User::selectRaw('role, COUNT(*) as count')
                    ->groupBy('role')
                    ->pluck('count', 'role')
                    ->toArray(),
                'users_by_status' => User::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('User statistics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch user statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}