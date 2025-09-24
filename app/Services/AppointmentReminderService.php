<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Setting;
use App\Services\EnhancedEmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AppointmentReminderService
{
    protected $emailService;

    public function __construct(EnhancedEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send appointment reminders based on settings
     */
    public function sendReminders()
    {
        $sendReminders = Setting::get('send_reminders', true);
        
        if (!$sendReminders) {
            Log::info('Appointment reminders are disabled in settings');
            return;
        }

        $reminderHours = Setting::get('reminder_hours', 24);
        
        // Calculate the reminder time window
        $reminderTime = Carbon::now()->addHours($reminderHours);
        $reminderTimeStart = $reminderTime->copy()->subMinutes(30);
        $reminderTimeEnd = $reminderTime->copy()->addMinutes(30);
        
        // Find appointments that need reminders
        $appointments = Appointment::with('patient.user')
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->where(function($query) use ($reminderTimeStart, $reminderTimeEnd) {
                $query->whereBetween('appointment_date', [
                    $reminderTimeStart->format('Y-m-d'),
                    $reminderTimeEnd->format('Y-m-d')
                ])
                ->where(function($timeQuery) use ($reminderTimeStart, $reminderTimeEnd) {
                    $timeQuery->whereBetween('appointment_time', [
                        $reminderTimeStart->format('H:i:s'),
                        $reminderTimeEnd->format('H:i:s')
                    ]);
                });
            })
            ->whereDoesntHave('reminders', function($query) {
                $query->where('type', 'reminder')
                      ->where('sent_at', '>=', Carbon::now()->subDays(1));
            })
            ->get();

        $sentCount = 0;
        $errorCount = 0;

        foreach ($appointments as $appointment) {
            try {
                // Send reminder email
                $this->emailService->sendAppointmentReminder($appointment);
                
                // Log the reminder (you might want to create a reminders table)
                Log::info('Appointment reminder sent', [
                    'appointment_id' => $appointment->appointment_id,
                    'patient_id' => $appointment->patient_id,
                    'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                    'appointment_time' => $appointment->appointment_time->format('H:i')
                ]);
                
                $sentCount++;
                
            } catch (\Exception $e) {
                Log::error('Failed to send appointment reminder', [
                    'appointment_id' => $appointment->appointment_id,
                    'error' => $e->getMessage()
                ]);
                
                $errorCount++;
            }
        }

        Log::info('Appointment reminder batch completed', [
            'total_found' => count($appointments),
            'sent_successfully' => $sentCount,
            'errors' => $errorCount
        ]);

        return [
            'total' => count($appointments),
            'sent' => $sentCount,
            'errors' => $errorCount
        ];
    }

    /**
     * Get appointments that are due for reminders
     */
    public function getUpcomingReminders()
    {
        $reminderHours = Setting::get('reminder_hours', 24);
        $reminderTime = Carbon::now()->addHours($reminderHours);

        return Appointment::with('patient.user')
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->whereDate('appointment_date', $reminderTime->format('Y-m-d'))
            ->whereTime('appointment_time', '>=', $reminderTime->format('H:i:s'))
            ->get();
    }

    /**
     * Check if an appointment needs a reminder
     */
    public function needsReminder(Appointment $appointment): bool
    {
        $sendReminders = Setting::get('send_reminders', true);
        
        if (!$sendReminders) {
            return false;
        }

        if ($appointment->status !== 'active' || $appointment->approval_status !== 'approved') {
            return false;
        }

        $reminderHours = Setting::get('reminder_hours', 24);
        $appointmentDateTime = Carbon::parse($appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->appointment_time->format('H:i:s'));
        $hoursUntilAppointment = Carbon::now()->diffInHours($appointmentDateTime, false);

        // Send reminder if appointment is within the reminder window (±30 minutes)
        return ($hoursUntilAppointment <= $reminderHours + 0.5) && ($hoursUntilAppointment >= $reminderHours - 0.5);
    }
}