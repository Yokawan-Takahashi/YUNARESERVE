<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\Slot;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminReservationCreateTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $staff;
    private Event $event;
    private Slot $slot;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->tenant = Tenant::create(['slug' => 'manual-rsv', 'company_name' => '手動登録テスト社', 'status' => 'active']);
        app()->instance('tenant', $this->tenant);

        $this->staff = User::create([
            'tenant_id' => $this->tenant->id, 'name' => 'スタッフ',
            'email' => 'staff@manual-rsv.com', 'password' => bcrypt('p'), 'role' => 'staff',
        ]);
        $this->event = Event::create(['tenant_id' => $this->tenant->id, 'title' => '手動テスト', 'status' => 'published']);
        $this->slot  = Slot::create(['event_id' => $this->event->id, 'date' => '2027-01-10', 'start_time' => '10:00', 'capacity' => 5]);
    }

    public function test_create_form_is_accessible_for_staff(): void
    {
        $response = $this->actingAs($this->staff)->get('/admin/reservations/create');
        $response->assertStatus(200);
        $response->assertSee('予約手動登録');
    }

    public function test_viewer_cannot_access_create_form(): void
    {
        $viewer = User::create([
            'tenant_id' => $this->tenant->id, 'name' => '閲覧者',
            'email' => 'viewer@manual-rsv.com', 'password' => bcrypt('p'), 'role' => 'viewer',
        ]);
        $response = $this->actingAs($viewer)->get('/admin/reservations/create');
        $response->assertStatus(403);
    }

    public function test_staff_can_create_reservation_manually(): void
    {
        $this->actingAs($this->staff)->post('/admin/reservations', [
            'event_id'   => $this->event->id,
            'slot_id'    => $this->slot->id,
            'name'       => '手動 太郎',
            'email'      => 'manual@example.com',
            'companions' => 0,
            'send_email' => 1,
        ]);

        $this->assertDatabaseHas('reservations', [
            'name'      => '手動 太郎',
            'email'     => 'manual@example.com',
            'slot_id'   => $this->slot->id,
            'status'    => 'reserved',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_reserved_count_increments_after_manual_create(): void
    {
        $this->actingAs($this->staff)->post('/admin/reservations', [
            'event_id' => $this->event->id, 'slot_id' => $this->slot->id,
            'name' => 'カウントテスト', 'email' => 'count@example.com', 'companions' => 0,
        ]);

        $this->assertEquals(1, $this->slot->fresh()->reserved_count);
    }

    public function test_duplicate_email_in_same_slot_is_rejected(): void
    {
        Reservation::create([
            'tenant_id' => $this->tenant->id, 'code' => 'MANUAL01',
            'event_id' => $this->event->id, 'slot_id' => $this->slot->id,
            'name' => '既存', 'email' => 'dup@example.com',
            'companions' => 0, 'status' => 'reserved',
            'cancel_token' => str_repeat('x', 64),
        ]);

        $response = $this->actingAs($this->staff)->post('/admin/reservations', [
            'event_id' => $this->event->id, 'slot_id' => $this->slot->id,
            'name' => '重複', 'email' => 'dup@example.com', 'companions' => 0,
        ]);
        $response->assertSessionHasErrors('email');
    }
}
