<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenantA;
    private Tenant $tenantB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantA = Tenant::create([
            'slug' => 'tenant-a',
            'company_name' => 'テナントA社',
            'status' => 'active',
        ]);

        $this->tenantB = Tenant::create([
            'slug' => 'tenant-b',
            'company_name' => 'テナントB社',
            'status' => 'active',
        ]);
    }

    private function actAsTenant(Tenant $tenant): void
    {
        app()->instance('tenant', $tenant);
    }

    private function actAsSuperAdmin(): void
    {
        app()->forgetInstance('tenant');
    }

    /** テナントAとしてカテゴリを作ると tenant_id が自動セットされる */
    public function test_tenant_id_is_auto_set_on_create(): void
    {
        $this->actAsTenant($this->tenantA);

        $category = Category::create(['name' => 'カテゴリA']);

        $this->assertEquals($this->tenantA->id, $category->tenant_id);
    }

    /** テナントA文脈ではテナントBのレコードが一覧に出ない */
    public function test_tenant_a_cannot_list_tenant_b_records(): void
    {
        $this->actAsSuperAdmin();
        Category::create(['name' => 'Bのカテゴリ', 'tenant_id' => $this->tenantB->id]);

        $this->actAsTenant($this->tenantA);
        $categories = Category::all();

        $this->assertCount(0, $categories);
        $this->assertFalse($categories->contains('name', 'Bのカテゴリ'));
    }

    /** テナントA文脈ではテナントBのIDを直接指定しても find できない */
    public function test_tenant_a_cannot_find_tenant_b_record_by_id(): void
    {
        $this->actAsSuperAdmin();
        $categoryB = Category::create(['name' => 'Bのカテゴリ', 'tenant_id' => $this->tenantB->id]);

        $this->actAsTenant($this->tenantA);
        $found = Category::find($categoryB->id);

        $this->assertNull($found);
    }

    /** テナントA文脈ではテナントBのレコードを更新できない */
    public function test_tenant_a_cannot_update_tenant_b_record(): void
    {
        $this->actAsSuperAdmin();
        $categoryB = Category::create(['name' => 'Bのカテゴリ', 'tenant_id' => $this->tenantB->id]);
        $originalName = $categoryB->name;

        $this->actAsTenant($this->tenantA);
        $affected = Category::where('id', $categoryB->id)->update(['name' => '書き換え試み']);

        $this->assertEquals(0, $affected);

        $this->actAsSuperAdmin();
        $this->assertEquals($originalName, Category::withoutGlobalScopes()->find($categoryB->id)->name);
    }

    /** テナントA文脈ではテナントBのレコードを削除できない */
    public function test_tenant_a_cannot_delete_tenant_b_record(): void
    {
        $this->actAsSuperAdmin();
        $categoryB = Category::create(['name' => 'Bのカテゴリ', 'tenant_id' => $this->tenantB->id]);

        $this->actAsTenant($this->tenantA);
        $affected = Category::where('id', $categoryB->id)->delete();

        $this->assertEquals(0, $affected);

        $this->actAsSuperAdmin();
        $this->assertNotNull(Category::withoutGlobalScopes()->find($categoryB->id));
    }

    /** テナントA・Bそれぞれが自分のレコードだけ見える */
    public function test_each_tenant_sees_only_own_records(): void
    {
        $this->actAsTenant($this->tenantA);
        Category::create(['name' => 'Aのカテゴリ1']);
        Category::create(['name' => 'Aのカテゴリ2']);

        $this->actAsTenant($this->tenantB);
        Category::create(['name' => 'Bのカテゴリ1']);

        $this->actAsTenant($this->tenantA);
        $this->assertCount(2, Category::all());

        $this->actAsTenant($this->tenantB);
        $this->assertCount(1, Category::all());
    }
}
