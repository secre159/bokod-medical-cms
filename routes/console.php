<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automated emails
Schedule::command('email:medication-reminders')
    ->daily()
    ->at('08:00')
    ->description('Send daily medication reminders to patients');

Schedule::command('email:health-tips')
    ->monthly()
    ->monthlyOn(1, '09:00')
    ->description('Send monthly health tips to all patients');

Schedule::command('email:stock-alerts')
    ->dailyAt('07:00')
    ->description('Check medicine stock levels and send alerts to administrators');

// Additional scheduling options (commented out):
// Weekly medication reminders (alternative to daily):
// Schedule::command('email:medication-reminders')->weekly()->wednesdays()->at('09:00');

// Bi-weekly health tips:
// Schedule::command('email:health-tips')->cron('0 9 1,15 * *'); // 1st and 15th of each month

// Stock alerts twice daily:
// Schedule::command('email:stock-alerts')->twiceDaily(7, 19); // 7 AM and 7 PM
