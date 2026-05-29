<x-mail::message>
# YUNARI RESERVE へようこそ

{{ $user->name }} 様

YUNARI RESERVE のご利用を開始する準備が整いました。
以下のログイン情報でダッシュボードにアクセスしてください。

<x-mail::panel>
**テナント名：** {{ $tenant->company_name }}

**ログインURL：** {{ url('/login') }}

**メールアドレス：** {{ $user->email }}

**初期パスワード：** {{ $password }}
</x-mail::panel>

ログイン後、まず以下の手順でサービスを開始できます。

1. **ダッシュボード** → 左メニューから「プラン・課金」でサブスクリプションを開始
2. **テナント設定** → ロゴとブランドカラーを設定
3. **イベント作成** → 予約を受け付けるイベントと枠を作成
4. **公開URL確認** → `{{ url($tenant->slug) }}` でお客様向けページを確認

<x-mail::button :url="url('/login')">
ダッシュボードにログインする
</x-mail::button>

ご不明な点がございましたら、このメールへの返信またはサポートまでお問い合わせください。

YUNARI RESERVE 運営チーム<br>
{{ config('app.url') }}
</x-mail::message>
