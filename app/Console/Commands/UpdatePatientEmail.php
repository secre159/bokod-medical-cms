<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Console\Command;

class UpdatePatientEmail extends Command
{
    protected $signature = 'update:patient-email {email} {patient_id?}';
    protected $description = 'Update a patient email address for testing';

    public function handle()
    {
        $email = $this->argument('email');
        $patientId = $this->argument('patient_id');
        
        if ($patientId) {
            $patient = Patient::find($patientId);
            if (!$patient) {
                $this->error("Patient with ID {$patientId} not found.");
                return 1;
            }
        } else {
            // Find first patient with appointments
            $patient = Patient::whereHas('appointments')->first();
            if (!$patient) {
                $this->error("No patients with appointments found.");
                return 1;
            }
        }
        
        $oldEmail = $patient->email;
        $patient->update(['email' => $email]);
        
        $this->info("✅ Updated patient email:");
        $this->info("- Patient: {$patient->patient_name}");
        $this->info("- Old email: {$oldEmail}");
        $this->info("- New email: {$email}");
        
        // Show appointments for this patient
        $appointments = $patient->appointments()->get();
        if ($appointments->count() > 0) {
            $this->info("\n📅 Appointments for this patient:");
            foreach ($appointments as $appointment) {
                $this->info("- ID: {$appointment->appointment_id}, Date: {$appointment->appointment_date->format('Y-m-d')}, Time: {$appointment->appointment_time->format('H:i')}");
            }
            
            $this->info("\n🎯 You can now test reschedule emails by dragging appointment #{$appointments->first()->appointment_id} in the calendar!");
        }
        
        return 0;
    }
}