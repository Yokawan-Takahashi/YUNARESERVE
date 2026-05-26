@extends('superadmin.layouts.app')
@section('title', '運営ダッシュボード')
@section('content')

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">総テナント</p>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-slate-800">{{ $totalTenants }}</span>
            <span class="text-sm text-slate-400">社</span>
        </div>
    </div>
    <div class="card p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">有効テナント</p>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-emerald-600">{{ $activeTenants }}</span>
            <span class="text-sm text-slate-400">社</span>
        </div>
    </div>
    <div class="card p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">総ユーザー数</p>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-indigo-600">{{ $totalUsers }}</span>
            <span class="text-sm text-slate-400">名</span>
        </div>
    </div>
    <div class="card p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">有効予約（全体）</p>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-violet-600">{{ $totalReservations }}</span>
            <span class="text-sm text-slate-400">件</span>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-700">直近追加テナント</h2>
        <a href="{{ route('superadmin.tenants.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">すべて見る →</a>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">会社名</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">スラッグ</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">業種</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ユーザー</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ステータス</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">作成日</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($recentTenants as $tenant)
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-5 py-3.5 font-medium text-slate-800">{{ $tenant->company_name }}</td>
                <td class="px-5 py-3.5 font-mono text-xs text-slate-500">{{ $tenant->slug }}</td>
                <td class="px-5 py-3.5 text-slate-500">{{ $tenant->industry ?? '―' }}</td>
                <td class="px-5 py-3.5 text-slate-600">{{ $tenant->users_count }} 名</td>
                <td class="px-5 py-3.5">
                    @if($tenant->status === 'active')
                        <span class="badge-published">有効</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-700">停止</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $tenant->created_at->format('Y/m/d') }}</td>
                <td class="px-5 py-3.5">
                    <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">詳細 →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">テナントがありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
