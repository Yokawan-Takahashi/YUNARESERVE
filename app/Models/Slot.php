<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = [
        'event_id', 'date', 'start_time', 'end_time', 'capacity', 'reserved_count', 'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function remainingCapacity(): int
    {
        return max(0, $this->capacity - $this->reserved_count);
    }

    public function isFull(): bool
    {
        return $this->reserved_count >= $this->capacity;
    }

    public function isAccepting(): bool
    {
        return $this->status === 'open' && !$this->isFull();
    }
}
