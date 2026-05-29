<?php

namespace Tests\Feature;

use App\Mail\ReservationAdminNotifyMail;
use App\Mail\ReservationConfirmMail;
use App\Models\Event;
use App\Models\Slot;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private Event $event;
    private Slot $slot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug' => 'mailtest', 'company_name' => 'メールテスト社', 'status' => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->event = Event::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'メールテストイベント', 'status' => 'published',
        ]);

        $this->slot = Slot::create([
            'event_id' => $this->event->id,
            'date' => '2026-09-01', 'start_time' => '14:00', 'capacity' => 5,
        ]);
    }

    private function postBooking(array $override = []): void
    {
        $this->post("/{$this->tenant->slug}/events/{$this->event->id}/book/{$this->slot->id}", array_merge([
            'name'          => '鈴木花子',
            'kana'          => 'すずきはなこ',
            'email'         => 'hanako@example.com',
            'email_confirm' => 'hanako@example.com',
            'phone'         => '080-0000-1111',
            'companions'    => 1,
        ], $override));
    }

    /** 予約完了メールが予約者に送信される */
    public function test_reservation_confirm_mail_is_sent_to_guest(): void
    {
        Mail::fake();

        $this->postBooking();

        Mail::assertSent(ReservationConfirmMail::class, function ($mail) {
            return $mail->hasTo('hanako@example.com');
        });
    }

    /** 予約通知メールが管理者に送信される */
    public function test_admin_notify_mail_is_sent_when_notify_email_set(): void
    {
        Mail::fake();

        $this->tenant->update(['notify_email' => 'admin@example.com']);

        $this->postBooking();

        Mail::assertSent(ReservationAdminNotifyMail::class, function ($mail) {
            return $mail->hasTo('admin@example.com');
        });
    }

    /** notify_email が未設定の場合は管理者メールを送らない */
    public function test_admin_notify_mail_not_sent_when_no_notify_email(): void
    {
        Mail::fake();

        $this->assertNull($this->tenant->notify_email);

        $this->postBooking();

        Mail::assertNotSent(ReservationAdminNotifyMail::class);
    }

    /** 予約後に mail_logs が2件(confirm + admin_notify)作られる（notify_emailあり） */
    public function test_mail_logs_are_created_on_booking(): void
    {
        Mail::fake();

        $this->tenant->update(['notify_email' => 'owner@example.com']);

        $this->postBooking();

        $this->assertDatabaseHas('mail_logs', [
            'tenant_id' => $this->tenant->id,
            'type'      => 'reservation_confirm',
            'to'        => 'hanako@example.com',
        ]);
        $this->assertDatabaseHas('mail_logs', [
            'tenant_id' => $this->tenant->id,
            'type'      => 'admin_notify',
            'to'        => 'owner@example.com',
        ]);
        $this->assertDatabaseCount('mail_logs', 2);
    }

    /** notify_email 未設定時は mail_logs が1件だけ（confirmのみ） */
    public function test_only_confirm_log_when_no_notify_email(): void
    {
        Mail::fake();

        $this->postBooking();

        $this->assertDatabaseCount('mail_logs', 1);
        $this->assertDatabaseHas('mail_logs', ['type' => 'reservation_confirm']);
    }

    /** 確認メールにキャンセルURLが含まれる */
    public function test_confirm_mail_contains_cancel_url(): void
    {
        Mail::fake();

        $this->postBooking();

        Mail::assertSent(ReservationConfirmMail::class, function ($mail) {
            $reservation = $mail->reservation;
            return str_contains(
                route('public.cancel', ['slug' => $this->tenant->slug, 'token' => $reservation->cancel_token]),
                '/cancel/'
            );
        });
    }
}
