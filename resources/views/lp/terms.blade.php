<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>利用規約 — YUNARI RESERVE</title>
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
    <h1 class="text-3xl font-black text-slate-900 mb-2">利用規約</h1>
    <p class="text-sm text-slate-400 mb-10">最終更新日：{{ date('Y年m月d日') }}</p>

    <div class="prose prose-slate max-w-none space-y-8 text-sm leading-relaxed text-slate-700">

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第1条（適用）</h2>
            <p>本利用規約（以下「本規約」）は、YUNARI RESERVE（以下「当サービス」）の利用に関する条件を定めるものです。ご利用登録をいただいた時点で、本規約に同意したものとみなします。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第2条（サービスの内容）</h2>
            <p>当サービスは、SaaS型予約管理システムを月額課金方式で提供します。テナント（ご契約事業者）は、専用の管理画面から予約受付ページを設定・公開することができます。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第3条（利用登録）</h2>
            <p>利用登録は、当サービスが定める方法により申込を完了した時点で成立します。運営が審査の上、テナントアカウントを発行します。以下に該当する場合、登録をお断りする場合があります。</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>虚偽の情報を申告した場合</li>
                <li>過去に本規約に違反したことがある場合</li>
                <li>その他、運営が不適切と判断した場合</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第4条（料金・支払い）</h2>
            <p>利用料金は、当サービスが定めるプランに従い、Stripe を通じた月額自動課金（クレジットカード決済）にて請求します。月途中の解約による日割り返金はいたしません。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第5条（禁止事項）</h2>
            <p>テナントは以下の行為をしてはなりません。</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>法令または公序良俗に違反する行為</li>
                <li>当サービスのシステムへの不正アクセス</li>
                <li>他テナントまたは第三者への迷惑行為</li>
                <li>当サービスの運営を妨害する行為</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第6条（サービスの停止・変更）</h2>
            <p>運営は、以下の場合にサービスを停止または変更することがあります。サービスの停止・変更によって生じた損害について、運営は責任を負いません。</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>システムのメンテナンス・障害対応</li>
                <li>天災・事故など不可抗力</li>
                <li>その他、運営が必要と判断した場合</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第7条（解約）</h2>
            <p>テナントはいつでも解約の申請ができます。解約は当月末をもって有効となり、翌月以降の課金は発生しません。解約後のデータは一定期間保持の後、削除されます。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第8条（免責事項）</h2>
            <p>当サービスは現状有姿で提供され、特定目的への適合性を保証しません。テナントが本サービスを利用することにより生じた損害について、運営の故意または重大な過失による場合を除き、責任を負いません。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第9条（規約の変更）</h2>
            <p>運営は本規約を変更する場合があります。変更後の規約は、当サービス上に掲載した時点から効力を持ちます。</p>
        </section>

        <section>
            <h2 class="text-lg font-bold text-slate-800 mb-3">第10条（準拠法・管轄）</h2>
            <p>本規約は日本法に準拠します。紛争が生じた場合は、運営の所在地を管轄する裁判所を専属的合意管轄とします。</p>
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
