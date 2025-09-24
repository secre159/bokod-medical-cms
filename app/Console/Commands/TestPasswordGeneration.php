<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestPasswordGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password {count=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test secure password generation for patient accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->argument('count');
        
        $this->info("Generating {$count} secure passwords...");
        $this->newLine();
        
        for ($i = 1; $i <= $count; $i++) {
            $password = $this->generateSecurePassword();
            $this->line("Password {$i}: {$password}");
        }
        
        $this->newLine();
        $this->info('Password generation test completed!');
        $this->line('All passwords contain: lowercase, uppercase, numbers, and special characters');
    }
    
    /**
     * Generate a secure random password (same method as PatientController)
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
}
