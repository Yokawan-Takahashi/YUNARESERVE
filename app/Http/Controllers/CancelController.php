<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancelController extends Controller
{
    public function show(string $token)
    {
        $reservation = Reservation::withoutGlobalScopes()
            ->where('cancel_token', $token)
            ->with(['event', 'slot'])
            ->firstOrFail();

        $tenant = app('tenant') ?? $reservation->tenant;

        return view('public.cancel', compact('reservation', 'tenant'));
    }

    public function destroy(string $token)
    {
        $reservation = Reservation::withoutGlobalScopes()
            ->where('cancel_token', $token)
            ->with(['event', 'slot'])
            ->firstOrFail();

        if ($reservation->isCancelled()) {
            return redirect()->route('public.cancel', $token)
                ->withErrors(['cancel' => 'この予約はすでにキャンセル済みです。']);
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'cancelled']);

            $slot = $reservation->slot;
            $slot->decrement('reserved_count');

            // 満席だったスロットを受付可能に戻す
            if ($slot->fresh()->status === 'full') {
                $slot->update(['status' => 'open']);
            }
        });

        $tenant = app('tenant') ?? $reservation->tenant;
        $mailService = new MailService();
        $mailService->sendCancelConfirm($reservation, $tenant);

        return redirect()->route('public.cancel.done', $token);
    }

    public function done(string $token)
    {
        $reservation = Reservation::withoutGlobalScopes()
            ->where('cancel_token', $token)
            ->with(['event', 'slot'])
            ->firstOrFail();

        $tenant = app('tenant') ?? $reservation->tenant;

        return view('public.cancel-done', compact('reservation', 'tenant'));
    }
}
