<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $fillable = ['tenant_id', 'reservation_id', 'type', 'to', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
