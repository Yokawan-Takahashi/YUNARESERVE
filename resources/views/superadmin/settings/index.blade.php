@extends('superadmin.layouts.app')
@section('title', 'システム設定')
@section('content')
<div class="max-w-2xl">

    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">システム設定</h1>
        <p class="text-xs text-slate-400 mt-0.5">Stripe・料金・メールの設定。保存すると即時反映され、.env の編集は不要です。</p>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 rounded-lg px-4 py-3 text-xs text-rose-700 mb-5">
        入力内容を確認してください。
        <ul class="list-disc list-inside mt-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('superadmin.settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ── Stripe ───────────────────────────────────────── --}}
        <div class="card p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stripe（月額課金）</p>
                <p class="text-xs text-slate-400 mt-1">
                    Webhook 宛先：<code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-600">{{ url('/stripe/webhook') }}</code>
                </p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">公開可能キー（Publishable key）</label>
                <input type="text" name="stripe_key" value="{{ old('stripe_key', $settings['stripe_key'] ?? '') }}" class="field font-mono" placeholder="pk_test_... / pk_live_...">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">シークレットキー（Secret key）</label>
                <input type="password" name="stripe_secret" value="" class="field font-mono" autocomplete="new-password"
                    placeholder="{{ !empty($settings['stripe_secret']) ? '設定済み（変更する場合のみ入力）' : 'sk_test_... / sk_live_...' }}">
                <p class="text-xs text-slate-400 mt-1">暗号化して保存されます。空欄のまま保存すると既存値を維持します。</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Webhook 署名シークレット</label>
                <input type="password" name="stripe_webhook_secret" value="" class="field font-mono" autocomplete="new-password"
                    placeholder="{{ !empty($settings['stripe_webhook_secret']) ? '設定済み（変更する場合のみ入力）' : 'whsec_...' }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">スタンダード Price ID</label>
                    <input type="text" name="stripe_price_id_standard" value="{{ old('stripe_price_id_standard', $settings['stripe_price_id_standard'] ?? '') }}" class="field font-mono" placeholder="price_...">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">プレミアム Price ID</label>
                    <input type="text" name="stripe_price_id_premium" value="{{ old('stripe_price_id_premium', $settings['stripe_price_id_premium'] ?? '') }}" class="field font-mono" placeholder="price_...（任意）">
                </div>
            </div>
        </div>

        {{-- ── 料金（表示用） ─────────────────────────────────── --}}
        <div class="card p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">料金表示（円／月）</p>
                <p class="text-xs text-slate-400 mt-1">画面表示用の金額です。実際の請求額は Stripe の Price で決まります。</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">スタンダード</label>
                    <input type="number" name="plan_standard_amount" value="{{ old('plan_standard_amount', $settings['plan_standard_amount'] ?? '') }}" class="field" min="0" placeholder="4980">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">プレミアム</label>
                    <input type="number" name="plan_premium_amount" value="{{ old('plan_premium_amount', $settings['plan_premium_amount'] ?? '') }}" class="field" min="0" placeholder="9800">
                </div>
            </div>
        </div>

        {{-- ── メール ───────────────────────────────────────── --}}
        <div class="card p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">メール送信</p>
                <p class="text-xs text-slate-400 mt-1">sendmail はレンタルサーバー標準。外部SMTPを使う場合は smtp を選択して各項目を入力してください。</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">送信方式</label>
                @php $mailer = old('mail_mailer', $settings['mail_mailer'] ?? 'sendmail'); @endphp
                <select name="mail_mailer" class="field">
                    <option value="sendmail" {{ $mailer === 'sendmail' ? 'selected' : '' }}>sendmail（サーバー標準）</option>
                    <option value="smtp" {{ $mailer === 'smtp' ? 'selected' : '' }}>SMTP（外部メールサーバー）</option>
                    <option value="log" {{ $mailer === 'log' ? 'selected' : '' }}>log（送信せずログ記録／検証用）</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">SMTP ホスト</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" class="field" placeholder="smtp.example.com">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">ポート</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '') }}" class="field" placeholder="587">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">ユーザー名</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="field" autocomplete="off">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">パスワード</label>
                    <input type="password" name="mail_password" value="" class="field" autocomplete="new-password"
                        placeholder="{{ !empty($settings['mail_password']) ? '設定済み（変更する場合のみ入力）' : '' }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">暗号化</label>
                    @php $enc = old('mail_encryption', $settings['mail_encryption'] ?? ''); @endphp
                    <select name="mail_encryption" class="field">
                        <option value="" {{ $enc === '' ? 'selected' : '' }}>なし／STARTTLS自動（587）</option>
                        <option value="smtps" {{ $enc === 'smtps' ? 'selected' : '' }}>SSL/TLS（465）</option>
                        <option value="tls" {{ $enc === 'tls' ? 'selected' : '' }}>tls</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">差出人アドレス</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" class="field" placeholder="noreply@reserve.yoka-wan.co.jp">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">差出人名</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" class="field" placeholder="YUNARI-RESERVE">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                設定を保存する
            </button>
        </div>
    </form>
</div>
@endsection
