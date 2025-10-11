<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $type; // 'approved', 'cancelled', 'completed', 'reminder', 'rejected', 'rescheduled', 'reschedule_request'
    public $additionalData;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, string $type, array $additionalData = [])
    {
        // Ensure appointment has required relationships loaded
        if (!$appointment->relationLoaded('patient')) {
            $appointment->load('patient');
        }
        
        $this->appointment = $appointment;
        $this->type = $type;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'approved' => 'Appointment Approved - BOKOD CMS',
            'cancelled' => 'Appointment Cancelled - BOKOD CMS',
            'completed' => 'Appointment Follow-up - BOKOD CMS',
            'reminder' => 'Appointment Reminder - BOKOD CMS',
            'rejected' => 'Appointment Rejected - BOKOD CMS',
            'rescheduled' => 'Appointment Rescheduled - BOKOD CMS',
            'reschedule_request' => 'Reschedule Request Received - BOKOD CMS',
        ];
        
        $subject = $subjects[$this->type] ?? 'Appointment Update - BOKOD CMS';
        
        // EMERGENCY FIX: Hardcode safe values to bypass config issues
        $fromAddress = 'noreply@resend.dev';
        $fromName = 'BOKOD CMS';
        
        // Try to get config values but with ultra-safe fallbacks
        try {
            $configAddress = config('mail.from.address');
            $configName = config('mail.from.name');
            
            // Only use config if they are valid
            if (is_string($configAddress) && filter_var($configAddress, FILTER_VALIDATE_EMAIL)) {
                $fromAddress = $configAddress;
            }
            
            if (is_string($configName) && !empty(trim($configName))) {
                $fromName = $configName;
            }
        } catch (\Exception $e) {
            // Log config error but continue with safe defaults
            \Log::warning('AppointmentNotification: Config error, using defaults', [
                'error' => $e->getMessage()
            ]);
        }
        
        // Final validation before creating envelope
        if (!is_string($fromAddress) || !filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            $fromAddress = 'noreply@resend.dev';
        }
        
        if (!is_string($fromName) || empty(trim($fromName))) {
            $fromName = 'BOKOD CMS';
        }
        
        // Log what we're using for debugging
        \Log::info('AppointmentNotification envelope', [
            'from_address' => $fromAddress,
            'from_name' => $fromName,
            'subject' => $subject
        ]);

        // Create envelope with validated values
        try {
            return new Envelope(
                from: new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName),
                subject: $subject,
            );
        } catch (\Exception $e) {
            // Ultimate emergency fallback - no FROM specified at all
            \Log::error('AppointmentNotification envelope creation failed', [
                'error' => $e->getMessage(),
                'attempted_address' => $fromAddress,
                'attempted_name' => $fromName
            ]);
            
            return new Envelope(
                subject: 'Appointment Update - BOKOD CMS',
            );
        }
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "emails.appointments.{$this->type}",
            with: [
                'appointment' => $this->appointment,
                'patient' => $this->appointment->patient ?? null,
                'additionalData' => $this->additionalData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}