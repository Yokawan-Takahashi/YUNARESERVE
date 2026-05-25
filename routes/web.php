<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CancelController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FormFieldController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 公開側（tenant.resolve ミドルウェアは本番サブドメイン環境でのみ適用）
Route::get('/', [PublicEventController::class, 'index'])->name('public.index');
Route::get('/events/{event}', [PublicEventController::class, 'show'])->name('public.events.show');
// 予約フォーム・完了（T5）
Route::get('/events/{event}/book/{slot}', [BookingController::class, 'show'])->name('public.book');
Route::post('/events/{event}/book/{slot}', [BookingController::class, 'store'])->name('public.book.store');
Route::get('/done/{code}', [BookingController::class, 'done'])->name('public.done');
// キャンセル（T7）
Route::get('/cancel/{token}', [CancelController::class, 'show'])->name('public.cancel');
Route::delete('/cancel/{token}', [CancelController::class, 'destroy'])->name('public.cancel.destroy');
Route::get('/cancel/{token}/done', [CancelController::class, 'done'])->name('public.cancel.done');

// Breeze互換エイリアス（認証後のリダイレクト先）
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');

// 管理画面（要ログイン・viewer以上）
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:viewer'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // カテゴリ（staff以上で編集可）
    Route::resource('categories', CategoryController::class)->except(['show']);

    // イベント・枠
    Route::resource('events', EventController::class);
    Route::post('events/{event}/slots', [SlotController::class, 'store'])->name('events.slots.store');
    Route::put('events/{event}/slots/{slot}', [SlotController::class, 'update'])->name('events.slots.update');
    Route::delete('events/{event}/slots/{slot}', [SlotController::class, 'destroy'])->name('events.slots.destroy');

    // 予約者管理
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/export', [ReservationController::class, 'export'])->name('reservations.export');
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.status');
    Route::patch('reservations/{reservation}/memo', [ReservationController::class, 'updateMemo'])->name('reservations.memo');

    // カスタム項目
    Route::get('fields', [FormFieldController::class, 'index'])->name('fields.index');
    Route::post('fields', [FormFieldController::class, 'store'])->name('fields.store');
    Route::put('fields/{field}', [FormFieldController::class, 'update'])->name('fields.update');
    Route::delete('fields/{field}', [FormFieldController::class, 'destroy'])->name('fields.destroy');
});

// Breeze プロフィール
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
