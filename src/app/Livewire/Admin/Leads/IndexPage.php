<?php

namespace App\Livewire\Admin\Leads;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class IndexPage extends AdminPageComponent
{
    use WithPagination;

    private const STATUSES = ['new', 'contacted', 'qualified', 'booked', 'closed', 'lost'];

    private const SOURCES = ['unit_detail', 'trim_page', 'finance', 'trade_in', 'contact'];

    private const VIEW_MODES = ['kanban', 'table'];

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'status', except: '')]
    public string $status = '';

    #[Url(as: 'source', except: '')]
    public string $source = '';

    #[Url(as: 'assigned_to', except: '')]
    public string $assignedTo = '';

    #[Url(as: 'view', except: 'kanban', history: true)]
    public string $viewMode = 'kanban';

    public function mount(): void
    {
        $this->normalizeFilters();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('leadsPage');
    }

    public function updatedStatus(): void
    {
        $this->normalizeFilters();
        $this->resetPage('leadsPage');
    }

    public function updatedSource(): void
    {
        $this->normalizeFilters();
        $this->resetPage('leadsPage');
    }

    public function updatedAssignedTo(): void
    {
        $this->normalizeFilters();
        $this->resetPage('leadsPage');
    }

    public function setViewMode(string $viewMode): void
    {
        $this->viewMode = $viewMode;
        $this->normalizeFilters();
        $this->resetPage('leadsPage');
    }

    public function filterByStatus(string $status): void
    {
        $this->status = in_array($status, self::STATUSES, true) ? $status : '';
        $this->resetPage('leadsPage');
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'source', 'assignedTo']);
        $this->resetPage('leadsPage');
    }

    public function render(): View
    {
        $leads = $this->viewMode === 'table'
            ? $this->filteredQuery()->paginate(12, ['*'], 'leadsPage')
            : null;

        $kanbanLeads = null;

        if ($this->viewMode === 'kanban') {
            $allLeads = $this->filteredQuery()
                ->limit(40)
                ->get();

            $kanbanLeads = [
                'new' => $allLeads->where('status', 'new'),
                'consulting' => $allLeads->whereIn('status', ['contacted', 'qualified']),
                'negotiating' => $allLeads->where('status', 'booked'),
                'closed' => $allLeads->where('status', 'closed'),
            ];
        }

        return view('livewire.admin.leads.index-page', [
            'leads' => $leads,
            'kanbanLeads' => $kanbanLeads,
            'stageCounts' => [
                'all' => Lead::query()->count(),
                'new' => Lead::query()->where('status', 'new')->count(),
                'consulting' => Lead::query()->whereIn('status', ['contacted', 'qualified'])->count(),
                'negotiating' => Lead::query()->where('status', 'booked')->count(),
                'closed' => Lead::query()->where('status', 'closed')->count(),
            ],
            'staffUsers' => $this->assignableUsers(),
            'statusOptions' => self::STATUSES,
            'sourceOptions' => self::SOURCES,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Khach hang & Leads (CRM)',
            'adminPageDescription' => 'Quan ly pheu khach hang tiem nang, lich hen va dieu phoi chuyen vien tu van.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'leads.manage';
    }

    /**
     * @return Builder<Lead>
     */
    private function filteredQuery(): Builder
    {
        $search = trim($this->search);

        return Lead::query()
            ->with([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
                'trim.model.make',
            ])
            ->withCount(['notes', 'appointments'])
            ->when($this->status !== '', fn (Builder $query): Builder => $query->where('status', $this->status))
            ->when($this->source !== '', fn (Builder $query): Builder => $query->where('source', $this->source))
            ->when((int) $this->assignedTo > 0, fn (Builder $query): Builder => $query->where('assigned_to', (int) $this->assignedTo))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $innerQuery) use ($search): void {
                    $innerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->latest();
    }

    private function normalizeFilters(): void
    {
        $this->search = trim($this->search);

        if ($this->status !== '' && ! in_array($this->status, self::STATUSES, true)) {
            $this->status = '';
        }

        if ($this->source !== '' && ! in_array($this->source, self::SOURCES, true)) {
            $this->source = '';
        }

        if ((int) $this->assignedTo <= 0) {
            $this->assignedTo = '';
        }

        if (! in_array($this->viewMode, self::VIEW_MODES, true)) {
            $this->viewMode = self::VIEW_MODES[0];
        }
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
