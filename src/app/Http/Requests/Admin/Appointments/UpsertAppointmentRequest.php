<?php

namespace App\Http\Requests\Admin\Appointments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpsertAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'car_unit_id' => ['nullable', 'integer', 'exists:car_units,id'],
            'trim_id' => ['nullable', 'integer', 'exists:trims,id'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'handled_by' => ['nullable', 'integer', 'exists:users,id'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled', 'done'])],
            'note' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->filled('car_unit_id') && ! $this->filled('trim_id') && ! $this->filled('lead_id')) {
                    $validator->errors()->add('car_unit_id', 'Appointment can mot context xe hoac lead de xu ly.');
                }

                $scheduledAt = $this->date('scheduled_at');
                $status = (string) $this->input('status');

                if ($scheduledAt !== null && $scheduledAt->isPast() && in_array($status, ['pending', 'confirmed'], true)) {
                    $validator->errors()->add('scheduled_at', 'Lich hen pending/confirmed khong nen nam trong qua khu.');
                }
            },
        ];
    }
}
