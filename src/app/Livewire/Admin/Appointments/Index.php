<?php

namespace App\Livewire\Admin\Appointments;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\Appointment;
use App\Models\User;
use App\Services\Admin\AppointmentManagementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends AdminPageComponent
{
    use WithPagination;

    public const STATUSES = ['pending', 'confirmed', 'done', 'cancelled'];

    public const STATUS_LABELS = AppointmentManagementService::STATUS_LABELS;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'status', except: '')]
    public string $status = '';

    #[Url(as: 'date', except: '')]
    public string $dateFilter = '';

    #[Url(as: 'handled_by', except: 0)]
    public int $handledBy = 0;

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(): void
    {
        if ($this->status !== '' && ! in_array($this->status, self::STATUSES, true)) {
            $this->status = '';
        }

        if ($this->dateFilter !== '' && $this->dateFilter !== 'today') {
            $this->dateFilter = '';
        }

        $feedback = session()->pull('appointment_feedback');
        if (is_array($feedback)) {
            $this->feedback = $feedback;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage('appointmentsPage');
    }

    public function updatedStatus(): void
    {
        if ($this->status !== '' && ! in_array($this->status, self::STATUSES, true)) {
            $this->status = '';
        }
        $this->resetPage('appointmentsPage');
    }

    public function updatedDateFilter(): void
    {
        if ($this->dateFilter !== '' && $this->dateFilter !== 'today') {
            $this->dateFilter = '';
        }
        $this->resetPage('appointmentsPage');
    }

    public function updatedHandledBy(): void
    {
        $this->resetPage('appointmentsPage');
    }

    public function filterByStatus(string $status): void
    {
        if ($this->status === $status) {
            $this->status = '';
        } else {
            $this->status = in_array($status, self::STATUSES, true) ? $status : '';
            $this->dateFilter = '';
        }

        $this->resetPage('appointmentsPage');
    }

    public function filterByDate(string $date): void
    {
        if ($this->dateFilter === $date) {
            $this->dateFilter = '';
        } else {
            $this->dateFilter = $date === 'today' ? 'today' : '';
            $this->status = '';
        }

        $this->resetPage('appointmentsPage');
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'dateFilter', 'handledBy']);
        $this->resetPage('appointmentsPage');
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function updateStatus(int $appointmentId, string $status, AppointmentManagementService $service): void
    {
        $validated = validator(
            ['status' => $status],
            ['status' => ['required', Rule::in(self::STATUSES)]],
        )->validate();

        $appointment = Appointment::query()->findOrFail($appointmentId);
        $user = $this->authorizeAdminAccess($this->requiredPermission());

        $service->updateStatus($appointment, $validated['status'], $user);

        $this->feedback = [
            'type' => 'success',
            'message' => 'Đã cập nhật trạng thái lịch hẹn #' . $appointmentId . '.',
        ];
    }

    public function render(): View
    {
        $statusCounts = Appointment::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $todayCount = Appointment::query()
            ->whereDate('scheduled_at', today())
            ->count();

        $allCount = (int) $statusCounts->sum();

        return view('livewire.admin.appointments.index', [
            'appointments' => $this->filteredQuery()->paginate(12, ['*'], 'appointmentsPage'),
            'appointmentCounts' => [
                'all' => $allCount,
                'today' => $todayCount,
                'pending' => (int) ($statusCounts['pending'] ?? 0),
                'confirmed' => (int) ($statusCounts['confirmed'] ?? 0),
                'done' => (int) ($statusCounts['done'] ?? 0),
                'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
            ],
            'staffUsers' => $this->staffUsers(),
            'statusOptions' => self::STATUS_LABELS,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Lịch hẹn xem xe & Lái thử',
            'adminPageDescription' => 'Điều phối lịch lái thử, đón tiếp khách hàng và phân công chuyên viên tư vấn.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'appointments.manage';
    }

    /**
     * @return Builder<Appointment>
     */
    private function filteredQuery(): Builder
    {
        $search = trim($this->search);

        return Appointment::query()
            ->with([
                'user:id,name,email,phone',
                'handledBy:id,name',
                'lead:id,name,phone',
                'carUnit.trim.model.make',
                'carUnit.primaryMedia',
                'trim.model.make',
                'trim.carUnits.primaryMedia',
            ])
            ->when($this->status !== '', fn (Builder $query): Builder => $query->where('status', $this->status))
            ->when($this->dateFilter === 'today', fn (Builder $query): Builder => $query->whereDate('scheduled_at', today()))
            ->when($this->handledBy > 0, fn (Builder $query): Builder => $query->where('handled_by', $this->handledBy))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->whereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                        ->orWhereHas('lead', fn (Builder $l) => $l->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                        ->orWhere('note', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('scheduled_at');
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
