<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['event', 'slot'])
            ->latest();

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('date')) {
            $query->whereHas('slot', fn($q) => $q->whereDate('date', $request->date));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('email', 'like', "%{$kw}%")
                  ->orWhere('code', 'like', "%{$kw}%");
            });
        }

        $reservations = $query->paginate(30)->withQueryString();
        $events = Event::orderBy('title')->get(['id', 'title']);

        return view('admin.reservations.index', compact('reservations', 'events'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['event', 'slot', 'answers']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:reserved,cancelled',
        ]);

        $newStatus = $request->status;

        if ($reservation->status === $newStatus) {
            return back()->with('success', 'ステータスは変更ありません。');
        }

        DB::transaction(function () use ($reservation, $newStatus) {
            $oldStatus = $reservation->status;
            $reservation->update(['status' => $newStatus]);

            $slot = $reservation->slot;

            if ($oldStatus === 'reserved' && $newStatus === 'cancelled') {
                // 管理者キャンセル: reserved_count を戻し、満席なら open に
                $slot->decrement('reserved_count');
                if ($slot->fresh()->status === 'full') {
                    $slot->update(['status' => 'open']);
                }
            } elseif ($oldStatus === 'cancelled' && $newStatus === 'reserved') {
                // 管理者復活: reserved_count を増やし、定員超なら full に
                $slot->increment('reserved_count');
                if ($slot->fresh()->isFull()) {
                    $slot->update(['status' => 'full']);
                }
            }
        });

        return back()->with('success', 'ステータスを更新しました。');
    }

    public function updateMemo(Request $request, Reservation $reservation)
    {
        $request->validate([
            'memo' => 'nullable|string|max:1000',
        ]);

        $reservation->update(['memo' => $request->memo]);

        return back()->with('success', 'メモを更新しました。');
    }
}
