<?php

namespace App\Livewire\Admin\Leads;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\Lead;
use App\Models\User;
use App\Services\Admin\LeadWorkflowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;

class Show extends AdminPageComponent
{
    private const STATUSES = ['new', 'contacted', 'qualified', 'booked', 'closed', 'lost'];

    #[Locked]
    public int $leadId;

    /** @var array<string, mixed> */
    public array $form = [];

    public string $note = '';

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(Lead $lead): void
    {
        $this->leadId = $lead->id;
        $this->fillForm($lead);
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function save(LeadWorkflowService $service): void
    {
        $this->feedback = [];
        $this->resetErrorBag();

        $validated = $this->validate(
            $this->leadRules(),
            attributes: $this->validationAttributes(),
        );

        $user = $this->authorizeAdminAccess('leads.manage');
        $lead = Lead::query()->findOrFail($this->leadId);

        $updatedLead = $service->updateLead($lead, $validated['form'], $user);
        $this->fillForm($updatedLead);

        $this->feedback = [
            'type' => 'success',
            'message' => 'Đã cập nhật thông tin khách hàng & lead thành công.',
        ];
    }

    public function changeLeadStatus(string $status, LeadWorkflowService $service): void
    {
        $this->feedback = [];
        $user = $this->authorizeAdminAccess('leads.manage');
        $lead = Lead::query()->findOrFail($this->leadId);

        try {
            $updatedLead = $service->changeStatus($lead, $status, $user);
            $this->fillForm($updatedLead);

            $this->feedback = [
                'type' => 'success',
                'message' => 'Đã chuyển giai đoạn lead thành công.',
            ];
        } catch (\InvalidArgumentException $e) {
            $this->feedback = [
                'type' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function addNote(LeadWorkflowService $service): void
    {
        $this->feedback = [];
        $this->resetValidation('note');
        $this->note = trim($this->note);

        $validated = $this->validateOnly('note', [
            'note' => ['required', 'string', 'min:2'],
        ], attributes: [
            'note' => 'nội dung ghi chú',
        ]);

        $user = $this->authorizeAdminAccess('leads.manage');
        $lead = Lead::query()->findOrFail($this->leadId);

        $service->addNote($lead, (string) $validated['note'], $user);

        $this->note = '';
        $this->feedback = [
            'type' => 'success',
            'message' => 'Đã thêm ghi chú chăm sóc khách hàng.',
        ];
    }

    public function render(): View
    {
        $lead = Lead::query()
            ->with([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
                'carUnit.primaryMedia',
                'trim.model.make',
                'trim.carUnits.primaryMedia',
                'notes.createdBy:id,name',
                'appointments.handledBy:id,name',
                'appointments.carUnit.trim.model.make',
                'appointments.trim.model.make',
            ])
            ->findOrFail($this->leadId);

        return view('livewire.admin.leads.show', [
            'lead' => $lead,
            'staffUsers' => $this->assignableUsers(),
            'statusOptions' => self::STATUSES,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Hồ sơ Lead #' . $lead->id . ' - ' . $lead->name,
            'adminPageDescription' => 'Chi tiết phễu chuyển đổi, nhu cầu xe và lịch sử tương tác với khách hàng.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'leads.manage';
    }

    /**
     * @return array<string, mixed>
     */
    private function leadRules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.phone' => ['required', 'string', 'max:20'],
            'form.email' => ['nullable', 'email', 'max:255'],
            'form.message' => ['nullable', 'string'],
            'form.assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'form.status' => ['required', Rule::in(self::STATUSES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.name' => 'tên khách hàng',
            'form.phone' => 'số điện thoại',
            'form.email' => 'email',
            'form.message' => 'yêu cầu của khách hàng',
            'form.assigned_to' => 'chuyên viên tư vấn',
            'form.status' => 'trạng thái phễu',
        ];
    }

    private function fillForm(Lead $lead): void
    {
        $this->form = [
            'name' => (string) $lead->name,
            'phone' => (string) $lead->phone,
            'email' => (string) ($lead->email ?? ''),
            'assigned_to' => $lead->assigned_to === null ? '' : (string) $lead->assigned_to,
            'status' => (string) $lead->status,
            'message' => (string) ($lead->message ?? ''),
        ];
    }

    /**
     * @return Collection<int, User>
     */
    private function assignableUsers(): Collection
    {
        return User::query()
            ->whereHas('roles', fn (Builder $query): Builder => $query->whereIn('roles.name', ['admin', 'staff']))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
