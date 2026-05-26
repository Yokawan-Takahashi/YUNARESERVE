@extends('admin.layouts.app')
@section('title', 'カテゴリ編集')
@section('header-actions')
<a href="{{ route('admin.categories.index') }}" class="text-xs text-slate-500 hover:text-slate-700">← カテゴリ一覧</a>
@endsection
@section('content')
<div class="max-w-lg">
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">カテゴリ名 <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="field" required>
                @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">スコープ</label>
                    <select name="scope" class="field">
                        <option value="external" {{ old('scope', $category->scope) === 'external' ? 'selected' : '' }}>外部（公開）</option>
                        <option value="internal" {{ old('scope', $category->scope) === 'internal' ? 'selected' : '' }}>内部</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">アイコン（絵文字）</label>
                    <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" class="field">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">表示順</label>
                <input type="number" name="sort" value="{{ old('sort', $category->sort) }}" min="0" class="field max-w-[100px]">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="active" id="active" value="1" {{ old('active', $category->active) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600">
                <label for="active" class="text-sm text-slate-700">有効にする</label>
            </div>
            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">更新する</button>
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition">キャンセル</a>
            </div>
        </form>
    </div>
</div>
@endsection
