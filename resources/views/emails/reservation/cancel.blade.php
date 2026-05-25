<x-mail::message>
# キャンセルが完了しました

{{ $reservation->name }} 様

以下の予約をキャンセルいたしました。

<x-mail::panel>
**予約番号：** {{ $reservation->code }}

**イベント：** {{ $reservation->event->title }}

**日時：** {{ $reservation->slot->date->format('Y年m月d日') }} {{ substr($reservation->slot->start_time, 0, 5) }}{{ $reservation->slot->end_time ? ' 〜 ' . substr($reservation->slot->end_time, 0, 5) : '' }}

**キャンセル日時：** {{ $reservation->updated_at->format('Y/m/d H:i') }}
</x-mail::panel>

ご利用いただきありがとうございました。またのご予約をお待ちしております。

ご不明な点がございましたら、{{ $tenant->company_name }} までお問い合わせください。

{{ $tenant->company_name }}<br>
</x-mail::message>
