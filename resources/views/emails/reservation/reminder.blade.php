<x-mail::message>
# 明日のご予約のご案内

{{ $reservation->name }} 様

明日、以下のご予約がございます。お気をつけてお越しください。

<x-mail::panel>
**予約番号：** {{ $reservation->code }}

**イベント：** {{ $reservation->event->title }}

**日時：** {{ $reservation->slot->date->format('Y年m月d日') }} {{ substr($reservation->slot->start_time, 0, 5) }}{{ $reservation->slot->end_time ? ' 〜 ' . substr($reservation->slot->end_time, 0, 5) : '' }}

@if($reservation->slot->event->location)
**会場：** {{ $reservation->slot->event->location }}
@endif

**同伴者数：** {{ $reservation->companions }} 名
</x-mail::panel>

ご都合が悪くなった場合は、下のリンクからキャンセルをお願いします。

<x-mail::button :url="$cancelUrl" color="red">
予約をキャンセルする
</x-mail::button>

ご不明な点がございましたら、{{ $tenant->company_name }} までお問い合わせください。

{{ $tenant->company_name }}<br>
</x-mail::message>
