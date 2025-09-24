<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use App\Models\PatientVisit;
use App\Models\MedicalNote;
use App\Models\Prescription;
use App\Models\Appointment;
use App\Services\EnhancedEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Rules\PhoneNumberRule;
use App\Rules\EmailValidationRule;

class PatientController extends Controller
{
    protected $emailService;
    
    public function __construct(EnhancedEmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::with('user');
        
        // Filter by archived status
        if ($request->has('filter')) {
            if ($request->filter === 'archived') {
                $query->archived();
            } else {
                $query->active();
            }
        } else {
            $query->active(); // Default to active patients
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }
        
        $patients = $query->latest()->paginate(15);
        
        // API request for AJAX calls
        if ($request->has('api') && $request->api == true) {
            return response()->json([
                'success' => true,
                'data' => $query->latest()->get()->map(function($patient) {
                    return [
                        'id' => $patient->id,
                        'patient_name' => $patient->patient_name,
                        'email' => $patient->email,
                        'phone_number' => $patient->phone_number,
                        'position' => $patient->position
                    ];
                })
            ]);
        }
        
        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'email' => ['required', new EmailValidationRule, 'unique:patients,email'],
            'phone_number' => ['nullable', new PhoneNumberRule],
            'date_of_birth' => 'nullable|date|before:' . now()->subYears(16)->toDateString() . '|after:' . now()->subYears(80)->toDateString(),
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:100',
            'civil_status' => 'required|in:Single,Married,Divorced,Widowed',
            'course' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_phone' => ['nullable', new PhoneNumberRule],
            'emergency_contact_address' => 'nullable|string',
            // Medical information
            'height' => 'nullable|numeric|between:50,250',
            'weight' => 'nullable|numeric|between:10,300',
            'bmi' => 'nullable|numeric|between:10,50',
            'systolic_bp' => 'nullable|integer|between:60,250',
            'diastolic_bp' => 'nullable|integer|between:40,150',
            'blood_pressure' => 'nullable|string|max:20',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Generate random password
            $randomPassword = $this->generateSecurePassword();
            
            // Create user account for patient
            $user = User::create([
                'name' => $validated['patient_name'],
                'display_name' => $validated['patient_name'],
                'email' => $validated['email'],
                'password' => Hash::make($randomPassword),
                'role' => User::ROLE_PATIENT,
                'status' => User::STATUS_ACTIVE,
            ]);
            
            // Create patient record
            $validated['user_id'] = $user->id;
            $patient = Patient::create($validated);
            
            // Send welcome email with credentials
            $emailResult = $this->emailService->sendPatientWelcome($patient, $randomPassword);
            
            if ($emailResult['success']) {
                $welcomeEmailStatus = 'Welcome email with login credentials sent successfully to ' . $patient->email;
                $successMessage = 'Patient created successfully! Login credentials have been sent to their email address.';
            } else {
                $welcomeEmailStatus = 'Patient created but welcome email failed to send: ' . $emailResult['message'];
                $successMessage = 'Patient created successfully, but failed to send login credentials via email. Please provide credentials manually: Email: ' . $patient->email . ', Password: ' . $randomPassword;
                \Log::error('Welcome email failed', [
                    'patient_id' => $patient->id, 
                    'error' => $emailResult['message'],
                    'password' => $randomPassword // Log password for manual recovery if needed
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('patients.index')
                           ->with('success', $successMessage);
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create patient: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        $patient->load('user');
        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the patient
     */
    public function edit(Patient $patient)
    {
        $patient->load('user');
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'email' => ['required', new EmailValidationRule, Rule::unique('patients', 'email')->ignore($patient->id)],
            'phone_number' => ['nullable', new PhoneNumberRule],
            'date_of_birth' => 'nullable|date|before:' . now()->subYears(16)->toDateString() . '|after:' . now()->subYears(80)->toDateString(),
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:100',
            'civil_status' => 'required|in:Single,Married,Divorced,Widowed',
            'course' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_phone' => ['nullable', new PhoneNumberRule],
            'emergency_contact_address' => 'nullable|string',
            // Medical information
            'height' => 'nullable|numeric|between:50,250',
            'weight' => 'nullable|numeric|between:10,300',
            'bmi' => 'nullable|numeric|between:10,50',
            'systolic_bp' => 'nullable|integer|between:60,250',
            'diastolic_bp' => 'nullable|integer|between:40,150',
            'blood_pressure' => 'nullable|string|max:20',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update patient record
            $patient->update($validated);
            
            // Update associated user record
            if ($patient->user) {
                $patient->user->update([
                    'name' => $validated['patient_name'],
                    'display_name' => $validated['patient_name'],
                    'email' => $validated['email'],
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('patients.index')
                           ->with('success', 'Patient updated successfully!');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update patient: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Archive/Unarchive the specified patient
     */
    public function destroy(Patient $patient)
    {
        $action = $patient->archived ? 'restored' : 'archived';
        $patient->update(['archived' => !$patient->archived]);
        
        // Also update user status
        if ($patient->user) {
            $patient->user->update([
                'status' => $patient->archived ? User::STATUS_ARCHIVED : User::STATUS_ACTIVE
            ]);
        }
        
        return redirect()->route('patients.index')
                       ->with('success', "Patient {$action} successfully!");
    }
    
    /**
     * Show patient history page
     */
    public function history(Request $request)
    {
        // Get filter parameters
        $patientId = $request->get('patient_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $recordType = $request->get('record_type', 'all');
        $searchTerm = $request->get('search');
        
        // Base queries
        $patientsQuery = Patient::with(['user', 'appointments', 'prescriptions', 'visits', 'medicalNotes'])
                               ->active();
        
        // Get all patients for dropdown
        $patients = Patient::active()->orderBy('patient_name')->get();
        
        // Selected patient data
        $selectedPatient = null;
        $visits = collect();
        $appointments = collect();
        $prescriptions = collect();
        $medicalNotes = collect();
        $healthTrends = [];
        
        if ($patientId) {
            $selectedPatient = Patient::with(['user'])->find($patientId);
            
            if ($selectedPatient) {
                // Get visits with date filtering
                $visitsQuery = PatientVisit::with(['medicalNotes'])
                                         ->where('patient_id', $patientId);
                
                if ($dateFrom) {
                    $visitsQuery->where('visit_date', '>=', Carbon::parse($dateFrom));
                }
                
                if ($dateTo) {
                    $visitsQuery->where('visit_date', '<=', Carbon::parse($dateTo));
                }
                
                if ($searchTerm) {
                    $visitsQuery->where(function($q) use ($searchTerm) {
                        $q->where('disease', 'like', "%{$searchTerm}%")
                          ->orWhere('symptoms', 'like', "%{$searchTerm}%")
                          ->orWhere('notes', 'like', "%{$searchTerm}%");
                    });
                }
                
                $visits = $visitsQuery->orderBy('visit_date', 'desc')->get();
                
                // Get appointments with date filtering
                $appointmentsQuery = Appointment::with(['prescriptions'])
                                               ->where('patient_id', $patientId);
                
                if ($dateFrom) {
                    $appointmentsQuery->where('appointment_date', '>=', Carbon::parse($dateFrom));
                }
                
                if ($dateTo) {
                    $appointmentsQuery->where('appointment_date', '<=', Carbon::parse($dateTo));
                }
                
                if ($searchTerm) {
                    $appointmentsQuery->where(function($q) use ($searchTerm) {
                        $q->where('reason', 'like', "%{$searchTerm}%")
                          ->orWhere('notes', 'like', "%{$searchTerm}%")
                          ->orWhere('diagnosis', 'like', "%{$searchTerm}%")
                          ->orWhere('treatment_notes', 'like', "%{$searchTerm}%");
                    });
                }
                
                $appointments = $appointmentsQuery->orderBy('appointment_date', 'desc')->get();
                
                // Get prescriptions with date filtering
                $prescriptionsQuery = Prescription::with(['medicine', 'appointment', 'prescribedBy'])
                                                ->where('patient_id', $patientId);
                
                if ($dateFrom) {
                    $prescriptionsQuery->where('prescribed_date', '>=', Carbon::parse($dateFrom));
                }
                
                if ($dateTo) {
                    $prescriptionsQuery->where('prescribed_date', '<=', Carbon::parse($dateTo));
                }
                
                if ($searchTerm) {
                    $prescriptionsQuery->where(function($q) use ($searchTerm) {
                        $q->where('medicine_name', 'like', "%{$searchTerm}%")
                          ->orWhere('instructions', 'like', "%{$searchTerm}%")
                          ->orWhere('notes', 'like', "%{$searchTerm}%");
                    });
                }
                
                $prescriptions = $prescriptionsQuery->orderBy('prescribed_date', 'desc')->get();
                
                // Get medical notes with date filtering
                $medicalNotesQuery = MedicalNote::with(['createdBy', 'appointment', 'patientVisit'])
                                              ->where('patient_id', $patientId);
                
                if ($dateFrom) {
                    $medicalNotesQuery->where('note_date', '>=', Carbon::parse($dateFrom));
                }
                
                if ($dateTo) {
                    $medicalNotesQuery->where('note_date', '<=', Carbon::parse($dateTo));
                }
                
                if ($searchTerm) {
                    $medicalNotesQuery->where(function($q) use ($searchTerm) {
                        $q->where('title', 'like', "%{$searchTerm}%")
                          ->orWhere('content', 'like', "%{$searchTerm}%")
                          ->orWhere('note_type', 'like', "%{$searchTerm}%");
                    });
                }
                
                $medicalNotes = $medicalNotesQuery->orderBy('note_date', 'desc')->get();
                
                // Calculate health trends
                $healthTrends = $this->calculateHealthTrends($selectedPatient, $visits);
            }
        }
        
        // Filter by record type
        $filteredData = $this->filterByRecordType($recordType, $visits, $appointments, $prescriptions, $medicalNotes);
        
        return view('patients.history', [
            'patients' => $patients,
            'selectedPatient' => $selectedPatient,
            'visits' => $filteredData['visits'],
            'appointments' => $filteredData['appointments'],
            'prescriptions' => $filteredData['prescriptions'],
            'medicalNotes' => $filteredData['medicalNotes'],
            'healthTrends' => $healthTrends,
            'filters' => [
                'patient_id' => $patientId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'record_type' => $recordType,
                'search' => $searchTerm,
            ]
        ]);
    }
    
    /**
     * Calculate health trends for a patient
     */
    private function calculateHealthTrends($patient, $visits)
    {
        $trends = [
            'bmi' => [],
            'blood_pressure' => [],
            'temperature' => [],
            'pulse_rate' => [],
            'weight_status' => 'Unknown',
            'bp_status' => 'Unknown',
            'recent_changes' => []
        ];
        
        if (!$patient || $visits->isEmpty()) {
            return $trends;
        }
        
        // Process visits for trends
        foreach ($visits->sortBy('visit_date') as $visit) {
            $date = $visit->visit_date->format('M Y');
            
            if ($visit->temperature) {
                $trends['temperature'][] = [
                    'date' => $date,
                    'value' => $visit->temperature,
                    'normal' => $visit->temperature >= 36.1 && $visit->temperature <= 37.2
                ];
            }
            
            if ($visit->pulse_rate) {
                $trends['pulse_rate'][] = [
                    'date' => $date,
                    'value' => $visit->pulse_rate,
                    'normal' => $visit->pulse_rate >= 60 && $visit->pulse_rate <= 100
                ];
            }
            
            if ($visit->bp) {
                $trends['blood_pressure'][] = [
                    'date' => $date,
                    'value' => $visit->bp,
                    'normal' => $this->isBPNormal($visit->bp)
                ];
            }
        }
        
        // Add patient BMI if available
        if ($patient->bmi) {
            $trends['bmi'][] = [
                'date' => 'Current',
                'value' => $patient->bmi,
                'normal' => $patient->bmi >= 18.5 && $patient->bmi <= 24.9
            ];
            
            // Determine weight status
            if ($patient->bmi < 18.5) {
                $trends['weight_status'] = 'Underweight';
            } elseif ($patient->bmi <= 24.9) {
                $trends['weight_status'] = 'Normal';
            } elseif ($patient->bmi <= 29.9) {
                $trends['weight_status'] = 'Overweight';
            } else {
                $trends['weight_status'] = 'Obese';
            }
        }
        
        // Determine recent changes (last 2 visits comparison)
        $recentVisits = $visits->sortByDesc('visit_date')->take(2);
        if ($recentVisits->count() >= 2) {
            $latest = $recentVisits->first();
            $previous = $recentVisits->last();
            
            if ($latest->temperature && $previous->temperature) {
                $tempChange = $latest->temperature - $previous->temperature;
                if (abs($tempChange) > 0.5) {
                    $trends['recent_changes'][] = 'Temperature ' . ($tempChange > 0 ? 'increased' : 'decreased') . ' by ' . abs($tempChange) . '°C';
                }
            }
            
            if ($latest->pulse_rate && $previous->pulse_rate) {
                $pulseChange = $latest->pulse_rate - $previous->pulse_rate;
                if (abs($pulseChange) > 10) {
                    $trends['recent_changes'][] = 'Pulse rate ' . ($pulseChange > 0 ? 'increased' : 'decreased') . ' by ' . abs($pulseChange) . ' bpm';
                }
            }
        }
        
        return $trends;
    }
    
    /**
     * Check if blood pressure is normal
     */
    private function isBPNormal($bp)
    {
        // Simple BP parsing - assumes format like "120/80"
        if (preg_match('/(\d+)\/(\d+)/', $bp, $matches)) {
            $systolic = (int)$matches[1];
            $diastolic = (int)$matches[2];
            
            return $systolic < 130 && $diastolic < 80;
        }
        
        return false;
    }
    
    /**
     * Filter data by record type
     */
    private function filterByRecordType($recordType, $visits, $appointments, $prescriptions, $medicalNotes)
    {
        switch ($recordType) {
            case 'visits':
                return [
                    'visits' => $visits,
                    'appointments' => collect(),
                    'prescriptions' => collect(),
                    'medicalNotes' => collect()
                ];
            case 'appointments':
                return [
                    'visits' => collect(),
                    'appointments' => $appointments,
                    'prescriptions' => collect(),
                    'medicalNotes' => collect()
                ];
            case 'prescriptions':
                return [
                    'visits' => collect(),
                    'appointments' => collect(),
                    'prescriptions' => $prescriptions,
                    'medicalNotes' => collect()
                ];
            case 'notes':
                return [
                    'visits' => collect(),
                    'appointments' => collect(),
                    'prescriptions' => collect(),
                    'medicalNotes' => $medicalNotes
                ];
            default: // 'all'
                return [
                    'visits' => $visits,
                    'appointments' => $appointments,
                    'prescriptions' => $prescriptions,
                    'medicalNotes' => $medicalNotes
                ];
        }
    }
    
    /**
     * Generate a secure random password
     */
    private function generateSecurePassword($length = 12)
    {
        // Define character sets
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specialChars = '!@#$%&*';
        
        // Ensure at least one character from each set
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];
        
        // Fill the rest of the length with random characters from all sets
        $allChars = $lowercase . $uppercase . $numbers . $specialChars;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Shuffle the password to randomize character positions
        return str_shuffle($password);
    }
    
    /**
     * Reset patient password and send new credentials via email
     */
    public function resetPassword(Patient $patient)
    {
        try {
            if (!$patient->user) {
                return back()->withErrors(['error' => 'Patient does not have an associated user account.']);
            }
            
            DB::beginTransaction();
            
            // Generate new random password
            $newPassword = $this->generateSecurePassword();
            
            // Update user password
            $patient->user->update([
                'password' => Hash::make($newPassword)
            ]);
            
            // Send email with new credentials
            $emailResult = $this->emailService->sendPasswordResetNotification($patient, $newPassword);
            
            DB::commit();
            
            if ($emailResult['success']) {
                return back()->with('success', 'New login credentials have been generated and sent to ' . $patient->email);
            } else {
                \Log::error('Password reset email failed', [
                    'patient_id' => $patient->id,
                    'error' => $emailResult['message'],
                    'new_password' => $newPassword // Log for manual recovery
                ]);
                
                return back()->with('warning', 'Password reset successfully, but failed to send email. New password: ' . $newPassword);
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to reset password: ' . $e->getMessage()]);
        }
    }
}
