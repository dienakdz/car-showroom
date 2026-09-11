<?php

namespace App\Livewire\Admin\Sales;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\User;
use App\Services\Admin\SaleManagementService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class Form extends AdminPageComponent
{
    /** @var array<string, mixed> */
    public array $form = [];

    public string $activeTab = 'deal';

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(?int $car_unit_id = null, ?int $lead_id = null): void
    {
        $this->form = [
            'car_unit_id' => $car_unit_id,
            'lead_id' => $lead_id,
            'sold_price' => null,
            'sold_at' => now()->format('Y-m-d\TH:i'),
            'buyer_user_id' => null,
            'buyer_name' => '',
            'buyer_email' => '',
            'buyer_phone' => '',
        ];

        if ($car_unit_id !== null && $car_unit_id > 0) {
            $this->syncCarUnitContext($car_unit_id);
        }

        if ($lead_id !== null && $lead_id > 0) {
            $this->syncLeadContext($lead_id);
        }
    }

    public function updatedFormCarUnitId(?int $carUnitId): void
    {
        if ($carUnitId !== null && $carUnitId > 0) {
            $this->syncCarUnitContext($carUnitId);
        }
    }

    public function updatedFormLeadId(?int $leadId): void
    {
        if ($leadId !== null && $leadId > 0) {
            $this->syncLeadContext($leadId);
        }
    }

    public function updatedFormBuyerUserId(?int $userId): void
    {
        if ($userId !== null && $userId > 0) {
            $user = User::query()->find($userId);
            if ($user !== null) {
                $this->form['buyer_name'] = $user->name;
                $this->form['buyer_email'] = $user->email;
                $this->form['buyer_phone'] = $user->phone ?? '';
            }
        }
    }

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['deal', 'buyer'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function save(SaleManagementService $service): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->form = $this->normalizeForm($this->form);

        $validated = $this->validate(
            $this->rules(),
            attributes: $this->validationAttributes(),
        );

        if (empty($this->form['buyer_user_id']) && empty($this->form['buyer_email']) && empty($this->form['buyer_phone'])) {
            $this->activeTab = 'buyer';
            $this->addError('form.buyer_phone', 'Cần có email hoặc số điện thoại khi tạo khách hàng mới.');

            return;
        }

        $user = $this->authorizeAdminAccess($this->requiredPermission());

        $service->create($validated['form'], $user);

        session()->flash('sale_feedback', [
            'type' => 'success',
            'message' => 'Đã tạo hợp đồng bán xe và đóng inventory item thành công.',
        ]);

        $this->redirectRoute('admin.sales.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.sales.form', [
            'availableCarUnits' => $this->availableCarUnits(),
            'buyers' => User::query()->orderBy('name')->limit(100)->get(),
            'leads' => Lead::query()
                ->whereIn('status', ['new', 'contacted', 'qualified', 'negotiating'])
                ->latest()
                ->limit(100)
                ->get(),
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Tạo hợp đồng bán xe',
            'adminPageDescription' => 'Chốt giao dịch bán xe, tạo thông tin khách hàng nếu cần và đóng lead liên quan.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'sales.manage';
    }

    /**
     * @return Collection<int, CarUnit>
     */
    private function availableCarUnits(): Collection
    {
        return CarUnit::query()
            ->with('trim.model.make')
            ->whereIn('status', ['available', 'on_hold'])
            ->whereDoesntHave('sale')
            ->orderBy('stock_code')
            ->limit(100)
            ->get();
    }

    private function syncCarUnitContext(int $carUnitId): void
    {
        $carUnit = CarUnit::query()->find($carUnitId);
        if ($carUnit !== null && (empty($this->form['sold_price']) || (int) $this->form['sold_price'] === 0)) {
            $this->form['sold_price'] = $carUnit->price;
        }
    }

    private function syncLeadContext(int $leadId): void
    {
        $lead = Lead::query()->find($leadId);
        if ($lead === null) {
            return;
        }

        if (empty($this->form['buyer_user_id']) && $lead->user_id !== null) {
            $this->form['buyer_user_id'] = $lead->user_id;
        }

        if (empty($this->form['buyer_name']) && filled($lead->name)) {
            $this->form['buyer_name'] = $lead->name;
        }

        if (empty($this->form['buyer_phone']) && filled($lead->phone)) {
            $this->form['buyer_phone'] = $lead->phone;
        }

        if (empty($this->form['buyer_email']) && filled($lead->email)) {
            $this->form['buyer_email'] = $lead->email;
        }

        if (empty($this->form['car_unit_id']) && $lead->car_unit_id !== null) {
            $this->form['car_unit_id'] = $lead->car_unit_id;
            $this->syncCarUnitContext($lead->car_unit_id);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.car_unit_id' => ['required', 'integer', 'exists:car_units,id', 'unique:sales,car_unit_id'],
            'form.buyer_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'form.buyer_name' => ['required_without:form.buyer_user_id', 'nullable', 'string', 'max:255'],
            'form.buyer_email' => ['nullable', 'email', 'max:255'],
            'form.buyer_phone' => ['required_without:form.buyer_user_id', 'nullable', 'string', 'max:20'],
            'form.lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'form.sold_price' => ['nullable', 'integer', 'min:0'],
            'form.sold_at' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.car_unit_id' => 'xe trong kho',
            'form.buyer_user_id' => 'khách hàng',
            'form.buyer_name' => 'họ tên khách hàng',
            'form.buyer_email' => 'email khách hàng',
            'form.buyer_phone' => 'số điện thoại',
            'form.lead_id' => 'lead liên kết',
            'form.sold_price' => 'giá bán chốt',
            'form.sold_at' => 'thời gian chốt giao dịch',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeForm(array $data): array
    {
        return [
            'car_unit_id' => ! empty($data['car_unit_id']) ? (int) $data['car_unit_id'] : null,
            'lead_id' => ! empty($data['lead_id']) ? (int) $data['lead_id'] : null,
            'sold_price' => isset($data['sold_price']) && $data['sold_price'] !== '' ? (int) $data['sold_price'] : null,
            'sold_at' => ! empty($data['sold_at']) ? trim((string) $data['sold_at']) : null,
            'buyer_user_id' => ! empty($data['buyer_user_id']) ? (int) $data['buyer_user_id'] : null,
            'buyer_name' => ! empty($data['buyer_name']) ? trim((string) $data['buyer_name']) : null,
            'buyer_email' => ! empty($data['buyer_email']) ? strtolower(trim((string) $data['buyer_email'])) : null,
            'buyer_phone' => ! empty($data['buyer_phone']) ? preg_replace('/\D+/', '', (string) $data['buyer_phone']) : null,
        ];
    }
}
