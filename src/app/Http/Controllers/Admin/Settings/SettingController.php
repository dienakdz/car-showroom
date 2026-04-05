<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Settings\UpdateShowroomSettingsRequest;
use App\Services\Admin\ShowroomSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends AdminBaseController
{
    public function index(): View
    {
        return $this->adminView('admin.settings.index', [
            'adminPageTitle' => 'Settings & Showroom',
            'adminPageDescription' => 'Cap nhat thong tin showroom va mot so policy van hanh co ban.',
            'showroom' => $this->loadAdminShowroom(),
        ]);
    }

    public function update(UpdateShowroomSettingsRequest $request, ShowroomSettingsService $service): RedirectResponse
    {
        $service->update($request->validated());
        $this->pushSuccessToast('Da cap nhat showroom va settings.');

        return redirect()->route('admin.settings.index');
    }
}
