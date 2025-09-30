<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

class CheckTimezone extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'timezone:check';

    /**
     * The console command description.
     */
    protected $description = 'Check current timezone configuration and display time information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌏 TIMEZONE CONFIGURATION CHECK');
        $this->info('================================');
        
        // Application settings
        $this->info('📱 Application Settings:');
        $this->line('  - Config timezone: ' . config('app.timezone'));
        $this->line('  - PHP timezone: ' . date_default_timezone_get());
        $this->line('  - Timezone configured: ' . (TimezoneHelper::isConfigured() ? '✅ Yes' : '❌ No'));
        
        $this->info('');
        
        // Current times
        $this->info('⏰ Current Times:');
        $this->line('  - UTC time: ' . Carbon::now('UTC')->format('Y-m-d H:i:s T'));
        $this->line('  - Manila time: ' . Carbon::now('Asia/Manila')->format('Y-m-d H:i:s T'));
        $this->line('  - Server time: ' . Carbon::now()->format('Y-m-d H:i:s T'));
        $this->line('  - Helper time: ' . TimezoneHelper::now()->format('Y-m-d H:i:s T'));
        
        $this->info('');
        
        // Timezone info
        $info = TimezoneHelper::getTimezoneInfo();
        $this->info('📊 Timezone Details:');
        $this->line('  - Timezone: ' . $info['timezone']);
        $this->line('  - Abbreviation: ' . $info['abbreviation']);
        $this->line('  - Offset: ' . $info['offset_hours'] . ' hours from UTC');
        $this->line('  - DST active: ' . ($info['dst'] ? 'Yes' : 'No'));
        $this->line('  - Formatted: ' . $info['formatted_time']);
        
        $this->info('');
        
        // Test conversion
        $utcTime = '2025-01-01 12:00:00';
        $manilaTime = TimezoneHelper::toPhilippineTime($utcTime);
        $this->info('🔄 Conversion Test:');
        $this->line('  - UTC: ' . $utcTime);
        $this->line('  - Manila: ' . $manilaTime);
        
        // Recommendations
        $this->info('');
        if (TimezoneHelper::isConfigured()) {
            $this->info('✅ Timezone is properly configured for Philippines!');
        } else {
            $this->error('❌ Timezone needs to be configured!');
            $this->line('   Add APP_TIMEZONE=Asia/Manila to your .env file');
        }
        
        return 0;
    }
}