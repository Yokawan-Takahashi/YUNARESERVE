@extends('admin.layouts.app')
@section('title', '課金・プラン管理')
@section('content')

@if(request('checkout') === 'success')
<div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    サブスクリプションが開始されました。ご契約ありがとうございます。
</div>
@endif

@if(session('error'))
<div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm">
    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
    {{ session('error') }}
</div>
@endif

<div class="max-w-3xl space-y-6">

    {{-- プラン状況カード --}}
    <div class="card p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-700 mb-1">現在のプラン</h2>
                @if($subscription && $subscription->active())
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            有効
                        </span>
                        <span class="text-sm text-slate-600">スタンダードプラン</span>
                    </div>
                    @if($subscription->onTrial())
                    <p class="mt-2 text-xs text-amber-600">
                        トライアル期間中 — {{ $subscription->trial_ends_at?->format('Y年n月j日') }} 終了
                    </p>
                    @elseif($subscription->ends_at)
                    <p class="mt-2 text-xs text-slate-500">
                        解約予定日：{{ $subscription->ends_at->format('Y年n月j日') }}
                    </p>
                    @endif
                @elseif($subscription && $subscription->canceled())
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                            解約済み
                        </span>
                        <span class="text-sm text-slate-500">
                            @if($subscription->ends_at)
                                {{ $subscription->ends_at->format('Y年n月j日') }} まで利用可能
                            @endif
                        </span>
                    </div>
                @else
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                            未契約
                        </span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">現在ご契約中のプランはありません。</p>
                @endif
            </div>
            <div class="shrink-0">
                @if($subscription && $subscription->active())
                    <form method="POST" action="{{ route('admin.billing.portal') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Stripeポータルで管理
                        </button>
                    </form>
                @else
                    @if(config('cashier.price_id'))
                    <form method="POST" action="{{ route('admin.billing.checkout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            プランを開始する
                        </button>
                    </form>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- プラン詳細 --}}
    <div class="card p-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">スタンダードプラン 概要</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-slate-800">¥{{ number_format($plan['amount']) }}</p>
                <p class="text-xs text-slate-500 mt-1">/ 月（税込）</p>
            </div>
            <div class="sm:col-span-2 space-y-2">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    予約管理機能（イベント・枠・予約者管理）
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    自動メール配信（確認・リマインダー・キャンセル）
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    カスタム項目・ブランディング（ロゴ・カラー）
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    CSVエクスポート・予約統計
                </div>
            </div>
        </div>
    </div>

    {{-- 直近の請求 --}}
    @if($invoice)
    <div class="card p-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">直近の請求</h2>
        <div class="flex items-center justify-between py-2 border-b border-slate-100 text-sm">
            <span class="text-slate-600">{{ $invoice->date()->format('Y年n月j日') }}</span>
            <span class="font-medium text-slate-800">{{ $invoice->total() }}</span>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.billing.invoice', $invoice->id) }}"
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                PDFダウンロード →
            </a>
        </div>
    </div>
    @endif

    {{-- Stripe情報 --}}
    <div class="card p-4">
        <p class="text-xs text-slate-400">
            お支払い情報の更新・解約はStripeポータルから行えます。ご不明な点は <a href="mailto:{{ config('mail.from.address') }}" class="text-indigo-500 hover:underline">サポート</a> までお問い合わせください。
        </p>
    </div>

</div>
@endsection
