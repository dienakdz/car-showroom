<?php

use App\Http\Controllers\Admin\Appointments\AppointmentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\Catalog\TrimFormController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Inventory\CarUnitController;
use App\Http\Controllers\Admin\Inventory\CarUnitWorkflowController;
use App\Http\Controllers\Admin\Leads\LeadController;
use App\Http\Controllers\Admin\Leads\LeadNoteController;
use App\Http\Controllers\Admin\Reviews\TrimReviewController;
use App\Http\Controllers\Admin\Sales\SaleController;
use App\Http\Controllers\Admin\Settings\SettingController;
use App\Livewire\Admin\Catalog\Page as CatalogPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
                Route::get('', CatalogPage::class)->name('index');

                Route::get('/makes', function (Request $request): RedirectResponse {
                    $search = trim((string) $request->string('q'));

                    return redirect()->route('admin.catalog.index', array_filter([
                        'tab' => 'makes',
                        'make_q' => $search !== '' ? $search : null,
                    ], static fn (mixed $value): bool => $value !== null && $value !== ''));
                })->name('makes.index');

                Route::get('/models', function (Request $request): RedirectResponse {
                    $search = trim((string) $request->string('q'));
                    $makeId = $request->integer('make_id');

                    return redirect()->route('admin.catalog.index', array_filter([
                        'tab' => 'models',
                        'model_q' => $search !== '' ? $search : null,
                        'model_make' => $makeId > 0 ? $makeId : null,
                    ], static fn (mixed $value): bool => $value !== null && $value !== ''));
                })->name('models.index');

                Route::get('/trims', function (Request $request): RedirectResponse {
                    $search = trim((string) $request->string('q'));
                    $modelId = $request->integer('model_id');

                    return redirect()->route('admin.catalog.index', array_filter([
                        'tab' => 'trims',
                        'trim_q' => $search !== '' ? $search : null,
                        'trim_model' => $modelId > 0 ? $modelId : null,
                    ], static fn (mixed $value): bool => $value !== null && $value !== ''));
                })->name('trims.index');

                Route::get('/trims/create', [TrimFormController::class, 'create'])->name('trims.create');
                Route::post('/trims', [TrimFormController::class, 'store'])->name('trims.store');
                Route::get('/trims/{trimRecord}/edit', [TrimFormController::class, 'edit'])->name('trims.edit');
                Route::match(['put', 'patch'], '/trims/{trimRecord}', [TrimFormController::class, 'update'])->name('trims.update');
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
