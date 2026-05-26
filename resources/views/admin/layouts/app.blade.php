<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ダッシュボード') — {{ auth()->user()?->tenant?->company_name ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-800">
<div class="flex h-full">

    {{-- ─── サイドバー ─── --}}
    <aside class="w-60 shrink-0 flex flex-col bg-slate-900 h-screen sticky top-0">

        {{-- ロゴ --}}
        <div class="h-16 flex items-center gap-2.5 px-5 border-b border-white/10">
            <div class="w-7 h-7 rounded-lg bg-indigo-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-white font-semibold text-sm leading-tight">YUNARI<br><span class="text-indigo-300 font-normal text-xs tracking-wider">RESERVE</span></span>
        </div>

        {{-- ナビ --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @php
                $route = request()->route()?->getName() ?? '';
                $role  = auth()->user()?->role ?? 'viewer';
                $level = ['viewer'=>1,'staff'=>2,'admin'=>3,'owner'=>4,'superadmin'=>99][$role] ?? 0;
            @endphp

            <p class="px-3 pt-1 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">メイン</p>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route,'admin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                ダッシュボード
            </a>

            <a href="{{ route('admin.reservations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route,'admin.reservations') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                予約管理
            </a>

            <p class="px-3 pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">コンテンツ</p>

            <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route,'admin.events') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                イベント
            </a>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route,'admin.categories') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                カテゴリ
            </a>

            <a href="{{ route('admin.fields.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route,'admin.fields') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                カスタム項目
            </a>

            @if($level >= 3)
            <p class="px-3 pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">管理</p>

            <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route,'admin.staff') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                スタッフ管理
            </a>

            <p class="px-3 pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">設定</p>

            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route,'admin.settings') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                テナント設定
            </a>
            @endif

            @if($level >= 4)
            <a href="{{ route('admin.billing.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ str_starts_with($route,'admin.billing') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                プラン・課金
            </a>
            @endif

            @if($level >= 99)
            <div class="px-3 pt-4 pb-1">
                <a href="{{ route('superadmin.tenants.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-slate-400 hover:bg-white/5 hover:text-slate-200 transition border border-white/10">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    運営コンソールへ
                </a>
            </div>
            @endif
        </nav>

        {{-- ユーザー情報 --}}
        <div class="p-3 border-t border-white/10">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-white/5 transition group">
                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center shrink-0 text-white text-xs font-bold">
                    {{ mb_substr(auth()->user()?->name ?? '?', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-xs font-medium truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-slate-500 text-xs group-hover:text-slate-400">プロフィール設定</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="ログアウト" class="text-slate-500 hover:text-slate-300 transition" onclick="event.stopPropagation()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </a>
        </div>
    </aside>

    {{-- ─── メインコンテンツ ─── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- トップバー --}}
        <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0">
            <h1 class="text-base font-semibold text-slate-800">@yield('title', 'ダッシュボード')</h1>
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
            @if($errors->any() && !$errors->has('slot') && !$errors->has('email'))
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
