<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\Slot;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;
    private Event $event;
    private Slot $slot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'slug' => 'csv-test', 'company_name' => 'CSVテスト社', 'status' => 'active',
        ]);
        app()->instance('tenant', $this->tenant);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'CSV管理者',
            'email'     => 'csvadmin@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
        ]);

        $this->event = Event::create([
            'tenant_id' => $this->tenant->id,
            'title'     => 'CSVイベント',
            'status'    => 'published',
        ]);

        $this->slot = Slot::create([
            'event_id'   => $this->event->id,
            'date'       => '2026-12-01',
            'start_time' => '10:00',
            'end_time'   => '11:00',
            'capacity'   => 10,
        ]);

        Reservation::create([
            'tenant_id'    => $this->tenant->id,
            'code'         => 'CSVTEST1',
            'event_id'     => $this->event->id,
            'slot_id'      => $this->slot->id,
            'name'         => '山田太郎',
            'kana'         => 'やまだたろう',
            'email'        => 'yamada@example.com',
            'phone'        => '090-1111-2222',
            'companions'   => 2,
            'status'       => 'reserved',
            'cancel_token' => 'csv-test-cancel-token-64chars-aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ]);

        Reservation::create([
            'tenant_id'    => $this->tenant->id,
            'code'         => 'CSVTEST2',
            'event_id'     => $this->event->id,
            'slot_id'      => $this->slot->id,
            'name'         => '鈴木花子',
            'email'        => 'suzuki@example.com',
            'companions'   => 0,
            'status'       => 'cancelled',
            'cancel_token' => 'csv-test-cancel-token-64chars-bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
        ]);
    }

    /** CSVダウンロードが200で返る */
    public function test_csv_export_returns_200(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export');
        $response->assertStatus(200);
    }

    /** Content-Type が text/csv である */
    public function test_csv_content_type(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export');
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    /** Content-Disposition にファイル名が含まれる */
    public function test_csv_has_attachment_header(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.csv', $response->headers->get('Content-Disposition'));
    }

    /** UTF-8 BOM（\xEF\xBB\xBF）で始まる */
    public function test_csv_starts_with_utf8_bom(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export');
        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
    }

    /** ヘッダー行に必要な列名が含まれる */
    public function test_csv_has_header_columns(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export');
        $content = $response->streamedContent();

        foreach (['予約番号', 'イベント', '日付', 'お名前', 'メールアドレス', 'ステータス'] as $col) {
            $this->assertStringContainsString($col, $content);
        }
    }

    /** 予約データが出力される */
    public function test_csv_contains_reservation_data(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export');
        $content = $response->streamedContent();

        $this->assertStringContainsString('CSVTEST1', $content);
        $this->assertStringContainsString('山田太郎', $content);
        $this->assertStringContainsString('yamada@example.com', $content);
        $this->assertStringContainsString('やまだたろう', $content);
    }

    /** ステータス列に日本語ラベルが出る */
    public function test_csv_status_is_japanese(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export');
        $content = $response->streamedContent();

        $this->assertStringContainsString('予約済', $content);
        $this->assertStringContainsString('キャンセル', $content);
    }

    /** ステータス絞り込みが引き継がれる（reserved のみ） */
    public function test_csv_export_respects_status_filter(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export?status=reserved');
        $content = $response->streamedContent();

        $this->assertStringContainsString('CSVTEST1', $content);
        $this->assertStringNotContainsString('CSVTEST2', $content);
    }

    /** キーワード絞り込みが引き継がれる */
    public function test_csv_export_respects_keyword_filter(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reservations/export?keyword=山田');
        $content = $response->streamedContent();

        $this->assertStringContainsString('CSVTEST1', $content);
        $this->assertStringNotContainsString('CSVTEST2', $content);
    }

    /** 未ログインはリダイレクト */
    public function test_guest_cannot_download_csv(): void
    {
        $response = $this->get('/admin/reservations/export');
        $response->assertRedirect('/login');
    }
}
