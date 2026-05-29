<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プライバシーポリシー — YUNARI RESERVE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900 antialiased">

<nav class="fixed top-0 left-0 right-0 z-50 border-b border-slate-100 bg-white/90 backdrop-blur-md">
    <div class="max-w-4xl mx-auto px-5 h-16 flex items-center justify-between">
        <a href="{{ route('lp') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="font-black text-slate-900 tracking-tight">YUNARI RESERVE</span>
        </a>
        <a href="{{ route('lp') }}" class="text-sm text-slate-500 hover:text-indigo-600 transition">← トップへ戻る</a>
    </div>
</nav>

<main class="max-w-3xl mx-auto px-5 pt-32 pb-24">
    <h1 class="text-3xl font-black text-slate-900 mb-2">プライバシーポリシー</h1>
    <p class="text-sm text-slate-400 mb-10">最終更新日：{{ date('Y年m月d日') }}</p>

    <div class="space-y-8 text-sm leading-relaxed text-slate-700">

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">1. 収集する情報</h2>
            <p>当サービスは、以下の情報を収集します。</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li><strong>テナント情報：</strong>会社名・担当者名・メールアドレス・電話番号・業種</li>
                <li><strong>予約者情報：</strong>テナントが受け付ける予約に関する氏名・メールアドレス・その他入力項目</li>
                <li><strong>決済情報：</strong>クレジットカード情報はStripeが管理し、当サービスのサーバーには保存しません</li>
                <li><strong>利用ログ：</strong>アクセス日時・IPアドレス・ブラウザ情報</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">2. 情報の利用目的</h2>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>サービスの提供・改善・サポート対応</li>
                <li>月額課金の処理・請求書発行</li>
                <li>サービスに関する重要なお知らせの送信</li>
                <li>不正利用の検知・防止</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">3. 第三者への提供</h2>
            <p>運営は、以下の場合を除き、収集した個人情報を第三者に提供しません。</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>法令に基づく場合</li>
                <li>人命・財産の保護のために必要な場合</li>
                <li>サービス提供に必要な業務委託先（Stripe 等）への提供</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">4. テナントが収集する予約者情報について</h2>
            <p>テナントが予約フォームを通じて収集する予約者の個人情報は、各テナントが独立して管理します。当サービスはテナントによる情報の取り扱いについて責任を負いません。テナントは、各自の個人情報保護方針・法令に従い適切に取り扱う義務を負います。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">5. Cookieの使用</h2>
            <p>当サービスはセッション管理のためにCookieを使用します。ブラウザの設定によりCookieを無効にすることができますが、サービスの一部機能が利用できなくなる場合があります。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">6. セキュリティ</h2>
            <p>当サービスは、個人情報への不正アクセス・漏洩・改ざんを防ぐため、SSL/TLS暗号化通信・テナント間のデータ分離・適切なアクセス制御を実施しています。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">7. 個人情報の開示・訂正・削除</h2>
            <p>ご本人からの個人情報の開示・訂正・削除のご請求は、下記お問い合わせ先までご連絡ください。本人確認の上、合理的な期間内に対応いたします。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">8. ポリシーの変更</h2>
            <p>本ポリシーは予告なく変更する場合があります。変更後は当ページに掲載した時点から効力を持ちます。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">9. お問い合わせ</h2>
            <p>個人情報の取り扱いに関するお問い合わせは、<a href="{{ route('lp') }}#contact" class="text-indigo-600 hover:underline">お問い合わせフォーム</a>からご連絡ください。</p>
        </section>

    </div>
</main>

<footer class="bg-slate-900 text-slate-400 py-8">
    <div class="max-w-4xl mx-auto px-5 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
        <span class="font-semibold text-white">YUNARI RESERVE</span>
        <div class="flex items-center gap-4">
            <a href="{{ route('terms') }}" class="hover:text-white transition">利用規約</a>
            <a href="{{ route('privacy') }}" class="hover:text-white transition">プライバシーポリシー</a>
            <a href="{{ route('lp') }}#contact" class="hover:text-white transition">お問い合わせ</a>
        </div>
        <span>© {{ date('Y') }} YUNARI RESERVE</span>
    </div>
</footer>

</body>
</html>
