<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $lowStockMedicines;
    public $criticalStockMedicines;
    public $alertType; // 'low', 'critical', 'out_of_stock'

    /**
     * Create a new message instance.
     */
    public function __construct(array $lowStockMedicines = [], array $criticalStockMedicines = [], string $alertType = 'low')
    {
        $this->lowStockMedicines = $lowStockMedicines;
        $this->criticalStockMedicines = $criticalStockMedicines;
        $this->alertType = $alertType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'low' => 'Low Stock Alert - BOKOD CMS',
            'critical' => 'Critical Stock Alert - BOKOD CMS',
            'out_of_stock' => 'Out of Stock Alert - BOKOD CMS',
        ];

        return new Envelope(
            subject: $subjects[$this->alertType] ?? 'Stock Alert - BOKOD CMS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.stock.alert',
            with: [
                'lowStockMedicines' => $this->lowStockMedicines,
                'criticalStockMedicines' => $this->criticalStockMedicines,
                'alertType' => $this->alertType,
                'totalItems' => count($this->lowStockMedicines) + count($this->criticalStockMedicines),
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