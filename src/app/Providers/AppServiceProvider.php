<?php

namespace App\Providers;

use App\Http\Middleware\EnsureAdminAccess;
use App\Http\Middleware\EnsureUserHasPermission;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::addPersistentMiddleware([
            EnsureAdminAccess::class,
            EnsureUserHasPermission::class,
        ]);
    }
}
