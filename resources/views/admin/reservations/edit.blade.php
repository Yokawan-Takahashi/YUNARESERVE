@extends('admin.layouts.app')
@section('title', '予約編集')
@section('header-actions')
<a href="{{ route('admin.reservations.show', $reservation) }}" class="text-xs text-slate-500 hover:text-slate-700">← 予約詳細</a>
@endsection
@section('content')

<div class="max-w-xl">
    @if($errors->any())
    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm">
        <ul class="space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- 予約番号・枠（変更不可） --}}
    <div class="card p-5 mb-5">
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <dt class="text-xs text-slate-400 mb-0.5">予約番号</dt>
                <dd class="font-mono font-bold text-slate-800 tracking-widest">{{ $reservation->code }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 mb-0.5">ステータス</dt>
                <dd>
                    @if($reservation->status === 'reserved')
                        <span class="badge-reserved text-xs px-2 py-0.5">予約済</span>
                    @else
                        <span class="badge-cancelled text-xs px-2 py-0.5">キャンセル</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 mb-0.5">イベント</dt>
                <dd class="font-medium text-slate-800">{{ $reservation->event->title }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 mb-0.5">日時</dt>
                <dd class="font-medium text-slate-800">
                    {{ $reservation->slot->date->format('Y年m月d日') }}
                    {{ substr($reservation->slot->start_time, 0, 5) }}
                    @if($reservation->slot->end_time) 〜 {{ substr($reservation->slot->end_time, 0, 5) }} @endif
                </dd>
            </div>
        </dl>
        <p class="text-xs text-slate-400 mt-3">※ イベント・日時の変更はできません。変更が必要な場合はキャンセルして再登録してください。</p>
    </div>

    <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}" class="card p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">お名前 <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $reservation->name) }}"
                class="field @error('name') field-error @enderror" required>
            @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">ふりがな</label>
            <input type="text" name="kana" value="{{ old('kana', $reservation->kana) }}"
                class="field @error('kana') field-error @enderror">
            @error('kana')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">メールアドレス <span class="text-rose-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $reservation->email) }}"
                class="field @error('email') field-error @enderror" required>
            @error('email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">電話番号</label>
            <input type="tel" name="phone" value="{{ old('phone', $reservation->phone) }}"
                class="field @error('phone') field-error @enderror">
            @error('phone')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">同伴者数（本人を除く）</label>
            <div class="flex items-center gap-2">
                <input type="number" name="companions" value="{{ old('companions', $reservation->companions) }}"
                    class="field max-w-[100px]" min="0" max="99">
                <span class="text-sm text-slate-500">名</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">スタッフメモ</label>
            <textarea name="memo" rows="4" class="field resize-none" placeholder="スタッフ向けのメモ">{{ old('memo', $reservation->memo) }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                変更を保存する
            </button>
            <a href="{{ route('admin.reservations.show', $reservation) }}"
               class="flex-1 flex items-center justify-center py-2.5 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition">
                キャンセル
            </a>
        </div>
    </form>
</div>
@endsection
