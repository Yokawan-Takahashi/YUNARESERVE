@extends('admin.layouts.app')
@section('title', 'ダッシュボード')
@section('content')
<h1 class="text-xl font-bold mb-6">ダッシュボード</h1>

<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded shadow p-5 text-center">
        <p class="text-sm text-gray-500 mb-1">今日の予約</p>
        <p class="text-3xl font-bold text-indigo-600">{{ $todayCount }}</p>
        <p class="text-xs text-gray-400 mt-1">件</p>
    </div>
    <div class="bg-white rounded shadow p-5 text-center">
        <p class="text-sm text-gray-500 mb-1">今週の予約</p>
        <p class="text-3xl font-bold text-indigo-600">{{ $weekCount }}</p>
        <p class="text-xs text-gray-400 mt-1">件</p>
    </div>
    <div class="bg-white rounded shadow p-5 text-center">
        <p class="text-sm text-gray-500 mb-1">累計予約（有効）</p>
        <p class="text-3xl font-bold text-indigo-600">{{ $totalCount }}</p>
        <p class="text-xs text-gray-400 mt-1">件</p>
    </div>
</div>

<div class="flex items-center justify-between mb-3">
    <h2 class="text-lg font-semibold">直近の予約</h2>
    <a href="{{ route('admin.reservations.index') }}" class="text-sm text-indigo-600 hover:underline">すべて見る →</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-2 text-left">予約番号</th>
                <th class="px-4 py-2 text-left">イベント</th>
                <th class="px-4 py-2 text-left">日時</th>
                <th class="px-4 py-2 text-left">お名前</th>
                <th class="px-4 py-2 text-left">受付日</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recent as $r)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2">
                    <a href="{{ route('admin.reservations.show', $r) }}" class="text-indigo-600 hover:underline font-mono">{{ $r->code }}</a>
                </td>
                <td class="px-4 py-2">{{ $r->event->title }}</td>
                <td class="px-4 py-2 whitespace-nowrap">
                    {{ $r->slot->date->format('m/d') }}
                    {{ substr($r->slot->start_time, 0, 5) }}
                </td>
                <td class="px-4 py-2">{{ $r->name }} 様</td>
                <td class="px-4 py-2 text-gray-500">{{ $r->created_at->format('m/d H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">予約はありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
