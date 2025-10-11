<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Services\EnhancedEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

class AppointmentController extends Controller
{
    protected $emailService;
    
    public function __construct(EnhancedEmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        // Simple debug - log that we reached the controller
        \Log::info('AppointmentController@index called', [
            'status' => $request->get('status'),
            'has_filters' => !empty($request->get('status')) || !empty($request->get('approval')) || !empty($request->get('date_filter')) || !empty($request->get('search'))
        ]);
        
        $query = Appointment::with('patient');
        
        // Filter by status
        if ($request->filled('status')) {
            $query = $query->where('status', $request->status);
        }
        // Don't filter by default - show all appointments unless specifically filtered
        
        // Filter by approval status
        if ($request->filled('approval')) {
            $query = $query->where('approval_status', $request->approval);
        }
        
        // Filter by date range
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query = $query->today();
                    break;
                case 'week':
                    $query = $query->thisWeek();
                    break;
                case 'upcoming':
                    $query = $query->upcoming();
                    break;
                case 'overdue':
                    $query = $query->overdue();
                    break;
            }
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query = $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($patientQuery) use ($search) {
                    $patientQuery->where('patient_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone_number', 'like', "%{$search}%");
                })->orWhere('reason', 'like', "%{$search}%");
            });
        }
        
        $appointments = $query->orderBy('appointment_date')
                             ->orderBy('appointment_time')
                             ->paginate(15);
                             
        // Simple debug log
        \Log::info('Appointments query completed', [
            'total_results' => $appointments->total(),
            'current_page' => $appointments->currentPage(),
            'per_page' => $appointments->perPage(),
            'filters_applied' => [
                'status' => $request->get('status'),
                'approval' => $request->get('approval'),
                'date_filter' => $request->get('date_filter'),
                'search' => $request->get('search')
            ]
        ]);
        
        // Get statistics
        $stats = [
            'total' => Appointment::active()->count(),
            'today' => Appointment::active()->today()->count(),
            'pending_approval' => Appointment::pendingApproval()->count(),
            'pending_reschedule' => Appointment::pendingReschedule()->count(),
            'overdue' => Appointment::overdue()->count(),
        ];
        
        return view('appointments.index', compact('appointments', 'stats'));
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create()
    {
        $patients = Patient::active()->orderBy('patient_name')->get();
        return view('appointments.create', compact('patients'));
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        // Get today's date in Philippine timezone for validation
        $todayInPhilippines = TimezoneHelper::now()->toDateString();
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date|after_or_equal:' . $todayInPhilippines,
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:500',
        ]);
        
        // Check for appointment conflicts
        $conflict = $this->checkAppointmentConflict(
            $validated['appointment_date'], 
            $validated['appointment_time'],
            $validated['patient_id']
        );
        
        if ($conflict) {
            return back()->withErrors([
                'appointment_time' => 'This time slot conflicts with an existing appointment. Please choose a different time.'
            ])->withInput();
        }
        
        // Philippine School Hours Validation
        // Parse dates in Philippine timezone context
        $appointmentDate = Carbon::createFromFormat('Y-m-d', $validated['appointment_date'], TimezoneHelper::PHILIPPINE_TIMEZONE);
        $appointmentTime = Carbon::createFromFormat('H:i', $validated['appointment_time'], TimezoneHelper::PHILIPPINE_TIMEZONE);
        
        // Check if it's Monday-Friday only (using Philippine timezone)
        if ($appointmentDate->dayOfWeek === 0 || $appointmentDate->dayOfWeek === 6) {
            return back()->withErrors([
                'appointment_date' => 'Appointments can only be scheduled Monday through Friday.'
            ])->withInput();
        }
        
        // Check Philippine holidays
        if ($this->isPhilippineHoliday($appointmentDate)) {
            return back()->withErrors([
                'appointment_date' => 'Appointments cannot be scheduled on Philippine holidays.'
            ])->withInput();
        }
        
        // Validate school hours (8 AM to 12 PM and 1 PM to 5 PM)
        $morningStart = Carbon::createFromTime(8, 0);
        $morningEnd = Carbon::createFromTime(12, 0);
        $afternoonStart = Carbon::createFromTime(13, 0);
        $afternoonEnd = Carbon::createFromTime(17, 0);
        
        $isValidTime = ($appointmentTime->gte($morningStart) && $appointmentTime->lt($morningEnd)) ||
                      ($appointmentTime->gte($afternoonStart) && $appointmentTime->lte($afternoonEnd));
        
        if (!$isValidTime) {
            return back()->withErrors([
                'appointment_time' => 'Appointments can only be scheduled during school hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.'
            ])->withInput();
        }
        
        try {
            $validated['status'] = Appointment::STATUS_ACTIVE;
            $validated['approval_status'] = Appointment::APPROVAL_APPROVED; // Auto-approve admin created appointments
            $validated['reschedule_status'] = Appointment::RESCHEDULE_NONE;
            
            $appointment = Appointment::create($validated);
            
            // Send appointment confirmation email
            try {
                $this->emailService->sendAppointmentNotification($appointment, 'created');
            } catch (\Exception $e) {
                \Log::error('Appointment creation email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment scheduled successfully! Patient has been notified via email.',
                    'appointment' => [
                        'id' => $appointment->appointment_id,
                        'patient_name' => $appointment->patient ? $appointment->patient->patient_name : 'Unknown',
                        'date' => $appointment->appointment_date->format('Y-m-d'),
                        'time' => $appointment->appointment_time->format('H:i')
                    ]
                ]);
            }
            
            return redirect()->route('appointments.index')
                           ->with('success', 'Appointment scheduled successfully!');
                           
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to create appointment: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        // Auto-update status if needed
        $appointment->autoUpdateStatus();
        
        $appointment->load('patient');
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the appointment
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::active()->orderBy('patient_name')->get();
        $appointment->load('patient');
        return view('appointments.edit', compact('appointment', 'patients'));
    }

    /**
     * Update the specified appointment
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Get today's date in Philippine timezone for validation
        $todayInPhilippines = TimezoneHelper::now()->toDateString();
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date|after_or_equal:' . $todayInPhilippines,
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:500',
            'status' => ['required', Rule::in([Appointment::STATUS_ACTIVE, Appointment::STATUS_CANCELLED, Appointment::STATUS_COMPLETED, Appointment::STATUS_OVERDUE])],
        ]);
        
        // Check for conflicts (excluding current appointment)
        $conflict = $this->checkAppointmentConflict(
            $validated['appointment_date'], 
            $validated['appointment_time'],
            $validated['patient_id'],
            $appointment->appointment_id
        );
        
        if ($conflict) {
            return back()->withErrors([
                'appointment_time' => 'This time slot conflicts with an existing appointment.'
            ])->withInput();
        }
        
        try {
            // Check if date/time changed for email notification
            $dateTimeChanged = $appointment->appointment_date->format('Y-m-d') !== $validated['appointment_date'] ||
                             $appointment->appointment_time->format('H:i') !== $validated['appointment_time'];
            
            $appointment->update($validated);
            
            // Send email notification if date/time was changed
            if ($dateTimeChanged) {
                try {
                    $this->emailService->sendAppointmentNotification($appointment, 'rescheduled');
                } catch (\Exception $e) {
                    \Log::error('Appointment update email failed', [
                        'appointment_id' => $appointment->appointment_id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                return redirect()->route('appointments.index')
                               ->with('success', 'Appointment updated successfully! Patient has been notified of the changes via email.');
            }
            
            return redirect()->route('appointments.index')
                           ->with('success', 'Appointment updated successfully!');
                           
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update appointment: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified appointment (cancel)
     */
    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->update([
                'status' => Appointment::STATUS_CANCELLED
            ]);
            
            // Send cancellation notification email
            try {
                $this->emailService->sendAppointmentNotification($appointment, 'cancelled');
            } catch (\Exception $e) {
                \Log::error('Appointment cancellation email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            return redirect()->route('appointments.index')
                           ->with('success', 'Appointment cancelled successfully! Patient has been notified via email.');
                           
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to cancel appointment: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Approve appointment
     */
    public function approve(Appointment $appointment)
    {
        try {
            $appointment->update([
                'approval_status' => Appointment::APPROVAL_APPROVED
            ]);
            
            // Send approval notification email
            try {
                $this->emailService->sendAppointmentNotification($appointment, 'approved');
            } catch (\Exception $e) {
                \Log::error('Appointment approval email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment approved successfully! Patient has been notified via email.'
                ]);
            }
            
            return redirect()->back()
                           ->with('success', 'Appointment approved successfully! Patient has been notified via email.');
                           
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to approve appointment: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Reject appointment
     */
    public function reject(Appointment $appointment)
    {
        try {
            $appointment->update([
                'approval_status' => Appointment::APPROVAL_REJECTED,
                'status' => Appointment::STATUS_CANCELLED
            ]);
            
            // Send rejection notification email
            try {
                $this->emailService->sendAppointmentNotification($appointment, 'rejected');
            } catch (\Exception $e) {
                \Log::error('Appointment rejection email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment rejected successfully! Patient has been notified via email.'
                ]);
            }
            
            return redirect()->back()
                           ->with('success', 'Appointment rejected successfully! Patient has been notified via email.');
                           
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to reject appointment: ' . $e->getMessage()]);
        }
    }
    
    
    /**
     * Show calendar view
     */
    public function calendar()
    {
        return view('appointments.calendar');
    }
    
    /**
     * Get calendar appointments as JSON
     */
    public function getCalendarAppointments(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $statusFilter = $request->input('status_filter');
        
        $query = Appointment::with('patient')
            ->whereBetween('appointment_date', [$start, $end]);
            
        // Apply status filter if provided
        if ($statusFilter) {
            switch ($statusFilter) {
                case 'approved':
                    $query->where('approval_status', Appointment::APPROVAL_APPROVED);
                    break;
                case 'pending':
                    $query->where('approval_status', Appointment::APPROVAL_PENDING);
                    break;
            }
        }
        
        $appointments = $query->get()->map(function ($appointment) {
            $displayTitle = $appointment->patient ? 
                $appointment->patient->patient_name : 
                'Unknown Patient';
                
            return [
                'id' => $appointment->appointment_id,
                'title' => $displayTitle,
                'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->appointment_time->format('H:i:s'),
                'end' => $appointment->appointment_date->format('Y-m-d') . 'T' . $this->calculateEndTime($appointment)->format('H:i:s'),
                'backgroundColor' => $this->getAppointmentColor($appointment),
                'borderColor' => $this->getAppointmentBorderColor($appointment),
                'textColor' => $this->getAppointmentTextColor($appointment),
                'classNames' => $this->getAppointmentClasses($appointment),
                'extendedProps' => [
                    'patient_id' => $appointment->patient_id,
                    'patient_name' => $displayTitle,
                    'reason' => $appointment->reason,
                    'status' => $appointment->status,
                    'approval_status' => $appointment->approval_status,
                    'reschedule_status' => $appointment->reschedule_status,
                    'appointment_time' => $appointment->appointment_time->format('g:i A'),
                    'formatted_datetime' => $appointment->formatted_date_time,
                    'can_edit' => $this->canEditAppointment($appointment),
                    'can_approve' => $this->canApproveAppointment($appointment),
                ]
            ];
        });
        
        return response()->json($appointments);
    }
    
    /**
     * Check for appointment conflicts
     */
    private function checkAppointmentConflict($date, $time, $patientId, $excludeId = null)
    {
        $query = Appointment::active()
            ->where('appointment_date', $date)
            ->where('appointment_time', $time);
        
        if ($excludeId) {
            $query->where('appointment_id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Get appointment color for calendar
     */
    private function getAppointmentColor(Appointment $appointment)
    {
        if ($appointment->status === Appointment::STATUS_CANCELLED) {
            return '#6c757d'; // Gray for cancelled
        }
        
        if ($appointment->status === Appointment::STATUS_COMPLETED) {
            return '#17a2b8'; // Info blue for completed
        }
        
        if (!$appointment->isApproved()) {
            return '#ffc107'; // Warning yellow for pending approval
        }
        
        if ($appointment->hasPendingReschedule()) {
            return '#fd7e14'; // Orange for pending reschedule
        }
        
        if ($appointment->isOverdue() && $appointment->status !== Appointment::STATUS_COMPLETED) {
            return '#dc3545'; // Red for overdue
        }
        
        return '#28a745'; // Green for normal appointments
    }
    
    /**
     * Get appointment border color for calendar
     */
    private function getAppointmentBorderColor(Appointment $appointment)
    {
        $baseColor = $this->getAppointmentColor($appointment);
        
        // Make border slightly darker
        return $this->darkenColor($baseColor, 0.2);
    }
    
    /**
     * Get appointment text color for calendar
     */
    private function getAppointmentTextColor(Appointment $appointment)
    {
        // Always use white text for good contrast
        return '#ffffff';
    }
    
    /**
     * Get CSS classes for appointment
     */
    private function getAppointmentClasses(Appointment $appointment)
    {
        $classes = ['appointment'];
        
        if ($appointment->isOverdue()) {
            $classes[] = 'overdue';
        }
        
        if ($appointment->hasPendingReschedule()) {
            $classes[] = 'reschedule-pending';
        }
        
        if (!$appointment->isApproved()) {
            $classes[] = 'approval-pending';
        }
        
        return $classes;
    }
    
    /**
     * Calculate appointment end time (assuming 30-minute appointments)
     */
    private function calculateEndTime(Appointment $appointment)
    {
        return $appointment->appointment_time->copy()->addMinutes(30);
    }
    
    /**
     * Check if appointment can be edited
     */
    private function canEditAppointment(Appointment $appointment)
    {
        return $appointment->status === Appointment::STATUS_ACTIVE && 
               !$appointment->isOverdue();
    }
    
    /**
     * Check if appointment can be approved
     */
    private function canApproveAppointment(Appointment $appointment)
    {
        return $appointment->approval_status === Appointment::APPROVAL_PENDING &&
               $appointment->status === Appointment::STATUS_ACTIVE;
    }
    
    /**
     * Darken a hex color
     */
    private function darkenColor($hex, $percent)
    {
        $hex = ltrim($hex, '#');
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = max(0, min(255, $r * (1 - $percent)));
        $g = max(0, min(255, $g * (1 - $percent)));
        $b = max(0, min(255, $b * (1 - $percent)));
        
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
    
    /**
     * Get appointment details for modal (AJAX)
     */
    public function getAppointmentDetails(Appointment $appointment)
    {
        $appointment->load('patient');
        
        $html = view('appointments.partials.appointment-details', compact('appointment'))->render();
        $actions = view('appointments.partials.appointment-actions', compact('appointment'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'actions' => $actions
        ]);
    }
    
    /**
     * Update appointment via AJAX (for drag & drop)
     */
    public function updateAppointmentTime(Request $request, Appointment $appointment)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);
        
        try {
            // Philippine School Schedule Validation
            $appointmentDate = Carbon::createFromFormat('Y-m-d', $request->appointment_date);
            $appointmentTime = Carbon::createFromFormat('H:i', $request->appointment_time);
            
            // Check if it's Monday-Friday only
            if ($appointmentDate->dayOfWeek === 0 || $appointmentDate->dayOfWeek === 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointments can only be scheduled Monday through Friday.'
                ], 400);
            }
            
            // Check Philippine holidays
            if ($this->isPhilippineHoliday($appointmentDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointments cannot be scheduled on Philippine holidays.'
                ], 400);
            }
            
            // Validate school hours (8 AM to 12 PM and 1 PM to 5 PM)
            $morningStart = Carbon::createFromTime(8, 0);
            $morningEnd = Carbon::createFromTime(12, 0);
            $afternoonStart = Carbon::createFromTime(13, 0);
            $afternoonEnd = Carbon::createFromTime(17, 0);
            
            $isValidTime = ($appointmentTime->gte($morningStart) && $appointmentTime->lt($morningEnd)) ||
                          ($appointmentTime->gte($afternoonStart) && $appointmentTime->lte($afternoonEnd));
            
            if (!$isValidTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointments can only be scheduled during school hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.'
                ], 400);
            }
            
            // Check for conflicts
            $conflict = $this->checkAppointmentConflict(
                $request->appointment_date,
                $request->appointment_time,
                $appointment->patient_id,
                $appointment->appointment_id
            );
            
            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Time slot is already occupied'
                ], 400);
            }
            
            $appointment->update([
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time
            ]);
            
            // TEMPORARILY DISABLE EMAIL SENDING TO STOP CRASHES
            \Log::info('Appointment rescheduled successfully - email notifications temporarily disabled', [
                'appointment_id' => $appointment->appointment_id,
                'patient_id' => $appointment->patient_id,
                'new_date' => $request->appointment_date,
                'new_time' => $request->appointment_time
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment rescheduled successfully! (Email notifications temporarily disabled)'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Approve reschedule request
     */
    public function approveReschedule(Request $request, Appointment $appointment)
    {
        if ($appointment->reschedule_status !== Appointment::RESCHEDULE_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'No pending reschedule request found for this appointment.'
            ], 400);
        }
        
        try {
            // Update the appointment with the requested date/time
            $appointment->update([
                'appointment_date' => $appointment->requested_date,
                'appointment_time' => $appointment->requested_time,
                'reschedule_status' => Appointment::RESCHEDULE_NONE,
                'approval_status' => Appointment::APPROVAL_APPROVED,
                'requested_date' => null,
                'requested_time' => null,
                'reschedule_reason' => null,
            ]);
            
            // Send approval notification email
            try {
                $this->emailService->sendAppointmentNotification($appointment, 'reschedule_approved');
            } catch (\Exception $e) {
                \Log::error('Reschedule approval email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Reschedule request approved successfully! Patient has been notified via email.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving reschedule request: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reject reschedule request
     */
    public function rejectReschedule(Request $request, Appointment $appointment)
    {
        if ($appointment->reschedule_status !== Appointment::RESCHEDULE_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'No pending reschedule request found for this appointment.'
            ], 400);
        }
        
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);
        
        try {
            // Reset reschedule request data
            $appointment->update([
                'reschedule_status' => Appointment::RESCHEDULE_NONE,
                'requested_date' => null,
                'requested_time' => null,
                'reschedule_reason' => null,
            ]);
            
            // Send rejection notification email
            try {
                $this->emailService->sendAppointmentNotification($appointment, 'reschedule_rejected', [
                    'rejection_reason' => $validated['rejection_reason'] ?? 'No reason provided'
                ]);
            } catch (\Exception $e) {
                \Log::error('Reschedule rejection email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Reschedule request rejected. Patient has been notified via email.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting reschedule request: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check if it's a Philippine holiday
     */
    private function isPhilippineHoliday(Carbon $date)
    {
        // Philippine Holidays for 2024-2025 (update annually)
        $holidays = [
            2024 => [
                '2024-01-01', // New Year's Day
                '2024-02-10', // Chinese New Year
                '2024-02-25', // EDSA People Power Revolution Anniversary
                '2024-03-28', // Maundy Thursday
                '2024-03-29', // Good Friday
                '2024-04-09', // Araw ng Kagitingan (Day of Valor)
                '2024-05-01', // Labor Day
                '2024-06-12', // Independence Day
                '2024-08-21', // Ninoy Aquino Day
                '2024-08-26', // National Heroes Day
                '2024-11-01', // All Saints' Day
                '2024-11-30', // Bonifacio Day
                '2024-12-25', // Christmas Day
                '2024-12-30', // Rizal Day
                '2024-12-31'  // New Year's Eve
            ],
            2025 => [
                '2025-01-01', // New Year's Day
                '2025-01-29', // Chinese New Year
                '2025-02-25', // EDSA People Power Revolution Anniversary
                '2025-04-17', // Maundy Thursday
                '2025-04-18', // Good Friday
                '2025-04-09', // Araw ng Kagitingan (Day of Valor)
                '2025-05-01', // Labor Day
                '2025-06-12', // Independence Day
                '2025-08-21', // Ninoy Aquino Day
                '2025-08-25', // National Heroes Day
                '2025-11-01', // All Saints' Day
                '2025-11-30', // Bonifacio Day
                '2025-12-25', // Christmas Day
                '2025-12-30', // Rizal Day
                '2025-12-31'  // New Year's Eve
            ]
        ];
        
        $year = $date->year;
        $dateString = $date->toDateString();
        
        return isset($holidays[$year]) && in_array($dateString, $holidays[$year]);
    }
    
    /**
     * Mark appointment as completed
     */
    public function complete(Appointment $appointment)
    {
        try {
            $appointment->update([
                'status' => Appointment::STATUS_COMPLETED
            ]);
            
            // Send completion follow-up email
            try {
                $this->emailService->sendAppointmentNotification($appointment, 'completed');
            } catch (\Exception $e) {
                \Log::error('Appointment completion email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment marked as completed! Follow-up email sent to patient.'
                ]);
            }
            
            return redirect()->back()
                           ->with('success', 'Appointment marked as completed! Follow-up email sent to patient.');
                           
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to complete appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to complete appointment: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Cancel appointment (AJAX compatible)
     */
    public function cancel(Appointment $appointment)
    {
        try {
            $appointment->update([
                'status' => Appointment::STATUS_CANCELLED
            ]);
            
            // Send cancellation notification email
            try {
                $this->emailService->sendAppointmentNotification($appointment, 'cancelled');
            } catch (\Exception $e) {
                \Log::error('Appointment cancellation email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment cancelled successfully! Patient has been notified via email.'
                ]);
            }
            
            return redirect()->back()
                           ->with('success', 'Appointment cancelled successfully! Patient has been notified via email.');
                           
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to cancel appointment: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Request reschedule for appointment
     */
    public function reschedule(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'requested_date' => 'required|date|after_or_equal:today',
            'requested_time' => 'required|date_format:H:i',
            'reschedule_reason' => 'nullable|string|max:500',
        ]);
        
        try {
            // Validate Philippine school schedule for new requested time
            $requestedDate = Carbon::createFromFormat('Y-m-d', $validated['requested_date']);
            $requestedTime = Carbon::createFromFormat('H:i', $validated['requested_time']);
            
            // Check if it's Monday-Friday only
            if ($requestedDate->dayOfWeek === 0 || $requestedDate->dayOfWeek === 6) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Appointments can only be rescheduled to Monday through Friday.'
                    ], 400);
                }
                return back()->withErrors([
                    'requested_date' => 'Appointments can only be rescheduled to Monday through Friday.'
                ])->withInput();
            }
            
            // Check Philippine holidays
            if ($this->isPhilippineHoliday($requestedDate)) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Appointments cannot be rescheduled to Philippine holidays.'
                    ], 400);
                }
                return back()->withErrors([
                    'requested_date' => 'Appointments cannot be rescheduled to Philippine holidays.'
                ])->withInput();
            }
            
            // Validate school hours (8 AM to 12 PM and 1 PM to 5 PM)
            $morningStart = Carbon::createFromTime(8, 0);
            $morningEnd = Carbon::createFromTime(12, 0);
            $afternoonStart = Carbon::createFromTime(13, 0);
            $afternoonEnd = Carbon::createFromTime(17, 0);
            
            $isValidTime = ($requestedTime->gte($morningStart) && $requestedTime->lt($morningEnd)) ||
                          ($requestedTime->gte($afternoonStart) && $requestedTime->lte($afternoonEnd));
            
            if (!$isValidTime) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Appointments can only be rescheduled during school hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.'
                    ], 400);
                }
                return back()->withErrors([
                    'requested_time' => 'Appointments can only be rescheduled during school hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.'
                ])->withInput();
            }
            
            $appointment->update([
                'reschedule_status' => Appointment::RESCHEDULE_PENDING,
                'requested_date' => $validated['requested_date'],
                'requested_time' => $validated['requested_time'],
                'reschedule_reason' => $validated['reschedule_reason'] ?? null,
            ]);
            
            // Send reschedule request notification email with improved error handling
            try {
                // Ensure appointment has patient loaded
                if (!$appointment->relationLoaded('patient')) {
                    $appointment->load('patient');
                }
                
                // Only send email if patient has an email address
                if ($appointment->patient && $appointment->patient->email) {
                    $emailResult = $this->emailService->sendAppointmentNotification($appointment, 'reschedule_request');
                    
                    if (!$emailResult['success']) {
                        \Log::warning('Reschedule request email not sent', [
                            'appointment_id' => $appointment->appointment_id,
                            'reason' => $emailResult['message']
                        ]);
                    }
                } else {
                    \Log::info('Reschedule request email skipped - no patient email', [
                        'appointment_id' => $appointment->appointment_id
                    ]);
                }
                
            } catch (\Symfony\Component\Mime\Exception\InvalidArgumentException $e) {
                // Specific handling for email header issues
                \Log::error('Email header validation failed for reschedule request', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
                // Continue without failing the request
            } catch (\Exception $e) {
                \Log::error('Appointment reschedule request email failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue without failing the request
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reschedule request submitted successfully! You will be notified of the decision via email.'
                ]);
            }
            
            return redirect()->back()
                           ->with('success', 'Reschedule request submitted successfully! You will be notified of the decision via email.');
                           
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to request reschedule: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to request reschedule: ' . $e->getMessage()])
                        ->withInput();
        }
    }
    
    /**
     * Permanently delete appointment
     */
    public function delete(Appointment $appointment)
    {
        try {
            $appointmentId = $appointment->appointment_id;
            $patientName = $appointment->patient ? $appointment->patient->patient_name : 'Unknown Patient';
            
            $appointment->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment deleted permanently!'
                ]);
            }
            
            return redirect()->route('appointments.index')
                           ->with('success', "Appointment for {$patientName} deleted successfully!");
                           
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to delete appointment: ' . $e->getMessage()]);
        }
    }
}
