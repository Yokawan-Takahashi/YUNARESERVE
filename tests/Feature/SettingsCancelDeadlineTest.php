<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsCancelDeadlineTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create(['slug' => 'settings-deadline', 'company_name' => '設定テスト社', 'status' => 'active']);
        app()->instance('tenant', $this->tenant);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id, 'name' => '管理者',
            'email' => 'admin@settings-deadline.com', 'password' => bcrypt('p'), 'role' => 'admin',
        ]);
    }

    public function test_admin_can_set_cancel_deadline(): void
    {
        $this->actingAs($this->admin)->put(route('admin.settings.update'), [
            'company_name'         => '設定テスト社',
            'cancel_deadline_days' => 3,
            'privacy_policy_url'   => 'https://example.com/privacy',
        ]);

        $updated = $this->tenant->fresh();
        $this->assertEquals(3, $updated->cancel_deadline_days);
        $this->assertEquals('https://example.com/privacy', $updated->privacy_policy_url);
    }

    public function test_cancel_deadline_can_be_null(): void
    {
        $this->tenant->update(['cancel_deadline_days' => 5]);

        // フォームで空欄にして送信（空文字 → null）
        $this->actingAs($this->admin)->put(route('admin.settings.update'), [
            'company_name'         => '設定テスト社',
            'cancel_deadline_days' => '',
        ]);

        $this->assertNull($this->tenant->fresh()->cancel_deadline_days);
    }

    public function test_invalid_privacy_url_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.settings.update'), [
            'company_name'       => '設定テスト社',
            'privacy_policy_url' => 'not-a-url',
        ]);

        $response->assertSessionHasErrors('privacy_policy_url');
    }
}
