<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Sales\StoreSaleRequest;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\User;
use App\Services\Admin\SaleManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaleController extends AdminBaseController
{
    public function index(): View
    {
        $sales = Sale::query()
            ->with([
                'buyer:id,name,email,phone',
                'createdBy:id,name',
                'carUnit.trim.model.make',
            ])
            ->orderByDesc('sold_at')
            ->paginate(12);

        return $this->adminView('admin.sales.index', [
            'adminPageTitle' => 'Sales log',
            'adminPageDescription' => 'Theo doi xe da chot, buyer va nguoi tao giao dich.',
            'sales' => $sales,
        ]);
    }

    public function create(): View
    {
        return $this->adminView('admin.sales.create', [
            'adminPageTitle' => 'Tao sale',
            'adminPageDescription' => 'Chot giao dich offline, tao buyer neu can va dong lead lien quan.',
            'availableCarUnits' => CarUnit::query()
                ->with('trim.model.make')
                ->whereIn('status', ['available', 'on_hold'])
                ->whereDoesntHave('sale')
                ->orderBy('stock_code')
                ->limit(100)
                ->get(),
            'buyers' => User::query()->orderBy('name')->limit(100)->get(),
            'leads' => Lead::query()->latest()->limit(100)->get(),
        ]);
    }

    public function store(StoreSaleRequest $request, SaleManagementService $service): RedirectResponse
    {
        $service->create($request->validated(), $request->user());
        $this->pushSuccessToast('Da tao sale va dong inventory item.');

        return redirect()->route('admin.sales.index');
    }
}
