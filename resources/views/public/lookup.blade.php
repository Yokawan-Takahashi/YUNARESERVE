@extends('public.layouts.app')
@section('title', '予約照会')
@section('content')
@php $brandColor = $tenant?->color ?? '#4f46e5'; @endphp

<div class="max-w-lg mx-auto px-5 py-10">

    {{-- 照会フォーム --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="h-1.5 w-full" style="background-color: {{ $brandColor }};"></div>
        <div class="p-7">
            <h1 class="text-xl font-bold text-slate-900 mb-1">予約照会</h1>
            <p class="text-sm text-slate-500 mb-6">予約番号とご登録のメールアドレスで照会できます。</p>

            @error('lookup')
            <div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ $message }}
            </div>
            @enderror

            <form method="POST" action="{{ route('public.lookup.search', $tenant) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">予約番号 <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}"
                        class="field font-mono tracking-widest uppercase @error('code') field-error @enderror"
                        required placeholder="XXXXXXXX" autocomplete="off">
                    @error('code')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">メールアドレス <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="field @error('email') field-error @enderror"
                        required placeholder="予約時のメールアドレス">
                    @error('email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                    class="w-full py-3 rounded-xl text-sm font-bold text-white transition brand-hover"
                    style="background-color: {{ $brandColor }};">
                    予約を照会する
                </button>
            </form>
        </div>
    </div>

    {{-- 照会結果 --}}
    @isset($reservation)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-700">予約内容</h2>
            @if($reservation->status === 'reserved')
                <span class="badge-reserved">予約済</span>
            @else
                <span class="badge-cancelled">キャンセル済</span>
            @endif
        </div>
        <div class="p-6 space-y-4">

            <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">予約番号</span>
                    <span class="font-bold text-slate-800 tracking-widest font-mono">{{ $reservation->code }}</span>
                </div>
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">イベント</span>
                    <span class="text-slate-700 font-medium">{{ $reservation->event->title }}</span>
                </div>
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">日時</span>
                    <span class="text-slate-700">
                        {{ $reservation->slot->date->format('Y年m月d日') }}
                        {{ substr($reservation->slot->start_time, 0, 5) }}
                        @if($reservation->slot->end_time) 〜 {{ substr($reservation->slot->end_time, 0, 5) }} @endif
                    </span>
                </div>
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">お名前</span>
                    <span class="text-slate-700">{{ $reservation->name }} 様</span>
                </div>
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">同伴者</span>
                    <span class="text-slate-700">{{ $reservation->companions }} 名</span>
                </div>
                @if($reservation->answers->isNotEmpty())
                @foreach($reservation->answers as $a)
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">{{ $a->field_label }}</span>
                    <span class="text-slate-700">{{ $a->answer }}</span>
                </div>
                @endforeach
                @endif
            </div>

            @if(! $reservation->isCancelled())
            <div class="pt-2">
                <p class="text-xs text-slate-400 mb-3">キャンセルをご希望の場合は下記よりお手続きください。</p>
                <a href="{{ route('public.cancel', [$tenant, $reservation->cancel_token]) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    この予約をキャンセルする
                </a>
            </div>
            @endif
        </div>
    </div>
    @endisset

    <div class="text-center mt-6">
        <a href="{{ route('public.index', $tenant) }}" class="text-sm brand-text hover:opacity-75 font-medium">← イベント一覧へ</a>
    </div>
</div>
@endsection


