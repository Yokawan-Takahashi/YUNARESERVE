@extends('public.layouts.app')
@section('title', 'キャンセル完了')
@section('content')
@php $brandColor = $tenant?->color ?? '#4f46e5'; @endphp

<div class="max-w-lg mx-auto px-5 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden text-center">
        <div class="h-2 w-full bg-rose-400"></div>
        <div class="p-8">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mb-2">キャンセルが完了しました</h1>
            <p class="text-sm text-slate-500 mb-8">キャンセル確認メールをお送りしました。</p>

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
                    </span>
                </div>
                <div class="flex gap-3 text-sm">
                    <span class="text-slate-400 w-24 shrink-0">お名前</span>
                    <span class="text-slate-700">{{ $reservation->name }} 様</span>
                </div>
            </div>

            <p class="text-xs text-slate-400 mb-6">
                ご利用いただきありがとうございました。またのご予約をお待ちしております。
            </p>

            <a href="{{ route('public.index', $tenant) }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-medium text-white transition brand-hover"
               style="background-color: {{ $brandColor }};">
                イベント一覧へ戻る
            </a>
        </div>
    </div>
</div>
@endsection


