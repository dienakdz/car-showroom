<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Inventory\UpsertCarUnitRequest;
use App\Models\BodyType;
use App\Models\CarUnit;
use App\Models\Color;
use App\Models\Drivetrain;
use App\Models\FuelType;
use App\Models\Transmission;
use App\Models\Trim;
use App\Services\Admin\InventoryWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarUnitController extends AdminBaseController
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $condition = trim((string) $request->string('condition'));
        $trimId = $request->integer('trim_id');

        $carUnits = CarUnit::query()
            ->with(['trim.model.make'])
            ->withCount(['leads', 'appointments'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($condition !== '', fn ($query) => $query->where('condition', $condition))
            ->when($trimId > 0, fn ($query) => $query->where('trim_id', $trimId))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('stock_code', 'like', '%' . $search . '%')
                        ->orWhere('vin', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return $this->adminView('admin.inventory.index', [
            'adminPageTitle' => 'Inventory',
            'adminPageDescription' => 'Quan ly xe trong kho, workflow publish/archive va giu xe.',
            'carUnits' => $carUnits,
            'trims' => Trim::query()->with('model.make')->orderBy('name')->get(),
            'filters' => [
                'q' => $search,
                'status' => $status,
                'condition' => $condition,
                'trim_id' => $trimId,
            ],
        ]);
    }

    public function create(): View
    {
        return $this->formView(new CarUnit(), 'Them xe vao kho', 'Khai bao inventory item, media va thong tin noi bo cho xe.');
    }

    public function store(UpsertCarUnitRequest $request, InventoryWorkflowService $service): RedirectResponse
    {
        $carUnit = $service->save($request->validated(), $request->user());
        $this->pushSuccessToast('Da tao car unit moi.');

        return redirect()->route('admin.inventory.edit', $carUnit);
    }

    public function edit(CarUnit $carUnit): View
    {
        return $this->formView(
            $carUnit->load([
                'trim.model.make',
                'media',
                'holds.createdBy',
                'priceHistories.changedBy',
                'sale',
            ]),
            'Cap nhat inventory item',
            'Chinh sua listing, media, ghi chu noi bo va theo doi lich su workflow.'
        );
    }

    public function update(UpsertCarUnitRequest $request, CarUnit $carUnit, InventoryWorkflowService $service): RedirectResponse
    {
        $service->save($request->validated(), $request->user(), $carUnit);
        $this->pushSuccessToast('Da cap nhat inventory item.');

        return redirect()->route('admin.inventory.edit', $carUnit);
    }

    protected function formView(CarUnit $carUnit, string $title, string $description): View
    {
        return $this->adminView('admin.inventory.form', [
            'adminPageTitle' => $title,
            'adminPageDescription' => $description,
            'carUnit' => $carUnit,
            'trims' => Trim::query()->with('model.make')->orderBy('name')->get(),
            'bodyTypes' => BodyType::query()->orderBy('name')->get(),
            'fuelTypes' => FuelType::query()->orderBy('name')->get(),
            'transmissions' => Transmission::query()->orderBy('name')->get(),
            'drivetrains' => Drivetrain::query()->orderBy('name')->get(),
            'colors' => Color::query()->orderBy('name')->get(),
        ]);
    }
}
