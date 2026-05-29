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

        // {slug} は AppServiceProvider の Route::bind により SubstituteBindings が
        // Tenant モデルに変換済み（404 は firstOrFail が投げる）
        $tenant = $request->route('slug');

        if (! $tenant instanceof Tenant) {
            abort(404, 'テナントが見つかりません');
        }

        app()->instance('tenant', $tenant);

        return $next($request);
    }
}
