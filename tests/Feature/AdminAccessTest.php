<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug' => 'demo',
            'company_name' => 'デモ社',
            'status' => 'active',
        ]);
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'tenant_id' => $this->tenant->id,
            'name' => "テスト{$role}",
            'email' => "{$role}@example.com",
            'password' => bcrypt('password'),
            'role' => $role,
        ]);
    }

    /** 未ログインで /admin はログインへリダイレクト */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    /** viewer以上はダッシュボードにアクセスできる */
    public function test_viewer_can_access_admin(): void
    {
        $user = $this->makeUser('viewer');
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    /** staff はダッシュボードにアクセスできる */
    public function test_staff_can_access_admin(): void
    {
        $user = $this->makeUser('staff');
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    /** admin はダッシュボードにアクセスできる */
    public function test_admin_can_access_admin(): void
    {
        $user = $this->makeUser('admin');
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    /** owner はダッシュボードにアクセスできる */
    public function test_owner_can_access_admin(): void
    {
        $user = $this->makeUser('owner');
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    /** staff はadmin限定ルート（スタッフ管理）に 403 */
    public function test_staff_cannot_access_admin_only_route(): void
    {
        $staff = $this->makeUser('staff');
        app()->instance('tenant', $this->tenant);
        $response = $this->actingAs($staff)->get('/admin/staff');
        $response->assertStatus(403);
    }

    /** admin はadmin限定ルート（スタッフ管理）に 200 */
    public function test_admin_can_access_admin_only_route(): void
    {
        $admin = $this->makeUser('admin');
        app()->instance('tenant', $this->tenant);
        $response = $this->actingAs($admin)->get('/admin/staff');
        $response->assertStatus(200);
    }

    /** owner はadmin限定ルート（スタッフ管理）に 200（上位roleは下位をカバー） */
    public function test_owner_can_access_admin_only_route(): void
    {
        $owner = $this->makeUser('owner');
        app()->instance('tenant', $this->tenant);
        $response = $this->actingAs($owner)->get('/admin/staff');
        $response->assertStatus(200);
    }
}
