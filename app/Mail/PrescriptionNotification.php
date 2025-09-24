<?php

namespace App\Mail;

use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrescriptionNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $prescription;
    public $type; // 'new', 'updated', 'reminder'
    public $additionalData;

    /**
     * Create a new message instance.
     */
    public function __construct(Prescription $prescription, string $type, array $additionalData = [])
    {
        $this->prescription = $prescription;
        $this->type = $type;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'new' => 'New Prescription - BOKOD CMS',
            'updated' => 'Prescription Updated - BOKOD CMS',
            'reminder' => 'Medication Reminder - BOKOD CMS',
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? 'Prescription Update - BOKOD CMS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "emails.prescriptions.{$this->type}",
            with: [
                'prescription' => $this->prescription,
                'patient' => $this->prescription->patient,
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