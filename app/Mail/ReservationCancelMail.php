<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationCancelMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Reservation $reservation,
        public readonly Tenant $tenant,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【キャンセル完了】' . $this->reservation->event->title . ' — ' . $this->tenant->company_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservation.cancel',
            with: [
                'reservation' => $this->reservation,
                'tenant'      => $this->tenant,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
