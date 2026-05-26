@extends('admin.layouts.app')
@section('title', 'カテゴリ管理')
@section('header-actions')
@php $level = ['viewer'=>1,'staff'=>2,'admin'=>3,'owner'=>4,'superadmin'=>99][auth()->user()?->role ?? 'viewer'] ?? 0; @endphp
@if($level >= 2)
<a href="{{ route('admin.categories.create') }}"
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
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">名前</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">スコープ</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">順序</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">状態</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($categories as $cat)
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-5 py-3.5 font-medium text-slate-800">{{ $cat->icon }} {{ $cat->name }}</td>
                <td class="px-5 py-3.5 text-slate-500">{{ $cat->scope ?: '―' }}</td>
                <td class="px-5 py-3.5 text-slate-400">{{ $cat->sort }}</td>
                <td class="px-5 py-3.5">
                    @if($cat->active)
                        <span class="badge-published">有効</span>
                    @else
                        <span class="badge-cancelled">無効</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        @if($level >= 2)
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">編集</a>
                        @endif
                        @if($level >= 3)
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('削除しますか？')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-rose-500 hover:text-rose-600 font-medium">削除</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-slate-400">カテゴリがありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
