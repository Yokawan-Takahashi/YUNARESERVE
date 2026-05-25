<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理画面') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">
    <nav class="bg-indigo-700 text-white px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.dashboard') }}" class="font-bold text-lg">{{ config('app.name') }}</a>
            <a href="{{ route('admin.events.index') }}" class="text-sm hover:underline">イベント</a>
            <a href="{{ route('admin.categories.index') }}" class="text-sm hover:underline">カテゴリ</a>
            <a href="{{ route('admin.fields.index') }}" class="text-sm hover:underline">カスタム項目</a>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm hover:underline">ログアウト（{{ auth()->user()->name }}）</button>
        </form>
    </nav>

    <main class="max-w-5xl mx-auto p-6">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
