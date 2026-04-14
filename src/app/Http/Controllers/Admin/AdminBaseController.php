<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Support\Admin\AdminContextResolver;
use Illuminate\Support\Collection;
use Illuminate\View\View;

abstract class AdminBaseController extends Controller
{
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
        return app(AdminContextResolver::class)->resolve();
    }
}
