@extends('admin.layouts.app')
@section('title', '予約一覧')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">予約一覧</h1>
    <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500">{{ $reservations->total() }} 件</span>
        <a href="{{ route('admin.reservations.export', request()->query()) }}"
           class="bg-green-600 text-white px-4 py-1.5 rounded text-sm hover:bg-green-700">
            CSVダウンロード
        </a>
    </div>
</div>

{{-- 絞り込みフォーム --}}
<form method="GET" action="{{ route('admin.reservations.index') }}" class="bg-white rounded shadow p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">イベント</label>
        <select name="event_id" class="border rounded px-2 py-1 text-sm">
            <option value="">すべて</option>
            @foreach($events as $ev)
            <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>{{ $ev->title }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">日付</label>
        <input type="date" name="date" value="{{ request('date') }}" class="border rounded px-2 py-1 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">ステータス</label>
        <select name="status" class="border rounded px-2 py-1 text-sm">
            <option value="">すべて</option>
            <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>予約済</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">キーワード（名前・メール・番号）</label>
        <input type="text" name="keyword" value="{{ request('keyword') }}" class="border rounded px-2 py-1 text-sm w-48">
    </div>
    <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded text-sm hover:bg-indigo-700">絞り込み</button>
    <a href="{{ route('admin.reservations.index') }}" class="text-sm text-gray-500 hover:underline">リセット</a>
</form>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-2 text-left">予約番号</th>
                <th class="px-4 py-2 text-left">イベント</th>
                <th class="px-4 py-2 text-left">日時</th>
                <th class="px-4 py-2 text-left">お名前</th>
                <th class="px-4 py-2 text-left">ステータス</th>
                <th class="px-4 py-2 text-left">受付日</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $r)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2 font-mono">
                    <a href="{{ route('admin.reservations.show', $r) }}" class="text-indigo-600 hover:underline">{{ $r->code }}</a>
                </td>
                <td class="px-4 py-2">{{ $r->event->title }}</td>
                <td class="px-4 py-2 whitespace-nowrap">
                    {{ $r->slot->date->format('Y/m/d') }}
                    {{ substr($r->slot->start_time, 0, 5) }}
                </td>
                <td class="px-4 py-2">{{ $r->name }} 様</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-0.5 rounded text-xs {{ $r->status === 'reserved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                        {{ $r->status === 'reserved' ? '予約済' : 'キャンセル' }}
                    </span>
                </td>
                <td class="px-4 py-2 text-gray-500">{{ $r->created_at->format('m/d H:i') }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('admin.reservations.show', $r) }}" class="text-indigo-600 hover:underline text-xs">詳細</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">予約がありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $reservations->links() }}</div>
@endsection
