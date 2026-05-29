<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantEditTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create(['slug' => 'edit-tenant', 'company_name' => '編集テスト社', 'status' => 'active']);

        $superadminTenant = Tenant::create(['slug' => 'operator', 'company_name' => 'OPERATOR', 'status' => 'active']);
        app()->instance('tenant', $superadminTenant);

        $this->superadmin = User::create([
            'tenant_id' => $superadminTenant->id, 'name' => '運営者',
            'email' => 'op@yunari.com', 'password' => bcrypt('p'), 'role' => 'superadmin',
        ]);
    }

    public function test_superadmin_can_access_tenant_edit(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get("/superadmin/tenants/{$this->tenant->slug}/edit");
        $response->assertStatus(200);
        $response->assertSee('編集テスト社');
    }

    public function test_superadmin_can_update_tenant(): void
    {
        $this->actingAs($this->superadmin)
            ->put("/superadmin/tenants/{$this->tenant->slug}", [
                'company_name' => '更新後会社名',
                'status'       => 'active',
                'notify_email' => 'notify@edit-tenant.com',
                'industry'     => 'その他',
            ]);

        $updated = $this->tenant->fresh();
        $this->assertEquals('更新後会社名', $updated->company_name);
        $this->assertEquals('notify@edit-tenant.com', $updated->notify_email);
    }

    public function test_tenant_edit_shows_slug_readonly(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get("/superadmin/tenants/{$this->tenant->slug}/edit");
        $response->assertSee('edit-tenant');
    }

    public function test_non_superadmin_cannot_edit_tenant(): void
    {
        $adminTenant = Tenant::create(['slug' => 'other-admin', 'company_name' => '別テナント', 'status' => 'active']);
        $admin = User::create([
            'tenant_id' => $adminTenant->id, 'name' => '管理者',
            'email' => 'admin@other.com', 'password' => bcrypt('p'), 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->get("/superadmin/tenants/{$this->tenant->slug}/edit");
        $response->assertStatus(403);
    }
}
