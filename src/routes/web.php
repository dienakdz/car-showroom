<?php

use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\InventoryController;
use App\Http\Controllers\Clients\LeadController;
use App\Http\Controllers\Clients\PagesController;
use App\Http\Controllers\Clients\TrimsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/kho-xe', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/xe-moi', [InventoryController::class, 'index'])->defaults('condition', 'new')->name('inventory.new');
Route::get('/xe-cu', [InventoryController::class, 'index'])->defaults('condition', 'used')->name('inventory.used');
Route::get('/xe-cpo', [InventoryController::class, 'index'])->defaults('condition', 'cpo')->name('inventory.cpo');

Route::get('/xe/{stockCode}', [InventoryController::class, 'show'])->name('car.show');
Route::get('/phien-ban/{trimSlug}', [TrimsController::class, 'show'])->name('trim.show');

Route::get('/ve-chung-toi', [PagesController::class, 'about'])->name('about');
Route::get('/lien-he', [PagesController::class, 'contact'])->defaults('source', 'contact')->name('contact');
Route::get('/tai-chinh', [PagesController::class, 'contact'])->defaults('source', 'finance')->name('finance');
Route::get('/thu-cu-doi-moi', [PagesController::class, 'contact'])->defaults('source', 'trade_in')->name('tradein');
Route::post('/lead', [LeadController::class, 'store'])->name('lead.store');
