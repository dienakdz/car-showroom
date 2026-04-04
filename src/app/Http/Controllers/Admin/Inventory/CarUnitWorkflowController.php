<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Inventory\UpdateCarUnitHoldRequest;
use App\Http\Requests\Admin\Inventory\UpdateCarUnitPriceRequest;
use App\Models\CarUnit;
use App\Services\Admin\InventoryWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

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

    public function hold(UpdateCarUnitHoldRequest $request, CarUnit $carUnit, InventoryWorkflowService $service): RedirectResponse
    {
        $service->hold(
            $carUnit,
            Carbon::parse($request->string('hold_until')->value()),
            $request->string('reason')->value() ?: null,
            $request->user()
        );
        $this->pushSuccessToast('Da dat hold cho xe.');

        return back();
    }

    public function release(CarUnit $carUnit, InventoryWorkflowService $service): RedirectResponse
    {
        $service->release($carUnit);
        $this->pushSuccessToast('Da release hold.');

        return back();
    }

    public function updatePrice(UpdateCarUnitPriceRequest $request, CarUnit $carUnit, InventoryWorkflowService $service): RedirectResponse
    {
        $priceInput = $request->input('price');

        $service->updatePrice(
            $carUnit,
            $priceInput === null || $priceInput === '' ? null : (int) $priceInput,
            $request->user()
        );
        $this->pushSuccessToast('Da cap nhat gia xe.');

        return back();
    }
}
