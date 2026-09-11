<?php

namespace App\Livewire\Admin\Appointments;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\Appointment;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Trim;
use App\Models\User;
use App\Services\Admin\AppointmentManagementService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;

class Form extends AdminPageComponent
{
    #[Locked]
    public ?int $appointmentId = null;

    /** @var array<string, mixed> */
    public array $form = [];

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(?Appointment $appointmentRecord = null, ?int $lead_id = null): void
    {
        $this->appointmentId = $appointmentRecord?->id;
        $this->fillForm($appointmentRecord);

        if ($appointmentRecord === null && $lead_id !== null && $lead_id > 0) {
            $this->form['lead_id'] = $lead_id;
            $this->syncLeadContext($lead_id);
        }

        $feedback = session()->pull('appointment_feedback');
        if (is_array($feedback)) {
            $this->feedback = $feedback;
        }
    }

    public function updatedFormLeadId(?int $leadId): void
    {
        if ($leadId !== null && $leadId > 0) {
            $this->syncLeadContext($leadId);
        }
    }

    public function updatedFormCarUnitId(?int $carUnitId): void
    {
        if ($carUnitId !== null && $carUnitId > 0) {
            $carUnit = CarUnit::query()->find($carUnitId);
            if ($carUnit !== null && (int) $carUnit->trim_id > 0) {
                $this->form['trim_id'] = $carUnit->trim_id;
            }
        }
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function save(AppointmentManagementService $service): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->form = $this->normalizeForm($this->form);

        $validated = $this->validate(
            $this->rules(),
            attributes: $this->validationAttributes(),
        );

        if (empty($this->form['car_unit_id']) && empty($this->form['trim_id']) && empty($this->form['lead_id'])) {
            $this->addError('form.car_unit_id', 'Lịch hẹn cần ít nhất một liên kết xe hoặc lead khách hàng.');

            return;
        }

        $user = $this->authorizeAdminAccess($this->requiredPermission());

        $appointment = $this->appointmentId !== null
            ? Appointment::query()->findOrFail($this->appointmentId)
            : null;

        $savedAppointment = $service->save($validated['form'], $user, $appointment);

        if ($this->appointmentId === null) {
            session()->flash('appointment_feedback', [
                'type' => 'success',
                'message' => 'Đã tạo lịch hẹn mới thành công.',
            ]);

            $this->redirectRoute('admin.appointments.edit', $savedAppointment, navigate: true);

            return;
        }

        $this->fillForm($savedAppointment);
        $this->feedback = [
            'type' => 'success',
            'message' => 'Đã cập nhật thông tin lịch hẹn.',
        ];
    }

    public function render(): View
    {
        $appointment = $this->appointmentId !== null
            ? Appointment::query()
                ->with(['user', 'carUnit.trim.model.make', 'trim.model.make', 'lead', 'handledBy'])
                ->findOrFail($this->appointmentId)
            : new Appointment;

        return view('livewire.admin.appointments.form', [
            'appointment' => $appointment,
            'staffUsers' => $this->staffUsers(),
            'customerUsers' => User::query()->orderBy('name')->limit(100)->get(),
            'leads' => Lead::query()->latest()->limit(100)->get(),
            'carUnits' => CarUnit::query()
                ->with('trim.model.make')
                ->whereIn('status', ['available', 'on_hold'])
                ->latest()
                ->limit(100)
                ->get(),
            'trims' => Trim::query()->with('model.make')->orderBy('name')->limit(100)->get(),
            'statusOptions' => [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'done' => 'Đã hoàn tất',
                'cancelled' => 'Đã hủy',
            ],
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => $this->appointmentId !== null ? 'Cập nhật lịch hẹn' : 'Tạo lịch hẹn mới',
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
        return [
            'form.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'form.lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'form.car_unit_id' => ['nullable', 'integer', 'exists:car_units,id'],
            'form.trim_id' => ['nullable', 'integer', 'exists:trims,id'],
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
            'form.car_unit_id' => 'xe trong kho',
            'form.trim_id' => 'phiên bản xe',
            'form.handled_by' => 'chuyên viên đón tiếp',
            'form.status' => 'trạng thái',
            'form.scheduled_at' => 'thời gian hẹn',
            'form.note' => 'ghi chú',
        ];
    }

    private function fillForm(?Appointment $appointment): void
    {
        $this->form = [
            'user_id' => $appointment?->user_id,
            'lead_id' => $appointment?->lead_id,
            'car_unit_id' => $appointment?->car_unit_id,
            'trim_id' => $appointment?->trim_id,
            'handled_by' => $appointment?->handled_by,
            'status' => $appointment !== null ? (string) $appointment->status : 'pending',
            'scheduled_at' => optional($appointment?->scheduled_at)->format('Y-m-d\TH:i') ?? now()->addDay()->setHour(10)->setMinute(0)->format('Y-m-d\TH:i'),
            'note' => $appointment !== null ? (string) ($appointment->note ?? '') : '',
        ];
    }

    private function syncLeadContext(int $leadId): void
    {
        $lead = Lead::query()->find($leadId);
        if ($lead === null) {
            return;
        }

        if (empty($this->form['user_id']) && $lead->user_id !== null) {
            $this->form['user_id'] = $lead->user_id;
        }

        if (empty($this->form['car_unit_id']) && $lead->car_unit_id !== null) {
            $this->form['car_unit_id'] = $lead->car_unit_id;
        }

        if (empty($this->form['trim_id']) && $lead->trim_id !== null) {
            $this->form['trim_id'] = $lead->trim_id;
        }

        if (empty($this->form['handled_by']) && $lead->assigned_to !== null) {
            $this->form['handled_by'] = $lead->assigned_to;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeForm(array $data): array
    {
        return [
            'user_id' => ! empty($data['user_id']) ? (int) $data['user_id'] : null,
            'lead_id' => ! empty($data['lead_id']) ? (int) $data['lead_id'] : null,
            'car_unit_id' => ! empty($data['car_unit_id']) ? (int) $data['car_unit_id'] : null,
            'trim_id' => ! empty($data['trim_id']) ? (int) $data['trim_id'] : null,
            'handled_by' => ! empty($data['handled_by']) ? (int) $data['handled_by'] : null,
            'status' => trim((string) ($data['status'] ?? 'pending')),
            'scheduled_at' => ! empty($data['scheduled_at']) ? trim((string) $data['scheduled_at']) : null,
            'note' => ! empty($data['note']) ? trim((string) $data['note']) : null,
        ];
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
