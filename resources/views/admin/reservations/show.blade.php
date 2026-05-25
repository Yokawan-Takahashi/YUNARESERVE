@extends('admin.layouts.app')
@section('title', '予約詳細 ' . $reservation->code)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.reservations.index') }}" class="text-sm text-indigo-600 hover:underline">← 予約一覧</a>
</div>

<div class="flex items-start gap-6">
    {{-- 予約情報 --}}
    <div class="flex-1 space-y-4">
        <div class="bg-white rounded shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-lg font-bold">予約詳細</h1>
                <span class="px-3 py-1 rounded text-sm font-medium {{ $reservation->status === 'reserved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                    {{ $reservation->status === 'reserved' ? '予約済' : 'キャンセル' }}
                </span>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex gap-4">
                    <dt class="w-28 text-gray-500 shrink-0">予約番号</dt>
                    <dd class="font-mono font-bold">{{ $reservation->code }}</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-28 text-gray-500 shrink-0">イベント</dt>
                    <dd>{{ $reservation->event->title }}</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-28 text-gray-500 shrink-0">日時</dt>
                    <dd>
                        {{ $reservation->slot->date->format('Y年m月d日') }}
                        {{ substr($reservation->slot->start_time, 0, 5) }}
                        @if($reservation->slot->end_time) 〜 {{ substr($reservation->slot->end_time, 0, 5) }} @endif
                    </dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-28 text-gray-500 shrink-0">お名前</dt>
                    <dd>{{ $reservation->name }} 様{{ $reservation->kana ? '（' . $reservation->kana . '）' : '' }}</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-28 text-gray-500 shrink-0">メール</dt>
                    <dd>{{ $reservation->email }}</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-28 text-gray-500 shrink-0">電話</dt>
                    <dd>{{ $reservation->phone ?: '―' }}</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-28 text-gray-500 shrink-0">同伴者</dt>
                    <dd>{{ $reservation->companions }} 名</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-28 text-gray-500 shrink-0">受付日時</dt>
                    <dd>{{ $reservation->created_at->format('Y/m/d H:i') }}</dd>
                </div>
                @if($reservation->answers->isNotEmpty())
                <div class="pt-2 border-t">
                    <p class="text-gray-500 text-xs mb-2">カスタム項目</p>
                    @foreach($reservation->answers as $answer)
                    <div class="flex gap-4">
                        <dt class="w-28 text-gray-500 shrink-0">{{ $answer->field_label }}</dt>
                        <dd>{{ $answer->answer }}</dd>
                    </div>
                    @endforeach
                </div>
                @endif
            </dl>
        </div>

        {{-- ステータス変更 --}}
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-sm font-semibold mb-3">ステータス変更</h2>
            <form method="POST" action="{{ route('admin.reservations.status', $reservation) }}">
                @csrf @method('PATCH')
                <div class="flex gap-3 items-center">
                    <select name="status" class="border rounded px-3 py-1.5 text-sm">
                        <option value="reserved" {{ $reservation->status === 'reserved' ? 'selected' : '' }}>予約済</option>
                        <option value="cancelled" {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                    </select>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded text-sm hover:bg-indigo-700"
                        onclick="return confirm('ステータスを変更しますか？')">
                        変更
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- メモ --}}
    <div class="w-72">
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-sm font-semibold mb-3">スタッフメモ</h2>
            <form method="POST" action="{{ route('admin.reservations.memo', $reservation) }}">
                @csrf @method('PATCH')
                <textarea name="memo" rows="6"
                    class="w-full border rounded px-3 py-2 text-sm resize-none"
                    placeholder="スタッフ向けのメモ（公開されません）">{{ old('memo', $reservation->memo) }}</textarea>
                <button type="submit" class="mt-2 w-full bg-gray-600 text-white py-1.5 rounded text-sm hover:bg-gray-700">
                    保存
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
