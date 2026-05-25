<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function export(Request $request): StreamedResponse
    {
        $query = Reservation::with(['event', 'slot'])->latest();

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

        $filename = 'reservations_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM（Excelで文字化け防止）
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                '予約番号', 'イベント', '日付', '開始時間', '終了時間',
                'お名前', 'ふりがな', 'メールアドレス', '電話番号',
                '同伴者数', 'ステータス', 'メモ', '受付日時',
            ]);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->code,
                        $r->event->title,
                        $r->slot->date->format('Y/m/d'),
                        substr($r->slot->start_time, 0, 5),
                        $r->slot->end_time ? substr($r->slot->end_time, 0, 5) : '',
                        $r->name,
                        $r->kana ?? '',
                        $r->email,
                        $r->phone ?? '',
                        $r->companions,
                        $r->status === 'reserved' ? '予約済' : 'キャンセル',
                        $r->memo ?? '',
                        $r->created_at->format('Y/m/d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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
