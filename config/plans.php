<?php

/**
 * YUNARI RESERVE — 料金プラン定義
 *
 * 料金変更: amount を変更して Stripe ダッシュボードの対応 Price を更新し、
 *           price_id を新しい Price ID に差し替えるだけで反映される。
 *
 * プラン追加: 配列にエントリを追加して .env に STRIPE_PRICE_ID_xxx を追記する。
 */
return [

    'standard' => [
        'name'        => 'スタンダード',
        'description' => 'スタート向け。基本機能すべて利用可能。',
        'amount'      => 4980,     // 円/月（表示用。実際の請求は Stripe 側で管理）
        'currency'    => 'jpy',
        'interval'    => 'month',
        'price_id'    => env('STRIPE_PRICE_ID_STANDARD', env('STRIPE_PRICE_ID')),
        'features'    => ['イベント無制限', '予約管理', 'メール通知', 'CSV出力'],
    ],

    'premium' => [
        'name'        => 'プレミアム',
        'description' => '複数スタッフ・高度なカスタマイズが必要な店舗向け。',
        'amount'      => 9800,
        'currency'    => 'jpy',
        'interval'    => 'month',
        'price_id'    => env('STRIPE_PRICE_ID_PREMIUM'),
        'features'    => ['スタンダードの全機能', 'スタッフ無制限', '優先サポート', 'カスタムドメイン（将来）'],
    ],

];
