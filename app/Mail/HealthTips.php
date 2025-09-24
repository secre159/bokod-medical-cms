<?php

namespace App\Mail;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HealthTips extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $patient;
    public $healthTips;
    public $season;
    public $additionalData;

    /**
     * Create a new message instance.
     */
    public function __construct(Patient $patient = null, array $healthTips = [], string $season = null, array $additionalData = [])
    {
        $this->patient = $patient;
        $this->healthTips = $healthTips;
        $this->season = $season ?? $this->getCurrentSeason();
        $this->additionalData = $additionalData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $seasonalTitle = ucfirst($this->season);
        return new Envelope(
            subject: "Health Tips for {$seasonalTitle} - BOKOD CMS",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.health.tips',
            with: [
                'patient' => $this->patient,
                'healthTips' => $this->healthTips,
                'season' => $this->season,
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

    /**
     * Get current season based on month
     */
    private function getCurrentSeason(): string
    {
        $month = date('n'); // 1-12

        // Philippines has wet (rainy) and dry seasons
        if ($month >= 6 && $month <= 11) {
            return 'rainy'; // June to November
        } else {
            return 'dry'; // December to May
        }
    }
}