<x-mail::message>
# 新しい予約が入りました

**{{ $reservation->event->title }}** に新規予約がありました。

<x-mail::panel>
**予約番号：** {{ $reservation->code }}

**イベント：** {{ $reservation->event->title }}

**日時：** {{ $reservation->slot->date->format('Y年m月d日') }} {{ substr($reservation->slot->start_time, 0, 5) }}{{ $reservation->slot->end_time ? ' 〜 ' . substr($reservation->slot->end_time, 0, 5) : '' }}

**予約者：** {{ $reservation->name }} 様{{ $reservation->kana ? '（' . $reservation->kana . '）' : '' }}

**メールアドレス：** {{ $reservation->email }}

**電話番号：** {{ $reservation->phone ?: '未記入' }}

**同伴者数：** {{ $reservation->companions }} 名

**受付日時：** {{ $reservation->created_at->format('Y/m/d H:i') }}
</x-mail::panel>

<x-mail::button :url="route('admin.dashboard')">
管理画面で確認する
</x-mail::button>

{{ $tenant->company_name }} 管理システム<br>
</x-mail::message>
