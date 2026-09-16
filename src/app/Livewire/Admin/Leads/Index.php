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

class Index extends AdminPageComponent
{
    use WithPagination;

    private const STATUSES = ['new', 'contacted', 'qualified', 'booked', 'closed', 'lost'];

    private const SOURCES = ['unit_detail', 'trim_page', 'finance', 'trade_in', 'contact'];

    private const VIEW_MODES = ['kanban', 'table'];

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

    public function updatedSearch(): void
    {
        $this->resetPage('leadsPage');
    }

    public function updatedStatus(): void
    {
        if ($this->status !== '' && ! in_array($this->status, [...self::STATUSES, 'consulting'], true)) {
            $this->status = '';
        }
        $this->resetPage('leadsPage');
    }

    public function updatedSource(): void
    {
        if ($this->source !== '' && ! in_array($this->source, self::SOURCES, true)) {
            $this->source = '';
        }
        $this->resetPage('leadsPage');
    }

    public function updatedAssignedTo(): void
    {
        if ((int) $this->assignedTo <= 0) {
            $this->assignedTo = '';
        }
        $this->resetPage('leadsPage');
    }

    public function setViewMode(string $viewMode): void
    {
        $this->viewMode = in_array($viewMode, self::VIEW_MODES, true) ? $viewMode : 'kanban';
        $this->resetPage('leadsPage');
    }

    public function filterByStatus(string $status): void
    {
        if ($this->status === $status) {
            $this->status = '';
        } else {
            $allowed = [...self::STATUSES, 'consulting'];
            $this->status = in_array($status, $allowed, true) ? $status : '';
        }
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
            $kanbanStages = [
                'new' => ['new'],
                'consulting' => ['contacted', 'qualified'],
                'negotiating' => ['booked'],
                'closed' => ['closed'],
            ];

            $baseQuery = $this->filteredQuery(includeStatus: false);

            $kanbanLeads = [];
            foreach ($kanbanStages as $stageKey => $statuses) {
                $stageQuery = (clone $baseQuery)->whereIn('status', $statuses);

                if ($this->status !== '') {
                    if ($this->status === 'consulting') {
                        if ($stageKey !== 'consulting') {
                            $stageQuery->whereRaw('1 = 0');
                        }
                    } elseif (! in_array($this->status, $statuses, true)) {
                        $stageQuery->whereRaw('1 = 0');
                    } else {
                        $stageQuery->where('status', $this->status);
                    }
                }

                $kanbanLeads[$stageKey] = $stageQuery->limit(20)->get();
            }
        }

        /** @var array<string, int> $rawCounts */
        $rawCounts = Lead::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $stageCounts = [
            'all' => (int) array_sum($rawCounts),
            'new' => (int) ($rawCounts['new'] ?? 0),
            'consulting' => (int) (($rawCounts['contacted'] ?? 0) + ($rawCounts['qualified'] ?? 0)),
            'negotiating' => (int) ($rawCounts['booked'] ?? 0),
            'closed' => (int) ($rawCounts['closed'] ?? 0),
            'lost' => (int) ($rawCounts['lost'] ?? 0),
        ];

        return view('livewire.admin.leads.index', [
            'leads' => $leads,
            'kanbanLeads' => $kanbanLeads,
            'stageCounts' => $stageCounts,
            'staffUsers' => $this->assignableUsers(),
            'statusOptions' => self::STATUSES,
            'sourceOptions' => self::SOURCES,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Khách hàng & Leads (CRM)',
            'adminPageDescription' => 'Quản lý phễu khách hàng tiềm năng, lịch hẹn và điều phối chuyên viên tư vấn.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'leads.manage';
    }

    /**
     * @return Builder<Lead>
     */
    private function filteredQuery(bool $includeStatus = true): Builder
    {
        $search = trim($this->search);

        return Lead::query()
            ->with([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
                'carUnit.primaryMedia',
                'trim.model.make',
                'trim.carUnits.primaryMedia',
            ])
            ->withCount(['notes', 'appointments'])
            ->when($includeStatus && $this->status !== '', function (Builder $query): void {
                if ($this->status === 'consulting') {
                    $query->whereIn('status', ['contacted', 'qualified']);
                } else {
                    $query->where('status', $this->status);
                }
            })
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
