@extends('public.layouts.app')
@section('title', '予約キャンセル')
@section('content')
@php $brandColor = $tenant?->color ?? '#4f46e5'; @endphp

<div class="max-w-lg mx-auto px-5 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="h-2 w-full bg-rose-500"></div>
        <div class="p-8">
            <h1 class="text-xl font-bold text-slate-900 mb-2">予約のキャンセル</h1>

            @if($errors->has('cancel'))
            <div class="mb-4 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ $errors->first('cancel') }}
            </div>
            @endif

            @if($reservation->isCancelled())
            <div class="py-6 text-center">
                <p class="text-slate-500 mb-4">この予約はすでにキャンセル済みです。</p>
                <a href="{{ route('public.index') }}" class="text-sm font-medium brand-text">← イベント一覧へ</a>
            </div>
            @elseif(!$canCancel)
            <div class="py-6 text-center">
                <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="font-semibold text-slate-800 mb-2">キャンセル受付期限を過ぎています</p>
                <p class="text-sm text-slate-500 mb-6">
                    キャンセルの受付はイベント当日の {{ $tenant->cancel_deadline_days }} 日前までです。<br>
                    お急ぎの場合はお問い合わせください。
                </p>
                <a href="{{ route('public.index') }}" class="text-sm font-medium brand-text">← イベント一覧へ</a>
            </div>
            @else
            <p class="text-sm text-slate-500 mb-6">以下の予約をキャンセルしてよろしいですか？キャンセル後は元に戻せません。</p>

            <div class="bg-slate-50 rounded-xl p-5 space-y-3 mb-8">
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">予約番号</span>
                    <span class="font-bold text-slate-800 tracking-widest font-mono">{{ $reservation->code }}</span>
                </div>
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">イベント</span>
                    <span class="text-slate-700">{{ $reservation->event->title }}</span>
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
            </div>

            <div class="flex gap-3">
                <form method="POST" action="{{ route('public.cancel.destroy', $reservation->cancel_token) }}" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('本当にキャンセルしますか？')"
                        class="w-full py-3 rounded-xl text-sm font-semibold text-white bg-rose-500 hover:bg-rose-600 transition">
                        キャンセルを確定する
                    </button>
                </form>
                <a href="{{ route('public.index') }}"
                   class="flex-1 flex items-center justify-center py-3 rounded-xl text-sm font-medium bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                    戻る
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
