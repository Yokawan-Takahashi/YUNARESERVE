<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $tenant = app('tenant');
                if ($tenant !== null) {
                    $model->tenant_id = $tenant->id;
                }
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
