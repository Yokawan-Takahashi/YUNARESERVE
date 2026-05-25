<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    // role の優先順位（数字が大きいほど権限が強い）
    private const ROLE_LEVELS = [
        'viewer'     => 1,
        'staff'      => 2,
        'admin'      => 3,
        'owner'      => 4,
        'superadmin' => 99,
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        $userLevel = self::ROLE_LEVELS[$user->role] ?? 0;

        foreach ($roles as $required) {
            $requiredLevel = self::ROLE_LEVELS[$required] ?? 0;
            if ($userLevel >= $requiredLevel) {
                return $next($request);
            }
        }

        abort(403, '権限がありません');
    }
}
