<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug'         => 'settings-test',
            'company_name' => '設定テスト社',
            'status'       => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => '管理者',
            'email'     => 'admin@settings-test.com',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
        ]);
    }

    /** 設定ページが表示される */
    public function test_settings_index_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSee('設定テスト社');
    }

    /** 会社名を更新できる */
    public function test_can_update_company_name(): void
    {
        $this->actingAs($this->admin)->put('/admin/settings', [
            'company_name' => '新しい会社名',
        ]);

        $this->assertEquals('新しい会社名', $this->tenant->fresh()->company_name);
    }

    /** 通知メールを更新できる */
    public function test_can_update_notify_email(): void
    {
        $this->actingAs($this->admin)->put('/admin/settings', [
            'company_name' => '設定テスト社',
            'notify_email' => 'notify@example.com',
        ]);

        $this->assertEquals('notify@example.com', $this->tenant->fresh()->notify_email);
    }

    /** ブランドカラーを更新できる */
    public function test_can_update_brand_color(): void
    {
        $this->actingAs($this->admin)->put('/admin/settings', [
            'company_name' => '設定テスト社',
            'color'        => '#FF5733',
        ]);

        $this->assertEquals('#FF5733', $this->tenant->fresh()->color);
    }

    /** 不正なカラー値はバリデーションエラー */
    public function test_invalid_color_fails_validation(): void
    {
        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'company_name' => '設定テスト社',
            'color'        => 'not-a-color',
        ]);

        $response->assertSessionHasErrors('color');
    }

    /** ロゴをアップロードできる */
    public function test_can_upload_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.png', 200, 200);

        $this->actingAs($this->admin)->put('/admin/settings', [
            'company_name' => '設定テスト社',
            'logo'         => $file,
        ]);

        $tenant = $this->tenant->fresh();
        $this->assertNotNull($tenant->logo_path);
        Storage::disk('public')->assertExists($tenant->logo_path);
    }

    /** 会社名は必須 */
    public function test_company_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'company_name' => '',
        ]);

        $response->assertSessionHasErrors('company_name');
    }
}
