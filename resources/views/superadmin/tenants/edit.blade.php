@extends('superadmin.layouts.app')
@section('title', 'テナント編集 — ' . $tenant->company_name)
@section('content')

<div class="max-w-2xl space-y-5">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="text-sm text-indigo-300 hover:text-white">← 詳細へ戻る</a>
        <span class="text-indigo-500/50">›</span>
        <span class="text-sm text-white">{{ $tenant->company_name }}</span>
    </div>

    <div class="bg-indigo-900/50 rounded-2xl border border-indigo-800/60 p-6">
        <h2 class="text-sm font-semibold text-indigo-200 mb-5">テナント情報を編集</h2>

        <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" class="space-y-5">
            @csrf @method('PUT')

            {{-- 会社名 --}}
            <div>
                <label class="block text-xs font-medium text-indigo-300 mb-1.5">会社名・店舗名 <span class="text-rose-400">*</span></label>
                <input type="text" name="company_name" value="{{ old('company_name', $tenant->company_name) }}"
                    class="w-full rounded-xl bg-indigo-950/60 border border-indigo-700/60 text-white text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('company_name') border-rose-500 @enderror"
                    required>
                @error('company_name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- スラッグ（読み取り専用） --}}
            <div>
                <label class="block text-xs font-medium text-indigo-300 mb-1.5">スラッグ（変更不可）</label>
                <input type="text" value="{{ $tenant->slug }}" readonly
                    class="w-full rounded-xl bg-indigo-950/30 border border-indigo-800/40 text-indigo-400 text-sm px-4 py-2.5 font-mono cursor-not-allowed">
                <p class="text-xs text-indigo-500 mt-1">スラッグはURLの一部のため変更できません</p>
            </div>

            {{-- 業種 --}}
            <div>
                <label class="block text-xs font-medium text-indigo-300 mb-1.5">業種</label>
                <select name="industry"
                    class="w-full rounded-xl bg-indigo-950/60 border border-indigo-700/60 text-white text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">選択なし</option>
                    @foreach($industries as $ind)
                    <option value="{{ $ind }}" {{ old('industry', $tenant->industry) === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 通知メール --}}
            <div>
                <label class="block text-xs font-medium text-indigo-300 mb-1.5">管理者通知メール</label>
                <input type="email" name="notify_email" value="{{ old('notify_email', $tenant->notify_email) }}"
                    class="w-full rounded-xl bg-indigo-950/60 border border-indigo-700/60 text-white text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('notify_email') border-rose-500 @enderror"
                    placeholder="owner@example.com">
                @error('notify_email')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- ステータス --}}
            <div>
                <label class="block text-xs font-medium text-indigo-300 mb-1.5">ステータス</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="active" {{ old('status', $tenant->status) === 'active' ? 'checked' : '' }} class="text-indigo-500">
                        <span class="text-sm text-white">有効（active）</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'checked' : '' }} class="text-indigo-500">
                        <span class="text-sm text-rose-300">停止中（suspended）</span>
                    </label>
                </div>
                @error('status')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2 border-t border-indigo-800/40">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-500 transition">
                    更新する
                </button>
                <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="px-6 py-2.5 bg-indigo-950/60 text-indigo-300 text-sm font-medium rounded-xl hover:bg-indigo-900 transition">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
