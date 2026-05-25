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
        $host = $request->getHost();
        // slug.yunari-reserve.jp 形式からslugを抽出
        $slug = explode('.', $host)[0];

        $tenant = Tenant::where('slug', $slug)->where('status', 'active')->first();

        if ($tenant === null) {
            abort(404, 'テナントが見つかりません');
        }

        app()->instance('tenant', $tenant);

        return $next($request);
    }
}
