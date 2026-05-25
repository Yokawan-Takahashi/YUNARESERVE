<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // テナント未解決時のデフォルト（CLI・superadmin文脈でnull返却）
        // instance(null) は isset で検知されないため bind クロージャで登録
        $this->app->bind('tenant', fn() => null);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
