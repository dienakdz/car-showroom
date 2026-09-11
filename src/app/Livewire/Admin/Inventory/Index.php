<?php

namespace App\Livewire\Admin\Inventory;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\CarUnit;
use App\Models\Trim;
use App\Services\Admin\InventoryWorkflowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends AdminPageComponent
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'status', except: '')]
    public string $status = '';

    #[Url(as: 'condition', except: '')]
    public string $condition = '';

    #[Url(as: 'trim_id', except: 0)]
    public int $trimId = 0;

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(): void
    {
        $feedback = session()->pull('inventory_feedback');
        if (is_array($feedback)) {
            $this->feedback = $feedback;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage('inventoryPage');
    }

    public function updatedStatus(): void
    {
        $this->resetPage('inventoryPage');
    }

    public function updatedCondition(): void
    {
        $this->resetPage('inventoryPage');
    }

    public function updatedTrimId(): void
    {
        $this->resetPage('inventoryPage');
    }

    public function filterByStatus(string $status): void
    {
        $this->status = in_array($status, ['available', 'on_hold', 'draft', 'sold', 'archived'], true) ? $status : '';
        $this->resetPage('inventoryPage');
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'condition', 'trimId']);
        $this->resetPage('inventoryPage');
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function publish(int $carUnitId, InventoryWorkflowService $service): void
    {
        $this->authorizeAdminAccess($this->requiredPermission());
        $carUnit = CarUnit::query()->findOrFail($carUnitId);

        $service->publish($carUnit);

        $this->feedback = [
            'type' => 'success',
            'message' => "Đã publish xe [{$carUnit->stock_code}] lên sàn inventory.",
        ];
    }

    public function archive(int $carUnitId, InventoryWorkflowService $service): void
    {
        $this->authorizeAdminAccess($this->requiredPermission());
        $carUnit = CarUnit::query()->findOrFail($carUnitId);

        $service->archive($carUnit);

        $this->feedback = [
            'type' => 'success',
            'message' => "Đã lưu trữ (archive) xe [{$carUnit->stock_code}].",
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.inventory.index', [
            'carUnits' => $this->filteredQuery()->paginate(12, ['*'], 'inventoryPage'),
            'statusCounts' => [
                'all' => CarUnit::query()->count(),
                'available' => CarUnit::query()->where('status', 'available')->count(),
                'on_hold' => CarUnit::query()->where('status', 'on_hold')->count(),
                'draft' => CarUnit::query()->where('status', 'draft')->count(),
                'sold' => CarUnit::query()->where('status', 'sold')->count(),
                'archived' => CarUnit::query()->where('status', 'archived')->count(),
            ],
            'trims' => $this->availableTrims(),
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Quản lý kho xe',
            'adminPageDescription' => 'Theo dõi chi tiết xe trong kho, định giá và quy trình xuất bản, giữ xe.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'inventory.manage';
    }

    /**
     * @return Builder<CarUnit>
     */
    private function filteredQuery(): Builder
    {
        $search = trim($this->search);

        return CarUnit::query()
            ->with([
                'trim.model.make',
                'media',
            ])
            ->withCount(['leads', 'appointments'])
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->condition !== '', fn (Builder $q) => $q->where('condition', $this->condition))
            ->when($this->trimId > 0, fn (Builder $q) => $q->where('trim_id', $this->trimId))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->where('stock_code', 'like', "%{$search}%")
                        ->orWhere('vin', 'like', "%{$search}%")
                        ->orWhereHas('trim', function (Builder $t) use ($search): void {
                            $t->where('name', 'like', "%{$search}%")
                                ->orWhereHas('model', function (Builder $m) use ($search): void {
                                    $m->where('name', 'like', "%{$search}%")
                                        ->orWhereHas('make', function (Builder $mk) use ($search): void {
                                            $mk->where('name', 'like', "%{$search}%");
                                        });
                                });
                        });
                });
            })
            ->latest();
    }

    /**
     * @return Collection<int, Trim>
     */
    private function availableTrims(): Collection
    {
        return Trim::query()
            ->with('model.make')
            ->orderBy('name')
            ->get();
    }
}
