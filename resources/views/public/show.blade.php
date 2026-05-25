<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} — {{ $tenant?->company_name ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow px-6 py-4">
        <a href="{{ route('public.index') }}" class="text-indigo-600 hover:underline text-sm">← 一覧へ戻る</a>
        <h1 class="text-xl font-bold mt-1">{{ $tenant?->company_name ?? config('app.name') }}</h1>
    </header>

    <main class="max-w-2xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-2">{{ $event->title }}</h2>
        @if($event->category)
            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">{{ $event->category->name }}</span>
        @endif

        <dl class="mt-4 space-y-2 text-sm">
            @if($event->location)
            <div class="flex gap-2"><dt class="text-gray-500 w-20">場所</dt><dd>{{ $event->location }}</dd></div>
            @endif
            @if($event->target)
            <div class="flex gap-2"><dt class="text-gray-500 w-20">対象</dt><dd>{{ $event->target }}</dd></div>
            @endif
            <div class="flex gap-2"><dt class="text-gray-500 w-20">参加費</dt><dd>{{ $event->fee > 0 ? '¥'.number_format($event->fee) : '無料' }}</dd></div>
        </dl>

        @if($event->description)
        <div class="mt-4 prose prose-sm max-w-none text-gray-700">
            {!! nl2br(e($event->description)) !!}
        </div>
        @endif

        <h3 class="text-lg font-bold mt-8 mb-3">予約枠を選んでください</h3>
        @forelse($event->slots as $slot)
        <div class="bg-white rounded shadow p-4 mb-3 flex items-center justify-between">
            <div>
                <p class="font-semibold">{{ $slot->date->format('Y年m月d日') }} {{ substr($slot->start_time, 0, 5) }}
                    @if($slot->end_time) 〜 {{ substr($slot->end_time, 0, 5) }} @endif
                </p>
                <p class="text-sm text-gray-600">定員: {{ $slot->capacity }}名 / 残: {{ $slot->remainingCapacity() }}名</p>
            </div>
            @if($slot->isAccepting())
                <a href="{{ route('public.book', [$event, $slot]) }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">予約する</a>
            @elseif($slot->isFull())
                <span class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded">満席</span>
            @else
                <span class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded">受付終了</span>
            @endif
        </div>
        @empty
        <p class="text-gray-500">現在受付中の枠がありません。</p>
        @endforelse
    </main>
</body>
</html>
