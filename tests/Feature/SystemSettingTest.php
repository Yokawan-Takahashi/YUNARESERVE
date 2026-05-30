<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingTest extends TestCase
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

    /** スーパー管理者はシステム設定画面を見られる */
    public function test_superadmin_can_view_settings(): void
    {
        $this->actingAs($this->superadmin)
            ->get('/superadmin/settings')
            ->assertStatus(200)
            ->assertSee('システム設定');
    }

    /** 一般管理者はシステム設定画面に入れない */
    public function test_regular_admin_cannot_access_settings(): void
    {
        $this->actingAs($this->regularAdmin)
            ->get('/superadmin/settings')
            ->assertStatus(403);
    }

    /** 未認証はログインへ */
    public function test_guest_redirected_to_login(): void
    {
        $this->get('/superadmin/settings')->assertRedirect('/login');
    }

    /** 設定を保存でき、config へ反映される */
    public function test_superadmin_can_save_settings_and_apply_to_config(): void
    {
        $this->actingAs($this->superadmin)
            ->put('/superadmin/settings', [
                'stripe_key'               => 'pk_test_abc',
                'stripe_secret'            => 'sk_test_secret123',
                'stripe_webhook_secret'    => 'whsec_test123',
                'stripe_price_id_standard' => 'price_standard',
                'plan_standard_amount'     => 4980,
                'mail_mailer'              => 'smtp',
                'mail_host'                => 'smtp.example.com',
                'mail_port'                => 587,
                'mail_from_address'        => 'noreply@example.com',
            ])
            ->assertRedirect(route('superadmin.settings.index'))
            ->assertSessionHas('success');

        $this->assertSame('pk_test_abc', Setting::get('stripe_key'));
        $this->assertSame('sk_test_secret123', Setting::get('stripe_secret'));

        // 起動時の上書きを再現し config が変わることを確認
        Setting::applyToConfig();
        $this->assertSame('sk_test_secret123', config('cashier.secret'));
        $this->assertSame('whsec_test123', config('cashier.webhook.secret'));
        $this->assertSame('price_standard', config('plans.standard.price_id'));
        $this->assertSame(4980, config('plans.standard.amount'));
        $this->assertSame('jpy', config('cashier.currency'));
        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.example.com', config('mail.mailers.smtp.host'));
    }

    /** シークレットは暗号化されて保存される（平文で残らない） */
    public function test_secret_is_stored_encrypted(): void
    {
        Setting::put('stripe_secret', 'sk_test_plain');

        $raw = Setting::where('key', 'stripe_secret')->value('value');
        $this->assertNotSame('sk_test_plain', $raw); // 平文ではない
        $this->assertSame('sk_test_plain', Setting::get('stripe_secret')); // 復号して取得できる
    }

    /** シークレットを空欄で送信すると既存値が維持される */
    public function test_blank_secret_keeps_existing_value(): void
    {
        Setting::put('stripe_secret', 'sk_test_existing');

        $this->actingAs($this->superadmin)
            ->put('/superadmin/settings', [
                'stripe_key'    => 'pk_test_new',
                'stripe_secret' => '', // 空欄
            ])
            ->assertRedirect();

        $this->assertSame('sk_test_existing', Setting::get('stripe_secret'));
        $this->assertSame('pk_test_new', Setting::get('stripe_key'));
    }
}
