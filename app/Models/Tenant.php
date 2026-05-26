<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    use Billable;

    protected $fillable = [
        'slug',
        'company_name',
        'industry',
        'logo_path',
        'color',
        'notify_email',
        'cancel_deadline_days',
        'privacy_policy_url',
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

    public function events()
    {
        return $this->hasMany(\App\Models\Event::class);
    }

    public function reservations()
    {
        return $this->hasMany(\App\Models\Reservation::class);
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
