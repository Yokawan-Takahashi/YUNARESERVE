<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'slug',
        'company_name',
        'industry',
        'logo_path',
        'color',
        'notify_email',
        'status',
        'features',
        'stripe_customer_id',
        'stripe_subscription_id',
        'plan',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasFeature(string $feature): bool
    {
        return (bool) ($this->features[$feature] ?? false);
    }
}
