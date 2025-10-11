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
        
        // NUCLEAR OPTION: Completely hardcode values to guarantee no null headers
        $fromAddress = 'noreply@resend.dev';
        $fromName = 'BOKOD CMS';
        
        // Log what we're using for debugging
        \Log::info('AppointmentNotification envelope (hardcoded)', [
            'from_address' => $fromAddress,
            'from_name' => $fromName,
            'subject' => $subject
        ]);

        // Create envelope with hardcoded validated values
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName),
            subject: $subject,
        );
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