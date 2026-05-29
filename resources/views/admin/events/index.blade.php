@extends('admin.layouts.app')
@section('title', 'イベント管理')
@section('header-actions')
@php $level = ['viewer'=>1,'staff'=>2,'admin'=>3,'owner'=>4,'superadmin'=>99][auth()->user()?->role ?? 'viewer'] ?? 0; @endphp
@if($level >= 2)
<a href="{{ route('admin.events.create') }}"
   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    新規作成
</a>
@endif
@endsection
@section('content')

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">タイトル</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">カテゴリ</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ステータス</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">枠数</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($events as $event)
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-5 py-4">
                    <p class="font-medium text-slate-800">{{ $event->title }}</p>
                    @if($event->location)
                    <p class="flex items-center gap-1 text-xs text-slate-400 mt-0.5">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $event->location }}
                    </p>
                    @endif
                </td>
                <td class="px-5 py-4 text-slate-500">{{ $event->category?->name ?? '―' }}</td>
                <td class="px-5 py-4">
                    @if($event->status === 'published')
                        <span class="badge-published">公開中</span>
                    @else
                        <span class="badge-draft">下書き</span>
                    @endif
                </td>
                <td class="px-5 py-4 text-slate-600">{{ $event->slots_count ?? '―' }} 枠</td>
                <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        @if($event->status === 'published')
                        <a href="{{ url(auth()->user()->tenant->slug . '/events/' . $event->id) }}" target="_blank"
                           class="text-xs text-slate-500 hover:text-indigo-600 font-medium" title="公開ページを確認">
                            公開URL
                        </a>
                        @endif
                        @if($level >= 2)
                        <a href="{{ route('admin.events.edit', $event) }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">編集</a>
                        @endif
                        @if($level >= 3)
                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('削除しますか？')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-rose-500 hover:text-rose-600 font-medium">削除</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-slate-400">イベントがありません。まず「新規作成」から始めましょう。</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $events->links() }}</div>
@endsection
