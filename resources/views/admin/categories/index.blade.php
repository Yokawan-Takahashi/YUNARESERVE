@extends('admin.layouts.app')
@section('title', 'カテゴリ管理')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">カテゴリ</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ 新規作成</a>
</div>
<table class="w-full bg-white rounded shadow text-sm">
    <thead class="bg-gray-50 border-b">
        <tr>
            <th class="px-4 py-2 text-left">名前</th>
            <th class="px-4 py-2 text-left">スコープ</th>
            <th class="px-4 py-2 text-left">表示順</th>
            <th class="px-4 py-2 text-left">状態</th>
            <th class="px-4 py-2"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $cat)
        <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2">{{ $cat->name }}</td>
            <td class="px-4 py-2">{{ $cat->scope }}</td>
            <td class="px-4 py-2">{{ $cat->sort }}</td>
            <td class="px-4 py-2">{{ $cat->active ? '有効' : '無効' }}</td>
            <td class="px-4 py-2 flex gap-2 justify-end">
                <a href="{{ route('admin.categories.edit', $cat) }}" class="text-indigo-600 hover:underline">編集</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('削除しますか？')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline">削除</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">カテゴリがありません</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
