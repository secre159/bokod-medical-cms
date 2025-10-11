<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentNotification;
use App\Models\Appointment;

class TestResendEmail extends Command
{
    protected $signature = 'test:resend {email} {--appointment_id=}';
    protected $description = 'Test Resend email service with detailed diagnostics';

    public function handle()
    {
        $testEmail = $this->argument('email');
        $appointmentId = $this->option('appointment_id');
        
        $this->info("Testing Resend email service...");
        $this->info("Target Email: " . $testEmail);
        
        // Check configuration
        $this->info("\n=== CONFIGURATION CHECK ===");
        $this->info("MAIL_MAILER: " . (config('mail.default') ?? 'NULL'));
        $this->info("MAIL_FROM_ADDRESS: " . (config('mail.from.address') ?? 'NULL'));
        $this->info("MAIL_FROM_NAME: " . (config('mail.from.name') ?? 'NULL'));
        $this->info("RESEND_API_KEY: " . (config('resend.api_key') ?? env('RESEND_API_KEY') ?? 'NULL'));
        
        // Test 1: Simple mail test
        $this->info("\n=== TEST 1: Simple Mail Test ===");
        try {
            Mail::raw('This is a test email from Bokod CMS', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email from Bokod CMS')
                        ->from(config('mail.from.address', 'noreply@resend.dev'), config('mail.from.name', 'Bokod CMS'));
            });
            $this->info("✅ Simple mail test PASSED");
        } catch (\Exception $e) {
            $this->error("❌ Simple mail test FAILED: " . $e->getMessage());
            $this->error("Error Type: " . get_class($e));
            return 1;
        }
        
        // Test 2: AppointmentNotification with mock data
        $this->info("\n=== TEST 2: AppointmentNotification Test ===");
        try {
            // Get or create a test appointment
            if ($appointmentId) {
                $appointment = Appointment::with('patient')->find($appointmentId);
                if (!$appointment) {
                    $this->error("Appointment {$appointmentId} not found");
                    return 1;
                }
            } else {
                // Create mock appointment data
                $appointment = new Appointment();
                $appointment->appointment_id = 99999;
                $appointment->appointment_date = now()->addDay();
                $appointment->appointment_time = now()->setTime(10, 0);
                $appointment->reason = 'Test appointment';
                
                // Create mock patient
                $patient = new \App\Models\Patient();
                $patient->patient_name = 'Test Patient';
                $patient->email = $testEmail;
                $appointment->setRelation('patient', $patient);
            }
            
            $this->info("Testing with appointment ID: " . $appointment->appointment_id);
            $this->info("Patient name: " . ($appointment->patient->patient_name ?? 'NULL'));
            $this->info("Patient email: " . ($appointment->patient->email ?? 'NULL'));
            
            // Test the mailable
            $mailable = new AppointmentNotification($appointment, 'reschedule_request');
            Mail::to($testEmail)->send($mailable);
            
            $this->info("✅ AppointmentNotification test PASSED");
            
        } catch (\Exception $e) {
            $this->error("❌ AppointmentNotification test FAILED: " . $e->getMessage());
            $this->error("Error Type: " . get_class($e));
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
        
        // Test 3: Check email queue (if using)
        $this->info("\n=== TEST 3: Queue Status ===");
        $this->info("QUEUE_CONNECTION: " . config('queue.default'));
        
        $this->info("\n✅ All tests passed! Resend email service is working correctly.");
        $this->info("You can now re-enable email notifications in your appointment methods.");
        
        return 0;
    }
}