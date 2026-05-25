<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'category_id', 'label', 'type', 'options', 'required', 'hidden', 'sort',
    ];

    protected $casts = [
        'options'  => 'array',
        'required' => 'boolean',
        'hidden'   => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
