<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AppointmentReminderService;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'appointments:send-reminders {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     */
    protected $description = 'Send appointment reminder emails based on system settings';

    protected $reminderService;

    public function __construct(AppointmentReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting appointment reminder process...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No emails will be sent');
            $upcomingReminders = $this->reminderService->getUpcomingReminders();
            
            $this->info("Found {$upcomingReminders->count()} appointments that would receive reminders:");
            
            foreach ($upcomingReminders as $appointment) {
                $this->line("  - {$appointment->patient->patient_name} on {$appointment->appointment_date->format('M d, Y')} at {$appointment->appointment_time->format('g:i A')}");
            }
            
            return 0;
        }

        $results = $this->reminderService->sendReminders();

        $this->info("Reminder process completed:");
        $this->line("  • Total appointments found: {$results['total']}");
        $this->line("  • Reminders sent successfully: {$results['sent']}");
        
        if ($results['errors'] > 0) {
            $this->error("  • Failed to send: {$results['errors']}");
        }

        if ($results['sent'] > 0) {
            $this->info("✅ Successfully sent {$results['sent']} appointment reminders!");
        } else {
            $this->comment("ℹ️  No reminders needed at this time.");
        }

        return 0;
    }
}