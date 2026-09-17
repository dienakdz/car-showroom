<?php

namespace App\Livewire\Admin\Appointments;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\Appointment;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\User;
use App\Services\Admin\AppointmentManagementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;

class Form extends AdminPageComponent
{
    #[Locked]
    public ?int $appointmentId = null;

    public string $customerMode = 'existing_lead';

    /** @var array<string, mixed> */
    public array $form = [];

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(?Appointment $appointmentRecord = null, int|string|null $lead_id = null): void
    {
        $this->appointmentId = $appointmentRecord?->id;
        $this->fillForm($appointmentRecord);

        $leadId = (int) $lead_id;
        if (! $appointmentRecord && $leadId > 0) {
            $this->form['lead_id'] = $leadId;
            $this->syncLeadContext($leadId);
        }

        $this->feedback = (array) session()->pull('appointment_feedback', []);
    }

    public function setCustomerMode(string $mode): void
    {
        if (in_array($mode, ['existing_lead', 'new_lead'], true)) {
            $this->customerMode = $mode;
            if ($mode === 'new_lead') {
                $this->form['lead_id'] = null;
                $this->form['user_id'] = null;
            }
        }
    }

    public function updatedFormLeadId(mixed $leadId): void
    {
        $id = (int) $leadId;
        $this->form['lead_id'] = $id > 0 ? $id : null;

        if ($id > 0) {
            $this->syncLeadContext($id);
        }
    }

    public function updatedFormUserId(mixed $userId): void
    {
        $id = (int) $userId;
        $this->form['user_id'] = $id > 0 ? $id : null;

        if ($id > 0 && $lead = Lead::query()->where('user_id', $id)->whereNotIn('status', ['closed', 'lost'])->latest('id')->first()) {
            $this->form['lead_id'] = $lead->id;
            $this->syncLeadContext($lead->id);
        }
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function quickChangeStatus(string $status, AppointmentManagementService $service): void
    {
        if ($this->appointmentId === null || ! in_array($status, Index::STATUSES, true)) {
            return;
        }

        $user = $this->authorizeAdminAccess($this->requiredPermission());
        $appointment = Appointment::query()->findOrFail($this->appointmentId);

        $saved = $service->updateStatus($appointment, $status, $user);
        $this->fillForm($saved);

        $this->feedback = [
            'type' => 'success',
            'message' => 'Đã cập nhật trạng thái lịch hẹn: "' . Index::STATUS_LABELS[$status] . '".',
        ];
    }

    public function save(AppointmentManagementService $service): void
    {
        $this->feedback = [];
        $this->resetErrorBag();

        $validated = $this->validate(
            $this->rules(),
            attributes: $this->validationAttributes(),
        );

        $formData = $validated['form'];
        $formData['customer_mode'] = $this->customerMode;

        $user = $this->authorizeAdminAccess($this->requiredPermission());

        $appointment = $this->appointmentId !== null
            ? Appointment::query()->findOrFail($this->appointmentId)
            : null;

        $savedAppointment = $service->save($formData, $user, $appointment);

        if ($this->appointmentId === null) {
            session()->flash('appointment_feedback', [
                'type' => 'success',
                'message' => 'Đã tạo lịch hẹn mới #' . $savedAppointment->id . ' thành công.',
            ]);

            $this->redirectRoute('admin.appointments.edit', $savedAppointment, navigate: true);

            return;
        }

        $this->fillForm($savedAppointment);
        $this->feedback = [
            'type' => 'success',
            'message' => 'Đã lưu cập nhật thông tin lịch hẹn.',
        ];
    }

    public function render(): View
    {
        $appointment = $this->appointmentId !== null
            ? Appointment::query()
                ->with(['user', 'carUnit.trim.model.make', 'carUnit.primaryMedia', 'lead', 'handledBy'])
                ->findOrFail($this->appointmentId)
            : new Appointment;

        $selectedCarUnitId = (int) ($this->form['car_unit_id'] ?? 0);
        $selectedLeadId = (int) ($this->form['lead_id'] ?? 0);

        $carUnits = CarUnit::query()
            ->with(['trim.model.make', 'primaryMedia'])
            ->where(function (Builder $q) use ($selectedCarUnitId): void {
                $q->whereIn('status', ['available', 'on_hold']);
                if ($selectedCarUnitId > 0) {
                    $q->orWhere('id', $selectedCarUnitId);
                }
            })
            ->orderBy('stock_code')
            ->get();

        $leads = Lead::query()
            ->select(['id', 'name', 'phone', 'user_id', 'car_unit_id', 'assigned_to', 'status'])
            ->with(['carUnit.trim.model.make', 'user:id,name,email,phone'])
            ->where(function (Builder $q) use ($selectedLeadId): void {
                $q->whereNotIn('status', ['closed', 'lost']);
                if ($selectedLeadId > 0) {
                    $q->orWhere('id', $selectedLeadId);
                }
            })
            ->latest()
            ->get();

        return view('livewire.admin.appointments.form', [
            'appointment' => $appointment,
            'activeCarUnit' => $selectedCarUnitId > 0 ? $carUnits->firstWhere('id', $selectedCarUnitId) : null,
            'activeLead' => $selectedLeadId > 0 ? $leads->firstWhere('id', $selectedLeadId) : null,
            'staffUsers' => $this->staffUsers(),
            'customerUsers' => User::query()->select(['id', 'name', 'phone', 'email'])->orderBy('name')->get(),
            'leads' => $leads,
            'carUnits' => $carUnits,
            'statusOptions' => Index::STATUS_LABELS,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => $this->appointmentId !== null ? 'Cập nhật lịch hẹn #' . $this->appointmentId : 'Tạo lịch hẹn mới',
            'adminPageDescription' => $this->appointmentId !== null
                ? 'Chỉnh sửa thông tin, chuyên viên tư vấn và trạng thái buổi hẹn.'
                : 'Đặt lịch hẹn mới xem xe hoặc lái thử tại showroom.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'appointments.manage';
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $isNewLead = $this->customerMode === 'new_lead';

        return [
            'form.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'form.lead_id' => [$isNewLead || ! empty($this->form['user_id']) ? 'nullable' : 'required', 'integer', 'exists:leads,id'],
            'form.customer_name' => [$isNewLead ? 'required' : 'nullable', 'string', 'max:255'],
            'form.customer_phone' => [$isNewLead ? 'required' : 'nullable', 'string', 'max:50'],
            'form.customer_email' => ['nullable', 'email', 'max:255'],
            'form.car_unit_id' => ['required', 'integer', 'exists:car_units,id'],
            'form.handled_by' => ['nullable', 'integer', 'exists:users,id'],
            'form.status' => ['required', Rule::in(Index::STATUSES)],
            'form.scheduled_at' => ['required', 'date'],
            'form.note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.user_id' => 'khách hàng',
            'form.lead_id' => 'lead liên kết',
            'form.customer_name' => 'tên khách hàng',
            'form.customer_phone' => 'số điện thoại',
            'form.customer_email' => 'email khách hàng',
            'form.car_unit_id' => 'xe trong kho',
            'form.handled_by' => 'chuyên viên đón tiếp',
            'form.status' => 'trạng thái',
            'form.scheduled_at' => 'thời gian hẹn',
            'form.note' => 'ghi chú',
        ];
    }

    private function fillForm(?Appointment $appointment): void
    {
        $this->customerMode = 'existing_lead';

        $this->form = [
            'user_id' => $appointment?->user_id,
            'lead_id' => $appointment?->lead_id,
            'customer_name' => '',
            'customer_phone' => '',
            'customer_email' => '',
            'car_unit_id' => $appointment?->car_unit_id,
            'handled_by' => $appointment?->handled_by,
            'status' => $appointment !== null ? (string) $appointment->status : 'pending',
            'scheduled_at' => optional($appointment?->scheduled_at)->format('Y-m-d\TH:i') ?? now()->addDay()->setHour(10)->setMinute(0)->format('Y-m-d\TH:i'),
            'note' => $appointment !== null ? (string) ($appointment->note ?? '') : '',
        ];
    }

    private function syncLeadContext(int $leadId): void
    {
        if (! $lead = Lead::query()->find($leadId)) {
            return;
        }

        $this->form['user_id'] = $lead->user_id;
        $this->form['car_unit_id'] = $lead->car_unit_id ?? $this->form['car_unit_id'];
        $this->form['handled_by'] = $lead->assigned_to ?? $this->form['handled_by'];
    }

    /**
     * @return Collection<int, User>
     */
    private function staffUsers(): Collection
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('roles.name', ['admin', 'staff']))
            ->orderBy('name')
            ->get();
    }
}
