<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — {{ $tenant?->company_name ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php $brandColor = $tenant?->color ?? '#4f46e5'; @endphp
    <style>
        :root { --brand-color: {{ $brandColor }}; }
        .brand-bg  { background-color: {{ $brandColor }}; }
        .brand-text { color: {{ $brandColor }}; }
        .brand-border { border-color: {{ $brandColor }}; }
        .brand-hover:hover { opacity: 0.88; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 flex flex-col">

    {{-- ヘッダー --}}
    <header class="bg-white border-b border-slate-100 sticky top-0 z-20">
        <div class="max-w-4xl mx-auto px-5 h-16 flex items-center justify-between">
            <a href="{{ route('public.index', $tenant) }}" class="flex items-center gap-3">
                @if($tenant?->logo_path)
                    <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="{{ $tenant->company_name }}" class="h-8 object-contain">
                @else
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold shrink-0"
                         style="background-color: {{ $brandColor }}">
                        {{ mb_substr($tenant?->company_name ?? 'Y', 0, 1) }}
                    </div>
                    <span class="font-bold text-slate-800">{{ $tenant?->company_name ?? config('app.name') }}</span>
                @endif
            </a>
            <nav class="flex items-center gap-1">
                <a href="{{ route('public.index', $tenant) }}" class="px-3 py-1.5 text-sm text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition">
                    イベント一覧
                </a>
                <a href="{{ route('public.lookup', $tenant) }}" class="px-3 py-1.5 text-sm text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition">
                    予約照会
                </a>
            </nav>
        </div>
    </header>

    {{-- メイン --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- フッター --}}
    <footer class="bg-white border-t border-slate-100 mt-auto">
        <div class="max-w-4xl mx-auto px-5 py-6 text-center text-xs text-slate-400">
            © {{ date('Y') }} {{ $tenant?->company_name ?? config('app.name') }}
            @if($tenant?->company_name)
             — Powered by <a href="{{ route('lp') }}" class="hover:text-slate-600 transition">YUNARI RESERVE</a>
            @endif
        </div>
    </footer>
</body>
</html>
