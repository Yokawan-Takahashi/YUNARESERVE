@extends('admin.layouts.app')
@section('title', 'テナント設定')
@section('content')
<div class="max-w-2xl space-y-5">

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- 基本情報 --}}
        <div class="card p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-5">基本情報</h2>
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">会社名・店舗名 <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" value="{{ old('company_name', $tenant->company_name) }}"
                        class="field" required placeholder="株式会社〇〇">
                    @error('company_name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">管理者通知メールアドレス</label>
                    <input type="email" name="notify_email" value="{{ old('notify_email', $tenant->notify_email) }}"
                        class="field" placeholder="owner@example.com">
                    <p class="text-xs text-slate-400 mt-1">新規予約があった際にこのアドレスへ通知します</p>
                    @error('notify_email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ブランディング --}}
        <div class="card p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-5">ブランディング</h2>
            <div class="space-y-5">

                {{-- ロゴ --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">ロゴ画像</label>
                    @if($tenant->logo_path)
                    <div class="mb-3 flex items-center gap-4">
                        <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="現在のロゴ"
                            class="h-12 object-contain rounded border border-slate-200 p-1">
                        <span class="text-xs text-slate-400">現在のロゴ</span>
                    </div>
                    @endif
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">JPG / PNG / WebP、2MB以内。公開ページのヘッダーに表示されます。</p>
                    @error('logo')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- ブランドカラー --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">ブランドカラー</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $tenant->color ?? '#4f46e5') }}"
                            class="w-10 h-10 rounded-lg border border-slate-300 cursor-pointer p-0.5">
                        <input type="text" id="colorHex"
                            value="{{ old('color', $tenant->color ?? '#4f46e5') }}"
                            class="field max-w-[120px] font-mono text-sm"
                            placeholder="#4f46e5"
                            oninput="document.querySelector('input[name=color]').value=this.value">
                        <p class="text-xs text-slate-400">公開ページのボタン・リンクに使用されます</p>
                    </div>
                    @error('color')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <script>
                        document.querySelector('input[type=color]').addEventListener('input', function() {
                            document.getElementById('colorHex').value = this.value;
                        });
                    </script>
                </div>

                {{-- プレビュー --}}
                <div>
                    <p class="text-xs font-medium text-slate-600 mb-2">プレビュー</p>
                    <div class="rounded-lg border border-slate-200 overflow-hidden">
                        <div class="px-4 py-3 bg-white flex items-center gap-3" id="previewHeader">
                            <div id="previewLogo" class="w-8 h-8 rounded bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600">
                                {{ mb_substr($tenant->company_name, 0, 1) }}
                            </div>
                            <span class="text-sm font-semibold text-slate-800" id="previewName">{{ $tenant->company_name }}</span>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 flex items-center gap-2">
                            <button type="button" class="px-4 py-1.5 rounded-lg text-sm font-medium text-white transition" id="previewBtn" style="background-color: {{ $tenant->color ?? '#4f46e5' }}">予約する</button>
                            <a href="#" class="text-sm font-medium" id="previewLink" style="color: {{ $tenant->color ?? '#4f46e5' }}">詳細を見る →</a>
                        </div>
                    </div>
                    <script>
                        document.querySelector('input[name=company_name]').addEventListener('input', function() {
                            document.getElementById('previewName').textContent = this.value;
                        });
                        document.querySelector('input[type=color]').addEventListener('input', function() {
                            document.getElementById('previewBtn').style.backgroundColor = this.value;
                            document.getElementById('previewLink').style.color = this.value;
                        });
                    </script>
                </div>
            </div>
        </div>

        {{-- 予約ルール --}}
        <div class="card p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-5">予約ルール</h2>
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">キャンセル受付締切</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-500">イベント当日の</span>
                        <input type="number" name="cancel_deadline_days"
                            value="{{ old('cancel_deadline_days', $tenant->cancel_deadline_days) }}"
                            class="field w-24 text-center" min="0" max="365" placeholder="—">
                        <span class="text-sm text-slate-500">日前まで</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">空白の場合はキャンセル期限なし（当日まで受付）</p>
                    @error('cancel_deadline_days')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">プライバシーポリシーURL</label>
                    <input type="url" name="privacy_policy_url"
                        value="{{ old('privacy_policy_url', $tenant->privacy_policy_url) }}"
                        class="field" placeholder="https://example.com/privacy">
                    <p class="text-xs text-slate-400 mt-1">設定すると予約フォームに同意チェックボックスが表示されます</p>
                    @error('privacy_policy_url')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- 保存 --}}
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                設定を保存する
            </button>
        </div>
    </form>
</div>
@endsection
