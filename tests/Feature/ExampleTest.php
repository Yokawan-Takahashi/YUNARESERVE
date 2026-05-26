<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** テナントなしでルートにアクセスすると404 */
    public function test_root_without_tenant_returns_404(): void
    {
        $response = $this->get('/');
        $response->assertStatus(404);
    }

    /** テナントコンテキストがあればルートが200を返す */
    public function test_the_application_returns_a_successful_response(): void
    {
        $tenant = Tenant::create(['slug' => 'test', 'company_name' => 'テスト', 'status' => 'active']);
        app()->instance('tenant', $tenant);

        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
