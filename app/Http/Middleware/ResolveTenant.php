<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // テスト等で既にテナントインスタンスがセットされていれば解決済みとみなす
        if (app('tenant') !== null) {
            return $next($request);
        }

        $host = $request->getHost();
        $slug = explode('.', $host)[0];

        // ローカル開発用: APP_TENANT_SLUG が設定されていればそれを使う
        if (app()->environment('local', 'testing') && $envSlug = config('app.tenant_slug')) {
            $slug = $envSlug;
        }

        $tenant = Tenant::where('slug', $slug)->where('status', 'active')->first();

        if ($tenant === null) {
            abort(404, 'テナントが見つかりません');
        }

        app()->instance('tenant', $tenant);

        return $next($request);
    }
}
