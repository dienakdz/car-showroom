<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\CarUnit;
use App\Services\Admin\InventoryWorkflowService;
use Illuminate\Http\RedirectResponse;

class CarUnitWorkflowController extends AdminBaseController
{
    public function publish(CarUnit $carUnit, InventoryWorkflowService $service): RedirectResponse
    {
        $service->publish($carUnit);
        $this->pushSuccessToast('Da publish xe len inventory.');

        return back();
    }

    public function archive(CarUnit $carUnit, InventoryWorkflowService $service): RedirectResponse
    {
        $service->archive($carUnit);
        $this->pushSuccessToast('Da archive xe.');

        return back();
    }
}
