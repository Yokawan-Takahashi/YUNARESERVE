<?php

namespace App\Services;

use App\Mail\ReservationAdminNotifyMail;
use App\Mail\ReservationConfirmMail;
use App\Models\MailLog;
use App\Models\Reservation;
use App\Models\Tenant;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendReservationConfirm(Reservation $reservation, Tenant $tenant): void
    {
        $reservation->load(['event', 'slot']);

        Mail::to($reservation->email)
            ->send(new ReservationConfirmMail($reservation, $tenant));

        MailLog::create([
            'tenant_id'      => $tenant->id,
            'reservation_id' => $reservation->id,
            'type'           => 'reservation_confirm',
            'to'             => $reservation->email,
            'sent_at'        => now(),
        ]);
    }

    public function sendAdminNotify(Reservation $reservation, Tenant $tenant): void
    {
        $notifyEmail = $tenant->notify_email;

        if (empty($notifyEmail)) {
            return;
        }

        $reservation->load(['event', 'slot']);

        Mail::to($notifyEmail)
            ->send(new ReservationAdminNotifyMail($reservation, $tenant));

        MailLog::create([
            'tenant_id'      => $tenant->id,
            'reservation_id' => $reservation->id,
            'type'           => 'admin_notify',
            'to'             => $notifyEmail,
            'sent_at'        => now(),
        ]);
    }
}
