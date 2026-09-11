<?php

namespace App\Livewire\Admin\Leads;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
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

    public function save(): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->form = $this->normalizeForm($this->form);

        $validated = $this->validate(
            $this->leadRules(),
            attributes: $this->validationAttributes(),
        );

        $lead = Lead::query()->findOrFail($this->leadId);
        $lead->update($validated['form']);
        $this->fillForm($lead->refresh());

        $this->feedback = [
            'type' => 'success',
            'message' => 'Da cap nhat lead.',
        ];
    }

    public function addNote(): void
    {
        $this->feedback = [];
        $this->resetValidation('note');
        $this->note = trim($this->note);
        $validated = $this->validateOnly('note', [
            'note' => ['required', 'string'],
        ], attributes: [
            'note' => 'noi dung ghi chu',
        ]);

        $user = $this->authorizeAdminAccess('leads.manage');

        LeadNote::query()->create([
            'lead_id' => $this->leadId,
            'created_by' => $user->id,
            'note' => trim((string) $validated['note']),
        ]);

        $this->note = '';
        $this->feedback = [
            'type' => 'success',
            'message' => 'Da them note cho lead.',
        ];
    }

    public function render(): View
    {
        $lead = Lead::query()
            ->with([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
                'trim.model.make',
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
            'adminPageTitle' => 'Chi tiet lead #' . $lead->id,
            'adminPageDescription' => 'Cap nhat pipeline, note log va assignment cho lead.',
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
            'form.assigned_to' => ['nullable', 'integer', Rule::in($this->assignableUsers()->modelKeys())],
            'form.status' => ['required', Rule::in(self::STATUSES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.name' => 'ten lead',
            'form.phone' => 'so dien thoai',
            'form.email' => 'email',
            'form.message' => 'noi dung yeu cau',
            'form.assigned_to' => 'nhan vien phu trach',
            'form.status' => 'trang thai',
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
     * @param  array<string, mixed>  $form
     * @return array<string, mixed>
     */
    private function normalizeForm(array $form): array
    {
        return [
            'name' => trim((string) ($form['name'] ?? '')),
            'phone' => trim((string) ($form['phone'] ?? '')),
            'email' => $this->nullableString($form['email'] ?? null),
            'assigned_to' => (int) ($form['assigned_to'] ?? 0) > 0 ? (int) $form['assigned_to'] : null,
            'status' => (string) ($form['status'] ?? ''),
            'message' => $this->nullableString($form['message'] ?? null),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }

    /**
     * @return Collection<int, User>
     */
    private function assignableUsers(): Collection
    {
        return User::query()
            ->whereHas('roles', fn (Builder $query): Builder => $query->whereIn('roles.name', ['admin', 'staff']))
            ->orderBy('name')
            ->get();
    }
}
