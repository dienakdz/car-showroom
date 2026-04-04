<?php

namespace App\Http\Controllers\Admin\Leads;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Leads\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends AdminBaseController
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->string('q')),
            'status' => trim((string) $request->string('status')),
            'source' => trim((string) $request->string('source')),
            'assigned_to' => $request->integer('assigned_to'),
        ];

        $leads = Lead::query()
            ->with([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
                'trim.model.make',
            ])
            ->withCount(['notes', 'appointments'])
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['source'] !== '', fn ($query) => $query->where('source', $filters['source']))
            ->when($filters['assigned_to'] > 0, fn ($query) => $query->where('assigned_to', $filters['assigned_to']))
            ->when($filters['q'] !== '', function ($query) use ($filters): void {
                $query->where(function ($innerQuery) use ($filters): void {
                    $innerQuery
                        ->where('name', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('phone', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('email', 'like', '%' . $filters['q'] . '%');
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return $this->adminView('admin.leads.index', [
            'adminPageTitle' => 'Lead CRM',
            'adminPageDescription' => 'Filter lead, xem context xe va dieu phoi cho staff.',
            'leads' => $leads,
            'filters' => $filters,
            'staffUsers' => $this->assignableUsers(),
        ]);
    }

    public function show(Lead $lead): View
    {
        $lead->load([
            'assignedTo:id,name',
            'carUnit.trim.model.make',
            'trim.model.make',
            'notes.createdBy:id,name',
            'appointments.handledBy:id,name',
            'appointments.carUnit.trim.model.make',
            'appointments.trim.model.make',
        ]);

        return $this->adminView('admin.leads.show', [
            'adminPageTitle' => 'Chi tiet lead #' . $lead->id,
            'adminPageDescription' => 'Cap nhat pipeline, note log va assignment cho lead.',
            'lead' => $lead,
            'staffUsers' => $this->assignableUsers(),
        ]);
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validated());
        $this->pushSuccessToast('Da cap nhat lead.');

        return redirect()->route('admin.leads.show', $lead);
    }

    protected function assignableUsers()
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('roles.name', ['admin', 'staff']))
            ->orderBy('name')
            ->get();
    }
}
