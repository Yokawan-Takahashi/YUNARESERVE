<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\Slot;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationLookupTest extends TestCase
{
    use RefreshDatabase;

    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::create(['slug' => 'lookup-test', 'company_name' => '照会テスト社', 'status' => 'active']);
        app()->instance('tenant', $tenant);

        $event = Event::create(['tenant_id' => $tenant->id, 'title' => '照会テストイベント', 'status' => 'published']);
        $slot  = Slot::create(['event_id' => $event->id, 'date' => '2026-12-01', 'start_time' => '10:00', 'capacity' => 10]);

        $this->reservation = Reservation::create([
            'tenant_id' => $tenant->id, 'code' => 'LOOKP001',
            'event_id' => $event->id, 'slot_id' => $slot->id,
            'name' => '照会太郎', 'email' => 'lookup@example.com',
            'companions' => 0, 'status' => 'reserved',
            'cancel_token' => 'lookup-cancel-token-64chars-000000000000000000000000000000000000000',
        ]);
    }

    public function test_lookup_page_is_accessible(): void
    {
        $response = $this->get('/my-reservation');
        $response->assertStatus(200);
        $response->assertSee('予約照会');
    }

    public function test_can_find_reservation_with_code_and_email(): void
    {
        $response = $this->post('/my-reservation', [
            'code' => 'LOOKP001', 'email' => 'lookup@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertSee('照会太郎');
        $response->assertSee('照会テストイベント');
    }

    public function test_wrong_email_returns_error(): void
    {
        $response = $this->post('/my-reservation', [
            'code' => 'LOOKP001', 'email' => 'wrong@example.com',
        ]);

        $response->assertSessionHasErrors('lookup');
    }

    public function test_wrong_code_returns_error(): void
    {
        $response = $this->post('/my-reservation', [
            'code' => 'XXXXXXXX', 'email' => 'lookup@example.com',
        ]);

        $response->assertSessionHasErrors('lookup');
    }

    public function test_cancel_link_shown_for_active_reservation(): void
    {
        $response = $this->post('/my-reservation', [
            'code' => 'LOOKP001', 'email' => 'lookup@example.com',
        ]);

        $response->assertSee('この予約をキャンセルする');
    }

    public function test_code_search_is_case_insensitive(): void
    {
        $response = $this->post('/my-reservation', [
            'code' => 'lookp001', 'email' => 'lookup@example.com',
        ]);

        $response->assertSee('照会太郎');
    }
}
