<?php

namespace App\Http\Controllers\Admin\Appointments;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Appointments\UpsertAppointmentRequest;
use App\Models\Appointment;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Trim;
use App\Models\User;
use App\Services\Admin\AppointmentManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends AdminBaseController
{
    public function index(Request $request): View
    {
        $filters = [
            'status' => trim((string) $request->string('status')),
            'handled_by' => $request->integer('handled_by'),
        ];

        $appointments = Appointment::query()
            ->with([
                'user:id,name',
                'handledBy:id,name',
                'lead:id,name',
                'carUnit.trim.model.make',
                'trim.model.make',
            ])
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['handled_by'] > 0, fn ($query) => $query->where('handled_by', $filters['handled_by']))
            ->orderByDesc('scheduled_at')
            ->paginate(12)
            ->withQueryString();

        return $this->adminView('admin.appointments.index', [
            'adminPageTitle' => 'Appointments',
            'adminPageDescription' => 'Confirm, reschedule va theo doi lich hen showroom.',
            'appointments' => $appointments,
            'filters' => $filters,
            'staffUsers' => $this->staffUsers(),
        ]);
    }

    public function create(Request $request): View
    {
        $appointment = new Appointment();
        $appointment->lead_id = $request->integer('lead_id') ?: null;

        return $this->formView($appointment, 'Tao appointment', 'Tao lich hen thu cong hoac gan lead da co.');
    }

    public function store(UpsertAppointmentRequest $request, AppointmentManagementService $service): RedirectResponse
    {
        $appointment = $service->save($request->validated(), $request->user());
        $this->pushSuccessToast('Da tao appointment moi.');

        return redirect()->route('admin.appointments.edit', $appointment);
    }

    public function edit(Appointment $appointment): View
    {
        return $this->formView(
            $appointment->load([
                'user',
                'carUnit.trim.model.make',
                'trim.model.make',
                'lead',
                'handledBy',
            ]),
            'Cap nhat appointment',
            'Chinh sua thoi gian, staff xu ly va trang thai buoi hen.'
        );
    }

    public function update(UpsertAppointmentRequest $request, Appointment $appointment, AppointmentManagementService $service): RedirectResponse
    {
        $service->save($request->validated(), $request->user(), $appointment);
        $this->pushSuccessToast('Da cap nhat appointment.');

        return redirect()->route('admin.appointments.edit', $appointment);
    }

    protected function formView(Appointment $appointment, string $title, string $description): View
    {
        return $this->adminView('admin.appointments.form', [
            'adminPageTitle' => $title,
            'adminPageDescription' => $description,
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
        ]);
    }

    protected function staffUsers()
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('roles.name', ['admin', 'staff']))
            ->orderBy('name')
            ->get();
    }
}
