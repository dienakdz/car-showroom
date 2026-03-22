<?php

namespace App\Http\Controllers\Clients;

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
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+()\\-\\s.]{8,20}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string'],
            'car_unit_id' => ['nullable', 'integer', 'exists:car_units,id'],
            'trim_id' => ['nullable', 'integer', 'exists:trims,id'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
        ]);

        if (($validated['source'] ?? 'contact') !== 'contact'
            && ($validated['car_unit_id'] ?? null) === null
            && ($validated['trim_id'] ?? null) === null) {
            return back()
                ->withErrors(['trim_id' => 'Ngu canh tu van khong hop le. Vui long chon xe hoac phien ban.'])
                ->withInput();
        }

        $this->createLead($validated);
        $this->pushSuccessToast('Yeu cau cua ban da duoc gui. Showroom se lien he som.');

        return back();
    }
}
