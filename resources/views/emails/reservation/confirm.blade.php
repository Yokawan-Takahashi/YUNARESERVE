<x-mail::message>
# ご予約ありがとうございます

{{ $reservation->name }} 様

以下の内容でご予約を承りました。

<x-mail::panel>
**予約番号：** {{ $reservation->code }}

**イベント：** {{ $reservation->event->title }}

**日時：** {{ $reservation->slot->date->format('Y年m月d日') }} {{ substr($reservation->slot->start_time, 0, 5) }}{{ $reservation->slot->end_time ? ' 〜 ' . substr($reservation->slot->end_time, 0, 5) : '' }}

@if($reservation->slot->event->location)
**会場：** {{ $reservation->slot->event->location }}
@endif

**お名前：** {{ $reservation->name }} 様

**メールアドレス：** {{ $reservation->email }}
</x-mail::panel>

---

ご予約をキャンセルされる場合は、下のリンクからお手続きください。

<x-mail::button :url="$cancelUrl" color="red">
予約をキャンセルする
</x-mail::button>

ご不明な点がございましたら、{{ $tenant->company_name }} までお問い合わせください。

{{ $tenant->company_name }}<br>
</x-mail::message>
