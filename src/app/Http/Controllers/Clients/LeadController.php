<?php

namespace App\Http\Controllers\Clients;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends ClientBaseController
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source' => ['required', Rule::in(self::LEAD_SOURCES)],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string'],
            'car_unit_id' => ['nullable', 'integer', 'exists:car_units,id'],
            'trim_id' => ['nullable', 'integer', 'exists:trims,id'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
        ]);

        $assignedTo = User::query()
            ->whereHas('roles', fn ($query) => $query->where('roles.name', 'staff'))
            ->value('id');

        Lead::query()->create([
            'user_id' => null,
            'car_unit_id' => $validated['car_unit_id'] ?? null,
            'trim_id' => $validated['trim_id'] ?? null,
            'assigned_to' => $assignedTo ?: null,
            'source' => $validated['source'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
        ]);

        return back()->with('success', 'Yeu cau cua ban da duoc gui. Showroom se lien he som.');
    }
}
