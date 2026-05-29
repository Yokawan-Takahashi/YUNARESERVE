<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Tenant;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancelController extends Controller
{
    private function checkCancelDeadline(Reservation $reservation, Tenant $tenant): bool
    {
        if ($tenant->cancel_deadline_days === null) {
            return true;
        }
        $deadline = $reservation->slot->date->subDays($tenant->cancel_deadline_days)->endOfDay();
        return now()->lte($deadline);
    }

    public function show(Tenant $tenant, string $token)
    {
        $reservation = Reservation::withoutGlobalScopes()
            ->where('cancel_token', $token)
            ->with(['event', 'slot'])
            ->firstOrFail();

        $canCancel = $this->checkCancelDeadline($reservation, $tenant);

        return view('public.cancel', compact('reservation', 'tenant', 'canCancel'));
    }

    public function destroy(Tenant $tenant, string $token)
    {
        $reservation = Reservation::withoutGlobalScopes()
            ->where('cancel_token', $token)
            ->with(['event', 'slot'])
            ->firstOrFail();

        if ($reservation->isCancelled()) {
            return redirect()->route('public.cancel', [$tenant->slug, $token])
                ->withErrors(['cancel' => 'この予約はすでにキャンセル済みです。']);
        }

        if (!$this->checkCancelDeadline($reservation, $tenant)) {
            return redirect()->route('public.cancel', [$tenant->slug, $token])
                ->withErrors(['cancel' => 'キャンセル受付期限を過ぎています。']);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'cancelled']);

            $slot = $reservation->slot;
            $slot->decrement('reserved_count');

            if ($slot->fresh()->status === 'full') {
                $slot->update(['status' => 'open']);
            }
        });

        $mailService = new MailService();
        $mailService->sendCancelConfirm($reservation, $tenant);

        return redirect()->route('public.cancel.done', [$tenant->slug, $token]);
    }

    public function done(Tenant $tenant, string $token)
    {
        $reservation = Reservation::withoutGlobalScopes()
            ->where('cancel_token', $token)
            ->with(['event', 'slot'])
            ->firstOrFail();

        return view('public.cancel-done', compact('reservation', 'tenant'));
    }
}
