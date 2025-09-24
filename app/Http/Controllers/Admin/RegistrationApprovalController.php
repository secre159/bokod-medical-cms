<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RegistrationApprovalController extends Controller
{
    /**
     * Display pending registrations
     */
    public function index(Request $request)
    {
        $query = User::with(['patient'])
                    ->where('role', User::ROLE_PATIENT)
                    ->where('registration_source', User::SOURCE_SELF);
        
        // Filter by registration status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('registration_status', $status);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($patientQuery) use ($search) {
                      $patientQuery->where('position', 'like', "%{$search}%")
                                  ->orWhere('course', 'like', "%{$search}%");
                  });
            });
        }
        
        $registrations = $query->latest()->paginate(15);
        
        // Get counts for tabs
        $counts = [
            'pending' => User::where('role', User::ROLE_PATIENT)
                            ->where('registration_source', User::SOURCE_SELF)
                            ->where('registration_status', User::REGISTRATION_PENDING)
                            ->count(),
            'approved' => User::where('role', User::ROLE_PATIENT)
                             ->where('registration_source', User::SOURCE_SELF)
                             ->where('registration_status', User::REGISTRATION_APPROVED)
                             ->count(),
            'rejected' => User::where('role', User::ROLE_PATIENT)
                             ->where('registration_source', User::SOURCE_SELF)
                             ->where('registration_status', User::REGISTRATION_REJECTED)
                             ->count(),
        ];
        $counts['all'] = array_sum($counts);
        
        return view('admin.registrations.index', compact('registrations', 'counts', 'status'));
    }
    
    /**
     * Show detailed registration information
     */
    public function show(User $user)
    {
        $user->load('patient');
        
        // Ensure this is a self-registered patient
        if ($user->role !== User::ROLE_PATIENT || $user->registration_source !== User::SOURCE_SELF) {
            abort(404);
        }
        
        return view('admin.registrations.show', compact('user'));
    }
    
    /**
     * Approve a registration
     */
    public function approve(Request $request, User $user)
    {
        // Validate that this is a pending self-registration
        if ($user->role !== User::ROLE_PATIENT || 
            $user->registration_source !== User::SOURCE_SELF ||
            $user->registration_status !== User::REGISTRATION_PENDING) {
            return back()->with('error', 'Invalid registration or already processed.');
        }
        
        try {
            DB::beginTransaction();
            
            // Update user status
            $user->update([
                'registration_status' => User::REGISTRATION_APPROVED,
                'status' => User::STATUS_ACTIVE,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'email_verified_at' => now(), // Auto-verify email on approval
            ]);
            
            // Send approval email
            $this->sendApprovalEmail($user);
            
            DB::commit();
            
            return back()->with('success', "Registration approved! {$user->name} can now access the health portal.");
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve registration: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a registration
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        // Validate that this is a pending self-registration
        if ($user->role !== User::ROLE_PATIENT || 
            $user->registration_source !== User::SOURCE_SELF ||
            $user->registration_status !== User::REGISTRATION_PENDING) {
            return back()->with('error', 'Invalid registration or already processed.');
        }
        
        try {
            DB::beginTransaction();
            
            // Update user status
            $user->update([
                'registration_status' => User::REGISTRATION_REJECTED,
                'status' => User::STATUS_ARCHIVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'rejection_reason' => $request->rejection_reason,
            ]);
            
            // Send rejection email
            $this->sendRejectionEmail($user, $request->rejection_reason);
            
            DB::commit();
            
            return back()->with('success', "Registration rejected. {$user->name} has been notified.");
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject registration: ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk approve registrations
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        $approvedCount = 0;
        
        try {
            DB::beginTransaction();
            
            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                
                if ($user && 
                    $user->role === User::ROLE_PATIENT && 
                    $user->registration_source === User::SOURCE_SELF &&
                    $user->registration_status === User::REGISTRATION_PENDING) {
                    
                    $user->update([
                        'registration_status' => User::REGISTRATION_APPROVED,
                        'status' => User::STATUS_ACTIVE,
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                        'email_verified_at' => now(),
                    ]);
                    
                    $this->sendApprovalEmail($user);
                    $approvedCount++;
                }
            }
            
            DB::commit();
            
            return back()->with('success', "Successfully approved {$approvedCount} registration(s).");
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process bulk approval: ' . $e->getMessage());
        }
    }
    
    /**
     * Send approval email to user
     */
    private function sendApprovalEmail(User $user)
    {
        try {
            // Using a simple mail approach - you can enhance this with Mailable classes
            Mail::send('emails.registration-approved', ['user' => $user], function($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('BSU Health Portal - Registration Approved');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get pending registrations count for dashboard
     */
    public function pendingCount()
    {
        $count = User::where('role', User::ROLE_PATIENT)
                    ->where('registration_source', User::SOURCE_SELF)
                    ->where('registration_status', User::REGISTRATION_PENDING)
                    ->count();
                    
        return response()->json(['pending_count' => $count]);
    }
    
    /**
     * Send rejection email to user
     */
    private function sendRejectionEmail(User $user, string $reason)
    {
        try {
            Mail::send('emails.registration-rejected', [
                'user' => $user, 
                'reason' => $reason
            ], function($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('BSU Health Portal - Registration Status');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }
}
