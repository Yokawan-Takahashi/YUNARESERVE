<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\Slot;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationAdminTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;
    private Event $event;
    private Slot $slot;
    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug' => 'resv-admin-test', 'company_name' => '予約管理テスト社', 'status' => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => '管理者',
            'email'     => 'admin@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
        ]);

        $this->event = Event::create([
            'tenant_id' => $this->tenant->id,
            'title'     => '管理テストイベント',
            'status'    => 'published',
        ]);

        $this->slot = Slot::create([
            'event_id'      => $this->event->id,
            'date'          => '2026-11-01',
            'start_time'    => '10:00',
            'capacity'      => 5,
            'reserved_count'=> 1,
        ]);

        $this->reservation = Reservation::create([
            'tenant_id'    => $this->tenant->id,
            'code'         => 'ADMTEST1',
            'event_id'     => $this->event->id,
            'slot_id'      => $this->slot->id,
            'name'         => '管理テスト太郎',
            'email'        => 'taro@example.com',
            'companions'   => 0,
            'status'       => 'reserved',
            'cancel_token' => 'admin-test-cancel-token-64chars-0000000000000000000000000000000000',
        ]);
    }

    /** ダッシュボードに統計カードが表示される */
    public function test_dashboard_shows_stats(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('今日の予約');
        $response->assertSee('今週の予約');
        $response->assertSee('累計予約');
    }

    /** ダッシュボードに直近予約が表示される */
    public function test_dashboard_shows_recent_reservations(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('ADMTEST1');
        $response->assertSee('管理テスト太郎');
    }

    /** 予約一覧が表示される */
    public function test_reservation_index_is_shown(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations');
        $response->assertStatus(200);
        $response->assertSee('ADMTEST1');
        $response->assertSee('管理テスト太郎');
    }

    /** イベントで絞り込みできる */
    public function test_can_filter_by_event(): void
    {
        $other = Event::create([
            'tenant_id' => $this->tenant->id, 'title' => '別イベント', 'status' => 'published',
        ]);
        $otherSlot = Slot::create([
            'event_id' => $other->id, 'date' => '2026-11-02', 'start_time' => '11:00', 'capacity' => 5,
        ]);
        Reservation::create([
            'tenant_id'  => $this->tenant->id, 'code' => 'OTHERRSV',
            'event_id'   => $other->id, 'slot_id' => $otherSlot->id,
            'name'       => '別太郎', 'email' => 'other@example.com',
            'companions' => 0, 'status' => 'reserved',
            'cancel_token' => 'other-cancel-token-64chars-000000000000000000000000000000000000000',
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/reservations?event_id=' . $this->event->id);

        $response->assertSee('ADMTEST1');
        $response->assertDontSee('OTHERRSV');
    }

    /** ステータスで絞り込みできる */
    public function test_can_filter_by_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/reservations?status=cancelled');

        $response->assertDontSee('ADMTEST1');
    }

    /** 予約詳細が表示される */
    public function test_reservation_show_is_displayed(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/reservations/' . $this->reservation->id);

        $response->assertStatus(200);
        $response->assertSee('ADMTEST1');
        $response->assertSee('管理テスト太郎');
        $response->assertSee('taro@example.com');
    }

    /** 管理者がキャンセルすると status が cancelled になる */
    public function test_admin_can_cancel_reservation(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch('/admin/reservations/' . $this->reservation->id . '/status', ['status' => 'cancelled']);

        $response->assertRedirect();
        $this->assertEquals('cancelled', $this->reservation->fresh()->status);
    }

    /** 管理者キャンセルで reserved_count が減る */
    public function test_admin_cancel_decrements_reserved_count(): void
    {
        $this->actingAs($this->admin)
            ->patch('/admin/reservations/' . $this->reservation->id . '/status', ['status' => 'cancelled']);

        $this->assertEquals(0, $this->slot->fresh()->reserved_count);
    }

    /** 管理者キャンセルで満席スロットが open に戻る */
    public function test_admin_cancel_reopens_full_slot(): void
    {
        $this->slot->update(['status' => 'full']);

        $this->actingAs($this->admin)
            ->patch('/admin/reservations/' . $this->reservation->id . '/status', ['status' => 'cancelled']);

        $this->assertEquals('open', $this->slot->fresh()->status);
    }

    /** キャンセルを reserved に戻すと reserved_count が増える */
    public function test_admin_can_restore_cancelled_to_reserved(): void
    {
        $this->reservation->update(['status' => 'cancelled']);
        $this->slot->update(['reserved_count' => 0]);

        $this->actingAs($this->admin)
            ->patch('/admin/reservations/' . $this->reservation->id . '/status', ['status' => 'reserved']);

        $this->assertEquals('reserved', $this->reservation->fresh()->status);
        $this->assertEquals(1, $this->slot->fresh()->reserved_count);
    }

    /** メモを保存できる */
    public function test_admin_can_save_memo(): void
    {
        $this->actingAs($this->admin)
            ->patch('/admin/reservations/' . $this->reservation->id . '/memo', ['memo' => 'これはテストメモです']);

        $this->assertEquals('これはテストメモです', $this->reservation->fresh()->memo);
    }

    /** メモを空にして更新できる */
    public function test_admin_can_clear_memo(): void
    {
        $this->reservation->update(['memo' => '既存メモ']);

        $this->actingAs($this->admin)
            ->patch('/admin/reservations/' . $this->reservation->id . '/memo', ['memo' => '']);

        $this->assertNull($this->reservation->fresh()->memo);
    }

    /** キーワードで名前検索できる */
    public function test_can_search_by_name_keyword(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/reservations?keyword=管理テスト');

        $response->assertSee('ADMTEST1');
    }

    /** キーワードで一致しないものは出ない */
    public function test_keyword_search_excludes_non_matching(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/reservations?keyword=存在しない名前xyz');

        $response->assertDontSee('ADMTEST1');
    }
}
