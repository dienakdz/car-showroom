<?php

namespace App\Livewire\Admin\Customers;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\User;
use App\Services\Admin\CustomerManagementService;
use Illuminate\View\View;
use Livewire\Attributes\Locked;

class Show extends AdminPageComponent
{
    #[Locked]
    public int $customerId;

    public string $tab = 'purchases';

    public function mount(User $customer): void
    {
        if (! $customer->hasRole('customer')) {
            abort(404, 'Không tìm thấy thông tin khách hàng này.');
        }

        $this->customerId = $customer->id;
    }

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['purchases', 'appointments', 'leads', 'reviews'], true)) {
            $this->tab = $tab;
        }
    }

    public function toggleStatus(CustomerManagementService $service): void
    {
        $actor = $this->authorizeAdminAccess('customers.manage');
        $customer = $this->customer();

        $newStatus = $service->toggleStatus($customer, $actor);

        $statusText = $newStatus ? 'kích hoạt' : 'tạm khóa';
        $this->toast('success', "Đã {$statusText} tài khoản khách hàng thành công.");
    }

    public function render(): View
    {
        $customer = $this->customer();

        return view('livewire.admin.customers.show', [
            'customer' => $customer,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => "Hồ sơ khách hàng: {$customer->name}",
            'adminPageDescription' => 'Hồ sơ 360 độ và toàn bộ lịch sử mua xe, lịch hẹn, yêu cầu tư vấn.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'customers.manage';
    }

    private function customer(): User
    {
        return User::query()
            ->with([
                'purchases.carUnit.trim.model.make',
                'purchases.carUnit.media',
                'purchases.createdBy',
                'appointments.carUnit.trim.model.make',
                'appointments.carUnit.media',
                'appointments.trim.model.make',
                'appointments.handledBy',
                'leads.carUnit.trim.model.make',
                'leads.carUnit.media',
                'leads.trim.model.make',
                'leads.assignedTo',
                'trimReviews.trim.model.make',
            ])
            ->customer()
            ->findOrFail($this->customerId);
    }
}
