<?php

use App\Http\Controllers\Clients\AuthController;
use App\Http\Controllers\Clients\AppointmentController;
use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\InventoryController;
use App\Http\Controllers\Clients\LeadController;
use App\Http\Controllers\Clients\PagesController;
use App\Http\Controllers\Clients\TrimsController;
use App\Http\Controllers\Clients\TrimReviewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dang-nhap', [AuthController::class, 'show'])->name('login');
Route::get('/tai-khoan', [AuthController::class, 'account'])
    ->middleware('auth')
    ->name('account.show');
Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/dang-ky', [AuthController::class, 'register'])->name('register');
Route::post('/tai-khoan/cap-nhat', [AuthController::class, 'updateProfile'])
    ->middleware('auth')
    ->name('account.profile.update');
Route::post('/tai-khoan/doi-mat-khau', [AuthController::class, 'updatePassword'])
    ->middleware('auth')
    ->name('account.password.update');
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');
Route::post('/appointments', [AppointmentController::class, 'store'])
    ->middleware('throttle:10,1')
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
Route::get('/lien-he', [PagesController::class, 'contact'])->defaults('source', 'contact')->name('contact');
Route::get('/tai-chinh', [PagesController::class, 'contact'])->defaults('source', 'finance')->name('finance');
Route::get('/thu-cu-doi-moi', [PagesController::class, 'contact'])->defaults('source', 'trade_in')->name('tradein');
Route::post('/lead', [LeadController::class, 'store'])->middleware('throttle:15,1')->name('lead.store');
