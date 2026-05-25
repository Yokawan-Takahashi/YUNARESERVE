<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationAdminNotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Reservation $reservation,
        public readonly Tenant $tenant,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【新規予約】' . $this->reservation->event->title . ' / ' . $this->reservation->name . ' 様',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservation.admin-notify',
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
