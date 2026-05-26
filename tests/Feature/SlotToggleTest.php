<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Slot;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlotToggleTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $staff;
    private Event $event;
    private Slot $slot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create(['slug' => 'slot-toggle', 'company_name' => '枠テスト社', 'status' => 'active']);
        app()->instance('tenant', $this->tenant);

        $this->staff = User::create([
            'tenant_id' => $this->tenant->id, 'name' => 'スタッフ',
            'email' => 'staff@slot-toggle.com', 'password' => bcrypt('p'), 'role' => 'staff',
        ]);
        $this->event = Event::create(['tenant_id' => $this->tenant->id, 'title' => '枠テスト', 'status' => 'published']);
        $this->slot  = Slot::create(['event_id' => $this->event->id, 'date' => '2027-02-01', 'start_time' => '10:00', 'capacity' => 10, 'status' => 'open']);
    }

    public function test_open_slot_can_be_closed(): void
    {
        $this->assertEquals('open', $this->slot->status);

        $this->actingAs($this->staff)
            ->patch("/admin/events/{$this->event->id}/slots/{$this->slot->id}/toggle");

        $this->assertEquals('closed', $this->slot->fresh()->status);
    }

    public function test_closed_slot_can_be_reopened(): void
    {
        $this->slot->update(['status' => 'closed']);

        $this->actingAs($this->staff)
            ->patch("/admin/events/{$this->event->id}/slots/{$this->slot->id}/toggle");

        $this->assertEquals('open', $this->slot->fresh()->status);
    }

    public function test_slot_can_be_edited_inline(): void
    {
        $this->actingAs($this->staff)
            ->put("/admin/events/{$this->event->id}/slots/{$this->slot->id}", [
                'date'       => '2027-03-15',
                'start_time' => '14:00',
                'end_time'   => '15:00',
                'capacity'   => 20,
                'status'     => 'open',
            ]);

        $updated = $this->slot->fresh();
        $this->assertEquals('2027-03-15', $updated->date->format('Y-m-d'));
        $this->assertEquals(20, $updated->capacity);
    }

    public function test_viewer_cannot_toggle_slot(): void
    {
        $viewer = User::create([
            'tenant_id' => $this->tenant->id, 'name' => '閲覧者',
            'email' => 'viewer@slot-toggle.com', 'password' => bcrypt('p'), 'role' => 'viewer',
        ]);

        $response = $this->actingAs($viewer)
            ->patch("/admin/events/{$this->event->id}/slots/{$this->slot->id}/toggle");

        $response->assertStatus(403);
        $this->assertEquals('open', $this->slot->fresh()->status);
    }
}
