<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Role-based dashboard
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } else {
            return $this->patientDashboard();
        }
    }
    
    private function adminDashboard()
    {
        $user = Auth::user();
        
        // Automatically update overdue appointments before showing dashboard
        // Temporarily disabled until PostgreSQL constraint is properly fixed
        // Appointment::updateOverdueAppointments();
        
        // Use Philippine timezone for consistent date comparisons
        $today = TimezoneHelper::now()->toDateString();
        $tomorrow = TimezoneHelper::now()->addDay()->toDateString();
        
        // Optimized statistics with reduced queries
        $stats = [];
        
        // Essential stats only (fast queries)
        $stats['total_patients'] = Patient::where('archived', false)->count();
        $stats['total_users'] = User::where('status', 'active')->count();
        
        // Today's appointments only
        $stats['appointments_today'] = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'active')
            ->count();
            
        // Pending approvals (most critical)
        $stats['pending_approvals'] = Appointment::where('approval_status', 'pending')
            ->where('status', 'active')
            ->count();
            
        // Pending registrations
        $stats['pending_registrations'] = User::where('registration_status', 'pending')
            ->where('role', 'patient')
            ->count();
        
        // Recent appointments with optimized eager loading (include today's appointments regardless of time to show overdue status)
        $upcomingDateLimit = TimezoneHelper::now()->addDays(2)->toDateString();
        $stats['upcoming_appointments'] = Appointment::select('appointment_id', 'patient_id', 'appointment_date', 'appointment_time', 'reason')
            ->with(['patient:id,patient_name'])
            ->where('appointment_date', '>=', $today)
            ->where('appointment_date', '<=', $upcomingDateLimit) // Reduced to 2 days
            ->where('status', 'active')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(3) // Reduced to 3
            ->get();
        
        // Recent unread messages for admin dashboard notifications
        $stats['recent_unread_messages'] = Message::whereHas('conversation', function($q) use ($user) {
                $q->forUser($user->id);
            })
            ->with(['sender', 'conversation.patient', 'conversation.admin'])
            ->notSentBy($user->id)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->each(function ($message) {
                if ($message->sender) {
                    $message->sender->avatar_url = $message->sender->getAvatarUrl(24);
                }
            });
        
        // Load other stats asynchronously via AJAX later
        $stats['load_async'] = true;
        
        return view('dashboard.admin', compact('stats'));
    }
    
    public function asyncStats()
    {
        // Return secondary statistics asynchronously
        // Use Philippine timezone for consistent date comparisons
        $today = TimezoneHelper::now()->toDateString();
        $tomorrow = TimezoneHelper::now()->addDay()->toDateString();
        
        $stats = [
            'appointments_tomorrow' => Appointment::whereDate('appointment_date', $tomorrow)
                ->where('status', 'active')
                ->count(),
            
            'active_prescriptions' => Prescription::where('status', 'active')
                ->where(function ($q) {
                    $todayInPhilippines = TimezoneHelper::now()->toDateString();
                    $q->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', $todayInPhilippines);
                })->count(),
            
            'low_stock_medicines' => Medicine::where('stock_quantity', '<=', 10)
                ->where('status', 'active')
                ->count(),
            
            'expiring_prescriptions' => Prescription::where('status', 'active')
                ->whereDate('expiry_date', '<=', TimezoneHelper::now()->addDays(7)->toDateString())
                ->count()
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Get recent unread messages for dashboard notifications
     */
    public function getRecentMessages()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return response()->json(['messages' => []]);
        }
        
        $messages = Message::whereHas('conversation', function($q) use ($user) {
                $q->forUser($user->id);
            })
            ->with(['sender', 'conversation.patient', 'conversation.admin'])
            ->notSentBy($user->id)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        $unreadCount = Message::whereHas('conversation', function($q) use ($user) {
                $q->forUser($user->id);
            })
            ->notSentBy($user->id)
            ->unread()
            ->count();
        
        // Add avatar URLs to messages
        $messages->each(function ($message) {
            if ($message->sender) {
                $message->sender->avatar_url = $message->sender->getAvatarUrl(24);
            }
        });
        
        return response()->json([
            'messages' => $messages,
            'unread_count' => $unreadCount
        ]);
    }
    
    private function patientDashboard()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', Auth::id())->first();
        
        if (!$patient) {
            return redirect()->route('dashboard.index')
                           ->with('error', 'Patient profile not found. Please contact the administrator.');
        }
        
        // Get unread messages count for patient
        $unreadMessagesCount = Message::whereHas('conversation', function($q) use ($user) {
                $q->forUser($user->id);
            })
            ->notSentBy($user->id)
            ->unread()
            ->count();
            
        // Recent unread messages for patient dashboard notifications
        $recentUnreadMessages = Message::whereHas('conversation', function($q) use ($user) {
                $q->forUser($user->id);
            })
            ->with(['sender', 'conversation.patient', 'conversation.admin'])
            ->notSentBy($user->id)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard.patient', compact('patient', 'unreadMessagesCount', 'recentUnreadMessages'));
    }
}
