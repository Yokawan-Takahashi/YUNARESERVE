<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Slot;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReservationController extends Controller
{
    public function create()
    {
        $events = Event::with(['slots' => fn($q) => $q->orderBy('date')->orderBy('start_time')])
            ->where('status', 'published')
            ->orderBy('title')
            ->get();

        $eventsJson = $events->map(fn($e) => [
            'id'    => $e->id,
            'title' => $e->title,
            'slots' => $e->slots->map(fn($s) => [
                'id'        => $s->id,
                'label'     => $s->date->format('Y/m/d') . ' ' . substr($s->start_time, 0, 5)
                    . ($s->end_time ? ' 〜 ' . substr($s->end_time, 0, 5) : ''),
                'remaining' => $s->remainingCapacity(),
                'status'    => $s->status,
            ])->values()->toArray(),
        ])->values()->toArray();

        return view('admin.reservations.create', compact('eventsJson'));
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'event_id'   => 'required|exists:events,id',
            'slot_id'    => 'required|exists:slots,id',
            'name'       => 'required|string|max:100',
            'kana'       => 'nullable|string|max:100',
            'email'      => 'required|email:rfc',
            'phone'      => ['nullable', 'string', 'max:20'],
            'companions' => 'nullable|integer|min:0|max:99',
            'memo'       => 'nullable|string|max:1000',
        ]);

        $event = Event::findOrFail($validated['event_id']);
        $slot  = Slot::findOrFail($validated['slot_id']);
        abort_if($slot->event_id !== $event->id, 422, '枠とイベントが一致しません');

        $reservation = DB::transaction(function () use ($validated, $event, $slot) {
            $lockedSlot = Slot::lockForUpdate()->find($slot->id);

            $duplicate = Reservation::where('slot_id', $slot->id)
                ->where('email', $validated['email'])
                ->where('status', 'reserved')
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'email' => 'このメールアドレスはすでに同じ枠に予約済みです。',
                ]);
            }

            $reservation = Reservation::create([
                'tenant_id'    => $event->tenant_id,
                'code'         => Reservation::generateCode(),
                'event_id'     => $event->id,
                'slot_id'      => $slot->id,
                'name'         => $validated['name'],
                'kana'         => $validated['kana'] ?? null,
                'email'        => $validated['email'],
                'phone'        => $validated['phone'] ?? null,
                'companions'   => $validated['companions'] ?? 0,
                'status'       => 'reserved',
                'memo'         => $validated['memo'] ?? null,
                'cancel_token' => Reservation::generateCancelToken(),
            ]);

            $lockedSlot->increment('reserved_count');
            if ($lockedSlot->fresh()->isFull()) {
                $lockedSlot->update(['status' => 'full']);
            }

            return $reservation;
        });

        if ($request->boolean('send_email', true)) {
            $tenant = auth()->user()->tenant;
            $mailService = new MailService();
            $mailService->sendReservationConfirm($reservation, $tenant);
        }

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', '予約を手動登録しました。');
    }

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

    public function edit(Reservation $reservation)
    {
        $reservation->load(['event', 'slot']);
        return view('admin.reservations.edit', compact('reservation'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'kana'       => 'nullable|string|max:100',
            'email'      => 'required|email:rfc',
            'phone'      => ['nullable', 'string', 'max:20'],
            'companions' => 'nullable|integer|min:0|max:99',
            'memo'       => 'nullable|string|max:1000',
        ]);

        $reservation->update($validated);

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', '予約情報を更新しました。');
    }

    public function resendEmail(Reservation $reservation)
    {
        abort_if($reservation->status !== 'reserved', 422, 'キャンセル済みの予約にはメールを送れません。');

        $tenant = auth()->user()->tenant;
        $mailService = new MailService();
        $mailService->sendReservationConfirm($reservation->load(['event', 'slot']), $tenant);

        return back()->with('success', '確認メールを再送しました。');
    }
}
