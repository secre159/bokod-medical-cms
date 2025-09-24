<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Medicine;
use App\Services\EnhancedEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PrescriptionController extends Controller
{
    protected $emailService;
    
    public function __construct(EnhancedEmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    /**
     * Display a listing of prescriptions with filtering
     */
    public function index(Request $request)
    {
        try {
            $query = Prescription::with(['patient']);
            
            // Filter by status
            if ($request->filled('status')) {
                $status = $request->get('status');
                if ($status === 'expired') {
                    $query->where('expiry_date', '<', now())
                          ->where('status', '!=', 'completed');
                } else {
                    $query->where('status', $status);
                }
            }
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('medicine_name', 'like', "%{$search}%")
                      ->orWhere('dosage', 'like', "%{$search}%")
                      ->orWhere('instructions', 'like', "%{$search}%")
                      ->orWhere('frequency', 'like', "%{$search}%")
                      ->orWhereHas('patient', function ($patientQuery) use ($search) {
                          $patientQuery->where('patient_name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }
            
            // Order by prescribed date (newest first)
            $query->orderBy('prescribed_date', 'desc');
            
            $prescriptions = $query->paginate(15);
            
            // Add computed properties to each prescription
            $prescriptions->getCollection()->transform(function ($prescription) {
                $prescription->is_expired = $prescription->expiry_date && $prescription->expiry_date->isPast();
                $prescription->is_expiring_soon = $prescription->expiry_date && 
                    $prescription->expiry_date->between(now(), now()->addDays(7));
                
                // Format frequency text
                $prescription->frequency_text = $this->formatFrequency($prescription->frequency);
                
                return $prescription;
            });
            
            // Statistics
            $stats = [
                'total_active' => Prescription::where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                    })->count(),
                'completed' => Prescription::where('status', 'completed')->count(),
                'expired' => Prescription::where('expiry_date', '<', now())
                    ->where('status', '!=', 'completed')->count(),
                'total' => Prescription::count(),
            ];
            
            return view('prescriptions.index', compact('prescriptions', 'stats'));
        } catch (\Exception $e) {
            Log::error('Prescription index error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error loading prescriptions: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new prescription
     */
    public function create(Request $request)
    {
        $patients = Patient::active()->orderBy('patient_name')->get();
        $medicines = Medicine::active()->orderBy('medicine_name')->get();
        $frequencies = Prescription::getFrequencies();
        
        // Check if patient_id is provided in the request (from appointment or patient profile)
        $selectedPatientId = $request->input('patient_id');
        $selectedPatient = null;
        $isPatientLocked = false;
        
        if ($selectedPatientId) {
            // Validate that the patient exists and is active
            $selectedPatient = Patient::active()->find($selectedPatientId);
            if ($selectedPatient) {
                $isPatientLocked = true;
            } else {
                // Patient not found or not active, clear the selection
                $selectedPatientId = null;
            }
        }
        
        return view('prescriptions.create', compact(
            'patients', 
            'medicines', 
            'frequencies',
            'selectedPatientId',
            'selectedPatient',
            'isPatientLocked'
        ));
    }

    /**
     * Store a newly created prescription
     */
    public function store(Request $request)
    {
        // Determine validation rules based on medicine selection type
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'quantity' => 'required|integer|min:1',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|string|max:50',
            'instructions' => 'required|string',
            'prescribed_date' => 'required|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:prescribed_date',
            'notes' => 'nullable|string',
            'medicine_selection_type' => 'required|in:inventory,custom,no_medicine',
        ];
        
        // Add conditional validation based on medicine selection type
        if ($request->input('medicine_selection_type') === 'inventory') {
            $rules['medicine_id'] = 'required|exists:medicines,id';
            $rules['dosage'] = 'required|string|max:255';
            $rules['frequency'] = 'required|string|max:50';
            $rules['quantity'] = 'required|integer|min:1';
        } elseif ($request->input('medicine_selection_type') === 'custom') {
            $rules['medicine_name_custom'] = 'required|string|max:255';
            $rules['generic_name_custom'] = 'nullable|string|max:255';
            $rules['dosage'] = 'required|string|max:255';
            $rules['frequency'] = 'required|string|max:50';
            $rules['quantity'] = 'required|integer|min:1';
        } else { // no_medicine
            $rules['consultation_type'] = 'nullable|string|max:100';
            // Make these fields optional for consultations
            unset($rules['dosage'], $rules['frequency']);
            $rules['dosage'] = 'nullable|string|max:255';
            $rules['frequency'] = 'nullable|string|max:50';
            $rules['quantity'] = 'nullable|integer|min:1';
        }
        
        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        try {
            // Handle medicine data based on selection type
            if ($validated['medicine_selection_type'] === 'inventory') {
                // Get medicine details from inventory
                $medicine = Medicine::findOrFail($validated['medicine_id']);
                $validated['medicine_name'] = $medicine->medicine_name;
                $validated['generic_name'] = $medicine->generic_name ?? null;
                
                // Check stock availability
                if ($medicine->stock_quantity < $validated['quantity']) {
                    $errorMessage = 'Insufficient stock. Available: ' . $medicine->stock_quantity . ' units.';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => ['quantity' => $errorMessage]
                        ], 422);
                    }
                    return back()->withErrors([
                        'quantity' => $errorMessage
                    ])->withInput();
                }
                
                // Deduct from stock
                $medicine->decrement('stock_quantity', $validated['quantity']);
                
            } elseif ($validated['medicine_selection_type'] === 'custom') {
                // Use custom medicine data
                $validated['medicine_name'] = $validated['medicine_name_custom'];
                $validated['generic_name'] = $validated['generic_name_custom'] ?? null;
                $validated['medicine_id'] = null; // No medicine_id for custom medicines
                $medicine = null; // For email notification
                
            } else { // no_medicine
                // Handle consultation without medicine
                $consultationType = $validated['consultation_type'] ?? 'general_consultation';
                $validated['medicine_name'] = 'Consultation: ' . str_replace('_', ' ', ucwords($consultationType, '_'));
                $validated['generic_name'] = null;
                $validated['medicine_id'] = null;
                $validated['dosage'] = $validated['dosage'] ?? 'Not Applicable';
                $validated['frequency'] = $validated['frequency'] ?? 'not_applicable';
                $validated['quantity'] = $validated['quantity'] ?? 1;
                // Keep consultation_type for storage
                $validated['consultation_type'] = $consultationType;
                $medicine = null; // For email notification
            }
            
            // Set default values
            $validated['status'] = Prescription::STATUS_ACTIVE;
            $validated['prescribed_by'] = Auth::id();
            
            // Set expiry date if not provided (default 30 days)
            if (!isset($validated['expiry_date'])) {
                $validated['expiry_date'] = Carbon::parse($validated['prescribed_date'])->addDays(30);
            }
            
            // Remove temporary custom fields before saving (keep consultation_type for no_medicine records)
            $tempConsultationType = $validated['consultation_type'] ?? null;
            unset($validated['medicine_selection_type'], $validated['medicine_name_custom'], $validated['generic_name_custom']);
            
            // Restore consultation_type if it's a no_medicine consultation
            if ($tempConsultationType && isset($validated['medicine_name']) && strpos($validated['medicine_name'], 'Consultation:') === 0) {
                $validated['consultation_type'] = $tempConsultationType;
            } else {
                unset($validated['consultation_type']);
            }
            
            // Create prescription
            $prescription = Prescription::create($validated);
            
            // Send new prescription notification email
            try {
                $medicines = [[
                    'medicine_name' => $validated['medicine_name'],
                    'dosage' => $validated['dosage'],
                    'quantity' => $validated['quantity'],
                    'instructions' => $validated['instructions']
                ]];
                
                $this->emailService->sendPrescriptionNotification(
                    $prescription, 
                    'new', 
                    ['medicines' => $medicines]
                );
                $emailStatus = 'Prescription notification sent to patient.';
            } catch (\Exception $e) {
                $emailStatus = 'Prescription created but email notification failed.';
                Log::error('Prescription notification email failed', [
                    'prescription_id' => $prescription->id ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
            
            $successMessage = "Prescription created successfully! " . $emailStatus . 
                             "\n\nPrescription Details:\n" .
                             "• Medicine: {$validated['medicine_name']}" . 
                             ($validated['generic_name'] ? " ({$validated['generic_name']})" : '') . "\n" .
                             "• Patient: {$prescription->patient->patient_name}\n" .
                             "• Quantity: {$validated['quantity']}\n" .
                             "• Dosage: {$validated['dosage']}";
            
            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'prescription' => $prescription->load('patient')
                ]);
            }
            
            return redirect()->route('prescriptions.index')
                           ->with('success', $successMessage);
                           
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating prescription: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified prescription
     */
    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'medicine']);
        
        return view('prescriptions.show', compact('prescription'));
    }

    /**
     * Show the form for editing the prescription
     */
    public function edit(Prescription $prescription)
    {
        $patients = Patient::active()->orderBy('patient_name')->get();
        $medicines = Medicine::active()->orderBy('medicine_name')->get();
        $frequencies = Prescription::getFrequencies();
        
        return view('prescriptions.edit', compact('prescription', 'patients', 'medicines', 'frequencies'));
    }

    /**
     * Update the specified prescription
     */
    public function update(Request $request, Prescription $prescription)
    {
        // Determine validation rules based on medicine selection type
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'quantity' => 'required|integer|min:1',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|string|max:50',
            'instructions' => 'required|string',
            'prescribed_date' => 'required|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:prescribed_date',
            'notes' => 'nullable|string',
            'medicine_selection_type' => 'required|in:inventory,custom',
        ];
        
        // Add conditional validation based on medicine selection type
        if ($request->input('medicine_selection_type') === 'inventory') {
            $rules['medicine_id'] = 'required|exists:medicines,id';
        } else {
            $rules['medicine_name_custom'] = 'required|string|max:255';
            $rules['generic_name_custom'] = 'nullable|string|max:255';
        }
        
        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        try {
            // Handle medicine data based on selection type
            if ($validated['medicine_selection_type'] === 'inventory') {
                $medicine = Medicine::findOrFail($validated['medicine_id']);
                $validated['medicine_name'] = $medicine->medicine_name;
                $validated['generic_name'] = $medicine->generic_name ?? null;
            } else {
                $validated['medicine_name'] = $validated['medicine_name_custom'];
                $validated['generic_name'] = $validated['generic_name_custom'] ?? null;
                $validated['medicine_id'] = null;
            }
            
            // Remove temporary custom fields before saving
            unset($validated['medicine_selection_type'], $validated['medicine_name_custom'], $validated['generic_name_custom']);
            
            $prescription->update($validated);
            
            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prescription updated successfully!',
                    'prescription' => $prescription->fresh()->load('patient')
                ]);
            }
            
            return redirect()->route('prescriptions.index')
                           ->with('success', 'Prescription updated successfully!');
                           
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating prescription: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Cancel the specified prescription
     */
    public function destroy(Prescription $prescription)
    {
        try {
            DB::beginTransaction();
            
            if ($prescription->status === 'active') {
                $prescription->update([
                    'status' => 'cancelled',
                    'updated_by' => Auth::id(),
                ]);
                
                // Restore stock if medicine was from inventory
                if ($prescription->medicine_id) {
                    $medicine = Medicine::find($prescription->medicine_id);
                    if ($medicine) {
                        $dispensedQuantity = $prescription->dispensed_quantity ?? 0;
                        $restoreQuantity = $prescription->quantity - $dispensedQuantity;
                        
                        if ($restoreQuantity > 0) {
                            $medicine->increment('stock_quantity', $restoreQuantity);
                        }
                    }
                }
                
                DB::commit();
                
                // Handle AJAX requests
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Prescription cancelled successfully.'
                    ]);
                }
                
                return redirect()->route('prescriptions.index')
                    ->with('success', 'Prescription cancelled successfully.');
            } else {
                $errorMessage = 'Only active prescriptions can be cancelled.';
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                return redirect()->route('prescriptions.index')
                    ->withErrors(['error' => $errorMessage]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Prescription cancel error: ' . $e->getMessage());
            $errorMessage = 'Error cancelling prescription: ' . $e->getMessage();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['error' => $errorMessage]);
        }
    }
    
    /**
     * Dispense medication for a prescription.
     */
    public function dispense(Request $request, Prescription $prescription)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:500',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        
        try {
            if ($prescription->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only active prescriptions can be dispensed.'
                ], 400);
            }
            
            $dispensedQuantity = $prescription->dispensed_quantity ?? 0;
            $remainingQuantity = $prescription->quantity - $dispensedQuantity;
            
            if ($request->quantity > $remainingQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot dispense more than remaining quantity.'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Update prescription with dispensed quantity
            $newDispensedQuantity = $dispensedQuantity + $request->quantity;
            $prescription->update([
                'dispensed_quantity' => $newDispensedQuantity,
                'status' => $newDispensedQuantity >= $prescription->quantity ? 'completed' : 'active',
                'updated_by' => Auth::id(),
            ]);
            
            // Create dispensing record (if you have a dispensing_records table)
            // DispenseRecord::create([...]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Medication dispensed successfully.',
                'dispensed_quantity' => $newDispensedQuantity,
                'remaining_quantity' => $prescription->quantity - $newDispensedQuantity,
                'status' => $prescription->status,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Prescription dispense error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error dispensing medication: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Format frequency text for display.
     */
    private function formatFrequency($frequency)
    {
        $frequencies = [
            'once_daily' => 'Once daily',
            'twice_daily' => 'Twice daily',
            'three_times_daily' => 'Three times daily',
            'four_times_daily' => 'Four times daily',
            'every_4_hours' => 'Every 4 hours',
            'every_6_hours' => 'Every 6 hours',
            'every_8_hours' => 'Every 8 hours',
            'every_12_hours' => 'Every 12 hours',
            'as_needed' => 'As needed',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
        
        return $frequencies[$frequency] ?? ucwords(str_replace('_', ' ', $frequency));
    }
    
    /**
     * Get prescription statistics for dashboard.
     */
    public function getStats()
    {
        try {
            $stats = [
                'total' => Prescription::count(),
                'active' => Prescription::where('status', 'active')->count(),
                'completed' => Prescription::where('status', 'completed')->count(),
                'expired' => Prescription::where('expiry_date', '<', now())
                    ->where('status', '!=', 'completed')->count(),
                'expiring_soon' => Prescription::where('expiry_date', '>', now())
                    ->where('expiry_date', '<=', now()->addDays(7))
                    ->where('status', 'active')->count(),
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Prescription stats error: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching prescription statistics.'], 500);
        }
    }
}
