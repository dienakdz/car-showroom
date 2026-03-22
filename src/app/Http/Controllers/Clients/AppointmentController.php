<?php

namespace App\Http\Controllers\Clients;

use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends ClientBaseController
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source' => ['required', Rule::in(self::LEAD_SOURCES)],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+()\\-\\s.]{8,20}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string'],
            'car_unit_id' => ['nullable', 'integer', 'exists:car_units,id'],
            'trim_id' => ['nullable', 'integer', 'exists:trims,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        if (($validated['car_unit_id'] ?? null) === null && ($validated['trim_id'] ?? null) === null) {
            return back()
                ->withErrors(['scheduled_at' => 'Can xac dinh xe hoac phien ban truoc khi dat lich.'])
                ->withInput();
        }

        $lead = $this->createLead([
            'source' => $validated['source'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'message' => $validated['message'] ?? null,
            'car_unit_id' => $validated['car_unit_id'] ?? null,
            'trim_id' => $validated['trim_id'] ?? null,
            'status' => 'booked',
        ]);

        Appointment::query()->create([
            'user_id' => auth()->id(),
            'car_unit_id' => $validated['car_unit_id'] ?? null,
            'trim_id' => $validated['trim_id'] ?? null,
            'lead_id' => $lead->id,
            'handled_by' => $lead->assigned_to,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'pending',
            'note' => $validated['message'] ?? null,
        ]);
        $this->pushSuccessToast('Yeu cau dat lich da duoc ghi nhan. Showroom se xac nhan voi ban som.');

        return back();
    }
}
