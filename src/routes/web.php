<?php

use App\Http\Controllers\Clients\AccountController;
use App\Http\Controllers\Clients\AppointmentController;
use App\Http\Controllers\Clients\AuthController;
use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\InventoryController;
use App\Http\Controllers\Clients\LeadController;
use App\Http\Controllers\Clients\NotificationController;
use App\Http\Controllers\Clients\PagesController;
use App\Http\Controllers\Clients\TrimReviewsController;
use App\Http\Controllers\Clients\TrimsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dang-nhap', [AuthController::class, 'show'])->name('login');
Route::get('/tai-khoan', [AccountController::class, 'show'])
    ->middleware(['auth', 'customer.access'])
    ->name('account.show');
Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/dang-ky', [AuthController::class, 'register'])->name('register');
Route::get('/kich-hoat-tai-khoan/{id}/{hash}', [AuthController::class, 'activate'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/gui-lai-email-kich-hoat', [AuthController::class, 'resendActivation'])
    ->middleware('throttle:6,1')
    ->name('verification.resend');
Route::post('/tai-khoan/cap-nhat', [AccountController::class, 'updateProfile'])
    ->middleware(['auth', 'customer.access'])
    ->name('account.profile.update');
Route::post('/tai-khoan/doi-mat-khau', [AccountController::class, 'updatePassword'])
    ->middleware(['auth', 'customer.access'])
    ->name('account.password.update');
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'customer.access'])->group(function (): void {
    Route::post('/tai-khoan/thong-bao/doc-tat-ca', [NotificationController::class, 'markAllAsRead'])->name('account.notifications.read-all');
    Route::post('/tai-khoan/thong-bao/{id}/doc', [NotificationController::class, 'markAsRead'])->name('account.notifications.read');
    Route::delete('/tai-khoan/thong-bao/{id}', [NotificationController::class, 'destroy'])->name('account.notifications.destroy');
});
$appointmentThrottle = config('showroom.throttle.appointments');
$leadThrottle = config('showroom.throttle.leads');

Route::post('/dat-lich-hen', [AppointmentController::class, 'store'])
    ->middleware("throttle:{$appointmentThrottle},1")
    ->name('appointments.store');

Route::get('/kho-xe', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/xe-moi', [InventoryController::class, 'index'])->defaults('condition', 'new')->name('inventory.new');
Route::get('/xe-cu', [InventoryController::class, 'index'])->defaults('condition', 'used')->name('inventory.used');
Route::get('/xe-cpo', [InventoryController::class, 'index'])->defaults('condition', 'cpo')->name('inventory.cpo');

Route::get('/xe/{stockCode}', [InventoryController::class, 'show'])->name('car.show');
Route::post('/phien-ban/{trimSlug}/danh-gia', [TrimReviewsController::class, 'store'])
    ->middleware(['auth', 'purchased.trim.review'])
    ->name('trim.reviews.store');
Route::get('/phien-ban/{trimSlug}', [TrimsController::class, 'show'])->name('trim.show');

Route::get('/ve-chung-toi', [PagesController::class, 'about'])->name('about');
Route::get('/lien-he', [PagesController::class, 'contact'])->name('contact');
Route::get('/tai-chinh', [PagesController::class, 'finance'])->name('finance');
Route::get('/thu-cu-doi-moi', [PagesController::class, 'tradeIn'])->name('tradein');
Route::post('/gui-yeu-cau-tu-van', [LeadController::class, 'store'])
    ->middleware("throttle:{$leadThrottle},1")
    ->name('lead.store');

require __DIR__ . '/admin.php';
