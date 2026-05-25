<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約キャンセル — {{ $tenant?->company_name ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow px-6 py-4">
        <h1 class="text-xl font-bold">{{ $tenant?->company_name ?? config('app.name') }}</h1>
    </header>

    <main class="max-w-lg mx-auto p-6">
        <div class="bg-white rounded shadow p-8">
            <h2 class="text-2xl font-bold mb-4">予約キャンセル</h2>

            @if($errors->has('cancel'))
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                    {{ $errors->first('cancel') }}
                </div>
            @endif

            @if($reservation->isCancelled())
                <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-4 text-sm">
                    この予約はすでにキャンセル済みです。
                </div>
                <a href="{{ route('public.index') }}" class="text-indigo-600 hover:underline text-sm">← トップに戻る</a>
            @else
                <p class="text-gray-600 mb-6">以下の予約をキャンセルしてよろしいですか？</p>

                <div class="bg-gray-50 rounded p-4 mb-6 space-y-2 text-sm">
                    <p><span class="text-gray-500">予約番号</span>
                       <span class="font-bold ml-2 tracking-widest">{{ $reservation->code }}</span></p>
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
                </div>

                <form method="POST" action="{{ route('public.cancel.destroy', $reservation->cancel_token) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full bg-red-600 text-white py-2 rounded font-semibold hover:bg-red-700 transition"
                        onclick="return confirm('本当にキャンセルしますか？')">
                        キャンセルを確定する
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('public.index') }}" class="text-gray-500 hover:underline text-sm">戻る</a>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
