<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Admin\AdminContextResolver;
use Illuminate\View\View;

abstract class AdminBaseController extends Controller
{
    /**
     * @param  array<string, mixed>  $data
     */
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

    /**
     * @return array<string, mixed>
     */
    protected function resolveAdminContext(): array
    {
        return app(AdminContextResolver::class)->resolve();
    }
}
