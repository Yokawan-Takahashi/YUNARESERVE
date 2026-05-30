<?php

use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FormFieldController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CancelController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\LpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\ReservationLookupController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdminSettingsController;
use Illuminate\Support\Facades\Route;

// ─── LP ──────────────────────────────────────────────────────────────────────
Route::get('/', [LpController::class, 'index'])->name('lp');
Route::get('/terms', [LpController::class, 'terms'])->name('terms');
Route::get('/privacy', [LpController::class, 'privacy'])->name('privacy');
Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');

// Breeze 認証後リダイレクト先（ロールに応じて振り分け）
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');

// ─── 管理画面 (viewer以上) ────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'tenant.context', 'role:viewer'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 予約管理
    Route::get('reservations/create',         [ReservationController::class, 'create'])->middleware('role:staff')->name('reservations.create');
    Route::post('reservations',               [ReservationController::class, 'adminStore'])->middleware('role:staff')->name('reservations.store');
    Route::get('reservations/export',         [ReservationController::class, 'export'])->name('reservations.export');
    Route::get('reservations',                [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/{reservation}',  [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('reservations/{reservation}/edit', [ReservationController::class, 'edit'])->middleware('role:staff')->name('reservations.edit');
    Route::put('reservations/{reservation}',  [ReservationController::class, 'update'])->middleware('role:staff')->name('reservations.update');
    Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->middleware('role:staff')->name('reservations.status');
    Route::patch('reservations/{reservation}/memo',   [ReservationController::class, 'updateMemo'])->middleware('role:staff')->name('reservations.memo');
    Route::post('reservations/{reservation}/resend-email', [ReservationController::class, 'resendEmail'])->middleware('role:staff')->name('reservations.resend');

    // イベント管理
    Route::get('events/create',               [EventController::class, 'create'])->middleware('role:staff')->name('events.create');
    Route::post('events',                     [EventController::class, 'store'])->middleware('role:staff')->name('events.store');
    Route::get('events',                      [EventController::class, 'index'])->name('events.index');
    Route::get('events/{event}',              [EventController::class, 'show'])->name('events.show');
    Route::get('events/{event}/edit',         [EventController::class, 'edit'])->middleware('role:staff')->name('events.edit');
    Route::put('events/{event}',              [EventController::class, 'update'])->middleware('role:staff')->name('events.update');
    Route::delete('events/{event}',           [EventController::class, 'destroy'])->middleware('role:admin')->name('events.destroy');

    // 予約枠管理
    Route::post('events/{event}/slots',              [SlotController::class, 'store'])->middleware('role:staff')->name('events.slots.store');
    Route::put('events/{event}/slots/{slot}',         [SlotController::class, 'update'])->middleware('role:staff')->name('events.slots.update');
    Route::patch('events/{event}/slots/{slot}/toggle',[SlotController::class, 'toggleStatus'])->middleware('role:staff')->name('events.slots.toggle');
    Route::delete('events/{event}/slots/{slot}',      [SlotController::class, 'destroy'])->middleware('role:admin')->name('events.slots.destroy');

    // カテゴリ管理
    Route::get('categories/create',           [CategoryController::class, 'create'])->middleware('role:staff')->name('categories.create');
    Route::post('categories',                 [CategoryController::class, 'store'])->middleware('role:staff')->name('categories.store');
    Route::get('categories',                  [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}/edit',  [CategoryController::class, 'edit'])->middleware('role:staff')->name('categories.edit');
    Route::put('categories/{category}',       [CategoryController::class, 'update'])->middleware('role:staff')->name('categories.update');
    Route::delete('categories/{category}',    [CategoryController::class, 'destroy'])->middleware('role:admin')->name('categories.destroy');

    // カスタム項目
    Route::get('fields',                      [FormFieldController::class, 'index'])->name('fields.index');
    Route::post('fields',                     [FormFieldController::class, 'store'])->middleware('role:staff')->name('fields.store');
    Route::put('fields/{field}',              [FormFieldController::class, 'update'])->middleware('role:staff')->name('fields.update');
    Route::delete('fields/{field}',           [FormFieldController::class, 'destroy'])->middleware('role:admin')->name('fields.destroy');

    // スタッフ管理
    Route::get('staff',                       [StaffController::class, 'index'])->middleware('role:admin')->name('staff.index');
    Route::post('staff',                      [StaffController::class, 'store'])->middleware('role:admin')->name('staff.store');
    Route::patch('staff/{user}/role',         [StaffController::class, 'updateRole'])->middleware('role:admin')->name('staff.role');
    Route::delete('staff/{user}',             [StaffController::class, 'destroy'])->middleware('role:admin')->name('staff.destroy');

    // テナント設定
    Route::get('settings',                    [SettingsController::class, 'index'])->middleware('role:admin')->name('settings.index');
    Route::put('settings',                    [SettingsController::class, 'update'])->middleware('role:admin')->name('settings.update');

    // 課金・プラン管理
    Route::get('billing',                     [BillingController::class, 'index'])->middleware('role:owner')->name('billing.index');
    Route::post('billing/portal',             [BillingController::class, 'portal'])->middleware('role:owner')->name('billing.portal');
    Route::post('billing/checkout',           [BillingController::class, 'checkout'])->middleware('role:owner')->name('billing.checkout');
    Route::get('billing/invoice/{invoice}',   [BillingController::class, 'invoice'])->middleware('role:owner')->name('billing.invoice');
});

// Breeze プロフィール
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── スーパー管理（運営のみ）─────────────────────────────────────────────────
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/',                           [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('tenants/create',              [SuperAdminController::class, 'create'])->name('tenants.create');
    Route::post('tenants',                    [SuperAdminController::class, 'store'])->name('tenants.store');
    Route::get('tenants',                     [SuperAdminController::class, 'index'])->name('tenants.index');
    Route::get('tenants/{tenant}/edit',       [SuperAdminController::class, 'edit'])->name('tenants.edit');
    Route::put('tenants/{tenant}',            [SuperAdminController::class, 'update'])->name('tenants.update');
    Route::get('tenants/{tenant}',            [SuperAdminDashboardController::class, 'showTenant'])->name('tenants.show');
    Route::patch('tenants/{tenant}/toggle',   [SuperAdminController::class, 'toggleStatus'])->name('tenants.toggle');

    // お問い合わせ管理
    Route::get('inquiries',                          [SuperAdminController::class, 'inquiries'])->name('inquiries.index');
    Route::patch('inquiries/{inquiry}/contact',      [SuperAdminController::class, 'markContacted'])->name('inquiries.contact');

    // システム設定（Stripe・料金・メール）
    Route::get('settings',                           [SuperAdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('settings',                           [SuperAdminSettingsController::class, 'update'])->name('settings.update');
});

// Stripe Webhook
Route::post('stripe/webhook', [\Laravel\Cashier\Http\Controllers\WebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');

require __DIR__.'/auth.php';

// ─── 公開側 /{slug}/... ─── ワイルドカードなので必ず最後 ─────────────────────
Route::prefix('{slug}')->name('public.')->middleware(['tenant.resolve'])->group(function () {
    Route::get('/',                              [PublicEventController::class, 'index'])->name('index');
    Route::get('/events/{event}',                [PublicEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/book/{slot}',    [BookingController::class,     'show'])->name('book');
    Route::post('/events/{event}/book/{slot}',   [BookingController::class,     'store'])->name('book.store');
    Route::get('/done/{code}',                   [BookingController::class,     'done'])->name('done');
    Route::get('/my-reservation',                [ReservationLookupController::class, 'show'])->name('lookup');
    Route::post('/my-reservation',               [ReservationLookupController::class, 'search'])->name('lookup.search');
    Route::get('/cancel/{token}',                [CancelController::class,      'show'])->name('cancel');
    Route::delete('/cancel/{token}',             [CancelController::class,      'destroy'])->name('cancel.destroy');
    Route::get('/cancel/{token}/done',           [CancelController::class,      'done'])->name('cancel.done');
});
