<?php

namespace App\Http\Requests\Admin\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'buyer_email' => filled($this->input('buyer_email')) ? strtolower(trim((string) $this->input('buyer_email'))) : null,
            'buyer_phone' => filled($this->input('buyer_phone')) ? preg_replace('/\D+/', '', (string) $this->input('buyer_phone')) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'car_unit_id' => ['required', 'integer', 'exists:car_units,id', 'unique:sales,car_unit_id'],
            'buyer_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'buyer_name' => ['required_without:buyer_user_id', 'nullable', 'string', 'max:255'],
            'buyer_email' => ['nullable', 'email', 'max:255'],
            'buyer_phone' => ['required_without:buyer_user_id', 'nullable', 'string', 'max:20'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'sold_price' => ['nullable', 'integer', 'min:0'],
            'sold_at' => ['required', 'date'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->filled('buyer_user_id') && ! $this->filled('buyer_email') && ! $this->filled('buyer_phone')) {
                    $validator->errors()->add('buyer_email', 'Can co email hoac so dien thoai khi tao buyer moi.');
                }
            },
        ];
    }
}
