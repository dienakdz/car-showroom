<?php

namespace App\Livewire\Admin\Customers;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\User;
use App\Services\Admin\CustomerManagementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends AdminPageComponent
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'filter', except: 'all')]
    public string $filter = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage('customersPage');
    }

    public function updatedFilter(): void
    {
        $this->resetPage('customersPage');
    }

    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, ['all', 'purchased', 'appointment', 'active', 'inactive'], true)
            ? $filter
            : 'all';
        $this->resetPage('customersPage');
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filter']);
        $this->resetPage('customersPage');
    }

    public function toggleCustomerStatus(int $customerId, CustomerManagementService $service): void
    {
        $actor = $this->authorizeAdminAccess('customers.manage');
        $customer = User::query()->customer()->findOrFail($customerId);

        $newStatus = $service->toggleStatus($customer, $actor);

        $statusText = $newStatus ? 'kích hoạt' : 'tạm khóa';
        $this->toast('success', "Đã {$statusText} tài khoản khách hàng {$customer->name}.");
    }

    public function render(CustomerManagementService $service): View
    {
        $customers = $this->query()
            ->withCount(['purchases', 'appointments', 'leads'])
            ->paginate(12, ['*'], 'customersPage');

        $stats = $service->getStats();

        return view('livewire.admin.customers.index', [
            'customers' => $customers,
            'stats' => $stats,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Quản lý khách hàng',
            'adminPageDescription' => 'Danh sách tài khoản khách hàng đã đăng ký trong hệ thống showroom.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'customers.manage';
    }

    /**
     * @return Builder<User>
     */
    private function query(): Builder
    {
        $query = User::query()->customer();

        $search = trim($this->search);
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        match ($this->filter) {
            'purchased' => $query->whereHas('purchases'),
            'appointment' => $query->whereHas('appointments', function (Builder $q): void {
                $q->whereIn('status', ['pending', 'confirmed'])
                    ->where('scheduled_at', '>=', now());
            }),
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => null,
        };

        return $query->latest('id');
    }
}
