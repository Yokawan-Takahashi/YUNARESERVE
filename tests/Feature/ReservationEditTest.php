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

class ReservationEditTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $staff;
    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->tenant = Tenant::create(['slug' => 'rsv-edit', 'company_name' => '編集テスト社', 'status' => 'active']);
        app()->instance('tenant', $this->tenant);

        $this->staff = User::create([
            'tenant_id' => $this->tenant->id, 'name' => 'スタッフ',
            'email' => 'staff@rsv-edit.com', 'password' => bcrypt('p'), 'role' => 'staff',
        ]);

        $event = Event::create(['tenant_id' => $this->tenant->id, 'title' => '編集テスト', 'status' => 'published']);
        $slot  = Slot::create(['event_id' => $event->id, 'date' => '2027-06-01', 'start_time' => '10:00', 'capacity' => 10, 'status' => 'open']);

        $this->reservation = Reservation::create([
            'tenant_id'    => $this->tenant->id,
            'code'         => 'EDIT0001',
            'event_id'     => $event->id,
            'slot_id'      => $slot->id,
            'name'         => '編集前 太郎',
            'email'        => 'before@rsv-edit.com',
            'companions'   => 0,
            'status'       => 'reserved',
            'cancel_token' => str_repeat('e', 64),
        ]);
    }

    public function test_staff_can_access_edit_form(): void
    {
        $response = $this->actingAs($this->staff)->get(route('admin.reservations.edit', $this->reservation));
        $response->assertStatus(200);
        $response->assertSee('編集前 太郎');
    }

    public function test_viewer_cannot_access_edit_form(): void
    {
        $viewer = User::create([
            'tenant_id' => $this->tenant->id, 'name' => '閲覧者',
            'email' => 'viewer@rsv-edit.com', 'password' => bcrypt('p'), 'role' => 'viewer',
        ]);

        $response = $this->actingAs($viewer)->get(route('admin.reservations.edit', $this->reservation));
        $response->assertStatus(403);
    }

    public function test_staff_can_update_reservation(): void
    {
        $this->actingAs($this->staff)->put(route('admin.reservations.update', $this->reservation), [
            'name'       => '更新後 花子',
            'email'      => 'after@rsv-edit.com',
            'companions' => 2,
        ]);

        $updated = $this->reservation->fresh();
        $this->assertEquals('更新後 花子', $updated->name);
        $this->assertEquals('after@rsv-edit.com', $updated->email);
        $this->assertEquals(2, $updated->companions);
    }

    public function test_staff_can_resend_confirmation_email(): void
    {
        $response = $this->actingAs($this->staff)
            ->post(route('admin.reservations.resend', $this->reservation));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
