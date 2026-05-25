<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'scope', 'icon', 'sort', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function formFields()
    {
        return $this->hasMany(FormField::class)->orderBy('sort');
    }
}
