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

    /** staff 未満のroleがないが、roleレベル検証：adminのみ許可のルートにstaffは403 */
    public function test_staff_cannot_access_admin_only_route(): void
    {
        // admin以上限定のルートをテスト用に登録
        \Illuminate\Support\Facades\Route::get('/test-admin-only', function () {
            return 'ok';
        })->middleware(['auth', 'role:admin']);

        $staff = $this->makeUser('staff');
        $response = $this->actingAs($staff)->get('/test-admin-only');
        $response->assertStatus(403);
    }

    /** adminはadmin限定ルートにアクセスできる */
    public function test_admin_can_access_admin_only_route(): void
    {
        \Illuminate\Support\Facades\Route::get('/test-admin-only2', function () {
            return 'ok';
        })->middleware(['auth', 'role:admin']);

        $admin = $this->makeUser('admin');
        $response = $this->actingAs($admin)->get('/test-admin-only2');
        $response->assertStatus(200);
    }

    /** ownerはadmin限定ルートにアクセスできる（上位roleは下位をカバー） */
    public function test_owner_can_access_admin_only_route(): void
    {
        \Illuminate\Support\Facades\Route::get('/test-admin-only3', function () {
            return 'ok';
        })->middleware(['auth', 'role:admin']);

        $owner = $this->makeUser('owner');
        $response = $this->actingAs($owner)->get('/test-admin-only3');
        $response->assertStatus(200);
    }
}
