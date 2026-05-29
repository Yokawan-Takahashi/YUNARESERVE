<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'company_name', 'contact_name', 'email', 'phone', 'industry', 'message', 'contacted_at',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
    ];
}
