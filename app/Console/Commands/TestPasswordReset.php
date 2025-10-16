<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Notifications\ResetPassword;

class TestPasswordReset extends Command
{
    protected $signature = 'test:password-reset {email}';
    protected $description = 'Test password reset functionality for debugging';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing password reset for: {$email}");
        
        // Check if user exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found");
            return;
        }
        
        $this->info("User found: {$user->name} (ID: {$user->id})");
        $this->info("User status: {$user->status}");
        $this->info("User role: {$user->role}");
        
        // Check user status
        if (!$user->isActive()) {
            $this->error("User is not active - Status: {$user->status}");
            return;
        }
        
        // Check patient approval status
        if ($user->isPatient()) {
            $this->info("Checking patient approval status...");
            if ($user->isRegistrationPending()) {
                $this->error("Patient registration is pending approval");
                return;
            }
            if ($user->isRegistrationRejected()) {
                $this->error("Patient registration was rejected: {$user->rejection_reason}");
                return;
            }
        }
        
        $this->info("User passes all validation checks");
        
        // Test mail configuration
        $this->info("Testing mail configuration...");
        $this->info("Mail driver: " . config('mail.default'));
        $this->info("Mail from: " . config('mail.from.address'));
        $this->info("Mail from name: " . config('mail.from.name'));
        
        // Check environment variables
        $this->info("Environment variables:");
        $this->info("MAIL_MAILER: " . env('MAIL_MAILER'));
        $this->info("MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS'));
        $this->info("RESEND_API_KEY: " . (env('RESEND_API_KEY') ? 'Set (hidden)' : 'Not set'));
        
        // Attempt to send reset link
        $this->info("Attempting to send password reset link...");
        
        try {
            $status = Password::sendResetLink(['email' => $email]);
            
            $this->info("Password reset status: {$status}");
            
            if ($status === Password::RESET_LINK_SENT) {
                $this->info("✅ Password reset link sent successfully!");
            } else {
                $this->error("❌ Failed to send password reset link: {$status}");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Exception occurred: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
        
        // Test simple email sending
        $this->info("Testing simple email send...");
        
        try {
            Mail::raw('This is a test email from the password reset debugging.', function ($message) use ($email) {
                $message->to($email)
                       ->subject('Password Reset Debug Test');
            });
            
            $this->info("✅ Simple test email sent successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
        }
    }
}