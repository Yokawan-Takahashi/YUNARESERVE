<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '運営コンソール') — YUNARI RESERVE Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-800">
<div class="flex h-full">

    {{-- ─── 運営サイドバー（濃紺 / テナント側と色分け） ─── --}}
    <aside class="w-64 shrink-0 flex flex-col bg-indigo-950 h-screen sticky top-0">

        {{-- プラットフォームロゴ --}}
        <div class="h-16 flex items-center gap-3 px-5 border-b border-white/10">
            <div class="w-7 h-7 rounded-lg bg-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-indigo-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="leading-tight">
                <p class="text-white font-semibold text-sm">YUNARI RESERVE</p>
                <p class="text-indigo-300 text-xs font-medium tracking-wider">OPERATOR CONSOLE</p>
            </div>
        </div>

        {{-- ナビ --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @php $route = request()->route()?->getName() ?? ''; @endphp

            <p class="px-3 pt-1 pb-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider">メイン</p>

            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ $route === 'superadmin.dashboard' ? 'bg-white/10 text-white' : 'text-indigo-200 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                ダッシュボード
            </a>

            <p class="px-3 pt-4 pb-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider">テナント管理</p>

            <a href="{{ route('superadmin.tenants.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ in_array($route, ['superadmin.tenants.index', 'superadmin.tenants.show']) ? 'bg-white/10 text-white' : 'text-indigo-200 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                テナント一覧
            </a>

            <a href="{{ route('superadmin.tenants.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ $route === 'superadmin.tenants.create' ? 'bg-white/10 text-white' : 'text-indigo-200 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
                テナント新規作成
            </a>

            <p class="px-3 pt-4 pb-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider">申込管理</p>

            <a href="{{ route('superadmin.inquiries.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route, 'superadmin.inquiries') ? 'bg-white/10 text-white' : 'text-indigo-200 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                お問い合わせ
            </a>

            <p class="px-3 pt-4 pb-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider">システム</p>

            <a href="{{ route('superadmin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route, 'superadmin.settings') ? 'bg-white/10 text-white' : 'text-indigo-200 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                システム設定
            </a>

        </nav>

        {{-- テナント側管理画面へのリンク --}}
        <div class="px-3 pb-3">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-indigo-300 hover:bg-white/5 hover:text-indigo-100 transition border border-indigo-800">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                テナント管理画面へ
            </a>
        </div>

        {{-- ユーザー情報 --}}
        <div class="p-3 border-t border-white/10">
            <div class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-white/5 transition">
                <div class="w-8 h-8 rounded-full bg-indigo-400 flex items-center justify-center shrink-0 text-indigo-950 text-xs font-bold">
                    {{ mb_substr(auth()->user()?->name ?? '?', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-xs font-medium truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-indigo-400 text-xs">運営管理者</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="ログアウト" class="text-indigo-400 hover:text-indigo-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ─── メインコンテンツ ─── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- トップバー --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-indigo-100 text-indigo-700 tracking-wide">
                    OPERATOR
                </span>
                <h1 class="text-base font-semibold text-slate-800">@yield('title', '運営コンソール')</h1>
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
            </div>
        </header>

        {{-- コンテンツ --}}
        <main class="flex-1 overflow-y-auto p-6">
            @if(session('success'))
            <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="mb-5 flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 mt-0.5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <ul class="space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
