<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class UpdateUserLastLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-login-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update users with realistic last login times for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating user login times...');
        
        $users = User::all();
        
        foreach ($users as $index => $user) {
            // Create varied login times - some recent, some older, some never
            $random = rand(1, 10);
            
            if ($random <= 3) {
                // 30% never logged in
                $user->last_login_at = null;
                $status = 'never';
            } elseif ($random <= 6) {
                // 30% logged in within last week
                $user->last_login_at = Carbon::now()->subDays(rand(1, 7))->subHours(rand(1, 23))->subMinutes(rand(1, 59));
                $status = 'recent';
            } elseif ($random <= 8) {
                // 20% logged in within last month
                $user->last_login_at = Carbon::now()->subDays(rand(8, 30))->subHours(rand(1, 23));
                $status = 'this month';
            } else {
                // 20% logged in more than a month ago
                $user->last_login_at = Carbon::now()->subDays(rand(31, 90))->subHours(rand(1, 23));
                $status = 'old';
            }
            
            $user->save();
            
            $this->line("User {$user->name}: {$status}");
        }
        
        $this->info('\nLogin times updated successfully!');
        $this->info('Now login to test the real-time tracking feature.');
        
        return 0;
    }
}
