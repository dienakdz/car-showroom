<?php

namespace App\Livewire\Admin\Catalog\Models;

use App\Models\CarModel;
use App\Models\Make;
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

    #[Url(as: 'model_q', except: '')]
    public string $search = '';

    #[Url(as: 'model_make', except: '')]
    public string $makeFilter = '';

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
        $this->resetPage('modelsPage');
    }

    public function updatedMakeFilter(): void
    {
        $this->resetPage('modelsPage');

        if (($this->createForm['make_id'] ?? '') === '') {
            $this->createForm['make_id'] = $this->makeFilter;
        }
    }

    public function updatedPerPage(): void
    {
        $this->resetPage('modelsPage');
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
        $makeId = (int) ($this->createForm['make_id'] ?? 0);

        $validated = $this->validate([
            'createForm.make_id' => ['required', 'integer', 'exists:makes,id'],
            'createForm.name' => ['required', 'string', 'max:255'],
            'createForm.slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('models', 'slug')->where(fn ($query) => $query->where('make_id', $makeId)),
            ],
        ], attributes: $this->validationAttributes('createForm'));

        CarModel::query()->create($validated['createForm']);

        $this->resetCreateForm();
        $this->dispatch('catalog-updated');
        $this->setFeedback('success', 'Da tao model moi.');
        $this->resetPage('modelsPage');
    }

    public function startEdit(int $modelId): void
    {
        $model = CarModel::query()->findOrFail($modelId);

        $this->resetErrorBag();
        $this->feedback = [];
        $this->editingId = $model->id;
        $this->editForm = [
            'make_id' => (string) $model->make_id,
            'name' => $model->name,
            'slug' => $model->slug,
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

        $model = CarModel::query()->findOrFail($this->editingId);

        $this->feedback = [];
        $this->resetErrorBag();
        $this->editForm = $this->normalizeForm($this->editForm);
        $makeId = (int) ($this->editForm['make_id'] ?? 0);

        $validated = $this->validate([
            'editForm.make_id' => ['required', 'integer', 'exists:makes,id'],
            'editForm.name' => ['required', 'string', 'max:255'],
            'editForm.slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('models', 'slug')
                    ->where(fn ($query) => $query->where('make_id', $makeId))
                    ->ignore($model->id),
            ],
        ], attributes: $this->validationAttributes('editForm'));

        $model->update($validated['editForm']);

        $this->dispatch('catalog-updated');
        $this->resetEditState();
        $this->setFeedback('success', 'Da cap nhat model.');
    }

    public function delete(int $modelId): void
    {
        $model = CarModel::query()->withCount('trims')->findOrFail($modelId);

        if ($model->trims_count > 0) {
            $this->setFeedback('error', 'Khong the xoa model da co trim lien ket.');

            return;
        }

        $model->delete();

        if ($this->editingId === $modelId) {
            $this->resetEditState();
        }

        $this->dispatch('catalog-updated');
        $this->setFeedback('success', 'Da xoa model.');
    }

    public function render(): View
    {
        return view('livewire.admin.catalog.models.manager', [
            'makeOptions' => Make::query()->orderBy('name')->get(),
            'models' => CarModel::query()
                ->with('make')
                ->withCount('trims')
                ->when($this->makeFilter !== '', fn ($query) => $query->where('make_id', (int) $this->makeFilter))
                ->when($this->search !== '', function ($query): void {
                    $query->where(function ($innerQuery): void {
                        $innerQuery
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('slug', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('name')
                ->paginate($this->perPage, ['*'], 'modelsPage'),
        ]);
    }

    private function resetCreateForm(): void
    {
        $this->createForm = [
            'make_id' => $this->makeFilter,
            'name' => '',
            'slug' => '',
        ];
    }

    private function resetEditState(): void
    {
        $this->editingId = null;
        $this->editForm = [
            'make_id' => '',
            'name' => '',
            'slug' => '',
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
            'make_id' => (string) ($form['make_id'] ?? ''),
            'name' => $name,
            'slug' => Str::slug((string) (($form['slug'] ?? '') !== '' ? $form['slug'] : $name)),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(string $formProperty): array
    {
        return [
            $formProperty . '.make_id' => 'hang xe',
            $formProperty . '.name' => 'ten model',
            $formProperty . '.slug' => 'slug model',
        ];
    }

    private function setFeedback(string $type, string $message): void
    {
        $this->feedback = [
            'type' => $type,
            'message' => $message,
        ];
    }
}
