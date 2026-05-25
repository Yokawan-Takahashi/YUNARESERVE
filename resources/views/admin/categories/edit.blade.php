@extends('admin.layouts.app')
@section('title', 'カテゴリ編集')
@section('content')
<h1 class="text-xl font-bold mb-4">カテゴリ編集</h1>
<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="bg-white rounded shadow p-6 max-w-lg space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">カテゴリ名 <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full border rounded px-3 py-2" required>
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">スコープ</label>
        <select name="scope" class="w-full border rounded px-3 py-2">
            <option value="external" {{ old('scope', $category->scope) === 'external' ? 'selected' : '' }}>外部（公開）</option>
            <option value="internal" {{ old('scope', $category->scope) === 'internal' ? 'selected' : '' }}>内部</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">アイコン（任意）</label>
        <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">表示順</label>
        <input type="number" name="sort" value="{{ old('sort', $category->sort) }}" min="0" class="w-full border rounded px-3 py-2">
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="active" id="active" value="1" {{ old('active', $category->active) ? 'checked' : '' }}>
        <label for="active" class="text-sm">有効</label>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">更新</button>
        <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 rounded border hover:bg-gray-50">キャンセル</a>
    </div>
</form>
@endsection
