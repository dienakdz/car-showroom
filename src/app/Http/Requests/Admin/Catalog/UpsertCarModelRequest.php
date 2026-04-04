<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpsertCarModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug((string) ($this->input('slug') ?: $this->input('name'))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $carModelId = $this->route('carModel')?->id;
        $makeId = (int) $this->integer('make_id');

        return [
            'make_id' => ['required', 'integer', 'exists:makes,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('models', 'slug')
                    ->where(fn ($query) => $query->where('make_id', $makeId))
                    ->ignore($carModelId),
            ],
        ];
    }
}
