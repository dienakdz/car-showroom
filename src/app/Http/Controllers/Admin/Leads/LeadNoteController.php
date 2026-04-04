<?php

namespace App\Http\Controllers\Admin\Leads;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Leads\StoreLeadNoteRequest;
use App\Models\Lead;
use App\Models\LeadNote;
use Illuminate\Http\RedirectResponse;

class LeadNoteController extends AdminBaseController
{
    public function store(StoreLeadNoteRequest $request, Lead $lead): RedirectResponse
    {
        LeadNote::query()->create([
            'lead_id' => $lead->id,
            'created_by' => $request->user()->id,
            'note' => $request->string('note')->value(),
        ]);

        $this->pushSuccessToast('Da them note cho lead.');

        return redirect()->route('admin.leads.show', $lead);
    }
}
