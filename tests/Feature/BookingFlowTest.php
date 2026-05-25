<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\Slot;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private Event $event;
    private Slot $slot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug' => 'demo', 'company_name' => 'デモ社', 'status' => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->event = Event::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'テストイベント', 'status' => 'published',
        ]);

        $this->slot = Slot::create([
            'event_id' => $this->event->id,
            'date' => '2026-08-01', 'start_time' => '10:00', 'capacity' => 3,
        ]);
    }

    private function validData(array $override = []): array
    {
        return array_merge([
            'name'          => '山田太郎',
            'kana'          => 'やまだたろう',
            'email'         => 'taro@example.com',
            'email_confirm' => 'taro@example.com',
            'phone'         => '090-1234-5678',
            'companions'    => 0,
        ], $override);
    }

    /** 予約フォームが表示される */
    public function test_booking_form_is_shown(): void
    {
        $response = $this->get("/events/{$this->event->id}/book/{$this->slot->id}");
        $response->assertStatus(200);
        $response->assertSee('予約フォーム');
        $response->assertSee('テストイベント');
    }

    /** 正常に予約できる */
    public function test_user_can_make_reservation(): void
    {
        $response = $this->post("/events/{$this->event->id}/book/{$this->slot->id}", $this->validData());

        $response->assertRedirectContains('/done/');
        $this->assertDatabaseHas('reservations', [
            'event_id' => $this->event->id,
            'slot_id'  => $this->slot->id,
            'email'    => 'taro@example.com',
            'status'   => 'reserved',
        ]);
    }

    /** 予約すると reserved_count が増える */
    public function test_reserved_count_increments_after_booking(): void
    {
        $this->post("/events/{$this->event->id}/book/{$this->slot->id}", $this->validData());

        $this->assertEquals(1, $this->slot->fresh()->reserved_count);
    }

    /** 定員1の枠を予約すると満席になる */
    public function test_slot_becomes_full_when_capacity_reached(): void
    {
        $fullSlot = Slot::create([
            'event_id' => $this->event->id,
            'date' => '2026-08-02', 'start_time' => '11:00', 'capacity' => 1,
        ]);

        $this->post("/events/{$this->event->id}/book/{$fullSlot->id}", $this->validData());

        $this->assertEquals('full', $fullSlot->fresh()->status);
    }

    /** 満席枠への予約はバリデーションエラー */
    public function test_cannot_book_full_slot(): void
    {
        $fullSlot = Slot::create([
            'event_id' => $this->event->id, 'date' => '2026-08-03',
            'start_time' => '12:00', 'capacity' => 1, 'status' => 'full',
            'reserved_count' => 1,
        ]);

        $response = $this->post("/events/{$this->event->id}/book/{$fullSlot->id}", $this->validData());
        $response->assertSessionHasErrors('slot');
    }

    /** 同枠・同メールの二重予約を拒否する */
    public function test_duplicate_booking_is_rejected(): void
    {
        $this->post("/events/{$this->event->id}/book/{$this->slot->id}", $this->validData());
        $response = $this->post("/events/{$this->event->id}/book/{$this->slot->id}", $this->validData());

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('reservations', 1);
    }

    /** メール確認不一致はバリデーションエラー */
    public function test_email_confirm_mismatch_fails(): void
    {
        $response = $this->post("/events/{$this->event->id}/book/{$this->slot->id}",
            $this->validData(['email_confirm' => 'other@example.com'])
        );
        $response->assertSessionHasErrors('email_confirm');
    }

    /** 名前必須バリデーション */
    public function test_name_is_required(): void
    {
        $response = $this->post("/events/{$this->event->id}/book/{$this->slot->id}",
            $this->validData(['name' => ''])
        );
        $response->assertSessionHasErrors('name');
    }

    /** メール形式バリデーション */
    public function test_invalid_email_fails(): void
    {
        $response = $this->post("/events/{$this->event->id}/book/{$this->slot->id}",
            $this->validData(['email' => 'not-an-email', 'email_confirm' => 'not-an-email'])
        );
        $response->assertSessionHasErrors('email');
    }

    /** 完了画面に予約番号が表示される */
    public function test_done_page_shows_reservation_code(): void
    {
        $this->post("/events/{$this->event->id}/book/{$this->slot->id}", $this->validData());
        $reservation = Reservation::withoutGlobalScopes()->first();

        $response = $this->get("/done/{$reservation->code}");
        $response->assertStatus(200);
        $response->assertSee($reservation->code);
        $response->assertSee('山田太郎');
    }

    /** 下書きイベントは予約不可 */
    public function test_cannot_book_draft_event(): void
    {
        $draftEvent = Event::create([
            'tenant_id' => $this->tenant->id,
            'title' => '下書き', 'status' => 'draft',
        ]);
        $slot = Slot::create([
            'event_id' => $draftEvent->id,
            'date' => '2026-09-01', 'start_time' => '10:00', 'capacity' => 5,
        ]);

        $response = $this->post("/events/{$draftEvent->id}/book/{$slot->id}", $this->validData());
        $response->assertStatus(404);
    }
}
