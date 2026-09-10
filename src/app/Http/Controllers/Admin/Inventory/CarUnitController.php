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
use Illuminate\Http\JsonResponse;
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
            ->with(['trim.model.make', 'media'])
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

        $statusCounts = [
            'all' => CarUnit::query()->count(),
            'available' => CarUnit::query()->where('status', 'available')->count(),
            'on_hold' => CarUnit::query()->where('status', 'on_hold')->count(),
            'sold' => CarUnit::query()->where('status', 'sold')->count(),
        ];

        return $this->adminView('admin.inventory.index', [
            'adminPageTitle' => 'Quản lý kho xe',
            'adminPageDescription' => 'Theo dõi chi tiết xe trong kho, định giá và quy trình xuất bản, giữ xe.',
            'carUnits' => $carUnits,
            'statusCounts' => $statusCounts,
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
        return $this->formView(new CarUnit, 'Thêm xe mới vào kho', 'Khai báo thông tin định danh, thông số kỹ thuật, hình ảnh và định giá cho xe.');
    }

    public function store(UpsertCarUnitRequest $request, InventoryWorkflowService $service): RedirectResponse
    {
        $carUnit = $service->save($request->validated(), $request->user());
        $this->pushSuccessToast('Đã tạo xe mới trong kho thành công.');

        return redirect()->route('admin.inventory.edit', $carUnit);
    }

    public function edit(CarUnit $carUnit): View
    {
        return $this->formView(
            $carUnit->load([
                'trim.model.make',
                'media',
            ]),
            'Cập nhật thông tin xe',
            'Chỉnh sửa thông tin định danh, thông số kỹ thuật, hình ảnh và quản lý lịch sử trạng thái của xe.'
        );
    }

    public function update(UpsertCarUnitRequest $request, CarUnit $carUnit, InventoryWorkflowService $service): RedirectResponse
    {
        $service->save($request->validated(), $request->user(), $carUnit);
        $this->pushSuccessToast('Đã cập nhật thông tin xe thành công.');

        return redirect()->route('admin.inventory.edit', $carUnit);
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $path = $request->file('file')->store('inventory-media', 'public');
        $url = '/storage/' . $path;

        return response()->json([
            'success' => true,
            'path_or_url' => $url,
            'filename' => $request->file('file')->getClientOriginalName(),
        ]);
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
            'exteriorColors' => Color::query()->where('type', 'exterior')->orderBy('name')->get(),
            'interiorColors' => Color::query()->where('type', 'interior')->orderBy('name')->get(),
            'colors' => Color::query()->orderBy('name')->get(),
        ]);
    }
}
