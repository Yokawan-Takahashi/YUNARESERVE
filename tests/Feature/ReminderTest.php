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

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    /** 翌日の予約にリマインダーメールが送られる */
    public function test_reminder_sends_for_tomorrows_reservations(): void
    {
        Mail::fake();

        $tenant = Tenant::create([
            'slug'         => 'reminder-test',
            'company_name' => 'リマインダーテスト社',
            'status'       => 'active',
        ]);

        $event = Event::create([
            'tenant_id' => $tenant->id,
            'title'     => 'リマインダーイベント',
            'status'    => 'published',
        ]);

        $tomorrow = now()->addDay()->format('Y-m-d');
        $slot = Slot::create([
            'event_id'   => $event->id,
            'date'       => $tomorrow,
            'start_time' => '14:00',
            'capacity'   => 10,
        ]);

        Reservation::create([
            'tenant_id'    => $tenant->id,
            'code'         => 'RMDR0001',
            'event_id'     => $event->id,
            'slot_id'      => $slot->id,
            'name'         => 'リマインダー太郎',
            'email'        => 'reminder@example.com',
            'companions'   => 0,
            'status'       => 'reserved',
            'cancel_token' => 'reminder-cancel-token-64chars-0000000000000000000000000000000000',
        ]);

        $this->artisan('reminder:send', ['--date' => $tomorrow])
            ->assertExitCode(0);

        Mail::assertSent(\App\Mail\ReservationReminderMail::class, function ($mail) {
            return $mail->hasTo('reminder@example.com');
        });
    }

    /** キャンセル済み予約にはリマインダーを送らない */
    public function test_reminder_skips_cancelled_reservations(): void
    {
        Mail::fake();

        $tenant = Tenant::create([
            'slug'         => 'reminder-skip-test',
            'company_name' => 'スキップテスト社',
            'status'       => 'active',
        ]);

        $event = Event::create([
            'tenant_id' => $tenant->id,
            'title'     => 'スキップイベント',
            'status'    => 'published',
        ]);

        $tomorrow = now()->addDay()->format('Y-m-d');
        $slot = Slot::create([
            'event_id'   => $event->id,
            'date'       => $tomorrow,
            'start_time' => '15:00',
            'capacity'   => 10,
        ]);

        Reservation::create([
            'tenant_id'    => $tenant->id,
            'code'         => 'SKIPRMDR',
            'event_id'     => $event->id,
            'slot_id'      => $slot->id,
            'name'         => 'キャンセル済み太郎',
            'email'        => 'cancelled@example.com',
            'companions'   => 0,
            'status'       => 'cancelled',
            'cancel_token' => 'skip-cancel-token-64chars-00000000000000000000000000000000000000000',
        ]);

        $this->artisan('reminder:send', ['--date' => $tomorrow])
            ->assertExitCode(0);

        Mail::assertNotSent(\App\Mail\ReservationReminderMail::class);
    }

    /** --dateオプションなしで翌日が対象になる */
    public function test_reminder_defaults_to_tomorrow(): void
    {
        Mail::fake();

        $this->artisan('reminder:send')->assertExitCode(0);

        // 送信対象なしでも正常終了する
        Mail::assertNothingSent();
    }
}
