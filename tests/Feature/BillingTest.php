<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug'         => 'billing-test',
            'company_name' => '課金テスト社',
            'status'       => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'オーナー',
            'email'     => 'owner@billing-test.com',
            'password'  => bcrypt('password'),
            'role'      => 'owner',
        ]);
    }

    /** 課金ページが表示される */
    public function test_billing_index_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/billing');
        $response->assertStatus(200);
        $response->assertSee('課金・プラン管理');
        $response->assertSee('スタンダードプラン');
    }

    /** 未契約テナントに「未契約」が表示される */
    public function test_billing_shows_no_subscription_state(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/billing');
        $response->assertSee('未契約');
    }

    /** Stripe未設定でポータルへ行くとエラー */
    public function test_portal_redirects_with_error_when_no_stripe_id(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/billing/portal');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** STRIPE_PRICE_ID未設定でチェックアウトするとエラー */
    public function test_checkout_returns_error_when_no_price_id(): void
    {
        config(['cashier.price_id' => null]);

        $response = $this->actingAs($this->admin)
            ->post('/admin/billing/checkout');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** 未認証ユーザーは課金ページにアクセスできない */
    public function test_billing_requires_auth(): void
    {
        $response = $this->get('/admin/billing');
        $response->assertRedirect('/login');
    }

    /** Stripe webhookエンドポイントが存在する（405 or 200、404ではない）*/
    public function test_stripe_webhook_endpoint_exists(): void
    {
        $response = $this->post('/stripe/webhook');
        $this->assertNotEquals(404, $response->getStatusCode());
    }
}
