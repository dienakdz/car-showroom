<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\Inventory\CarUnitController;
use App\Http\Controllers\Admin\Inventory\CarUnitWorkflowController;
use App\Livewire\Admin\Appointments\Form as AppointmentForm;
use App\Livewire\Admin\Appointments\IndexPage as AppointmentsIndexPage;
use App\Livewire\Admin\Catalog\Page as CatalogPage;
use App\Livewire\Admin\Catalog\Trims\Form as TrimForm;
use App\Livewire\Admin\Dashboard\Page as DashboardPage;
use App\Livewire\Admin\Leads\IndexPage as LeadsIndexPage;
use App\Livewire\Admin\Leads\ShowPage as LeadShowPage;
use App\Livewire\Admin\Reviews\Page as ReviewsPage;
use App\Livewire\Admin\Sales\Form as SaleForm;
use App\Livewire\Admin\Sales\IndexPage as SalesIndexPage;
use App\Livewire\Admin\Settings\Page as SettingsPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    if (app()->environment('local')) {
        Route::get('/dev-login', function (Request $request): RedirectResponse {
            $admin = \App\Models\User::where('email', 'admin@showroom.test')->firstOrFail();
            auth()->login($admin);
            $target = (string) $request->query('to', route('admin.dashboard'));

            return redirect($target);
        })->name('dev.login');
    }

    Route::middleware(['auth', 'admin.access'])->group(function (): void {
        Route::get('/', DashboardPage::class)->name('dashboard');

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
                    ], static fn (mixed $value): bool => $value !== null));
                })->name('makes.index');

                Route::get('/models', function (Request $request): RedirectResponse {
                    $search = trim((string) $request->string('q'));
                    $makeId = $request->integer('make_id');

                    return redirect()->route('admin.catalog.index', array_filter([
                        'tab' => 'models',
                        'model_q' => $search !== '' ? $search : null,
                        'model_make' => $makeId > 0 ? $makeId : null,
                    ], static fn (mixed $value): bool => $value !== null));
                })->name('models.index');

                Route::get('/trims', function (Request $request): RedirectResponse {
                    $search = trim((string) $request->string('q'));
                    $modelId = $request->integer('model_id');

                    return redirect()->route('admin.catalog.index', array_filter([
                        'tab' => 'trims',
                        'trim_q' => $search !== '' ? $search : null,
                        'trim_model' => $modelId > 0 ? $modelId : null,
                    ], static fn (mixed $value): bool => $value !== null));
                })->name('trims.index');

                Route::get('/trims/create', TrimForm::class)->name('trims.create');
                Route::get('/trims/{trimRecord}/edit', TrimForm::class)->name('trims.edit');
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

                Route::post('/media/upload', [CarUnitController::class, 'uploadMedia'])->name('media.upload');
                Route::post('/{carUnit}/publish', [CarUnitWorkflowController::class, 'publish'])->name('publish');
                Route::post('/{carUnit}/archive', [CarUnitWorkflowController::class, 'archive'])->name('archive');
            });

        Route::prefix('leads')
            ->name('leads.')
            ->middleware('admin.permission:leads.manage')
            ->group(function (): void {
                Route::get('/', LeadsIndexPage::class)->name('index');
                Route::get('/{lead}', LeadShowPage::class)->name('show');
            });

        Route::prefix('appointments')
            ->name('appointments.')
            ->middleware('admin.permission:appointments.manage')
            ->group(function (): void {
                Route::get('/', AppointmentsIndexPage::class)->name('index');
                Route::get('/create', AppointmentForm::class)->name('create');
                Route::get('/{appointmentRecord}/edit', AppointmentForm::class)->name('edit');
            });

        Route::prefix('sales')
            ->name('sales.')
            ->middleware('admin.permission:sales.manage')
            ->group(function (): void {
                Route::get('/', SalesIndexPage::class)->name('index');
                Route::get('/create', SaleForm::class)->name('create');
            });

        Route::prefix('reviews')
            ->name('reviews.')
            ->middleware('admin.permission:reviews.approve')
            ->group(function (): void {
                Route::get('/', ReviewsPage::class)->name('index');
            });

        Route::prefix('settings')
            ->name('settings.')
            ->middleware('admin.permission:settings.manage')
            ->group(function (): void {
                Route::get('/', SettingsPage::class)->name('index');
            });
    });
});
