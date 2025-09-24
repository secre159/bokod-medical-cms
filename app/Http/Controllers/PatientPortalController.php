<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Setting;
use Carbon\Carbon;

class PatientPortalController extends Controller
{
    /**
     * Show patient appointments page
     */
    public function appointments()
    {
        // Ensure user is authenticated and has a patient profile
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return redirect()->route('dashboard.index')
                           ->with('error', 'Patient profile not found. Please contact administrator.');
        }

        return view('patient.appointments');
    }

    /**
     * Show patient medical history page
     */
    public function history()
    {
        // Ensure user is authenticated and has a patient profile
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return redirect()->route('dashboard.index')
                           ->with('error', 'Patient profile not found. Please contact administrator.');
        }

        return view('patient.history');
    }

    /**
     * Show patient prescriptions page
     */
    public function prescriptions()
    {
        // Ensure user is authenticated and has a patient profile
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return redirect()->route('dashboard.index')
                           ->with('error', 'Patient profile not found. Please contact administrator.');
        }

        return view('patient.prescriptions');
    }

    /**
     * Get patient appointments data for AJAX requests
     */
    public function getAppointmentsData(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $patient = $user->patient;
        $query = $patient->appointments()->with(['prescriptions.medicine']);

        // Apply filters if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('approval_status') && $request->approval_status) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->has('year') && $request->year) {
            $query->whereYear('appointment_date', $request->year);
        }

        if ($request->has('month') && $request->month) {
            $query->whereMonth('appointment_date', $request->month);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
                             ->orderBy('appointment_time', 'desc')
                             ->paginate(10);

        return response()->json([
            'appointments' => $appointments->items(),
            'pagination' => [
                'current_page' => $appointments->currentPage(),
                'last_page' => $appointments->lastPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total()
            ]
        ]);
    }

    /**
     * Get patient statistics
     */
    public function getStatistics()
    {
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $patient = $user->patient;

        $statistics = [
            'total_appointments' => $patient->appointments()->count(),
            'completed_appointments' => $patient->appointments()->where('status', 'completed')->count(),
            'cancelled_appointments' => $patient->appointments()->where('status', 'cancelled')->count(),
            'pending_appointments' => $patient->appointments()->where('approval_status', 'pending')->count(),
            'upcoming_appointments' => $patient->appointments()->upcoming()->count(),
            'total_prescriptions' => $patient->prescriptions ? $patient->prescriptions->count() : 0,
            'active_prescriptions' => $patient->prescriptions ? $patient->prescriptions()->where('status', 'active')->count() : 0,
        ];

        return response()->json($statistics);
    }

    /**
     * Book a new appointment
     */
    public function bookAppointment(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Additional validation for Philippine school schedule
            $appointmentDate = \Carbon\Carbon::parse($validated['appointment_date']);
            $dayOfWeek = $appointmentDate->dayOfWeek;
            
            // Check if it's a weekend
            if ($dayOfWeek === 0 || $dayOfWeek === 6) {
                return response()->json([
                    'error' => 'Appointments are only available Monday through Friday.'
                ], 422);
            }
            
            // Check if it's a holiday
            $philippineHolidays = [
                '2024-01-01', '2024-04-09', '2024-04-10', '2024-05-01', '2024-06-12', '2024-08-26', '2024-11-30', '2024-12-25', '2024-12-30',
                '2025-01-01', '2025-04-17', '2025-04-18', '2025-05-01', '2025-06-12', '2025-08-25', '2025-11-30', '2025-12-25', '2025-12-30'
            ];
            
            if (in_array($appointmentDate->format('Y-m-d'), $philippineHolidays)) {
                return response()->json([
                    'error' => 'The selected date is a Philippine holiday. Please choose a different date.'
                ], 422);
            }
            
            // Check if time is within clinic hours
            $appointmentTime = \Carbon\Carbon::createFromFormat('H:i', $validated['appointment_time']);
            $morningStart = \Carbon\Carbon::createFromFormat('H:i', '08:00');
            $morningEnd = \Carbon\Carbon::createFromFormat('H:i', '12:00');
            $afternoonStart = \Carbon\Carbon::createFromFormat('H:i', '13:00');
            $afternoonEnd = \Carbon\Carbon::createFromFormat('H:i', '17:00');
            
            if (!($appointmentTime->between($morningStart, $morningEnd) || 
                  $appointmentTime->between($afternoonStart, $afternoonEnd))) {
                return response()->json([
                    'error' => 'Appointments are only available during clinic hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.'
                ], 422);
            }

            // Create the appointment
            $appointment = $user->patient->appointments()->create([
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'active',
                'approval_status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment request submitted successfully!',
                'appointment' => $appointment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to book appointment. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an appointment
     */
    public function cancelAppointment(Request $request, $appointmentId)
    {
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $appointment = $user->patient->appointments()->where('appointment_id', $appointmentId)->first();
        
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        if ($appointment->status === 'completed') {
            return response()->json(['error' => 'Cannot cancel a completed appointment'], 422);
        }

        if ($appointment->status === 'cancelled') {
            return response()->json(['error' => 'This appointment is already cancelled'], 422);
        }

        // Validate cancellation reason if provided
        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:1000',
        ]);

        try {
            $cancellationNote = 'Cancelled by patient on ' . now()->format('M d, Y h:i A');
            if (!empty($validated['cancellation_reason'])) {
                $cancellationNote .= "\nReason: " . $validated['cancellation_reason'];
            }

            $appointment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $validated['cancellation_reason'] ?? null,
                'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . $cancellationNote
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to cancel appointment. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reschedule an appointment
     */
    public function rescheduleAppointment(Request $request, $appointmentId)
    {
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $appointment = $user->patient->appointments()->where('appointment_id', $appointmentId)->first();
        
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        if ($appointment->status === 'completed') {
            return response()->json(['error' => 'Cannot reschedule a completed appointment'], 422);
        }

        if ($appointment->status === 'cancelled') {
            return response()->json(['error' => 'Cannot reschedule a cancelled appointment'], 422);
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        try {
            // Additional validation for Philippine school schedule
            $appointmentDate = \Carbon\Carbon::parse($validated['appointment_date']);
            $dayOfWeek = $appointmentDate->dayOfWeek;
            
            // Check if it's a weekend
            if ($dayOfWeek === 0 || $dayOfWeek === 6) {
                return response()->json([
                    'error' => 'Appointments are only available Monday through Friday.'
                ], 422);
            }
            
            // Check if it's a holiday
            $philippineHolidays = [
                '2024-01-01', '2024-04-09', '2024-04-10', '2024-05-01', '2024-06-12', '2024-08-26', '2024-11-30', '2024-12-25', '2024-12-30',
                '2025-01-01', '2025-04-17', '2025-04-18', '2025-05-01', '2025-06-12', '2025-08-25', '2025-11-30', '2025-12-25', '2025-12-30'
            ];
            
            if (in_array($appointmentDate->format('Y-m-d'), $philippineHolidays)) {
                return response()->json([
                    'error' => 'The selected date is a Philippine holiday. Please choose a different date.'
                ], 422);
            }
            
            // Check if time is within clinic hours
            $appointmentTime = \Carbon\Carbon::createFromFormat('H:i', $validated['appointment_time']);
            $morningStart = \Carbon\Carbon::createFromFormat('H:i', '08:00');
            $morningEnd = \Carbon\Carbon::createFromFormat('H:i', '12:00');
            $afternoonStart = \Carbon\Carbon::createFromFormat('H:i', '13:00');
            $afternoonEnd = \Carbon\Carbon::createFromFormat('H:i', '17:00');
            
            if (!($appointmentTime->between($morningStart, $morningEnd) || 
                  $appointmentTime->between($afternoonStart, $afternoonEnd))) {
                return response()->json([
                    'error' => 'Appointments are only available during clinic hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.'
                ], 422);
            }

            // Update the appointment with reschedule request data (don't change actual date/time yet)
            $appointment->update([
                'reschedule_status' => 'pending',
                'requested_date' => $validated['appointment_date'],
                'requested_time' => $validated['appointment_time'],
                'reschedule_reason' => $validated['reason'],
                'approval_status' => 'pending', // Set approval to pending for reschedule review
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reschedule request submitted successfully! You will be notified of the decision via email.',
                'appointment' => $appointment->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reschedule appointment. Please try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment details
     */
    public function getAppointmentDetails($appointmentId)
    {
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return response()->json(['error' => 'Patient profile not found'], 404);
        }

        $appointment = $user->patient->appointments()
                                   ->with(['prescriptions.medicine'])
                                   ->where('appointment_id', $appointmentId)->first();
        
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        return response()->json([
            'appointment' => $appointment
        ]);
    }
    
    /**
     * Export patient prescriptions to CSV
     */
    public function exportPrescriptions()
    {
        $user = Auth::user();
        
        if (!$user || !$user->patient) {
            return redirect()->route('dashboard.index')
                           ->with('error', 'Patient profile not found. Please contact administrator.');
        }

        try {
            $patient = $user->patient;
            $allPrescriptions = collect();
            
            // Get prescriptions from all appointments
            foreach($patient->appointments()->with(['prescriptions.medicine'])->get() as $appointment) {
                if ($appointment->prescriptions) {
                    foreach($appointment->prescriptions as $prescription) {
                        $prescription->appointment_date = $appointment->appointment_date;
                        $prescription->appointment_reason = $appointment->reason;
                        $allPrescriptions->push($prescription);
                    }
                }
            }
            
            $allPrescriptions = $allPrescriptions->sortByDesc('created_at');
            
            // Set CSV headers
            $filename = 'my_prescriptions_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];
            
            $callback = function() use ($allPrescriptions, $patient) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for Excel compatibility
                fwrite($file, "\xEF\xBB\xBF");
                
                // CSV Headers
                fputcsv($file, [
                    'Patient Name',
                    'Prescription Date',
                    'Medicine',
                    'Generic Name',
                    'Dosage',
                    'Frequency',
                    'Instructions',
                    'Quantity',
                    'Status',
                    'Appointment Date',
                    'Appointment Reason',
                    'Prescribed Date',
                    'Expiry Date'
                ]);
                
                // Data rows
                foreach ($allPrescriptions as $prescription) {
                    fputcsv($file, [
                        $patient->patient_name ?? 'N/A',
                        $prescription->created_at ? $prescription->created_at->format('Y-m-d H:i:s') : 'N/A',
                        $prescription->medicine->name ?? $prescription->medicine_name ?? 'N/A',
                        $prescription->medicine->generic_name ?? $prescription->generic_name ?? 'N/A',
                        $prescription->dosage ?? 'N/A',
                        $prescription->frequency ?? 'N/A',
                        $prescription->instructions ?? 'N/A',
                        $prescription->quantity ?? 'N/A',
                        ucfirst($prescription->status ?? 'N/A'),
                        $prescription->appointment_date ? $prescription->appointment_date->format('Y-m-d') : 'N/A',
                        $prescription->appointment_reason ?? 'N/A',
                        $prescription->prescribed_date ? $prescription->prescribed_date->format('Y-m-d') : 'N/A',
                        $prescription->expiry_date ? $prescription->expiry_date->format('Y-m-d') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            \Log::error('Patient prescription export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting prescriptions: ' . $e->getMessage());
        }
    }
}
