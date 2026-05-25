<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function show(Event $event, Slot $slot)
    {
        abort_if(!$event->isPublished(), 404);
        abort_if($slot->event_id !== $event->id, 404);

        $fields = $event->formFields()->get();
        $tenant = app('tenant');

        return view('public.book', compact('event', 'slot', 'fields', 'tenant'));
    }

    public function store(Request $request, Event $event, Slot $slot)
    {
        abort_if(!$event->isPublished(), 404);
        abort_if($slot->event_id !== $event->id, 404);

        $baseRules = [
            'name'       => 'required|string|max:100',
            'kana'       => 'nullable|string|max:100|regex:/^[ぁ-ん　 ー]+$/u',
            'email'      => 'required|email:rfc',
            'email_confirm' => 'required|same:email',
            'phone'      => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-\+\(\) ]+$/'],
            'companions' => 'nullable|integer|min:0|max:99',
        ];

        // カスタムフォーム項目のバリデーションルールを動的に追加
        $fields = $event->formFields()->get();
        foreach ($fields as $field) {
            if ($field->hidden) continue;
            $key = 'custom_' . $field->id;
            $rules = [];
            if ($field->required) $rules[] = 'required';
            else $rules[] = 'nullable';
            if ($field->type === 'number') $rules[] = 'numeric';
            $baseRules[$key] = $rules;
        }

        $validated = $request->validate($baseRules);

        // トランザクション + 行ロックで二重予約防止
        $reservation = DB::transaction(function () use ($validated, $event, $slot, $fields, $request) {
            // 対象枠を排他ロックで取得
            $lockedSlot = Slot::lockForUpdate()->find($slot->id);

            // 満席チェック
            if (!$lockedSlot->isAccepting()) {
                throw ValidationException::withMessages([
                    'slot' => 'この枠は満席または受付終了です。',
                ]);
            }

            // 同枠・同メール二重予約チェック
            $duplicate = Reservation::withoutGlobalScopes()
                ->where('slot_id', $slot->id)
                ->where('email', $validated['email'])
                ->where('status', 'reserved')
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'email' => 'このメールアドレスはすでに予約済みです。',
                ]);
            }

            // 予約作成
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
                'cancel_token' => Reservation::generateCancelToken(),
            ]);

            // カスタム項目の回答を保存
            foreach ($fields as $field) {
                if ($field->hidden) continue;
                $answer = $validated['custom_' . $field->id] ?? null;
                if ($answer !== null) {
                    $reservation->answers()->create([
                        'field_label' => $field->label,
                        'answer'      => $answer,
                    ]);
                }
            }

            // 定員減算 + 満席判定
            $lockedSlot->increment('reserved_count');
            if ($lockedSlot->fresh()->isFull()) {
                $lockedSlot->update(['status' => 'full']);
            }

            return $reservation;
        });

        return redirect()->route('public.done', $reservation->code);
    }

    public function done(string $code)
    {
        $reservation = Reservation::withoutGlobalScopes()
            ->where('code', $code)
            ->with(['event', 'slot'])
            ->firstOrFail();

        $tenant = app('tenant');
        return view('public.done', compact('reservation', 'tenant'));
    }
}
