<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Services\EnhancedEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailTestController extends Controller
{
    protected $emailService;

    public function __construct(EnhancedEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Show email testing dashboard
     */
    public function index()
    {
        // Get configuration status
        $configStatus = $this->emailService->checkConfiguration();
        
        // Get sample data for testing
        $samplePatient = Patient::whereNotNull('email')->first();
        $sampleAppointment = Appointment::with('patient')
            ->whereHas('patient', function($query) {
                $query->whereNotNull('email');
            })->first();
        $samplePrescription = Prescription::with('patient')
            ->whereHas('patient', function($query) {
                $query->whereNotNull('email');
            })->first();
        
        return view('admin.email-test', compact(
            'configStatus', 
            'samplePatient', 
            'sampleAppointment', 
            'samplePrescription'
        ));
    }
    
    /**
     * Test patient welcome email
     */
    public function testPatientWelcome(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'test_mode' => 'boolean'
        ]);
        
        $patient = Patient::findOrFail($request->patient_id);
        $testMode = $request->boolean('test_mode', true);
        
        $result = $this->emailService->sendPatientWelcome($patient, 'patient123', $testMode);
        
        return response()->json($result);
    }
    
    /**
     * Test appointment notification email
     */
    public function testAppointmentNotification(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,appointment_id',
            'notification_type' => 'required|in:approved,cancelled,completed,reminder,rejected',
            'test_mode' => 'boolean'
        ]);
        
        $appointment = Appointment::findOrFail($request->appointment_id);
        $testMode = $request->boolean('test_mode', true);
        $type = $request->notification_type;
        
        $additionalData = [];
        if ($type === 'rejected') {
            $additionalData['rejection_reason'] = 'Sample rejection reason for testing';
        } elseif ($type === 'cancelled') {
            $additionalData['cancellation_reason'] = 'Sample cancellation reason for testing';
        }
        
        $result = $this->emailService->sendAppointmentNotification($appointment, $type, $additionalData, $testMode);
        
        return response()->json($result);
    }
    
    /**
     * Test prescription notification email
     */
    public function testPrescriptionNotification(Request $request)
    {
        $request->validate([
            'prescription_id' => 'required|exists:prescriptions,prescription_id',
            'notification_type' => 'required|in:new,updated,reminder',
            'test_mode' => 'boolean'
        ]);
        
        $prescription = Prescription::findOrFail($request->prescription_id);
        $testMode = $request->boolean('test_mode', true);
        $type = $request->notification_type;
        
        $medicines = [[
            'medicine_name' => $prescription->medicine_name ?? 'Sample Medicine',
            'dosage' => $prescription->dosage ?? '500mg',
            'quantity' => $prescription->quantity ?? 30,
            'instructions' => $prescription->instructions ?? 'Take twice daily after meals'
        ]];
        
        $additionalData = ['medicines' => $medicines];
        
        $result = $this->emailService->sendPrescriptionNotification($prescription, $type, $additionalData, $testMode);
        
        return response()->json($result);
    }
    
    /**
     * Test health tips email
     */
    public function testHealthTips(Request $request)
    {
        $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'season' => 'required|in:rainy,dry',
            'test_mode' => 'boolean'
        ]);
        
        $patient = $request->patient_id ? Patient::findOrFail($request->patient_id) : null;
        $season = $request->season;
        $testMode = $request->boolean('test_mode', true);
        
        $additionalData = [
            'vaccination_reminders' => [
                'Flu vaccination',
                'Hepatitis A vaccination (for rainy season)',
                'Regular health check-ups'
            ]
        ];
        
        $result = $this->emailService->sendHealthTips($patient, [], $season, $additionalData, $testMode);
        
        return response()->json($result);
    }
    
    /**
     * Test stock alert email
     */
    public function testStockAlert(Request $request)
    {
        $request->validate([
            'alert_type' => 'required|in:low,critical,out_of_stock',
            'test_mode' => 'boolean'
        ]);
        
        $testMode = $request->boolean('test_mode', true);
        $alertType = $request->alert_type;
        
        // Sample data for testing
        $lowStockMedicines = [
            [
                'medicine_name' => 'Paracetamol 500mg',
                'current_stock' => 15,
                'reorder_level' => 20,
                'days_until_reorder' => '3 days'
            ],
            [
                'medicine_name' => 'Amoxicillin 250mg',
                'current_stock' => 18,
                'reorder_level' => 20,
                'days_until_reorder' => '5 days'
            ]
        ];
        
        $criticalStockMedicines = [
            [
                'medicine_name' => 'Insulin',
                'current_stock' => 5,
                'reorder_level' => 10,
                'status' => 'CRITICAL'
            ],
            [
                'medicine_name' => 'Aspirin 100mg',
                'current_stock' => 0,
                'reorder_level' => 20,
                'status' => 'OUT_OF_STOCK'
            ]
        ];
        
        $result = $this->emailService->sendStockAlert($lowStockMedicines, $criticalStockMedicines, $alertType, $testMode);
        
        return response()->json($result);
    }
    
    /**
     * Test medication reminders batch
     */
    public function testMedicationReminders(Request $request)
    {
        $testMode = $request->boolean('test_mode', true);
        
        $result = $this->emailService->sendMedicationReminders($testMode);
        
        return response()->json($result);
    }
    
    /**
     * Check email configuration
     */
    public function checkConfiguration()
    {
        $result = $this->emailService->checkConfiguration();
        
        return response()->json($result);
    }
}
