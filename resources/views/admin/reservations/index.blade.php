@extends('admin.layouts.app')
@section('title', '予約管理')
@section('header-actions')
@php $level = ['viewer'=>1,'staff'=>2,'admin'=>3,'owner'=>4,'superadmin'=>99][auth()->user()?->role ?? 'viewer'] ?? 0; @endphp
@if($level >= 2)
<a href="{{ route('admin.reservations.create') }}"
   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    手動登録
</a>
@endif
<a href="{{ route('admin.reservations.export', request()->query()) }}"
   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
    CSVダウンロード
</a>
@endsection
@section('content')

{{-- 絞り込み --}}
<form method="GET" action="{{ route('admin.reservations.index') }}" class="card p-4 mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-slate-500 mb-1.5">イベント</label>
            <select name="event_id" class="field">
                <option value="">すべてのイベント</option>
                @foreach($events as $ev)
                <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>{{ $ev->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">日付</label>
            <input type="date" name="date" value="{{ request('date') }}" class="field">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">ステータス</label>
            <select name="status" class="field">
                <option value="">すべて</option>
                <option value="reserved"  {{ request('status') === 'reserved'  ? 'selected' : '' }}>予約済</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
            </select>
        </div>
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-slate-500 mb-1.5">キーワード（名前・メール・番号）</label>
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="山田 / yamada@..." class="field">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">絞り込み</button>
            <a href="{{ route('admin.reservations.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition">リセット</a>
        </div>
    </div>
</form>

{{-- 件数 --}}
<div class="flex items-center justify-between mb-3">
    <p class="text-xs text-slate-500">{{ $reservations->total() }} 件</p>
</div>

{{-- テーブル --}}
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">予約番号</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">イベント</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">日時</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">お名前</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ステータス</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">受付日</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($reservations as $r)
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-5 py-3.5 font-mono text-xs text-indigo-600">
                    <a href="{{ route('admin.reservations.show', $r) }}" class="hover:text-indigo-700 font-medium">{{ $r->code }}</a>
                </td>
                <td class="px-5 py-3.5 text-slate-700 max-w-[160px] truncate">{{ $r->event->title }}</td>
                <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap text-xs">
                    {{ $r->slot->date->format('Y/m/d') }}<br>
                    <span class="text-slate-400">{{ substr($r->slot->start_time, 0, 5) }}</span>
                </td>
                <td class="px-5 py-3.5 text-slate-700">{{ $r->name }}</td>
                <td class="px-5 py-3.5">
                    @if($r->status === 'reserved')
                        <span class="badge-reserved">予約済</span>
                    @else
                        <span class="badge-cancelled">キャンセル</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $r->created_at->format('m/d H:i') }}</td>
                <td class="px-5 py-3.5">
                    <a href="{{ route('admin.reservations.show', $r) }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">詳細 →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-12 text-center text-slate-400">条件に一致する予約がありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $reservations->links() }}</div>
@endsection
