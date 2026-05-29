<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $operatorTenant;
    private User $superadmin;
    private User $regularAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operatorTenant = Tenant::create([
            'slug'         => 'operator',
            'company_name' => '運営テナント',
            'status'       => 'active',
        ]);

        $this->superadmin = User::create([
            'tenant_id' => $this->operatorTenant->id,
            'name'      => 'スーパー管理者',
            'email'     => 'super@operator.com',
            'password'  => bcrypt('password'),
            'role'      => 'superadmin',
        ]);

        $this->regularAdmin = User::create([
            'tenant_id' => $this->operatorTenant->id,
            'name'      => '一般管理者',
            'email'     => 'admin@operator.com',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
        ]);
    }

    /** スーパー管理者はテナント一覧を見られる */
    public function test_superadmin_can_view_tenants(): void
    {
        $response = $this->actingAs($this->superadmin)->get('/superadmin/tenants');
        $response->assertStatus(200);
        $response->assertSee('運営テナント');
    }

    /** 一般管理者はスーパー管理画面に入れない */
    public function test_regular_admin_cannot_access_superadmin(): void
    {
        $response = $this->actingAs($this->regularAdmin)->get('/superadmin/tenants');
        $response->assertStatus(403);
    }

    /** 未認証はスーパー管理画面に入れない */
    public function test_guest_cannot_access_superadmin(): void
    {
        $response = $this->get('/superadmin/tenants');
        $response->assertRedirect('/login');
    }

    /** テナント作成フォームが表示される */
    public function test_superadmin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->superadmin)->get('/superadmin/tenants/create');
        $response->assertStatus(200);
        $response->assertSee('テナント新規作成');
    }

    /** テナントを作成できる */
    public function test_superadmin_can_create_tenant(): void
    {
        $response = $this->actingAs($this->superadmin)->post('/superadmin/tenants', [
            'company_name'   => '新規テスト社',
            'slug'           => 'new-test-company',
            'industry'       => 'その他',
            'owner_name'     => 'オーナー太郎',
            'owner_email'    => 'owner@new-test.com',
            'owner_password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', ['slug' => 'new-test-company']);
        $this->assertDatabaseHas('users', ['email' => 'owner@new-test.com', 'role' => 'owner']);
    }

    /** 重複スラッグはバリデーションエラー */
    public function test_duplicate_slug_fails_validation(): void
    {
        $response = $this->actingAs($this->superadmin)->post('/superadmin/tenants', [
            'company_name'   => '重複社',
            'slug'           => 'operator',
            'owner_name'     => '太郎',
            'owner_email'    => 'dup@test.com',
            'owner_password' => 'password123',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    /** テナントのステータスをトグルできる */
    public function test_superadmin_can_toggle_tenant_status(): void
    {
        $tenant = Tenant::create([
            'slug'         => 'toggle-test',
            'company_name' => 'トグルテスト社',
            'status'       => 'active',
        ]);

        $this->actingAs($this->superadmin)
            ->patch('/superadmin/tenants/' . $tenant->slug . '/toggle');

        $this->assertEquals('suspended', $tenant->fresh()->status);
    }

    /** suspendedテナントをactiveに戻せる */
    public function test_superadmin_can_reactivate_tenant(): void
    {
        $tenant = Tenant::create([
            'slug'         => 'reactivate-test',
            'company_name' => '再有効化テスト社',
            'status'       => 'suspended',
        ]);

        $this->actingAs($this->superadmin)
            ->patch('/superadmin/tenants/' . $tenant->slug . '/toggle');

        $this->assertEquals('active', $tenant->fresh()->status);
    }
}
