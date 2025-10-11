<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Services\EnhancedEmailService;
use App\Mail\AppointmentNotification;

class DebugRescheduleEmail extends Command
{
    protected $signature = 'debug:reschedule-email {appointment_id?}';
    protected $description = 'Debug reschedule email sending';

    public function handle()
    {
        $appointmentId = $this->argument('appointment_id');
        
        if (!$appointmentId) {
            // Get first appointment
            $appointment = Appointment::with('patient')->first();
            if (!$appointment) {
                $this->error('No appointments found in database');
                return 1;
            }
        } else {
            $appointment = Appointment::with('patient')->find($appointmentId);
            if (!$appointment) {
                $this->error("Appointment {$appointmentId} not found");
                return 1;
            }
        }

        $this->info("Testing reschedule email for appointment {$appointment->appointment_id}");
        
        // Check patient data
        $this->info("Checking patient data...");
        if (!$appointment->patient) {
            $this->error("No patient found for appointment");
            return 1;
        }
        
        $this->info("Patient ID: " . $appointment->patient->id);
        $this->info("Patient Name: " . ($appointment->patient->patient_name ?? 'NULL'));
        $this->info("Patient Email: " . ($appointment->patient->email ?? 'NULL'));
        
        if (!$appointment->patient->email) {
            $this->error("Patient has no email address");
            return 1;
        }
        
        // Check email config
        $this->info("\nChecking email configuration...");
        $this->info("MAIL_FROM_ADDRESS: " . (config('mail.from.address') ?? 'NULL'));
        $this->info("MAIL_FROM_NAME: " . (config('mail.from.name') ?? 'NULL'));
        $this->info("MAIL_MAILER: " . (config('mail.default') ?? 'NULL'));
        
        // Test email creation
        $this->info("\nTesting email creation...");
        try {
            $mailable = new AppointmentNotification($appointment, 'reschedule_request');
            $this->info("✓ AppointmentNotification created successfully");
            
            // Test envelope
            $envelope = $mailable->envelope();
            $this->info("✓ Envelope created successfully");
            $this->info("Subject: " . $envelope->subject);
            
            // Test content
            $content = $mailable->content();
            $this->info("✓ Content created successfully");
            $this->info("View: " . $content->view);
            
        } catch (\Exception $e) {
            $this->error("Error creating email: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
        
        // Test email service
        $this->info("\nTesting email service...");
        try {
            $emailService = app(EnhancedEmailService::class);
            $result = $emailService->sendAppointmentNotification($appointment, 'reschedule_request', [], true);
            
            if ($result['success']) {
                $this->info("✓ Email service test successful (test mode)");
                $this->info("Result: " . $result['message']);
            } else {
                $this->error("Email service test failed: " . $result['message']);
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error in email service: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
        
        $this->info("\n✓ All tests passed! The email should work correctly.");
        return 0;
    }
}