<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Slot;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterCrudTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug' => 'demo', 'company_name' => 'デモ社', 'status' => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->owner = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'オーナー', 'email' => 'owner@example.com',
            'password' => bcrypt('password'), 'role' => 'owner',
        ]);
    }

    /** 管理者がカテゴリを作成できる */
    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->owner)->post('/admin/categories', [
            'name' => 'テストカテゴリ', 'scope' => 'external', 'sort' => 0,
        ]);
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'テストカテゴリ', 'tenant_id' => $this->tenant->id]);
    }

    /** 管理者がイベントを作成できる */
    public function test_admin_can_create_event(): void
    {
        $response = $this->actingAs($this->owner)->post('/admin/events', [
            'title' => 'テストイベント', 'status' => 'draft',
        ]);
        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', ['title' => 'テストイベント', 'tenant_id' => $this->tenant->id]);
    }

    /** イベントを公開状態で作成すると公開側に反映される */
    public function test_published_event_appears_on_public_page(): void
    {
        $this->actingAs($this->owner)->post('/admin/events', [
            'title' => '公開イベント', 'status' => 'published',
        ]);

        // 公開側トップで表示されること
        $response = $this->get('/' . $this->tenant->slug . '/');
        $response->assertStatus(200);
        $response->assertSee('公開イベント');
    }

    /** 下書きイベントは公開側に出ない */
    public function test_draft_event_does_not_appear_on_public_page(): void
    {
        $this->actingAs($this->owner)->post('/admin/events', [
            'title' => '下書きイベント', 'status' => 'draft',
        ]);

        $response = $this->get('/' . $this->tenant->slug . '/');
        $response->assertDontSee('下書きイベント');
    }

    /** 枠を追加すると管理画面に反映される */
    public function test_admin_can_add_slot_to_event(): void
    {
        $event = Event::create([
            'tenant_id' => $this->tenant->id,
            'title' => '枠テスト', 'status' => 'published',
        ]);

        $response = $this->actingAs($this->owner)->post("/admin/events/{$event->id}/slots", [
            'date' => '2026-07-01', 'start_time' => '10:00', 'capacity' => 10,
        ]);
        $response->assertRedirect(route('admin.events.edit', $event));
        $this->assertDatabaseHas('slots', ['event_id' => $event->id, 'capacity' => 10]);
    }

    /** 公開イベントの詳細ページが表示される */
    public function test_public_event_detail_page_shows(): void
    {
        $event = Event::create([
            'tenant_id' => $this->tenant->id,
            'title' => '詳細テスト', 'status' => 'published',
        ]);
        Slot::create([
            'event_id' => $event->id, 'date' => '2026-08-01',
            'start_time' => '09:00', 'capacity' => 5,
        ]);

        $response = $this->get("/{$this->tenant->slug}/events/{$event->id}");
        $response->assertStatus(200);
        $response->assertSee('詳細テスト');
        $response->assertSee('5');
    }

    /** カテゴリを削除できる */
    public function test_admin_can_delete_category(): void
    {
        $cat = Category::create(['tenant_id' => $this->tenant->id, 'name' => '削除対象', 'scope' => 'external']);
        $response = $this->actingAs($this->owner)->delete("/admin/categories/{$cat->id}");
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }
}
