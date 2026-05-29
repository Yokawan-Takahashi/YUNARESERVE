@extends('public.layouts.app')
@section('title', '予約完了')
@section('content')
@php $brandColor = $tenant?->color ?? '#4f46e5'; @endphp

<div class="max-w-lg mx-auto px-5 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden text-center">
        <div class="h-2 w-full" style="background-color: {{ $brandColor }};"></div>
        <div class="p-8">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5"
                 style="background-color: {{ $brandColor }}22;">
                <svg class="w-8 h-8" style="color: {{ $brandColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mb-2">予約が完了しました</h1>
            <p class="text-sm text-slate-500 mb-8">確認メールをお送りしました。ご確認ください。</p>

            <div class="bg-slate-50 rounded-xl p-5 text-left space-y-3 mb-8">
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
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">メール</span>
                    <span class="text-slate-700">{{ $reservation->email }}</span>
                </div>
            </div>

            <p class="text-xs text-slate-400 mb-6">
                キャンセルはメールに記載のリンクからお手続きください。
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('public.index', $tenant) }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-medium text-white transition brand-hover"
                   style="background-color: {{ $brandColor }};">
                    イベント一覧へ戻る
                </a>
                <a href="{{ route('public.lookup', $tenant) }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-medium bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                    予約を確認・照会する
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


