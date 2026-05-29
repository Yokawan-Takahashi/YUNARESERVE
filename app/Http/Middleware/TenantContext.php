<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // superadmin はテナント管理画面ではなく運営コンソールへ
        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        $currentTenant = app()->bound('tenant') ? app('tenant') : null;

        if ($currentTenant === null) {
            // ローカル開発 / テナント未解決: ログインユーザーのテナントから解決
            if ($user->tenant_id) {
                app()->instance('tenant', $user->tenant);
            }
        } elseif ($user->role !== 'superadmin') {
            // 本番サブドメイン: ユーザーのテナントとサブドメインのテナントが一致するか検証
            if ($user->tenant_id !== $currentTenant->id) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->withErrors(['email' => 'このテナントへのアクセス権限がありません。']);
            }
        }

        // 停止中テナントへのアクセスをブロック（superadmin を除く）
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        if ($tenant && $tenant->status !== 'active' && $user->role !== 'superadmin') {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'このテナントは現在停止中です。管理者にお問い合わせください。']);
        }

        return $next($request);
    }
}
