<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShowroomSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_on_hold_public' => $this->boolean('show_on_hold_public'),
            'email_lead_notifications' => $this->boolean('email_lead_notifications'),
            'default_currency' => strtoupper((string) $this->input('default_currency', 'VND')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'showroom_name' => ['required', 'string', 'max:255'],
            'showroom_phone' => ['required', 'string', 'max:20'],
            'showroom_email' => ['nullable', 'email', 'max:255'],
            'showroom_address' => ['nullable', 'string', 'max:255'],
            'showroom_description' => ['nullable', 'string'],
            'brand_name' => ['required', 'string', 'max:255'],
            'default_currency' => ['required', 'string', 'size:3'],
            'sales_hotline' => ['nullable', 'string', 'max:20'],
            'show_on_hold_public' => ['nullable', 'boolean'],
            'email_lead_notifications' => ['nullable', 'boolean'],
        ];
    }
}
