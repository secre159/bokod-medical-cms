<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentNotification;
use App\Models\Appointment;

class DiagnoseEmailHeaders extends Command
{
    protected $signature = 'diagnose:headers {email} {--appointment_id=}';
    protected $description = 'Deep diagnosis of email header issues in hosted environment';

    public function handle()
    {
        $testEmail = $this->argument('email');
        $appointmentId = $this->option('appointment_id');
        
        $this->info('=== BOKOD CMS EMAIL HEADER DIAGNOSIS ===');
        $this->newLine();
        
        // 1. Environment Analysis
        $this->analyzeEnvironment();
        $this->newLine();
        
        // 2. Configuration Check
        $this->checkConfiguration();
        $this->newLine();
        
        // 3. Header Validation Test
        $this->testEmailHeaders($testEmail);
        $this->newLine();
        
        // 4. Appointment Email Test
        $this->testAppointmentEmail($testEmail, $appointmentId);
        $this->newLine();
        
        $this->info('=== DIAGNOSIS COMPLETE ===');
        
        return Command::SUCCESS;
    }

    private function analyzeEnvironment()
    {
        $this->info('🌍 ENVIRONMENT ANALYSIS');
        
        $this->table(['Property', 'Value'], [
            ['Environment', app()->environment()],
            ['Config Cached', app()->configurationIsCached() ? 'YES' : 'NO'],
            ['Routes Cached', app()->routesAreCached() ? 'YES' : 'NO'],
            ['Base Path', base_path()],
            ['Storage Path', storage_path()],
        ]);
        
        // Check environment variables directly
        $this->info('🔍 DIRECT ENV VARIABLE CHECK:');
        $envVars = ['MAIL_MAILER', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME', 'RESEND_API_KEY'];
        
        foreach ($envVars as $var) {
            $value = $_ENV[$var] ?? getenv($var) ?: 'NOT SET';
            if ($var === 'RESEND_API_KEY' && $value !== 'NOT SET') {
                $value = '***PRESENT*** (length: ' . strlen($value) . ')';
            }
            $this->line("   {$var}: {$value}");
        }
    }

    private function checkConfiguration()
    {
        $this->info('⚙️ CONFIGURATION CHECK');
        
        // Mail configuration
        $mailConfig = config('mail');
        
        $this->table(['Config Key', 'Value'], [
            ['mail.default', $mailConfig['default'] ?? 'NULL'],
            ['mail.from.address', $mailConfig['from']['address'] ?? 'NULL'],
            ['mail.from.name', $mailConfig['from']['name'] ?? 'NULL'],
            ['services.resend.key', config('services.resend.key') ? '***PRESENT***' : 'NULL'],
        ]);
        
        // Resend mailer config
        if (isset($mailConfig['mailers']['resend'])) {
            $resendConfig = $mailConfig['mailers']['resend'];
            $this->info('📧 RESEND MAILER CONFIG:');
            $this->line('   Transport: ' . ($resendConfig['transport'] ?? 'NULL'));
            $apiKey = $resendConfig['key'] ?? 'NULL';
            if ($apiKey !== 'NULL') {
                $apiKey = '***PRESENT*** (length: ' . strlen($apiKey) . ')';
            }
            $this->line('   API Key: ' . $apiKey);
        } else {
            $this->error('❌ RESEND MAILER NOT CONFIGURED!');
        }
        
        // Check env() vs config() differences
        $this->info('🔄 ENV() vs CONFIG() COMPARISON:');
        $comparisons = [
            'MAIL_MAILER' => ['env' => env('MAIL_MAILER'), 'config' => config('mail.default')],
            'MAIL_FROM_ADDRESS' => ['env' => env('MAIL_FROM_ADDRESS'), 'config' => config('mail.from.address')],
            'MAIL_FROM_NAME' => ['env' => env('MAIL_FROM_NAME'), 'config' => config('mail.from.name')],
        ];
        
        foreach ($comparisons as $key => $values) {
            $match = $values['env'] === $values['config'] ? '✅' : '❌';
            $this->line("   {$key}: ENV({$values['env']}) vs CONFIG({$values['config']}) {$match}");
        }
    }

    private function testEmailHeaders($testEmail)
    {
        $this->info('🏷️ EMAIL HEADER VALIDATION TEST');
        
        try {
            // Test creating envelope with current config
            $fromAddress = config('mail.from.address');
            $fromName = config('mail.from.name');
            
            $this->line("Testing with FROM address: {$fromAddress}");
            $this->line("Testing with FROM name: {$fromName}");
            
            // Validate FROM address
            if (!filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
                $this->error("❌ Invalid FROM address format: {$fromAddress}");
                $fromAddress = 'noreply@resend.dev';
                $this->line("Using fallback: {$fromAddress}");
            }
            
            // Validate FROM name - this is where the issue likely is
            $this->line("FROM name type: " . gettype($fromName));
            $this->line("FROM name length: " . strlen($fromName ?? ''));
            $this->line("FROM name is_string: " . (is_string($fromName) ? 'YES' : 'NO'));
            $this->line("FROM name empty: " . (empty($fromName) ? 'YES' : 'NO'));
            $this->line("FROM name null: " . (is_null($fromName) ? 'YES' : 'NO'));
            
            if (empty($fromName) || !is_string($fromName)) {
                $this->error("❌ Invalid FROM name: " . var_export($fromName, true));
                $fromName = 'Bokod CMS';
                $this->line("Using fallback: {$fromName}");
            }
            
            // Test envelope creation
            $envelope = new \Illuminate\Mail\Mailables\Envelope(
                from: new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName),
                subject: 'Header Test Email'
            );
            
            $this->info("✅ Email envelope created successfully");
            
            // Test simple email with validated headers
            Mail::raw('Header validation test', function ($message) use ($testEmail, $fromAddress, $fromName) {
                $message->to($testEmail)
                        ->subject('Header Validation Test')
                        ->from($fromAddress, $fromName);
            });
            
            $this->info("✅ Header validation test PASSED");
            
        } catch (\Exception $e) {
            $this->error("❌ Header validation test FAILED: " . $e->getMessage());
            $this->error("Error type: " . get_class($e));
            
            if (str_contains($e->getMessage(), 'addTextHeader')) {
                $this->error("💡 This is the exact error we're seeing! Null value in email headers.");
                $this->line("Possible causes:");
                $this->line("- FROM name contains null or invalid characters");
                $this->line("- FROM address is null or invalid");
                $this->line("- Config cache contains stale values");
                
                // Get detailed stack trace
                $this->line("Full stack trace:");
                $this->line($e->getTraceAsString());
            }
        }
    }

    private function testAppointmentEmail($testEmail, $appointmentId)
    {
        $this->info('📅 APPOINTMENT EMAIL TEST');
        
        try {
            // Get or create test appointment
            if ($appointmentId) {
                $appointment = Appointment::with('patient')->find($appointmentId);
                if (!$appointment) {
                    $this->error("Appointment {$appointmentId} not found");
                    return;
                }
                $this->line("Using appointment ID: {$appointmentId}");
            } else {
                // Create minimal mock appointment
                $appointment = new Appointment();
                $appointment->appointment_id = 99999;
                $appointment->appointment_date = now()->addDay();
                $appointment->appointment_time = now()->setTime(10, 0);
                $appointment->reason = 'Email diagnosis test';
                
                // Mock patient
                $patient = new \App\Models\Patient();
                $patient->patient_name = 'Email Test Patient';
                $patient->email = $testEmail;
                $appointment->setRelation('patient', $patient);
                
                $this->line("Using mock appointment data");
            }
            
            $this->line("Patient: " . ($appointment->patient->patient_name ?? 'NULL'));
            $this->line("Email: " . ($appointment->patient->email ?? 'NULL'));
            
            // Test mailable creation without sending
            $mailable = new AppointmentNotification($appointment, 'rescheduled');
            $this->line("✅ AppointmentNotification mailable created");
            
            // Test envelope creation
            $envelope = $mailable->envelope();
            $this->line("✅ Email envelope created");
            
            // Attempt to send
            Mail::to($testEmail)->send($mailable);
            $this->info("✅ Appointment notification email sent successfully");
            
        } catch (\Exception $e) {
            $this->error("❌ Appointment email test FAILED: " . $e->getMessage());
            $this->error("Error type: " . get_class($e));
            
            if (str_contains($e->getMessage(), 'addTextHeader')) {
                $this->error("💥 Found the exact same error as in production!");
                $this->line("This confirms the issue is in the AppointmentNotification mailable.");
            }
            
            $this->line("Stack trace:");
            $this->line($e->getTraceAsString());
        }
    }
}