<?php

namespace App\Mail;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $patient;
    public $temporaryPassword;
    public $portalUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Patient $patient, string $temporaryPassword = null, string $portalUrl = null)
    {
        $this->patient = $patient;
        $this->temporaryPassword = $temporaryPassword;
        $this->portalUrl = $portalUrl ?? (config('app.url') . '/login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to BOKOD CMS Patient Portal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.patients.welcome',
            with: [
                'patient' => $this->patient,
                'temporaryPassword' => $this->temporaryPassword,
                'portalUrl' => $this->portalUrl,
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