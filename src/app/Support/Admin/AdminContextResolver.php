<?php

namespace App\Support\Admin;

use App\Models\Showroom;
use App\Support\ViewDataCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminContextResolver
{
    public const PERMISSION_KEYS = [
        'catalog.manage',
        'inventory.manage',
        'leads.manage',
        'appointments.manage',
        'sales.manage',
        'reviews.approve',
        'settings.manage',
    ];

    private bool $resolved = false;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    /**
     * @return array<string, mixed>
     */
    public function resolve(): array
    {
        if ($this->resolved) {
            return $this->context;
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

        $this->context = [
            'adminCurrentUser' => $user,
            'adminShowroom' => $showroom,
            'adminSettings' => $settings,
            'adminBrandName' => $brandName,
            'adminDefaultCurrency' => data_get($settings, 'site.default_currency.value', 'VND'),
            'adminRoleLabel' => Str::headline((string) ($roleNames->first() ?? 'Admin')),
            'adminPermissionMap' => collect(self::PERMISSION_KEYS)
                ->mapWithKeys(fn (string $permission): array => [$permission => $permissionNames->contains($permission)])
                ->all(),
        ];
        $this->resolved = true;

        return $this->context;
    }

    public function settings(): Collection
    {
        return $this->resolve()['adminSettings'];
    }

    public function showroom(): ?Showroom
    {
        return $this->resolve()['adminShowroom'];
    }
}
