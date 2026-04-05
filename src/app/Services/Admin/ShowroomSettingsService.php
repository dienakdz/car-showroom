<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Models\Showroom;
use App\Support\ViewDataCache;
use Illuminate\Support\Facades\DB;

class ShowroomSettingsService
{
    public function update(array $validated): void
    {
        DB::transaction(function () use ($validated): void {
            $showroom = Showroom::query()->first() ?? new Showroom();
            $showroom->fill([
                'name' => $validated['showroom_name'],
                'phone' => $validated['showroom_phone'],
                'email' => $validated['showroom_email'] ?? null,
                'address' => $validated['showroom_address'] ?? null,
                'description' => $validated['showroom_description'] ?? null,
            ]);
            $showroom->save();

            $settings = [
                'site.brand_name' => ['value' => $validated['brand_name']],
                'site.default_currency' => ['value' => $validated['default_currency']],
                'contact.sales_hotline' => ['value' => $validated['sales_hotline'] ?? null],
                'inventory.show_on_hold_public' => ['enabled' => (bool) ($validated['show_on_hold_public'] ?? false)],
                'notifications.lead_email_enabled' => ['enabled' => (bool) ($validated['email_lead_notifications'] ?? false)],
            ];

            foreach ($settings as $key => $value) {
                Setting::query()->updateOrCreate(
                    ['key' => $key],
                    ['value_json' => $value]
                );
            }
        });

        ViewDataCache::forgetShowroomAndSettings();
    }
}
