<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約完了 — {{ $tenant?->company_name ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow px-6 py-4">
        <h1 class="text-xl font-bold">{{ $tenant?->company_name ?? config('app.name') }}</h1>
    </header>

    <main class="max-w-lg mx-auto p-6">
        <div class="bg-white rounded shadow p-8 text-center">
            <div class="text-green-500 text-5xl mb-4">✓</div>
            <h2 class="text-2xl font-bold mb-2">予約が完了しました</h2>
            <p class="text-gray-600 mb-6">確認メールをお送りしました。</p>

            <div class="bg-gray-50 rounded p-4 mb-6 text-left space-y-2 text-sm">
                <p><span class="text-gray-500">予約番号</span>
                   <span class="font-bold text-lg ml-2 tracking-widest">{{ $reservation->code }}</span></p>
                <p><span class="text-gray-500">イベント</span>
                   <span class="ml-2">{{ $reservation->event->title }}</span></p>
                <p><span class="text-gray-500">日時</span>
                   <span class="ml-2">
                       {{ $reservation->slot->date->format('Y年m月d日') }}
                       {{ substr($reservation->slot->start_time, 0, 5) }}
                       @if($reservation->slot->end_time) 〜 {{ substr($reservation->slot->end_time, 0, 5) }} @endif
                   </span></p>
                <p><span class="text-gray-500">お名前</span>
                   <span class="ml-2">{{ $reservation->name }} 様</span></p>
                <p><span class="text-gray-500">メール</span>
                   <span class="ml-2">{{ $reservation->email }}</span></p>
            </div>

            <p class="text-xs text-gray-500 mb-6">
                キャンセルはメールに記載のURLからお手続きください。
            </p>

            <a href="{{ route('public.index') }}" class="text-indigo-600 hover:underline text-sm">← トップに戻る</a>
        </div>
    </main>
</body>
</html>
