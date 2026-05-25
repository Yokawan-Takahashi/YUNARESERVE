<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'category_id', 'title', 'description', 'location',
        'target', 'fee', 'items', 'notes', 'image_path', 'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class)->orderBy('date')->orderBy('start_time');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function formFields()
    {
        return $this->hasMany(FormField::class, 'category_id', 'category_id')
                    ->orderBy('sort');
    }
}
