<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\EnhancedEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DiagnoseEmailIssue extends Command
{
    protected $signature = 'diagnose:email';
    protected $description = 'Diagnose email configuration and test reschedule functionality';

    public function handle()
    {
        $this->info('🔍 Email System Diagnosis');
        $this->info('========================');
        
        // Check basic email configuration
        $this->checkEmailConfig();
        
        // Check email service
        $this->checkEmailService();
        
        // Test reschedule email specifically
        $this->testRescheduleEmail();
        
        // Provide recommendations
        $this->provideRecommendations();
    }
    
    private function checkEmailConfig()
    {
        $this->info("\n📧 Email Configuration:");
        $this->info("- Driver: " . config('mail.default'));
        $this->info("- Host: " . config('mail.mailers.smtp.host'));
        $this->info("- Port: " . config('mail.mailers.smtp.port'));
        $this->info("- Username: " . config('mail.mailers.smtp.username'));
        $this->info("- From Address: " . config('mail.from.address'));
        $this->info("- From Name: " . config('mail.from.name'));
        $this->info("- Queue Connection: " . config('queue.default'));
        
        if (config('mail.default') === 'log') {
            $this->warn("⚠️  Mail driver is set to 'log' - emails will be logged, not sent!");
        }
    }
    
    private function checkEmailService()
    {
        $this->info("\n🛠️  Email Service Status:");
        
        try {
            $emailService = app(EnhancedEmailService::class);
            $config = $emailService->checkConfiguration();
            
            if ($config['configured']) {
                $this->info("✅ Email service is properly configured");
            } else {
                $this->error("❌ Email service configuration issues:");
                foreach ($config['checks'] as $check => $status) {
                    $icon = $status ? "✅" : "❌";
                    $this->info("   {$icon} {$check}");
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Email service error: " . $e->getMessage());
        }
    }
    
    private function testRescheduleEmail()
    {
        $this->info("\n🧪 Testing Reschedule Email:");
        
        // Find appointment with email
        $appointment = Appointment::with('patient')
            ->whereHas('patient', function($q) {
                $q->whereNotNull('email');
            })
            ->first();
        
        if (!$appointment) {
            $this->error("❌ No appointments found with patient email addresses");
            return;
        }
        
        $this->info("Using appointment: #{$appointment->appointment_id} for {$appointment->patient->patient_name}");
        $this->info("Patient email: {$appointment->patient->email}");
        
        if (!$this->confirm('Send test reschedule email to this patient?')) {
            $this->info('Test skipped.');
            return;
        }
        
        try {
            // Test in multiple ways
            $this->info("\n📤 Sending test emails...");
            
            // Method 1: Direct email service
            $emailService = app(EnhancedEmailService::class);
            $result1 = $emailService->sendAppointmentNotification($appointment, 'rescheduled');
            
            $this->info("Direct email service: " . ($result1['success'] ? "✅ Success" : "❌ Failed - " . $result1['message']));
            
            // Method 2: Test mode (no actual sending)
            $result2 = $emailService->sendAppointmentNotification($appointment, 'rescheduled', [], true);
            $this->info("Test mode result: " . ($result2['success'] ? "✅ Success" : "❌ Failed - " . $result2['message']));
            
            if ($result2['success']) {
                $this->info("  Subject would be: " . $result2['subject']);
                $this->info("  Recipient would be: " . $result2['recipient']);
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
    
    private function provideRecommendations()
    {
        $this->info("\n💡 Recommendations:");
        $this->info("==================");
        
        // Check recent logs
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $recentLogs = shell_exec("tail -20 \"{$logFile}\" | grep -E '(email|notification|rescheduled)'");
            if ($recentLogs) {
                $this->info("Recent email-related log entries:");
                $this->info($recentLogs);
            }
        }
        
        $this->info("\nTo fix email issues:");
        $this->info("1. Make sure XAMPP/Apache is running");
        $this->info("2. Check your internet connection");
        $this->info("3. Verify Gmail app password is correct");
        $this->info("4. Try drag-and-drop reschedule again");
        $this->info("5. Check spam/junk folder in email client");
        
        $this->info("\nTo start queue worker for better email processing:");
        $this->info("php artisan queue:work --daemon");
        
        $this->info("\nTo monitor logs in real-time:");
        $this->info("tail -f storage/logs/laravel.log | grep -i email");
    }
}