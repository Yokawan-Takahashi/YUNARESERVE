<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面 — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
            <span class="font-bold text-gray-800">{{ config('app.name') }} 管理画面</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">ログアウト</button>
            </form>
        </nav>
        <main class="p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">ダッシュボード</h1>
            <p class="text-gray-600">ログイン中: {{ auth()->user()->name }}（{{ auth()->user()->role }}）</p>
        </main>
    </div>
</body>
</html>
