@extends('admin.layouts.app')
@section('title', 'ダッシュボード')
@section('content')

{{-- 統計カード --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">今日の新規予約</p>
            <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-slate-800">{{ $todayCount }}</span>
            <span class="text-sm text-slate-400 font-medium">件</span>
        </div>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">今週の予約</p>
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-slate-800">{{ $weekCount }}</span>
            <span class="text-sm text-slate-400 font-medium">件</span>
        </div>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">累計予約（有効）</p>
            <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-slate-800">{{ $totalCount }}</span>
            <span class="text-sm text-slate-400 font-medium">件</span>
        </div>
    </div>
</div>

{{-- 直近の予約 --}}
<div class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-700">直近の予約</h2>
        <a href="{{ route('admin.reservations.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">すべて見る →</a>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">予約番号</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">イベント</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">日時</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">お名前</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">状態</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">受付</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($recent as $r)
            <tr class="hover:bg-slate-50/50 transition">
                <td class="px-5 py-3.5">
                    <a href="{{ route('admin.reservations.show', $r) }}" class="text-indigo-600 hover:text-indigo-700 font-mono text-xs font-medium">{{ $r->code }}</a>
                </td>
                <td class="px-5 py-3.5 text-slate-700 max-w-[140px] truncate">{{ $r->event->title }}</td>
                <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap text-xs">{{ $r->slot->date->format('m/d') }} {{ substr($r->slot->start_time, 0, 5) }}</td>
                <td class="px-5 py-3.5 text-slate-700">{{ $r->name }}</td>
                <td class="px-5 py-3.5">
                    @if($r->status === 'reserved')
                        <span class="badge-reserved">予約済</span>
                    @else
                        <span class="badge-cancelled">キャンセル</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $r->created_at->format('m/d H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400 text-sm">予約はありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
