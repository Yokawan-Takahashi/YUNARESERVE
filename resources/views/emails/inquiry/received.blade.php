@component('mail::message')
# お問い合わせが届きました

YUNARI RESERVE のランディングページより新しいお問い合わせが届きました。

@component('mail::panel')
**会社名・屋号：** {{ $inquiry->company_name }}

**担当者名：** {{ $inquiry->contact_name }}

**メールアドレス：** {{ $inquiry->email }}

**電話番号：** {{ $inquiry->phone ?? '未記入' }}

**業種：** {{ $inquiry->industry ?? '未記入' }}

**お問い合わせ内容：**
{{ $inquiry->message ?: '（内容なし）' }}

**受信日時：** {{ $inquiry->created_at->format('Y年m月d日 H:i') }}
@endcomponent

@component('mail::button', ['url' => url('/superadmin/inquiries'), 'color' => 'primary'])
管理画面で確認する
@endcomponent

Thanks,<br>
YUNARI RESERVE システム
@endcomponent
