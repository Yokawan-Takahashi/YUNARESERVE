<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationLookupController extends Controller
{
    public function show()
    {
        $tenant = app('tenant');
        return view('public.lookup', compact('tenant'));
    }

    public function search(Request $request)
    {
        $tenant = app('tenant');

        $validated = $request->validate([
            'code'  => 'required|string|max:20',
            'email' => 'required|email',
        ]);

        $reservation = Reservation::withoutGlobalScopes()
            ->with(['event', 'slot', 'answers'])
            ->where('code', strtoupper(trim($validated['code'])))
            ->where('email', $validated['email'])
            ->first();

        if (! $reservation) {
            return back()->withErrors(['lookup' => '予約が見つかりませんでした。予約番号とメールアドレスをご確認ください。'])->withInput();
        }

        return view('public.lookup', compact('tenant', 'reservation'));
    }
}
