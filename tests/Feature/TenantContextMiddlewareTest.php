<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantContextMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_resolves_tenant_from_user_when_not_bound(): void
    {
        // テナントをバインドせずにログイン → TenantContextがユーザーのテナントをバインドする
        $tenant = Tenant::create(['slug' => 'ctx-test', 'company_name' => 'CTXテスト社', 'status' => 'active']);
        $user = User::create([
            'tenant_id' => $tenant->id, 'name' => 'スタッフ',
            'email' => 'staff@ctx-test.com', 'password' => bcrypt('p'), 'role' => 'staff',
        ]);

        // テナントをバインドしないまま admin にアクセス（ダッシュボードは200を返す）
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_suspended_tenant_access_is_blocked(): void
    {
        $tenant = Tenant::create(['slug' => 'suspended', 'company_name' => '停止テスト社', 'status' => 'suspended']);
        app()->instance('tenant', $tenant);

        $user = User::create([
            'tenant_id' => $tenant->id, 'name' => 'スタッフ',
            'email' => 'staff@suspended.com', 'password' => bcrypt('p'), 'role' => 'staff',
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect(route('login'));
    }

    public function test_superadmin_can_access_suspended_tenant(): void
    {
        $opTenant = Tenant::create(['slug' => 'operator2', 'company_name' => 'OP2', 'status' => 'active']);
        $suspendedTenant = Tenant::create(['slug' => 'susp2', 'company_name' => '停止2', 'status' => 'suspended']);
        app()->instance('tenant', $suspendedTenant);

        $superadmin = User::create([
            'tenant_id' => $opTenant->id, 'name' => '運営',
            'email' => 'op@susp2.com', 'password' => bcrypt('p'), 'role' => 'superadmin',
        ]);

        $response = $this->actingAs($superadmin)->get('/admin');
        // superadmin はブロックされない（ダッシュボードへリダイレクトまたは200）
        $this->assertNotEquals(302, $response->status() === 302 && str_contains($response->headers->get('Location', ''), 'login') ? 302 : 0);
    }

    public function test_wrong_tenant_user_is_logged_out(): void
    {
        $tenantA = Tenant::create(['slug' => 'tenant-a', 'company_name' => 'テナントA', 'status' => 'active']);
        $tenantB = Tenant::create(['slug' => 'tenant-b', 'company_name' => 'テナントB', 'status' => 'active']);

        // ユーザーはテナントAに属するが、テナントBのコンテキストでアクセス
        app()->instance('tenant', $tenantB);

        $user = User::create([
            'tenant_id' => $tenantA->id, 'name' => 'ユーザーA',
            'email' => 'user@tenant-a.com', 'password' => bcrypt('p'), 'role' => 'staff',
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect(route('login'));
    }
}
