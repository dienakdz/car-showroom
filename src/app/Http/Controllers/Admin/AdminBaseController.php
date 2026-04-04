<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Showroom;
use Illuminate\Support\Collection;
use Illuminate\View\View;

abstract class AdminBaseController extends Controller
{
    protected function adminView(string $view, array $data = []): View
    {
        $showroom = Showroom::query()->first();
        $settings = $this->loadAdminSettings();
        $brandName = trim((string) data_get($settings, 'site.brand_name.value', ''));

        if ($brandName === '') {
            $brandName = $showroom?->name ?: 'Car Showroom';
        }

        return view($view, array_merge([
            'adminCurrentUser' => auth()->user(),
            'adminShowroom' => $showroom,
            'adminSettings' => $settings,
            'adminBrandName' => $brandName,
            'adminDefaultCurrency' => data_get($settings, 'site.default_currency.value', 'VND'),
        ], $data));
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
        return Setting::query()
            ->whereIn('key', [
                'site.brand_name',
                'site.default_currency',
                'contact.sales_hotline',
                'inventory.show_on_hold_public',
                'notifications.lead_email_enabled',
            ])
            ->get()
            ->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->value_json]);
    }
}
