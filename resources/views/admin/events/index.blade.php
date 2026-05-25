@extends('admin.layouts.app')
@section('title', 'イベント管理')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">イベント</h1>
    <a href="{{ route('admin.events.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ 新規作成</a>
</div>
<table class="w-full bg-white rounded shadow text-sm">
    <thead class="bg-gray-50 border-b">
        <tr>
            <th class="px-4 py-2 text-left">タイトル</th>
            <th class="px-4 py-2 text-left">カテゴリ</th>
            <th class="px-4 py-2 text-left">ステータス</th>
            <th class="px-4 py-2 text-left">枠数</th>
            <th class="px-4 py-2"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($events as $event)
        <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2">{{ $event->title }}</td>
            <td class="px-4 py-2">{{ $event->category?->name ?? '―' }}</td>
            <td class="px-4 py-2">
                <span class="px-2 py-0.5 rounded text-xs {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $event->status === 'published' ? '公開' : '下書き' }}
                </span>
            </td>
            <td class="px-4 py-2">{{ $event->slots_count ?? '―' }}</td>
            <td class="px-4 py-2 flex gap-2 justify-end">
                <a href="{{ route('admin.events.edit', $event) }}" class="text-indigo-600 hover:underline">編集</a>
                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('削除しますか？')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline">削除</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">イベントがありません</td></tr>
        @endforelse
    </tbody>
</table>
<div class="mt-4">{{ $events->links() }}</div>
@endsection
