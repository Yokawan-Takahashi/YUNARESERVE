@extends('admin.layouts.app')
@section('title', 'カスタム項目')
@section('content')
@php $level = ['viewer'=>1,'staff'=>2,'admin'=>3,'owner'=>4,'superadmin'=>99][auth()->user()?->role ?? 'viewer'] ?? 0; @endphp
<div class="grid grid-cols-5 gap-6">

    {{-- 項目一覧 --}}
    <div class="col-span-3">
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">設定済みの項目</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ラベル</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">種別</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">カテゴリ</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">必須</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($fields as $field)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3 font-medium text-slate-800">
                            {{ $field->label }}
                            @if($field->hidden)<span class="ml-1 text-xs text-slate-400">（非表示）</span>@endif
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $field->type }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $field->category?->name ?? '全共通' }}</td>
                        <td class="px-5 py-3">
                            @if($field->required)
                                <span class="badge-reserved">必須</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($level >= 3)
                            <form method="POST" action="{{ route('admin.fields.destroy', $field) }}" onsubmit="return confirm('削除しますか？')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-rose-500 hover:text-rose-600 font-medium">削除</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400 text-sm">カスタム項目がありません</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 追加フォーム (staff以上) --}}
    <div class="col-span-2">
        <div class="card p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">項目を追加</h2>
            @if($level < 2)
            <p class="text-sm text-slate-400">カスタム項目の追加はスタッフ以上のロールが必要です。</p>
            @else
            <form method="POST" action="{{ route('admin.fields.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">ラベル <span class="text-rose-500">*</span></label>
                    <input type="text" name="label" value="{{ old('label') }}" class="field" required placeholder="例：参加動機">
                    @error('label')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">種別</label>
                    <select name="type" class="field">
                        <option value="text">テキスト（一行）</option>
                        <option value="number">数値</option>
                        <option value="textarea">テキストエリア（複数行）</option>
                        <option value="select">選択肢（ドロップダウン）</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">カテゴリ（絞り込み）</label>
                    <select name="category_id" class="field">
                        <option value="">全カテゴリ共通</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">選択肢（1行1項目 ※selectのみ）</label>
                    <textarea name="options" rows="3" class="field text-xs" placeholder="選択肢A&#10;選択肢B&#10;選択肢C">{{ old('options') }}</textarea>
                </div>
                <div class="flex gap-4">
                    <label class="flex items-center gap-1.5 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="required" value="1" {{ old('required') ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600">
                        必須
                    </label>
                    <label class="flex items-center gap-1.5 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="hidden" value="1" {{ old('hidden') ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600">
                        非表示（管理のみ）
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">表示順</label>
                    <input type="number" name="sort" value="{{ old('sort', 0) }}" min="0" class="field max-w-[80px]">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    追加する
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
