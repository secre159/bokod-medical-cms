<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnhancedEmailService;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\AppointmentNotification;

class DiagnoseEmailSystem extends Command
{
    protected $signature = 'email:diagnose {--test-send : Actually send test emails}';
    protected $description = 'Diagnose email system configuration and functionality';

    public function handle()
    {
        $this->info('🔍 Diagnosing Email System...');
        $this->newLine();

        // 1. Check basic configuration
        $this->info('1. Checking Email Configuration...');
        $this->checkEmailConfig();
        $this->newLine();

        // 2. Check queue configuration
        $this->info('2. Checking Queue Configuration...');
        $this->checkQueueConfig();
        $this->newLine();

        // 3. Check database connectivity
        $this->info('3. Checking Database...');
        $this->checkDatabase();
        $this->newLine();

        // 4. Check email service
        $this->info('4. Testing Enhanced Email Service...');
        $this->testEmailService();
        $this->newLine();

        // 5. Check for test appointment
        $this->info('5. Testing Appointment Email Flow...');
        $this->testAppointmentEmailFlow();
        $this->newLine();

        // 6. Check queue jobs
        $this->info('6. Checking Queue Jobs...');
        $this->checkQueueJobs();
        $this->newLine();

        $this->info('📋 Diagnosis Complete!');
        $this->newLine();
        $this->warn('💡 Recommendations:');
        $this->provideRecommendations();
    }

    private function checkEmailConfig()
    {
        $checks = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? '[SET]' : '[NOT SET]',
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];

        foreach ($checks as $key => $value) {
            $status = $value ? '✅' : '❌';
            $this->line("  $status $key: $value");
        }
    }

    private function checkQueueConfig()
    {
        $queueConnection = config('queue.default');
        $this->line("  📋 Queue Connection: $queueConnection");
        
        if ($queueConnection === 'sync') {
            $this->warn('  ⚠️  Queue is set to SYNC - emails will be sent immediately but may timeout');
        } elseif ($queueConnection === 'database') {
            // Check if jobs table exists
            try {
                DB::table('jobs')->count();
                $this->line('  ✅ Jobs table exists');
            } catch (\Exception $e) {
                $this->error('  ❌ Jobs table missing - run: php artisan migrate');
            }
        }
    }

    private function checkDatabase()
    {
        try {
            $patientCount = Patient::count();
            $appointmentCount = Appointment::count();
            $this->line("  ✅ Database connected");
            $this->line("  📊 Patients: $patientCount");
            $this->line("  📊 Appointments: $appointmentCount");
        } catch (\Exception $e) {
            $this->error("  ❌ Database error: " . $e->getMessage());
        }
    }

    private function testEmailService()
    {
        try {
            $emailService = app(EnhancedEmailService::class);
            
            // Test configuration check
            $configCheck = $emailService->checkConfiguration();
            if ($configCheck['configured']) {
                $this->line('  ✅ Email service configuration: PASSED');
            } else {
                $this->error('  ❌ Email service configuration: FAILED');
                $this->line('    Issues: ' . $configCheck['message']);
            }

            // Test with a dummy patient
            $testPatient = Patient::first();
            if ($testPatient) {
                $result = $emailService->sendPatientWelcome($testPatient, 'test123', true); // test mode
                $status = $result['success'] ? '✅' : '❌';
                $this->line("  $status Test welcome email: " . $result['message']);
            }

        } catch (\Exception $e) {
            $this->error("  ❌ Email service error: " . $e->getMessage());
        }
    }

    private function testAppointmentEmailFlow()
    {
        try {
            // Find a test appointment
            $appointment = Appointment::with('patient')->first();
            
            if (!$appointment) {
                $this->warn('  ⚠️  No appointments found - cannot test appointment email flow');
                return;
            }

            if (!$appointment->patient) {
                $this->warn('  ⚠️  Appointment has no patient - cannot test email flow');
                return;
            }

            if (!$appointment->patient->email) {
                $this->warn('  ⚠️  Patient has no email - cannot test email flow');
                return;
            }

            $this->line("  📧 Testing with Appointment ID: {$appointment->appointment_id}");
            $this->line("  👤 Patient: {$appointment->patient->patient_name}");
            $this->line("  📧 Email: {$appointment->patient->email}");

            // Test email service
            $emailService = app(EnhancedEmailService::class);
            $result = $emailService->sendAppointmentNotification($appointment, 'approved', [], true); // test mode
            
            $status = $result['success'] ? '✅' : '❌';
            $this->line("  $status Test approval email: " . $result['message']);

            if ($this->option('test-send')) {
                $this->warn('  🚀 Sending actual test email...');
                $realResult = $emailService->sendAppointmentNotification($appointment, 'approved');
                $realStatus = $realResult['success'] ? '✅' : '❌';
                $this->line("  $realStatus Real approval email: " . $realResult['message']);
            }

        } catch (\Exception $e) {
            $this->error("  ❌ Appointment email test error: " . $e->getMessage());
        }
    }

    private function checkQueueJobs()
    {
        try {
            if (config('queue.default') === 'database') {
                $pendingJobs = DB::table('jobs')->count();
                $failedJobs = DB::table('failed_jobs')->count();
                
                $this->line("  📋 Pending jobs: $pendingJobs");
                $this->line("  ❌ Failed jobs: $failedJobs");
                
                if ($pendingJobs > 0) {
                    $this->warn("  ⚠️  There are $pendingJobs pending jobs - queue worker may not be running");
                }
                
                if ($failedJobs > 0) {
                    $this->error("  ❌ There are $failedJobs failed jobs - check with: php artisan failed:show");
                }
            } else {
                $this->line('  📋 Queue not using database - cannot check job counts');
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Queue check error: " . $e->getMessage());
        }
    }

    private function provideRecommendations()
    {
        $this->line('  1. For Render deployment:');
        $this->line('     - Set QUEUE_CONNECTION=sync in production (or setup queue worker)');
        $this->line('     - Ensure all MAIL_* environment variables are set');
        $this->line('     - Use Gmail SMTP or service like SendGrid/Mailgun for reliability');
        $this->newLine();
        
        $this->line('  2. To test locally:');
        $this->line('     - Run: php artisan email:diagnose --test-send');
        $this->line('     - Check logs in storage/logs/');
        $this->line('     - For queued emails, run: php artisan queue:work');
        $this->newLine();
        
        $this->line('  3. For debugging on Render:');
        $this->line('     - Check Render logs for email errors');
        $this->line('     - Test SMTP connection with your email provider');
        $this->line('     - Verify Gmail app password is correct');
    }
}