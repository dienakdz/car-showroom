<?php

namespace App\Livewire\Admin\Catalog\Trims;

use App\Models\CarModel;
use App\Models\Trim;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'trim_q', except: '')]
    public string $search = '';

    #[Url(as: 'trim_model', except: '')]
    public string $modelFilter = '';

    public int $perPage = 10;

    public array $createForm = [];

    public array $editForm = [];

    public array $feedback = [];

    public ?int $editingId = null;

    public function mount(): void
    {
        $this->resetCreateForm();
        $this->resetEditState();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('trimsPage');
    }

    public function updatedModelFilter(): void
    {
        $this->resetPage('trimsPage');

        if (($this->createForm['model_id'] ?? '') === '') {
            $this->createForm['model_id'] = $this->modelFilter;
        }
    }

    public function updatedPerPage(): void
    {
        $this->resetPage('trimsPage');
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function create(): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->createForm = $this->normalizeForm($this->createForm);
        $modelId = (int) ($this->createForm['model_id'] ?? 0);

        $validated = $this->validate([
            'createForm.model_id' => ['required', 'integer', 'exists:models,id'],
            'createForm.name' => ['required', 'string', 'max:255'],
            'createForm.slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trims', 'slug')->where(fn ($query) => $query->where('model_id', $modelId)),
            ],
            'createForm.year_from' => ['nullable', 'integer', 'min:1900', 'max:' . now()->addYear()->format('Y')],
            'createForm.year_to' => ['nullable', 'integer', 'min:1900', 'max:' . now()->addYear()->format('Y'), 'gte:createForm.year_from'],
            'createForm.msrp' => ['nullable', 'integer', 'min:0'],
            'createForm.description' => ['nullable', 'string'],
        ], attributes: $this->validationAttributes('createForm'));

        $payload = $validated['createForm'];
        $payload['description'] = $this->nullableString($payload['description'] ?? null);

        Trim::query()->create($payload);

        $this->resetCreateForm();
        $this->dispatch('catalog-updated');
        $this->setFeedback('success', 'Da tao trim moi.');
        $this->resetPage('trimsPage');
    }

    public function startEdit(int $trimId): void
    {
        $trim = Trim::query()->findOrFail($trimId);

        $this->resetErrorBag();
        $this->feedback = [];
        $this->editingId = $trim->id;
        $this->editForm = [
            'model_id' => (string) $trim->model_id,
            'name' => $trim->name,
            'slug' => $trim->slug,
            'year_from' => $trim->year_from === null ? '' : (string) $trim->year_from,
            'year_to' => $trim->year_to === null ? '' : (string) $trim->year_to,
            'msrp' => $trim->msrp === null ? '' : (string) $trim->msrp,
            'description' => (string) ($trim->description ?? ''),
        ];
    }

    public function cancelEdit(): void
    {
        $this->resetEditState();
        $this->resetErrorBag();
    }

    public function update(): void
    {
        if ($this->editingId === null) {
            return;
        }

        $trim = Trim::query()->findOrFail($this->editingId);

        $this->feedback = [];
        $this->resetErrorBag();
        $this->editForm = $this->normalizeForm($this->editForm);
        $modelId = (int) ($this->editForm['model_id'] ?? 0);

        $validated = $this->validate([
            'editForm.model_id' => ['required', 'integer', 'exists:models,id'],
            'editForm.name' => ['required', 'string', 'max:255'],
            'editForm.slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trims', 'slug')
                    ->where(fn ($query) => $query->where('model_id', $modelId))
                    ->ignore($trim->id),
            ],
            'editForm.year_from' => ['nullable', 'integer', 'min:1900', 'max:' . now()->addYear()->format('Y')],
            'editForm.year_to' => ['nullable', 'integer', 'min:1900', 'max:' . now()->addYear()->format('Y'), 'gte:editForm.year_from'],
            'editForm.msrp' => ['nullable', 'integer', 'min:0'],
            'editForm.description' => ['nullable', 'string'],
        ], attributes: $this->validationAttributes('editForm'));

        $payload = $validated['editForm'];
        $payload['description'] = $this->nullableString($payload['description'] ?? null);

        $trim->update($payload);

        $this->dispatch('catalog-updated');
        $this->resetEditState();
        $this->setFeedback('success', 'Da cap nhat trim.');
    }

    public function delete(int $trimId): void
    {
        $trim = Trim::query()->withCount(['carUnits', 'reviews'])->findOrFail($trimId);

        if ($trim->car_units_count > 0 || $trim->reviews_count > 0) {
            $this->setFeedback('error', 'Khong the xoa trim da co inventory hoac review lien ket.');

            return;
        }

        $trim->delete();

        if ($this->editingId === $trimId) {
            $this->resetEditState();
        }

        $this->dispatch('catalog-updated');
        $this->setFeedback('success', 'Da xoa trim.');
    }

    public function render(): View
    {
        return view('livewire.admin.catalog.trims.manager', [
            'modelOptions' => CarModel::query()
                ->with('make')
                ->orderBy('name')
                ->get(),
            'trims' => Trim::query()
                ->with('model.make')
                ->withCount(['carUnits', 'reviews'])
                ->when($this->modelFilter !== '', fn ($query) => $query->where('model_id', (int) $this->modelFilter))
                ->when($this->search !== '', function ($query): void {
                    $query->where(function ($innerQuery): void {
                        $innerQuery
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('slug', 'like', '%' . $this->search . '%')
                            ->orWhere('description', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest('updated_at')
                ->paginate($this->perPage, ['*'], 'trimsPage'),
        ]);
    }

    private function resetCreateForm(): void
    {
        $this->createForm = [
            'model_id' => $this->modelFilter,
            'name' => '',
            'slug' => '',
            'year_from' => '',
            'year_to' => '',
            'msrp' => '',
            'description' => '',
        ];
    }

    private function resetEditState(): void
    {
        $this->editingId = null;
        $this->editForm = [
            'model_id' => '',
            'name' => '',
            'slug' => '',
            'year_from' => '',
            'year_to' => '',
            'msrp' => '',
            'description' => '',
        ];
    }

    /**
     * @param  array<string, mixed>  $form
     * @return array<string, string>
     */
    private function normalizeForm(array $form): array
    {
        $name = trim((string) ($form['name'] ?? ''));

        return [
            'model_id' => (string) ($form['model_id'] ?? ''),
            'name' => $name,
            'slug' => Str::slug((string) (($form['slug'] ?? '') !== '' ? $form['slug'] : $name)),
            'year_from' => (string) ($form['year_from'] ?? ''),
            'year_to' => (string) ($form['year_to'] ?? ''),
            'msrp' => (string) ($form['msrp'] ?? ''),
            'description' => trim((string) ($form['description'] ?? '')),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(string $formProperty): array
    {
        return [
            $formProperty . '.model_id' => 'model',
            $formProperty . '.name' => 'ten trim',
            $formProperty . '.slug' => 'slug trim',
            $formProperty . '.year_from' => 'nam bat dau',
            $formProperty . '.year_to' => 'nam ket thuc',
            $formProperty . '.msrp' => 'MSRP',
            $formProperty . '.description' => 'mo ta trim',
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }

    private function setFeedback(string $type, string $message): void
    {
        $this->feedback = [
            'type' => $type,
            'message' => $message,
        ];
    }
}
