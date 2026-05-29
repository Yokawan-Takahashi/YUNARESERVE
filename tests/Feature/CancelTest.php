<?php

namespace Tests\Feature;

use App\Mail\ReservationCancelMail;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Slot;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CancelTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private Event $event;
    private Slot $slot;
    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug' => 'cancel-test', 'company_name' => 'キャンセルテスト社', 'status' => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->event = Event::create([
            'tenant_id' => $this->tenant->id,
            'title' => 'キャンセルテストイベント', 'status' => 'published',
        ]);

        $this->slot = Slot::create([
            'event_id' => $this->event->id,
            'date' => '2026-10-01', 'start_time' => '10:00', 'capacity' => 5,
            'reserved_count' => 1,
        ]);

        $this->reservation = Reservation::create([
            'tenant_id'    => $this->tenant->id,
            'code'         => 'TESTCODE1',
            'event_id'     => $this->event->id,
            'slot_id'      => $this->slot->id,
            'name'         => 'テスト太郎',
            'email'        => 'test@example.com',
            'companions'   => 0,
            'status'       => 'reserved',
            'cancel_token' => 'test-cancel-token-64chars-abcdefghijklmnopqrstuvwxyz0123456789abc',
        ]);
    }

    private function cancelUrl(string $suffix = ''): string
    {
        $slug = $this->tenant->slug;
        $token = $this->reservation->cancel_token;
        return "/{$slug}/cancel/{$token}{$suffix}";
    }

    /** キャンセル確認画面が表示される */
    public function test_cancel_confirm_page_is_shown(): void
    {
        $response = $this->get($this->cancelUrl());

        $response->assertStatus(200);
        $response->assertSee('予約キャンセル');
        $response->assertSee($this->reservation->code);
        $response->assertSee('テスト太郎');
    }

    /** 不正なトークンは404 */
    public function test_invalid_token_returns_404(): void
    {
        $slug = $this->tenant->slug;
        $response = $this->get("/{$slug}/cancel/invalid-token-does-not-exist");
        $response->assertStatus(404);
    }

    /** キャンセル実行で status が cancelled になる */
    public function test_cancel_sets_status_to_cancelled(): void
    {
        Mail::fake();

        $this->delete($this->cancelUrl());

        $this->assertEquals('cancelled', $this->reservation->fresh()->status);
    }

    /** キャンセル実行で reserved_count が1減る */
    public function test_cancel_decrements_reserved_count(): void
    {
        Mail::fake();

        $this->delete($this->cancelUrl());

        $this->assertEquals(0, $this->slot->fresh()->reserved_count);
    }

    /** 満席スロットをキャンセルすると open に戻る */
    public function test_cancel_reopens_full_slot(): void
    {
        Mail::fake();

        $this->slot->update(['status' => 'full', 'reserved_count' => 1]);

        $this->delete($this->cancelUrl());

        $this->assertEquals('open', $this->slot->fresh()->status);
    }

    /** キャンセル後に完了ページへリダイレクト */
    public function test_cancel_redirects_to_done_page(): void
    {
        Mail::fake();

        $response = $this->delete($this->cancelUrl());

        $response->assertRedirect($this->cancelUrl('/done'));
    }

    /** キャンセル完了画面が表示される */
    public function test_cancel_done_page_is_shown(): void
    {
        Mail::fake();

        $this->reservation->update(['status' => 'cancelled']);

        $response = $this->get($this->cancelUrl('/done'));

        $response->assertStatus(200);
        $response->assertSee('キャンセルが完了しました');
        $response->assertSee($this->reservation->code);
    }

    /** キャンセル後にキャンセル完了メールが送信される */
    public function test_cancel_mail_is_sent(): void
    {
        Mail::fake();

        $this->delete($this->cancelUrl());

        Mail::assertSent(ReservationCancelMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    /** キャンセル後に mail_logs に cancel_confirm が記録される */
    public function test_cancel_mail_log_is_created(): void
    {
        Mail::fake();

        $this->delete($this->cancelUrl());

        $this->assertDatabaseHas('mail_logs', [
            'tenant_id' => $this->tenant->id,
            'type'      => 'cancel_confirm',
            'to'        => 'test@example.com',
        ]);
    }

    /** すでにキャンセル済みの予約は再キャンセルできない */
    public function test_already_cancelled_reservation_cannot_be_cancelled_again(): void
    {
        Mail::fake();

        $this->reservation->update(['status' => 'cancelled']);

        $response = $this->delete($this->cancelUrl());

        $response->assertRedirect();
        $response->assertSessionHasErrors('cancel');
        Mail::assertNotSent(ReservationCancelMail::class);
    }

    /** キャンセル済み予約のキャンセル確認画面には「キャンセル済み」メッセージが表示される */
    public function test_already_cancelled_shows_message_on_confirm_page(): void
    {
        $this->reservation->update(['status' => 'cancelled']);

        $response = $this->get($this->cancelUrl());

        $response->assertStatus(200);
        $response->assertSee('すでにキャンセル済み');
    }
}
