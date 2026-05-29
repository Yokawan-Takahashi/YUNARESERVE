<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** LP（トップページ）は常に200 */
    public function test_lp_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** 有効なスラッグの公開トップは200 */
    public function test_tenant_public_index_returns_200(): void
    {
        Tenant::create(['slug' => 'test', 'company_name' => 'テスト', 'status' => 'active']);

        $response = $this->get('/test');
        $response->assertStatus(200);
    }

    /** 存在しないスラッグは404 */
    public function test_unknown_slug_returns_404(): void
    {
        $response = $this->get('/nonexistent-tenant-slug');
        $response->assertStatus(404);
    }
}
