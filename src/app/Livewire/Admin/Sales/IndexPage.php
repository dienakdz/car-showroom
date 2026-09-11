<?php

namespace App\Livewire\Admin\Sales;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class IndexPage extends AdminPageComponent
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(): void
    {
        $feedback = session()->pull('sale_feedback');
        if (is_array($feedback)) {
            $this->feedback = $feedback;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage('salesPage');
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage('salesPage');
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function render(): View
    {
        return view('livewire.admin.sales.index-page', [
            'sales' => $this->filteredQuery()->paginate(12, ['*'], 'salesPage'),
            'totalRevenue' => (int) Sale::query()->sum('sold_price'),
            'monthlyCount' => Sale::query()->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'totalCount' => Sale::query()->count(),
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Quản lý Bán hàng & Hợp đồng',
            'adminPageDescription' => 'Theo dõi chi tiết hợp đồng bán xe, doanh thu và đối soát thanh toán.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'sales.manage';
    }

    /**
     * @return Builder<Sale>
     */
    private function filteredQuery(): Builder
    {
        $search = trim($this->search);

        return Sale::query()
            ->with([
                'buyer:id,name,email,phone',
                'createdBy:id,name',
                'carUnit.trim.model.make',
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $inner) use ($search): void {
                    $cleanId = ltrim(preg_replace('/^#?hd-?/i', '', $search) ?? '', '0');
                    if ($cleanId !== '' && is_numeric($cleanId)) {
                        $inner->orWhere('id', (int) $cleanId);
                    }

                    $inner->whereHas('buyer', function (Builder $b) use ($search): void {
                        $b->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                        ->orWhereHas('carUnit', function (Builder $cu) use ($search): void {
                            $cu->where('stock_code', 'like', "%{$search}%")
                                ->orWhereHas('trim', function (Builder $t) use ($search): void {
                                    $t->where('name', 'like', "%{$search}%")
                                        ->orWhereHas('model', function (Builder $m) use ($search): void {
                                            $m->where('name', 'like', "%{$search}%")
                                                ->orWhereHas('make', function (Builder $mk) use ($search): void {
                                                    $mk->where('name', 'like', "%{$search}%");
                                                });
                                        });
                                });
                        })
                        ->orWhereHas('createdBy', function (Builder $u) use ($search): void {
                            $u->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('sold_at');
    }
}
