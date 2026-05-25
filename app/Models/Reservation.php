<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'code', 'event_id', 'slot_id',
        'name', 'kana', 'email', 'phone', 'companions',
        'status', 'memo', 'cancel_token',
    ];

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::withoutGlobalScopes()->where('code', $code)->exists());
        return $code;
    }

    public static function generateCancelToken(): string
    {
        do {
            $token = Str::random(64);
        } while (static::withoutGlobalScopes()->where('cancel_token', $token)->exists());
        return $token;
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function answers()
    {
        return $this->hasMany(ReservationAnswer::class);
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
