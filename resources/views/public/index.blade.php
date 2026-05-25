<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant?->company_name ?? config('app.name') }} — 予約受付</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($tenant)
    <style>:root { --brand: {{ $tenant->color }}; }</style>
    @endif
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow px-6 py-4">
        <h1 class="text-xl font-bold" style="{{ $tenant ? 'color:'.$tenant->color : '' }}">
            {{ $tenant?->company_name ?? config('app.name') }}
        </h1>
    </header>

    <main class="max-w-3xl mx-auto p-6">
        <h2 class="text-lg font-semibold mb-4">予約受付中のイベント</h2>

        {{-- カテゴリ絞り込み --}}
        @if($categories->count())
        <form method="GET" class="mb-6 flex gap-2 flex-wrap">
            <a href="{{ route('public.index') }}" class="px-3 py-1 rounded border text-sm {{ !request('category_id') ? 'bg-indigo-600 text-white' : 'bg-white hover:bg-gray-50' }}">すべて</a>
            @foreach($categories as $cat)
            <a href="{{ route('public.index', ['category_id' => $cat->id]) }}"
               class="px-3 py-1 rounded border text-sm {{ request('category_id') == $cat->id ? 'bg-indigo-600 text-white' : 'bg-white hover:bg-gray-50' }}">
                {{ $cat->icon }} {{ $cat->name }}
            </a>
            @endforeach
        </form>
        @endif

        @forelse($events as $event)
        <div class="bg-white rounded shadow p-5 mb-4">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-bold text-lg mb-1">{{ $event->title }}</h3>
                    @if($event->category)
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">{{ $event->category->name }}</span>
                    @endif
                    @if($event->location)
                        <p class="text-sm text-gray-600 mt-1">📍 {{ $event->location }}</p>
                    @endif
                    @if($event->description)
                        <p class="text-sm text-gray-700 mt-2">{{ Str::limit($event->description, 120) }}</p>
                    @endif
                </div>
                @if($event->fee > 0)
                    <span class="text-sm font-bold text-gray-700">¥{{ number_format($event->fee) }}</span>
                @else
                    <span class="text-sm text-green-600 font-bold">無料</span>
                @endif
            </div>

            {{-- 直近の枠 --}}
            @php $openSlots = $event->slots->filter(fn($s) => $s->isAccepting()); @endphp
            @if($openSlots->count())
            <div class="mt-3 flex gap-2 flex-wrap">
                @foreach($openSlots->take(3) as $slot)
                <a href="{{ route('public.events.show', $event) }}"
                   class="inline-block border border-indigo-500 text-indigo-700 text-sm px-3 py-1 rounded hover:bg-indigo-50">
                    {{ $slot->date->format('m/d') }} {{ substr($slot->start_time, 0, 5) }}
                    （残{{ $slot->remainingCapacity() }}席）
                </a>
                @endforeach
            </div>
            @else
            <p class="mt-3 text-sm text-gray-500">現在受付中の枠がありません</p>
            @endif

            <a href="{{ route('public.events.show', $event) }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">詳細・予約 →</a>
        </div>
        @empty
        <p class="text-gray-500">現在受付中のイベントはありません。</p>
        @endforelse
    </main>
</body>
</html>
