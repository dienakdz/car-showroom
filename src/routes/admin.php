<?php

use App\Http\Controllers\Admin\Appointments\AppointmentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\Catalog\MakeController;
use App\Http\Controllers\Admin\Catalog\ModelController;
use App\Http\Controllers\Admin\Catalog\TrimController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Inventory\CarUnitController;
use App\Http\Controllers\Admin\Inventory\CarUnitWorkflowController;
use App\Http\Controllers\Admin\Leads\LeadController;
use App\Http\Controllers\Admin\Leads\LeadNoteController;
use App\Http\Controllers\Admin\Reviews\TrimReviewController;
use App\Http\Controllers\Admin\Sales\SaleController;
use App\Http\Controllers\Admin\Settings\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware(['auth', 'admin.access'])->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::prefix('catalog')
            ->name('catalog.')
            ->middleware('admin.permission:catalog.manage')
            ->group(function (): void {
                Route::get('/makes', [MakeController::class, 'index'])->name('makes.index');
                Route::post('/makes', [MakeController::class, 'store'])->name('makes.store');
                Route::match(['put', 'patch'], '/makes/{make}', [MakeController::class, 'update'])->name('makes.update');
                Route::delete('/makes/{make}', [MakeController::class, 'destroy'])->name('makes.destroy');

                Route::get('/models', [ModelController::class, 'index'])->name('models.index');
                Route::post('/models', [ModelController::class, 'store'])->name('models.store');
                Route::match(['put', 'patch'], '/models/{carModel}', [ModelController::class, 'update'])->name('models.update');
                Route::delete('/models/{carModel}', [ModelController::class, 'destroy'])->name('models.destroy');

                Route::get('/trims', [TrimController::class, 'index'])->name('trims.index');
                Route::get('/trims/create', [TrimController::class, 'create'])->name('trims.create');
                Route::post('/trims', [TrimController::class, 'store'])->name('trims.store');
                Route::get('/trims/{trimRecord}/edit', [TrimController::class, 'edit'])->name('trims.edit');
                Route::match(['put', 'patch'], '/trims/{trimRecord}', [TrimController::class, 'update'])->name('trims.update');
                Route::delete('/trims/{trimRecord}', [TrimController::class, 'destroy'])->name('trims.destroy');
            });

        Route::prefix('inventory')
            ->name('inventory.')
            ->middleware('admin.permission:inventory.manage')
            ->group(function (): void {
                Route::get('/', [CarUnitController::class, 'index'])->name('index');
                Route::get('/create', [CarUnitController::class, 'create'])->name('create');
                Route::post('/', [CarUnitController::class, 'store'])->name('store');
                Route::get('/{carUnit}/edit', [CarUnitController::class, 'edit'])->name('edit');
                Route::match(['put', 'patch'], '/{carUnit}', [CarUnitController::class, 'update'])->name('update');

                Route::post('/{carUnit}/publish', [CarUnitWorkflowController::class, 'publish'])->name('publish');
                Route::post('/{carUnit}/archive', [CarUnitWorkflowController::class, 'archive'])->name('archive');
                Route::post('/{carUnit}/hold', [CarUnitWorkflowController::class, 'hold'])->name('hold');
                Route::delete('/{carUnit}/hold', [CarUnitWorkflowController::class, 'release'])->name('hold.release');
                Route::post('/{carUnit}/price', [CarUnitWorkflowController::class, 'updatePrice'])->name('price.update');
            });

        Route::prefix('leads')
            ->name('leads.')
            ->middleware('admin.permission:leads.manage')
            ->group(function (): void {
                Route::get('/', [LeadController::class, 'index'])->name('index');
                Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
                Route::match(['put', 'patch'], '/{lead}', [LeadController::class, 'update'])->name('update');
                Route::post('/{lead}/notes', [LeadNoteController::class, 'store'])->name('notes.store');
            });

        Route::prefix('appointments')
            ->name('appointments.')
            ->middleware('admin.permission:appointments.manage')
            ->group(function (): void {
                Route::get('/', [AppointmentController::class, 'index'])->name('index');
                Route::get('/create', [AppointmentController::class, 'create'])->name('create');
                Route::post('/', [AppointmentController::class, 'store'])->name('store');
                Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
                Route::match(['put', 'patch'], '/{appointment}', [AppointmentController::class, 'update'])->name('update');
            });

        Route::prefix('sales')
            ->name('sales.')
            ->middleware('admin.permission:sales.manage')
            ->group(function (): void {
                Route::get('/', [SaleController::class, 'index'])->name('index');
                Route::get('/create', [SaleController::class, 'create'])->name('create');
                Route::post('/', [SaleController::class, 'store'])->name('store');
            });

        Route::prefix('reviews')
            ->name('reviews.')
            ->middleware('admin.permission:reviews.approve')
            ->group(function (): void {
                Route::get('/', [TrimReviewController::class, 'index'])->name('index');
                Route::match(['put', 'patch'], '/{trimReview}', [TrimReviewController::class, 'update'])->name('update');
            });

        Route::prefix('settings')
            ->name('settings.')
            ->middleware('admin.permission:settings.manage')
            ->group(function (): void {
                Route::get('/', [SettingController::class, 'index'])->name('index');
                Route::match(['put', 'patch'], '/', [SettingController::class, 'update'])->name('update');
            });
    });
});
