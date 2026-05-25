@extends('admin.layouts.app')
@section('title', 'カスタム項目')
@section('content')
<h1 class="text-xl font-bold mb-4">カスタム項目</h1>

<table class="w-full bg-white rounded shadow text-sm mb-8">
    <thead class="bg-gray-50 border-b">
        <tr>
            <th class="px-4 py-2 text-left">ラベル</th>
            <th class="px-4 py-2 text-left">種別</th>
            <th class="px-4 py-2 text-left">カテゴリ</th>
            <th class="px-4 py-2 text-left">必須</th>
            <th class="px-4 py-2 text-left">順序</th>
            <th class="px-4 py-2"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($fields as $field)
        <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2">{{ $field->label }}</td>
            <td class="px-4 py-2">{{ $field->type }}</td>
            <td class="px-4 py-2">{{ $field->category?->name ?? '全カテゴリ' }}</td>
            <td class="px-4 py-2">{{ $field->required ? '必須' : '' }}</td>
            <td class="px-4 py-2">{{ $field->sort }}</td>
            <td class="px-4 py-2">
                <form method="POST" action="{{ route('admin.fields.destroy', $field) }}" onsubmit="return confirm('削除しますか？')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline">削除</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">項目がありません</td></tr>
        @endforelse
    </tbody>
</table>

<div class="bg-white rounded shadow p-6 max-w-lg">
    <h2 class="font-bold mb-4">項目を追加</h2>
    <form method="POST" action="{{ route('admin.fields.store') }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">ラベル <span class="text-red-500">*</span></label>
            <input type="text" name="label" value="{{ old('label') }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">種別</label>
            <select name="type" class="w-full border rounded px-3 py-2">
                <option value="text">テキスト</option>
                <option value="number">数値</option>
                <option value="textarea">テキストエリア</option>
                <option value="select">選択肢</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">カテゴリ（絞り込み）</label>
            <select name="category_id" class="w-full border rounded px-3 py-2">
                <option value="">全カテゴリ共通</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">選択肢（1行1項目 / selectのみ）</label>
            <textarea name="options" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('options') }}</textarea>
        </div>
        <div class="flex gap-4">
            <label class="flex items-center gap-1 text-sm">
                <input type="checkbox" name="required" value="1" {{ old('required') ? 'checked' : '' }}>必須
            </label>
            <label class="flex items-center gap-1 text-sm">
                <input type="checkbox" name="hidden" value="1" {{ old('hidden') ? 'checked' : '' }}>非表示
            </label>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">表示順</label>
            <input type="number" name="sort" value="{{ old('sort', 0) }}" min="0" class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">追加</button>
    </form>
</div>
@endsection
