<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpsertTrimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug((string) ($this->input('slug') ?: $this->input('name'))),
            'feature_ids' => array_values(array_filter((array) $this->input('feature_ids', []))),
            'attributes' => is_array($this->input('attributes')) ? $this->input('attributes') : [],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $trimId = $this->route('trimRecord')?->id;
        $modelId = (int) $this->integer('model_id');
        $currentYear = (int) now()->addYear()->format('Y');

        return [
            'model_id' => ['required', 'integer', 'exists:models,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trims', 'slug')
                    ->where(fn ($query) => $query->where('model_id', $modelId))
                    ->ignore($trimId),
            ],
            'year_from' => ['nullable', 'integer', 'min:1900', 'max:' . $currentYear],
            'year_to' => ['nullable', 'integer', 'min:1900', 'max:' . $currentYear, 'gte:year_from'],
            'msrp' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['integer', 'exists:features,id'],
            'attributes' => ['nullable', 'array'],
            'attributes.*.value_string' => ['nullable', 'string', 'max:255'],
            'attributes.*.value_number' => ['nullable', 'numeric', 'min:0'],
            'attributes.*.value_boolean' => ['nullable', 'in:0,1'],
        ];
    }
}
