@extends('superadmin.layouts.app')
@section('title', 'テナント新規作成')
@section('header-actions')
<a href="{{ route('superadmin.tenants.index') }}" class="text-xs text-slate-500 hover:text-slate-700">← テナント一覧</a>
@endsection
@section('content')
<div class="max-w-xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('superadmin.tenants.store') }}" class="space-y-5">
            @csrf

            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-3">テナント情報</p>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">会社名・店舗名 <span class="text-rose-500">*</span></label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" class="field" required placeholder="株式会社〇〇">
                @error('company_name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">スラッグ <span class="text-rose-500">*</span></label>
                <input type="text" name="slug" value="{{ old('slug') }}" class="field font-mono" required placeholder="example-company">
                <p class="text-xs text-slate-400 mt-1">英数字とハイフンのみ。URLに使用されます（例: example-company.yunari-reserve.jp）</p>
                @error('slug')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">業種</label>
                <select name="industry" class="field">
                    @foreach($industries as $industry)
                    <option value="{{ $industry }}" {{ old('industry') === $industry ? 'selected' : '' }}>{{ $industry }}</option>
                    @endforeach
                </select>
                @error('industry')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-3 pt-2">オーナーアカウント</p>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">氏名 <span class="text-rose-500">*</span></label>
                <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="field" required placeholder="山田 太郎">
                @error('owner_name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">メールアドレス <span class="text-rose-500">*</span></label>
                <input type="email" name="owner_email" value="{{ old('owner_email') }}" class="field" required placeholder="owner@example.com">
                @error('owner_email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">初期パスワード <span class="text-rose-500">*</span></label>
                <input type="text" name="owner_password" value="{{ old('owner_password') }}" class="field font-mono" required minlength="8" placeholder="8文字以上">
                <p class="text-xs text-slate-400 mt-1">オーナーに別途通知してください</p>
                @error('owner_password')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    テナントを作成する
                </button>
                <a href="{{ route('superadmin.tenants.index') }}" class="px-6 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
