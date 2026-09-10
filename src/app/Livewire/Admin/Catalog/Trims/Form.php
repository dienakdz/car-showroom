<?php

namespace App\Livewire\Admin\Catalog\Trims;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\CarAttribute;
use App\Models\CarModel;
use App\Models\FeatureGroup;
use App\Models\Trim;
use App\Models\TrimAttributeValue;
use App\Services\Admin\TrimManagementService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;

class Form extends AdminPageComponent
{
    #[Locked]
    public ?int $trimId = null;

    /** @var array<string, mixed> */
    public array $form = [];

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public bool $slugManuallyEdited = false;

    public function mount(?Trim $trimRecord = null): void
    {
        $this->trimId = $trimRecord?->id;
        $this->slugManuallyEdited = $trimRecord !== null;
        $this->fillForm($trimRecord);

        $feedback = session()->pull('trim_form_feedback');

        if (is_array($feedback)) {
            $this->feedback = $feedback;
        }
    }

    public function updatedFormName(string $name): void
    {
        if (! $this->slugManuallyEdited) {
            $this->form['slug'] = Str::slug($name);
        }
    }

    public function updatedFormSlug(): void
    {
        $this->slugManuallyEdited = true;
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function save(TrimManagementService $service): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->form = $this->normalizeForm($this->form);

        $validated = $this->validate(
            $this->rules(),
            attributes: $this->validationAttributes(),
        );

        $trim = $this->trimId === null
            ? null
            : Trim::query()->findOrFail($this->trimId);

        $savedTrim = $service->save($validated['form'], $trim);

        if ($this->trimId === null) {
            session()->flash('trim_form_feedback', [
                'type' => 'success',
                'message' => 'Da tao phien ban xe moi.',
            ]);

            $this->redirectRoute('admin.catalog.trims.edit', $savedTrim, navigate: true);

            return;
        }

        $this->fillForm($savedTrim);
        $this->feedback = [
            'type' => 'success',
            'message' => 'Da cap nhat phien ban xe.',
        ];
    }

    public function render(): View
    {
        $trim = $this->trimId === null
            ? null
            : Trim::query()->with('model.make')->findOrFail($this->trimId);

        return view('livewire.admin.catalog.trims.form', [
            'trimRecord' => $trim,
            'models' => CarModel::query()
                ->with('make')
                ->orderBy('name')
                ->get(),
            'featureGroups' => FeatureGroup::query()
                ->with('features')
                ->orderBy('sort_order')
                ->get(),
            'attributes' => CarAttribute::query()
                ->orderBy('sort_order')
                ->get(),
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => $trim === null ? 'Tao trim moi' : 'Cap nhat trim',
            'adminPageDescription' => $trim === null
                ? 'Khai bao phien ban va metadata chung de su dung cho inventory + review.'
                : 'Dieu chinh spec, feature group va attribute value cho phien ban.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'catalog.manage';
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $modelId = (int) ($this->form['model_id'] ?? 0);
        $currentYear = now()->addYear()->format('Y');

        return [
            'form.model_id' => ['required', 'integer', 'exists:models,id'],
            'form.name' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trims', 'slug')
                    ->where(fn ($query) => $query->where('model_id', $modelId))
                    ->ignore($this->trimId),
            ],
            'form.year_from' => ['nullable', 'integer', 'min:1900', 'max:' . $currentYear],
            'form.year_to' => ['nullable', 'integer', 'min:1900', 'max:' . $currentYear, 'gte:form.year_from'],
            'form.msrp' => ['nullable', 'integer', 'min:0'],
            'form.description' => ['nullable', 'string'],
            'form.feature_ids' => ['array'],
            'form.feature_ids.*' => ['integer', 'exists:features,id'],
            'form.attributes' => ['array'],
            'form.attributes.*.value_string' => ['nullable', 'string', 'max:255'],
            'form.attributes.*.value_number' => ['nullable', 'numeric', 'min:0'],
            'form.attributes.*.value_boolean' => ['nullable', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.model_id' => 'dong xe',
            'form.name' => 'ten phien ban',
            'form.slug' => 'slug phien ban',
            'form.year_from' => 'nam bat dau',
            'form.year_to' => 'nam ket thuc',
            'form.msrp' => 'gia niem yet',
            'form.description' => 'mo ta phien ban',
            'form.feature_ids' => 'danh sach trang bi',
            'form.attributes' => 'thong so ky thuat',
        ];
    }

    private function fillForm(?Trim $trim): void
    {
        $attributes = CarAttribute::query()->orderBy('sort_order')->get();
        $attributeValues = $trim === null
            ? collect()
            : $trim->loadMissing(['features', 'attributeValues'])->attributeValues->keyBy('attribute_id');

        $formAttributes = [];

        foreach ($attributes as $attribute) {
            /** @var TrimAttributeValue|null $value */
            $value = $attributeValues->get($attribute->id);
            $formAttributes[(string) $attribute->id] = [
                'value_string' => (string) ($value->value_string ?? ''),
                'value_number' => $value?->value_number === null ? '' : (string) $value->value_number,
                'value_boolean' => $value?->value_boolean === null ? '' : (string) (int) $value->value_boolean,
            ];
        }

        if ($trim === null) {
            $this->form = [
                'model_id' => '',
                'name' => '',
                'slug' => '',
                'year_from' => '',
                'year_to' => '',
                'msrp' => '',
                'description' => '',
                'feature_ids' => [],
                'attributes' => $formAttributes,
            ];

            return;
        }

        $this->form = [
            'model_id' => (string) $trim->model_id,
            'name' => (string) $trim->name,
            'slug' => (string) $trim->slug,
            'year_from' => $trim->year_from === null ? '' : (string) $trim->year_from,
            'year_to' => $trim->year_to === null ? '' : (string) $trim->year_to,
            'msrp' => $trim->msrp === null ? '' : (string) $trim->msrp,
            'description' => (string) ($trim->description ?? ''),
            'feature_ids' => $trim->features->pluck('id')->map(fn (mixed $id): int => (int) $id)->all(),
            'attributes' => $formAttributes,
        ];
    }

    /**
     * @param  array<string, mixed>  $form
     * @return array<string, mixed>
     */
    private function normalizeForm(array $form): array
    {
        $name = trim((string) ($form['name'] ?? ''));
        $featureIds = array_values(array_unique(array_map(
            static fn (mixed $featureId): int => (int) $featureId,
            array_filter((array) ($form['feature_ids'] ?? []), static fn (mixed $featureId): bool => (int) $featureId > 0),
        )));

        return [
            'model_id' => (string) ($form['model_id'] ?? ''),
            'name' => $name,
            'slug' => Str::slug((string) (($form['slug'] ?? '') !== '' ? $form['slug'] : $name)),
            'year_from' => (string) ($form['year_from'] ?? ''),
            'year_to' => (string) ($form['year_to'] ?? ''),
            'msrp' => (string) ($form['msrp'] ?? ''),
            'description' => trim((string) ($form['description'] ?? '')),
            'feature_ids' => $featureIds,
            'attributes' => is_array($form['attributes'] ?? null) ? $form['attributes'] : [],
        ];
    }
}
