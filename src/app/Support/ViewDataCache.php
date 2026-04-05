<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\Showroom;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ViewDataCache
{
    public const STORE = 'file';

    public const SHOWROOM_KEY = 'view-data.showroom';

    public const ADMIN_SETTINGS_KEY = 'view-data.admin.settings';

    protected const ADMIN_SETTING_KEYS = [
        'site.brand_name',
        'site.default_currency',
        'contact.sales_hotline',
        'inventory.show_on_hold_public',
        'notifications.lead_email_enabled',
    ];

    public static function store(): Repository
    {
        return Cache::store(app()->runningInConsole() ? 'array' : self::STORE);
    }

    public static function rememberShowroom(): ?Showroom
    {
        return self::store()->rememberForever(self::SHOWROOM_KEY, fn (): ?Showroom => Showroom::query()->first());
    }

    public static function rememberAdminSettings(): Collection
    {
        return self::store()->rememberForever(self::ADMIN_SETTINGS_KEY, function (): Collection {
            return Setting::query()
                ->whereIn('key', self::ADMIN_SETTING_KEYS)
                ->get()
                ->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->value_json]);
        });
    }

    public static function forgetShowroomAndSettings(): void
    {
        self::store()->forget(self::SHOWROOM_KEY);
        self::store()->forget(self::ADMIN_SETTINGS_KEY);
    }
}
