<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Helpers\TimezoneHelper;
use Carbon\Carbon;

class TestTimezoneConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:timezone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test timezone consistency across the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Timezone Consistency...');
        $this->info('=====================================');
        
        // Test 1: Configuration Check
        $this->info('1. Configuration Check:');
        $appTimezone = config('app.timezone');
        $phpTimezone = date_default_timezone_get();
        $isConfigured = TimezoneHelper::isConfigured();
        
        $this->line("   App Timezone: {$appTimezone}");
        $this->line("   PHP Timezone: {$phpTimezone}");
        $this->line("   TimezoneHelper Configured: " . ($isConfigured ? 'Yes' : 'No'));
        
        if ($appTimezone !== 'Asia/Manila') {
            $this->error("   ❌ App timezone should be Asia/Manila");
        } else {
            $this->info("   ✅ App timezone correctly set");
        }
        
        // Test 2: Current Time Comparison
        $this->info("\n2. Current Time Comparison:");
        $utcNow = Carbon::now('UTC');
        $appNow = now();
        $philippineNow = TimezoneHelper::now();
        
        $this->line("   UTC Time: " . $utcNow->format('Y-m-d H:i:s T'));
        $this->line("   App now(): " . $appNow->format('Y-m-d H:i:s T'));
        $this->line("   TimezoneHelper::now(): " . $philippineNow->format('Y-m-d H:i:s T'));
        
        // Test 3: Appointment Model Scopes
        $this->info("\n3. Appointment Model Scope Tests:");
        
        // Create a test appointment for today (Philippine time)
        $testPatient = \App\Models\Patient::first();
        if (!$testPatient) {
            $this->error("   No patients found. Creating test patient...");
            $testUser = \App\Models\User::factory()->create(['role' => 'patient']);
            $testPatient = \App\Models\Patient::factory()->create(['user_id' => $testUser->id]);
        }
        
        $todayInPhilippines = TimezoneHelper::now()->toDateString();
        $testAppointment = Appointment::create([
            'patient_id' => $testPatient->id,
            'appointment_date' => $todayInPhilippines,
            'appointment_time' => '10:00',
            'reason' => 'Test appointment for timezone consistency',
            'status' => 'active',
            'approval_status' => 'approved',
            'reschedule_status' => 'none'
        ]);
        
        // Test today() scope
        $todayCount = Appointment::today()->count();
        $this->line("   Today's appointments count: {$todayCount}");
        
        // Test isToday() method
        $isTodayResult = $testAppointment->isToday();
        $this->line("   Test appointment isToday(): " . ($isTodayResult ? 'Yes' : 'No'));
        
        if ($isTodayResult) {
            $this->info("   ✅ Appointment isToday() method working correctly");
        } else {
            $this->error("   ❌ Appointment isToday() method not working correctly");
        }
        
        // Test upcoming() scope
        $upcomingCount = Appointment::upcoming()->count();
        $this->line("   Upcoming appointments count: {$upcomingCount}");
        
        // Test overdue() scope
        $overdueCount = Appointment::overdue()->count();
        $this->line("   Overdue appointments count: {$overdueCount}");
        
        // Test 4: Date Comparison Consistency
        $this->info("\n4. Date Comparison Tests:");
        
        $systemToday = now()->toDateString();
        $philippineToday = TimezoneHelper::now()->toDateString();
        
        $this->line("   System today (now()): {$systemToday}");
        $this->line("   Philippine today: {$philippineToday}");
        
        if ($systemToday === $philippineToday) {
            $this->info("   ✅ Date strings match");
        } else {
            $this->error("   ❌ Date strings don't match - timezone issue detected");
        }
        
        // Test 5: Week Calculation
        $this->info("\n5. Week Calculation Tests:");
        
        $systemWeekStart = now()->startOfWeek()->toDateString();
        $systemWeekEnd = now()->endOfWeek()->toDateString();
        
        $philippineWeekStart = TimezoneHelper::now()->startOfWeek()->toDateString();
        $philippineWeekEnd = TimezoneHelper::now()->endOfWeek()->toDateString();
        
        $this->line("   System week: {$systemWeekStart} to {$systemWeekEnd}");
        $this->line("   Philippine week: {$philippineWeekStart} to {$philippineWeekEnd}");
        
        $thisWeekCount = Appointment::thisWeek()->count();
        $this->line("   This week's appointments: {$thisWeekCount}");
        
        // Test 6: Time Display Consistency
        $this->info("\n6. Time Display Tests:");
        
        $timeInfo = TimezoneHelper::getTimezoneInfo();
        $this->line("   Timezone: " . $timeInfo['timezone']);
        $this->line("   Current time: " . $timeInfo['formatted_time']);
        $this->line("   Offset hours: " . $timeInfo['offset_hours']);
        
        // Clean up test appointment
        $testAppointment->delete();
        
        $this->info("\n=====================================");
        $this->info('Timezone consistency test completed!');
        $this->info('Review the results above for any issues.');
        
        return Command::SUCCESS;
    }
}