<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YUNARI RESERVE — 予約システムを、もっとシンプルに</title>
    <meta name="description" content="飲食・サロン・スクールなどあらゆる業種に対応。シンプルで使いやすいSaaS型予約管理システム。月額4,980円から始められます。">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-hero {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 70%, #6366f1 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #a5b4fc 0%, #e879f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .card-hover {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(99, 102, 241, 0.25);
        }
        .feature-icon {
            background: linear-gradient(135deg, #eef2ff 0%, #ede9fe 100%);
        }
        .step-line::after {
            content: '';
            position: absolute;
            top: 2rem;
            left: calc(50% + 3rem);
            width: calc(100% - 6rem);
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #a855f7);
        }
        @media (max-width: 768px) {
            .step-line::after { display: none; }
        }
        .pricing-card {
            background: linear-gradient(135deg, #312e81 0%, #4338ca 100%);
            position: relative;
            overflow: hidden;
        }
        .pricing-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .nav-blur {
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.9);
        }
        .input-focus {
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-focus:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
            outline: none;
        }
        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">

{{-- ナビゲーション --}}
<nav class="nav-blur fixed top-0 left-0 right-0 z-50 border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-5 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="font-black text-slate-900 tracking-tight">YUNARI RESERVE</span>
        </div>
        <div class="hidden md:flex items-center gap-6 text-sm text-slate-600">
            <a href="#features" class="hover:text-indigo-600 transition">機能</a>
            <a href="#howto" class="hover:text-indigo-600 transition">使い方</a>
            <a href="#pricing" class="hover:text-indigo-600 transition">料金</a>
            <a href="#contact" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                無料で試す
            </a>
        </div>
        <a href="#contact" class="md:hidden px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium">
            お問い合わせ
        </a>
    </div>
</nav>

{{-- ヒーロー --}}
<section class="gradient-hero relative pt-32 pb-24 md:pt-40 md:pb-32 overflow-hidden">
    {{-- 装飾 blobs --}}
    <div class="hero-blob w-96 h-96 bg-purple-400 top-10 -left-20"></div>
    <div class="hero-blob w-72 h-72 bg-indigo-300 bottom-0 right-10"></div>
    <div class="hero-blob w-48 h-48 bg-pink-400 top-32 right-1/4"></div>

    <div class="max-w-5xl mx-auto px-5 text-center relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 text-sm text-indigo-200 mb-8">
            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
            サービス提供中
        </div>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white leading-[1.1] tracking-tight mb-6">
            予約システムを、<br>
            <span class="gradient-text">もっとシンプルに。</span>
        </h1>
        <p class="text-lg md:text-xl text-indigo-200 max-w-2xl mx-auto leading-relaxed mb-10">
            飲食・サロン・スクール・セミナーなど、あらゆる業種の予約受付を一元管理。<br class="hidden md:block">
            初期費用ゼロ、最短1日で公開できるSaaS型予約管理システム。
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#contact" class="w-full sm:w-auto px-8 py-4 bg-white text-indigo-700 font-bold rounded-xl hover:bg-indigo-50 transition shadow-xl shadow-indigo-900/30 text-lg">
                無料で試してみる
            </a>
            <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-white/10 border border-white/30 text-white font-semibold rounded-xl hover:bg-white/20 transition text-lg">
                機能を見る →
            </a>
        </div>

        {{-- 実績バッジ --}}
        <div class="mt-16 flex flex-wrap items-center justify-center gap-8 text-indigo-200">
            <div class="text-center">
                <div class="text-3xl font-black text-white">¥4,980</div>
                <div class="text-xs mt-1">月額（税込）</div>
            </div>
            <div class="w-px h-10 bg-white/20 hidden sm:block"></div>
            <div class="text-center">
                <div class="text-3xl font-black text-white">1日</div>
                <div class="text-xs mt-1">最短公開</div>
            </div>
            <div class="w-px h-10 bg-white/20 hidden sm:block"></div>
            <div class="text-center">
                <div class="text-3xl font-black text-white">∞</div>
                <div class="text-xs mt-1">予約件数無制限</div>
            </div>
            <div class="w-px h-10 bg-white/20 hidden sm:block"></div>
            <div class="text-center">
                <div class="text-3xl font-black text-white">24/7</div>
                <div class="text-xs mt-1">オンライン受付</div>
            </div>
        </div>
    </div>

    {{-- ウェーブ --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0 80L1440 80L1440 30C1200 70 960 10 720 40C480 70 240 0 0 30L0 80Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- ロゴ帯（信頼感） --}}
<section class="bg-slate-50 py-10 border-b border-slate-100">
    <div class="max-w-5xl mx-auto px-5 text-center">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-6">対応業種</p>
        <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-slate-400">
            @foreach(['飲食・カフェ', 'ヘアサロン', 'エステ・整体', 'ヨガスタジオ', 'スクール・塾', 'セミナー・研修', 'レンタルスペース', 'その他あらゆる業種'] as $industry)
            <span class="text-sm font-medium">{{ $industry }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- 機能一覧 --}}
<section id="features" class="py-24 bg-white">
    <div class="max-w-6xl mx-auto px-5">
        <div class="text-center mb-16">
            <p class="text-indigo-600 font-semibold text-sm uppercase tracking-widest mb-3">Features</p>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4">必要な機能が、すべて揃っている</h2>
            <p class="text-slate-500 max-w-xl mx-auto">面倒な設定なし。予約受付に必要なすべての機能をすぐに使い始められます。</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'イベント・枠管理', 'desc' => 'イベントや予約枠を直感的に作成・管理。定員・締切・カテゴリの細かな設定も自由自在です。'],
                ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => '自動メール通知', 'desc' => '予約完了・前日リマインド・キャンセル確認メールを自動送信。参加者への連絡を完全自動化。'],
                ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'CSV出力', 'desc' => '予約者情報をUTF-8 BOM付きCSVでエクスポート。Excelでそのまま開いて活用できます。'],
                ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'スタッフ権限管理', 'desc' => 'オーナー・管理者・スタッフ・閲覧者の4段階ロール。必要な権限だけを付与できます。'],
                ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title' => 'Stripe月額課金', 'desc' => '安全なクレジットカード決済。サブスクリプション管理・請求書発行・Webhookによる自動処理に対応。'],
                ['icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'title' => 'カスタム入力項目', 'desc' => '予約フォームに独自の入力項目を追加可能。業種に合わせて自由にフォームをカスタマイズ。'],
            ] as $feature)
            <div class="card-hover bg-white border border-slate-100 rounded-2xl p-7 shadow-sm">
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $feature['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">{{ $feature['title'] }}</h3>
                <p class="text-slate-500 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 使い方 --}}
<section id="howto" class="py-24 bg-slate-50">
    <div class="max-w-5xl mx-auto px-5">
        <div class="text-center mb-16">
            <p class="text-indigo-600 font-semibold text-sm uppercase tracking-widest mb-3">How it works</p>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4">最短1日で予約受付を開始</h2>
            <p class="text-slate-500">複雑な設定は不要です。3ステップで公開完了。</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            @foreach([
                ['num' => '01', 'title' => 'お申し込み', 'desc' => 'このページのフォームからお申し込みください。運営担当より1営業日以内にご連絡します。'],
                ['num' => '02', 'title' => 'セットアップ', 'desc' => 'ロゴ・ブランドカラー・イベント枠を設定。直感的なUIで迷わず進められます。'],
                ['num' => '03', 'title' => '公開・運用', 'desc' => '専用URLをシェアするだけで予約受付スタート。あとは管理画面で確認するだけ。'],
            ] as $i => $step)
            <div class="relative {{ $i < 2 ? 'step-line' : '' }} text-center">
                <div class="inline-flex w-16 h-16 rounded-2xl bg-indigo-600 items-center justify-center text-white font-black text-xl mb-5 shadow-lg shadow-indigo-200">
                    {{ $step['num'] }}
                </div>
                <h3 class="font-bold text-slate-900 text-xl mb-3">{{ $step['title'] }}</h3>
                <p class="text-slate-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- スクリーンショット的なUI preview --}}
<section class="py-24 bg-white overflow-hidden">
    <div class="max-w-6xl mx-auto px-5">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-indigo-600 font-semibold text-sm uppercase tracking-widest mb-3">Dashboard</p>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-5 leading-tight">
                    必要な情報が、<br>ひと目でわかる
                </h2>
                <p class="text-slate-500 leading-relaxed mb-8">
                    今月の予約数・直近の予約・ステータス別集計をダッシュボードに集約。
                    スタッフが迷わず使える、シンプルで明快な管理画面です。
                </p>
                <ul class="space-y-3">
                    @foreach(['予約ステータス一括管理（確定・キャンセル・出席）', 'スタッフメモ・内部コメント機能', '予約者情報の一覧・詳細表示', '確認メール再送信'] as $item)
                    <li class="flex items-center gap-3 text-slate-700">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="relative">
                <div class="bg-slate-900 rounded-2xl p-4 shadow-2xl shadow-slate-900/30">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        <div class="flex-1 ml-2 h-5 bg-slate-700 rounded text-xs text-slate-400 flex items-center px-3">
                            reserve.yoka-wan.co.jp/admin
                        </div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-5 space-y-3">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-bold text-slate-800">ダッシュボード</span>
                            <span class="text-xs text-slate-400">2026年5月</span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([['今月の予約', '32件', 'bg-indigo-50 text-indigo-700'], ['確定済み', '28件', 'bg-emerald-50 text-emerald-700'], ['キャンセル', '4件', 'bg-rose-50 text-rose-700']] as $stat)
                            <div class="rounded-lg p-3 {{ $stat[2] }}">
                                <div class="text-xs font-medium opacity-75 mb-1">{{ $stat[0] }}</div>
                                <div class="text-lg font-black">{{ $stat[1] }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="bg-white rounded-lg p-3 space-y-2 mt-2">
                            @foreach([['山田 太郎', '春の特別体験会', '確定', 'bg-emerald-100 text-emerald-700'], ['佐藤 花子', 'ヨガ入門クラス', '確定', 'bg-emerald-100 text-emerald-700'], ['鈴木 一郎', '初回カウンセリング', '未確定', 'bg-amber-100 text-amber-700']] as $row)
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-700">{{ $row[0] }}</span>
                                <span class="text-slate-500 flex-1 mx-3 truncate">{{ $row[1] }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $row[3] }}">{{ $row[2] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- 装飾 --}}
                <div class="absolute -z-10 -bottom-6 -right-6 w-48 h-48 bg-indigo-100 rounded-full blur-2xl opacity-60"></div>
            </div>
        </div>
    </div>
</section>

{{-- 料金 --}}
<section id="pricing" class="py-24 bg-slate-50">
    <div class="max-w-4xl mx-auto px-5">
        <div class="text-center mb-16">
            <p class="text-indigo-600 font-semibold text-sm uppercase tracking-widest mb-3">Pricing</p>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4">シンプルな料金体系</h2>
            <p class="text-slate-500">初期費用・契約期間の縛りなし。いつでも解約できます。</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            {{-- スタンダード --}}
            <div class="pricing-card rounded-3xl p-8 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 bg-white/20 rounded-full px-3 py-1 text-xs font-medium mb-5">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                        人気プラン
                    </div>
                    <h3 class="text-2xl font-black mb-1">スタンダード</h3>
                    <p class="text-indigo-200 text-sm mb-6">個人・小規模向け</p>
                    <div class="flex items-end gap-1 mb-8">
                        <span class="text-5xl font-black">¥4,980</span>
                        <span class="text-indigo-200 mb-1">/月（税込）</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        @foreach(['予約件数・イベント数無制限', 'スタッフアカウント5名まで', 'カスタム入力項目', 'CSV出力', '自動メール通知', 'ブランドカラー・ロゴ設定', 'Stripe決済連携'] as $item)
                        <li class="flex items-center gap-3 text-sm">
                            <svg class="w-4 h-4 text-indigo-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="#contact" class="block w-full py-3.5 bg-white text-indigo-700 font-bold rounded-xl text-center hover:bg-indigo-50 transition">
                        無料で始める →
                    </a>
                </div>
            </div>

            {{-- エンタープライズ --}}
            <div class="bg-white border-2 border-slate-100 rounded-3xl p-8 relative">
                <div class="inline-flex items-center gap-2 bg-slate-100 rounded-full px-3 py-1 text-xs font-medium text-slate-500 mb-5">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                    法人・大規模向け
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-1">エンタープライズ</h3>
                <p class="text-slate-500 text-sm mb-6">カスタム要件対応</p>
                <div class="flex items-end gap-1 mb-8">
                    <span class="text-4xl font-black text-slate-900">要相談</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach(['スタンダードの全機能', 'スタッフアカウント無制限', 'カスタム機能開発', 'データ移行サポート', '優先サポート対応', 'SLA保証'] as $item)
                    <li class="flex items-center gap-3 text-sm text-slate-600">
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="#contact" class="block w-full py-3.5 border-2 border-indigo-600 text-indigo-600 font-bold rounded-xl text-center hover:bg-indigo-50 transition">
                    お問い合わせ →
                </a>
            </div>
        </div>
        <p class="text-center text-xs text-slate-400 mt-8">
            初期費用・解約手数料なし。クレジットカードでの月額自動更新。Stripe決済を利用。
        </p>
    </div>
</section>

{{-- FAQ --}}
<section class="py-24 bg-white">
    <div class="max-w-3xl mx-auto px-5">
        <div class="text-center mb-12">
            <p class="text-indigo-600 font-semibold text-sm uppercase tracking-widest mb-3">FAQ</p>
            <h2 class="text-3xl font-black text-slate-900">よくある質問</h2>
        </div>
        <div class="space-y-4">
            @foreach([
                ['q' => '無料トライアルはありますか？', 'a' => 'はい。お申し込みいただいた後、設定・確認のための試用期間を設けています。詳細はお問い合わせください。'],
                ['q' => '途中で解約することはできますか？', 'a' => 'いつでも解約可能です。月単位の課金のため、解約した月末まで利用できます。違約金等は一切かかりません。'],
                ['q' => 'スマートフォンからも使えますか？', 'a' => 'はい。管理画面・予約フォームともにスマートフォン対応のレスポンシブデザインです。'],
                ['q' => '既存の予約データを移行できますか？', 'a' => 'CSVインポートによるデータ移行をサポートしています。詳しくはお問い合わせください。'],
                ['q' => 'セキュリティ面は大丈夫ですか？', 'a' => 'テナントごとのデータ分離を実装しており、他テナントのデータにアクセスできない設計です。通信はSSL/TLS暗号化、決済はStripeに準拠した安全な処理を行います。'],
            ] as $faq)
            <details class="group bg-slate-50 rounded-2xl overflow-hidden">
                <summary class="flex items-center justify-between p-6 cursor-pointer font-semibold text-slate-900 list-none">
                    {{ $faq['q'] }}
                    <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed -mt-2">
                    {{ $faq['a'] }}
                </div>
            </details>
            @endforeach
        </div>
    </div>
</section>

{{-- お問い合わせ --}}
<section id="contact" class="py-24 bg-slate-50">
    <div class="max-w-2xl mx-auto px-5">
        <div class="text-center mb-12">
            <p class="text-indigo-600 font-semibold text-sm uppercase tracking-widest mb-3">Contact</p>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4">まずはお気軽に</h2>
            <p class="text-slate-500">1営業日以内にご担当者からご連絡いたします。</p>
        </div>

        @if(session('inquiry_sent'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center mb-8">
            <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="font-bold text-emerald-800 text-lg mb-1">お問い合わせを受け付けました</h3>
            <p class="text-emerald-700 text-sm">1営業日以内にご連絡いたします。しばらくお待ちください。</p>
        </div>
        @endif

        <div class="bg-white rounded-3xl p-8 md:p-10 shadow-xl shadow-slate-100 border border-slate-100">
            <form method="POST" action="{{ route('inquiry.store') }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">会社名・屋号 <span class="text-rose-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                               class="input-focus w-full border border-slate-200 rounded-xl px-4 py-3 text-sm"
                               placeholder="株式会社〇〇">
                        @error('company_name')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">担当者名 <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_name" value="{{ old('contact_name') }}" required
                               class="input-focus w-full border border-slate-200 rounded-xl px-4 py-3 text-sm"
                               placeholder="山田 太郎">
                        @error('contact_name')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">メールアドレス <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="input-focus w-full border border-slate-200 rounded-xl px-4 py-3 text-sm"
                               placeholder="info@example.com">
                        @error('email')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">電話番号</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="input-focus w-full border border-slate-200 rounded-xl px-4 py-3 text-sm"
                               placeholder="03-0000-0000">
                        @error('phone')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">業種</label>
                    <select name="industry" class="input-focus w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white">
                        <option value="">選択してください</option>
                        @foreach(['飲食・カフェ', 'ヘアサロン', 'エステ・ネイル', '整体・マッサージ', 'ヨガ・フィットネス', 'スクール・塾', 'セミナー・研修', 'レンタルスペース', 'その他'] as $ind)
                        <option value="{{ $ind }}" {{ old('industry') === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">お問い合わせ内容</label>
                    <textarea name="message" rows="4"
                              class="input-focus w-full border border-slate-200 rounded-xl px-4 py-3 text-sm resize-none"
                              placeholder="ご質問・ご要望などをご記入ください">{{ old('message') }}</textarea>
                    @error('message')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full py-4 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition text-lg shadow-lg shadow-indigo-200">
                    送信する →
                </button>
                <p class="text-xs text-slate-400 text-center">
                    送信いただいた情報は、サービスに関するご連絡にのみ使用します。
                </p>
            </form>
        </div>
    </div>
</section>

{{-- フッター --}}
<footer class="bg-slate-900 text-slate-400 py-12">
    <div class="max-w-6xl mx-auto px-5">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="font-bold text-white">YUNARI RESERVE</span>
            </div>
            <div class="flex items-center gap-6 text-sm">
                <a href="#features" class="hover:text-white transition">機能</a>
                <a href="#pricing" class="hover:text-white transition">料金</a>
                <a href="#contact" class="hover:text-white transition">お問い合わせ</a>
                <a href="{{ route('login') }}" class="hover:text-white transition">ログイン</a>
            </div>
        </div>
        <div class="border-t border-slate-800 mt-8 pt-8 flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
            <span>© {{ date('Y') }} YUNARI RESERVE. All rights reserved.</span>
            <div class="flex items-center gap-4">
                <a href="{{ route('terms') }}" class="hover:text-white transition">利用規約</a>
                <a href="{{ route('privacy') }}" class="hover:text-white transition">プライバシーポリシー</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
