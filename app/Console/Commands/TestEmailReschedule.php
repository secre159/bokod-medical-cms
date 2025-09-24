<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\EnhancedEmailService;
use Illuminate\Console\Command;

class TestEmailReschedule extends Command
{
    protected $signature = 'test:email-reschedule {appointment_id?}';
    protected $description = 'Test reschedule email functionality';

    public function handle()
    {
        $appointmentId = $this->argument('appointment_id');
        
        if (!$appointmentId) {
            // Get the first available appointment
            $appointment = Appointment::with('patient')->whereHas('patient', function($q) {
                $q->whereNotNull('email');
            })->first();
            
            if (!$appointment) {
                $this->error('No appointments found with patient email addresses.');
                return 1;
            }
            
            $this->info("Using appointment ID: {$appointment->appointment_id}");
        } else {
            $appointment = Appointment::with('patient')->find($appointmentId);
            
            if (!$appointment) {
                $this->error("Appointment with ID {$appointmentId} not found.");
                return 1;
            }
            
            if (!$appointment->patient || !$appointment->patient->email) {
                $this->error("Appointment patient has no email address.");
                return 1;
            }
        }
        
        $this->info("Testing reschedule email for:");
        $this->info("- Appointment ID: {$appointment->appointment_id}");
        $this->info("- Patient: {$appointment->patient->patient_name}");
        $this->info("- Email: {$appointment->patient->email}");
        $this->info("- Date: {$appointment->appointment_date->format('Y-m-d')}");
        $this->info("- Time: {$appointment->appointment_time->format('H:i')}");
        
        if (!$this->confirm('Send test reschedule email?')) {
            $this->info('Test cancelled.');
            return 0;
        }
        
        try {
            $emailService = app(EnhancedEmailService::class);
            
            $this->info('Sending reschedule email...');
            
            $result = $emailService->sendAppointmentNotification($appointment, 'rescheduled');
            
            if ($result['success']) {
                $this->info('✅ Email sent successfully!');
                $this->info("Recipient: {$result['recipient']}");
            } else {
                $this->error('❌ Email failed to send:');
                $this->error($result['message']);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Exception occurred:');
            $this->error($e->getMessage());
            return 1;
        }
        
        return 0;
    }
}