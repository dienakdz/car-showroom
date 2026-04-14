<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpsertCarUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $media = collect((array) $this->input('media', []))
            ->map(function ($row, int $index): array {
                $payload = is_array($row) ? $row : [];

                return [
                    'id' => $payload['id'] ?? null,
                    'type' => $payload['type'] ?? 'image',
                    'path_or_url' => trim((string) ($payload['path_or_url'] ?? '')),
                    'caption' => trim((string) ($payload['caption'] ?? '')),
                    'sort_order' => $payload['sort_order'] ?? $index,
                    'is_cover' => isset($payload['is_cover']) && (string) $payload['is_cover'] === '1',
                ];
            })
            ->filter(fn (array $row): bool => $row['path_or_url'] !== '' || filled($row['id']))
            ->values()
            ->all();

        $this->merge([
            'media' => $media,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $carUnitId = $this->route('carUnit')?->id;
        $currentYear = (int) now()->addYear()->format('Y');

        return [
            'trim_id' => ['required', 'integer', 'exists:trims,id'],
            'condition' => ['required', Rule::in(['new', 'used', 'cpo'])],
            'vin' => ['nullable', 'string', 'max:255', Rule::unique('car_units', 'vin')->ignore($carUnitId)],
            'stock_code' => ['required', 'string', 'max:255', Rule::unique('car_units', 'stock_code')->ignore($carUnitId)],
            'year' => ['required', 'integer', 'min:1900', 'max:' . $currentYear],
            'mileage' => [Rule::requiredIf(in_array($this->input('condition'), ['used', 'cpo'], true)), 'nullable', 'integer', 'min:0'],
            'body_type_id' => ['nullable', 'integer', 'exists:body_types,id'],
            'fuel_type_id' => ['nullable', 'integer', 'exists:fuel_types,id'],
            'transmission_id' => ['nullable', 'integer', 'exists:transmissions,id'],
            'drivetrain_id' => ['nullable', 'integer', 'exists:drivetrains,id'],
            'exterior_color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'interior_color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'price' => ['nullable', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['required', Rule::in(['draft', 'available', 'on_hold', 'sold', 'archived'])],
            'notes_internal' => ['nullable', 'string'],
            'media' => ['nullable', 'array'],
            'media.*.id' => ['nullable', 'integer', 'exists:car_unit_media,id'],
            'media.*.type' => ['required', Rule::in(['image', 'video'])],
            'media.*.path_or_url' => ['required', 'string', 'max:2048'],
            'media.*.caption' => ['nullable', 'string', 'max:255'],
            'media.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'media.*.is_cover' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $carUnit = $this->route('carUnit');
                $status = (string) $this->input('status');

                if ($status === 'sold' && ($carUnit === null || ! $carUnit->sale()->exists())) {
                    $validator->errors()->add('status', 'Trang thai sold phai duoc tao tu module sales.');
                }

                if ($status === 'on_hold' && ($carUnit === null || $carUnit->status !== 'on_hold')) {
                    $validator->errors()->add('status', 'Hay dung workflow hold / release de doi trang thai giu xe.');
                }
            },
        ];
    }
}
