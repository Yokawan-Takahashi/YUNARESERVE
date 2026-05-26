<?php

namespace App\Providers;

use App\Listeners\HandleStripeWebhook;
use App\Models\Tenant;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;

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
        Cashier::useCustomerModel(Tenant::class);
        Paginator::useTailwind();
        Event::listen(WebhookReceived::class, HandleStripeWebhook::class);
    }
}
