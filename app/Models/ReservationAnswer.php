<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationAnswer extends Model
{
    protected $fillable = ['reservation_id', 'field_label', 'answer'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
