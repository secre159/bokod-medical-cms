<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class SafeAppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $type;
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
        
        // Prefer configured sender, fallback to safe defaults
        $configuredAddress = config('mail.from.address');
        $configuredName = config('mail.from.name');
        
        $fromAddress = filter_var($configuredAddress, FILTER_VALIDATE_EMAIL) ? $configuredAddress : 'noreply@resend.dev';
        $fromName = (is_string($configuredName) && trim($configuredName) !== '') ? $configuredName : 'BOKOD CMS';
        
        // Log for debugging
        \Log::info('SafeAppointmentNotification creating envelope', [
            'type' => $this->type,
            'subject' => $subject,
            'from_address' => $fromAddress,
            'from_name' => $fromName
        ]);
        
        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: $subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Use simple template that always exists
        return new Content(
            view: 'emails.appointments.safe-notification',
            with: [
                'appointment' => $this->appointment,
                'patient' => $this->appointment->patient,
                'type' => $this->type,
                'additionalData' => $this->additionalData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}