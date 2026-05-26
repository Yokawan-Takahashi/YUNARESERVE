@extends('public.layouts.app')
@section('title', '予約受付')
@section('content')
@php $brandColor = $tenant?->color ?? '#4f46e5'; @endphp

{{-- ヒーロー --}}
<div class="text-white py-12 px-5" style="background: linear-gradient(135deg, {{ $brandColor }}ee, {{ $brandColor }}bb);">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-2xl font-bold mb-2">予約受付中のイベント</h1>
        <p class="text-white/80 text-sm">{{ $tenant?->company_name ?? config('app.name') }} のイベント・クラスをご予約いただけます</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-5 py-8">

    {{-- カテゴリフィルター --}}
    @if($categories->count())
    <div class="flex gap-2 flex-wrap mb-6">
        <a href="{{ route('public.index') }}"
           class="px-3 py-1.5 rounded-full text-sm font-medium transition {{ !request('category_id') ? 'text-white brand-bg' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-300' }}">
            すべて
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('public.index', ['category_id' => $cat->id]) }}"
           class="px-3 py-1.5 rounded-full text-sm font-medium transition {{ request('category_id') == $cat->id ? 'text-white brand-bg' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-300' }}">
            {{ $cat->icon }} {{ $cat->name }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- イベント一覧 --}}
    @forelse($events as $event)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition mb-4 overflow-hidden">
        <div class="p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        @if($event->category)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                            {{ $event->category->icon }} {{ $event->category->name }}
                        </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $event->title }}</h3>
                    @if($event->location)
                    <p class="text-sm text-slate-500 mb-1">
                        <span class="mr-1">📍</span>{{ $event->location }}
                    </p>
                    @endif
                    @if($event->description)
                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ Str::limit($event->description, 100) }}</p>
                    @endif
                </div>
                <div class="shrink-0 text-right">
                    @if($event->fee > 0)
                    <div class="text-lg font-bold text-slate-800">¥{{ number_format($event->fee) }}</div>
                    <div class="text-xs text-slate-400">参加費</div>
                    @else
                    <div class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">無料</div>
                    @endif
                </div>
            </div>

            {{-- 受付枠 --}}
            @php $openSlots = $event->slots->filter(fn($s) => $s->isAccepting()); @endphp
            @if($openSlots->count())
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-medium mb-2">受付中の日程</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($openSlots->take(4) as $slot)
                    <a href="{{ route('public.book', [$event, $slot]) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium border-2 transition brand-hover"
                       style="border-color: {{ $brandColor }}; color: {{ $brandColor }};">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $slot->date->format('m/d') }} {{ substr($slot->start_time, 0, 5) }}
                        <span class="text-xs opacity-75">（残{{ $slot->remainingCapacity() }}）</span>
                    </a>
                    @endforeach
                    @if($openSlots->count() > 4)
                    <a href="{{ route('public.events.show', $event) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm text-slate-500 border border-slate-200 hover:bg-slate-50">
                        +{{ $openSlots->count() - 4 }} 件
                    </a>
                    @endif
                </div>
            </div>
            @else
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-sm text-slate-400">現在受付中の日程はありません</p>
            </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('public.events.show', $event) }}" class="text-sm font-medium brand-text hover:opacity-75">
                    詳細を見る →
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-20">
        <div class="text-5xl mb-4">📅</div>
        <p class="text-slate-500 font-medium">現在受付中のイベントはありません</p>
        <p class="text-sm text-slate-400 mt-1">しばらくお待ちください</p>
    </div>
    @endforelse
</div>
@endsection
