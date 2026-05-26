<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $owner;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug' => 'staff-test', 'company_name' => 'スタッフテスト社', 'status' => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->owner = User::create([
            'tenant_id' => $this->tenant->id, 'name' => 'オーナー',
            'email' => 'owner@staff-test.com', 'password' => bcrypt('password'), 'role' => 'owner',
        ]);
        $this->admin = User::create([
            'tenant_id' => $this->tenant->id, 'name' => '管理者',
            'email' => 'admin@staff-test.com', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);
    }

    public function test_staff_index_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/staff');
        $response->assertStatus(200);
        $response->assertSee('オーナー');
        $response->assertSee('管理者');
    }

    public function test_admin_can_add_staff(): void
    {
        $this->actingAs($this->admin)->post('/admin/staff', [
            'name' => '新スタッフ', 'email' => 'new@staff-test.com',
            'password' => 'password123', 'role' => 'staff',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'new@staff-test.com', 'role' => 'staff', 'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_admin_can_update_staff_role(): void
    {
        $staff = User::create([
            'tenant_id' => $this->tenant->id, 'name' => 'スタッフ',
            'email' => 'staff@staff-test.com', 'password' => bcrypt('p'), 'role' => 'staff',
        ]);

        $this->actingAs($this->admin)->patch("/admin/staff/{$staff->id}/role", ['role' => 'viewer']);

        $this->assertEquals('viewer', $staff->fresh()->role);
    }

    public function test_owner_cannot_be_deleted_or_role_changed(): void
    {
        $response = $this->actingAs($this->admin)->delete("/admin/staff/{$this->owner->id}");
        $response->assertStatus(403);
    }

    public function test_staff_cannot_be_deleted_from_other_tenant(): void
    {
        $other = Tenant::create(['slug' => 'other', 'company_name' => '別テナント', 'status' => 'active']);
        $otherUser = User::create([
            'tenant_id' => $other->id, 'name' => '他テナントユーザー',
            'email' => 'other@other.com', 'password' => bcrypt('p'), 'role' => 'staff',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/staff/{$otherUser->id}");
        $response->assertStatus(403);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/staff', [
            'name' => '重複', 'email' => 'admin@staff-test.com',
            'password' => 'password123', 'role' => 'staff',
        ]);
        $response->assertSessionHasErrors('email');
    }
}
