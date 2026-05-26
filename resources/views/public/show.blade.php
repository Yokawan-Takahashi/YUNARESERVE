@extends('public.layouts.app')
@section('title', $event->title)
@section('content')
@php $brandColor = $tenant?->color ?? '#4f46e5'; @endphp

<div class="max-w-3xl mx-auto px-5 py-8">
    <a href="{{ route('public.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700 mb-6">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        イベント一覧へ
    </a>

    {{-- イベントヘッダー --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="h-2 w-full" style="background-color: {{ $brandColor }};"></div>
        <div class="p-6">
            @if($event->category)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 mb-3">
                {{ $event->category->icon }} {{ $event->category->name }}
            </span>
            @endif
            <h1 class="text-2xl font-bold text-slate-900 mb-4">{{ $event->title }}</h1>

            <dl class="grid grid-cols-2 gap-3 text-sm">
                @if($event->location)
                <div class="flex items-start gap-2">
                    <span class="text-slate-400 mt-0.5">📍</span>
                    <div><dt class="text-xs text-slate-400 mb-0.5">場所</dt><dd class="text-slate-700">{{ $event->location }}</dd></div>
                </div>
                @endif
                @if($event->target)
                <div class="flex items-start gap-2">
                    <span class="text-slate-400 mt-0.5">👥</span>
                    <div><dt class="text-xs text-slate-400 mb-0.5">対象</dt><dd class="text-slate-700">{{ $event->target }}</dd></div>
                </div>
                @endif
                <div class="flex items-start gap-2">
                    <span class="text-slate-400 mt-0.5">💴</span>
                    <div><dt class="text-xs text-slate-400 mb-0.5">参加費</dt>
                    <dd class="{{ $event->fee > 0 ? 'text-slate-700' : 'text-emerald-600 font-medium' }}">
                        {{ $event->fee > 0 ? '¥'.number_format($event->fee) : '無料' }}
                    </dd></div>
                </div>
            </dl>

            @if($event->description)
            <div class="mt-5 pt-5 border-t border-slate-100 text-sm text-slate-700 leading-relaxed">
                {!! nl2br(e($event->description)) !!}
            </div>
            @endif

            @if($event->items)
            <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-100 text-sm">
                <p class="font-medium text-amber-800 mb-1">🎒 持ち物・準備</p>
                <p class="text-amber-700">{{ $event->items }}</p>
            </div>
            @endif

            @if($event->notes)
            <div class="mt-3 p-4 bg-slate-50 rounded-xl text-sm text-slate-600">
                <p class="font-medium text-slate-700 mb-1">📝 備考・注意事項</p>
                <p>{{ $event->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- 枠一覧 --}}
    <h2 class="text-lg font-bold text-slate-800 mb-4">日程を選んで予約する</h2>

    @forelse($event->slots as $slot)
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-3 flex items-center justify-between hover:shadow-md transition">
        <div>
            <p class="font-semibold text-slate-800">
                {{ $slot->date->format('Y年m月d日（') }}{{ ['日','月','火','水','木','金','土'][$slot->date->dayOfWeek] }}）
                {{ substr($slot->start_time, 0, 5) }}
                @if($slot->end_time) 〜 {{ substr($slot->end_time, 0, 5) }} @endif
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
                定員 {{ $slot->capacity }}名 ／
                @if($slot->remainingCapacity() > 0)
                    <span class="text-slate-500">残り {{ $slot->remainingCapacity() }} 席</span>
                @else
                    <span class="text-rose-500">満席</span>
                @endif
            </p>
        </div>
        @if($slot->isAccepting())
        <a href="{{ route('public.book', [$event, $slot]) }}"
           class="px-5 py-2 rounded-xl text-sm font-semibold text-white transition brand-hover shrink-0"
           style="background-color: {{ $brandColor }};">
            予約する
        </a>
        @elseif($slot->isFull())
        <span class="px-5 py-2 rounded-xl text-sm font-medium bg-slate-100 text-slate-400 shrink-0">満席</span>
        @else
        <span class="px-5 py-2 rounded-xl text-sm font-medium bg-slate-100 text-slate-400 shrink-0">受付終了</span>
        @endif
    </div>
    @empty
    <div class="text-center py-12 text-slate-400">
        <p>現在受付中の日程がありません</p>
    </div>
    @endforelse
</div>
@endsection
