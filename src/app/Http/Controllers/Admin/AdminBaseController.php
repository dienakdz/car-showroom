<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Support\ViewDataCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

abstract class AdminBaseController extends Controller
{
    protected const ADMIN_PERMISSION_KEYS = [
        'catalog.manage',
        'inventory.manage',
        'leads.manage',
        'appointments.manage',
        'sales.manage',
        'reviews.approve',
        'settings.manage',
    ];

    protected bool $adminContextResolved = false;

    protected array $adminContext = [];

    protected function adminView(string $view, array $data = []): View
    {
        return view($view, array_merge($this->resolveAdminContext(), $data));
    }

    protected function pushSuccessToast(string $message): void
    {
        toastr()->success($message, [
            'positionClass' => 'toast-top-right',
            'closeButton' => true,
            'progressBar' => true,
            'timeOut' => 3000,
            'extendedTimeOut' => 1200,
            'preventDuplicates' => true,
        ]);
    }

    protected function pushErrorToast(string $message): void
    {
        toastr()->error($message, [
            'positionClass' => 'toast-top-right',
            'closeButton' => true,
            'progressBar' => true,
            'timeOut' => 3500,
            'extendedTimeOut' => 1200,
            'preventDuplicates' => true,
        ]);
    }

    protected function formatCurrency(int|float|null $value, ?string $currency = null): string
    {
        if ($value === null) {
            return 'Lien he';
        }

        return number_format((float) $value, 0, ',', '.') . ' ' . ($currency ?? 'VND');
    }

    protected function loadAdminSettings(): Collection
    {
        return $this->resolveAdminContext()['adminSettings'];
    }

    protected function loadAdminShowroom(): ?Showroom
    {
        return $this->resolveAdminContext()['adminShowroom'];
    }

    protected function resolveAdminContext(): array
    {
        if ($this->adminContextResolved) {
            return $this->adminContext;
        }

        $showroom = ViewDataCache::rememberShowroom();
        $settings = ViewDataCache::rememberAdminSettings();
        $brandName = trim((string) data_get($settings, 'site.brand_name.value', ''));
        $user = auth()->user();
        $roleNames = $user?->roleNames() ?? collect();
        $permissionNames = $user?->permissionNames() ?? collect();

        if ($brandName === '') {
            $brandName = $showroom?->name ?: 'Car Showroom';
        }

        $this->adminContext = [
            'adminCurrentUser' => $user,
            'adminShowroom' => $showroom,
            'adminSettings' => $settings,
            'adminBrandName' => $brandName,
            'adminDefaultCurrency' => data_get($settings, 'site.default_currency.value', 'VND'),
            'adminRoleLabel' => Str::headline((string) ($roleNames->first() ?? 'Admin')),
            'adminPermissionMap' => collect(self::ADMIN_PERMISSION_KEYS)
                ->mapWithKeys(fn (string $permission): array => [$permission => $permissionNames->contains($permission)])
                ->all(),
        ];
        $this->adminContextResolved = true;

        return $this->adminContext;
    }
}
