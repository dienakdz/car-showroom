<?php

namespace App\Livewire\Admin\Catalog\Trims;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\CarModel;
use App\Models\Trim;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Manager extends AdminPageComponent
{
    private const PER_PAGE_OPTIONS = [10, 25, 50];

    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'trim_q', except: '')]
    public string $search = '';

    #[Url(as: 'trim_model', except: '')]
    public string $modelFilter = '';

    #[Url(as: 'trim_sort', except: 'updated_desc')]
    public string $sort = 'updated_desc';

    public int $perPage = 10;

    public array $createForm = [];

    public array $editForm = [];

    #[Locked]
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
        if (! in_array($this->perPage, self::PER_PAGE_OPTIONS, true)) {
            $this->perPage = self::PER_PAGE_OPTIONS[0];
        }

        $this->resetPage('trimsPage');
    }

    public function updatedSort(): void
    {
        $this->resetPage('trimsPage');
    }

    public function create(): void
    {
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

        Trim::query()->create($validated['createForm']);

        $this->resetCreateForm();
        $this->dispatch('catalog-updated');
        $this->toast('success', 'Đã tạo phiên bản xe mới thành công.');
        $this->resetPage('trimsPage');
    }

    public function startEdit(int $trimId): void
    {
        $trim = Trim::query()->findOrFail($trimId);

        $this->resetErrorBag();
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

        $trim->update($validated['editForm']);

        $this->dispatch('catalog-updated');
        $this->resetEditState();
        $this->toast('success', 'Đã cập nhật phiên bản xe thành công.');
    }

    public function delete(int $trimId): void
    {
        $trim = Trim::query()->withCount(['carUnits', 'reviews'])->findOrFail($trimId);

        if ($trim->car_units_count > 0 || $trim->reviews_count > 0) {
            $this->toast('error', 'Không thể xóa phiên bản đã có xe trong kho hoặc đánh giá liên kết.');

            return;
        }

        $trim->delete();

        if ($this->editingId === $trimId) {
            $this->resetEditState();
        }

        $this->dispatch('catalog-updated');
        $this->toast('success', 'Đã xóa phiên bản xe thành công.');
    }

    public function render(): View
    {
        [$sortField, $sortDirection] = $this->resolveSort();

        return view('livewire.admin.catalog.trims.manager', [
            'modelOptions' => CarModel::query()
                ->with('make')
                ->orderBy('name')
                ->get(),
            'trims' => Trim::query()
                ->with('model.make')
                ->withCount('carUnits')
                ->when($this->modelFilter !== '', fn ($query) => $query->where('model_id', (int) $this->modelFilter))
                ->when($this->search !== '', function ($query): void {
                    $query->where(function ($innerQuery): void {
                        $innerQuery
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('slug', 'like', '%' . $this->search . '%')
                            ->orWhere('description', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy($sortField, $sortDirection)
                ->paginate($this->perPage, ['*'], 'trimsPage'),
        ]);
    }

    protected function requiredPermission(): ?string
    {
        return 'catalog.manage';
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
     * @return array<string, string|null>
     */
    private function normalizeForm(array $form): array
    {
        $name = trim((string) ($form['name'] ?? ''));

        return [
            'model_id' => (string) ($form['model_id'] ?? ''),
            'name' => $name,
            'slug' => Str::slug((string) (($form['slug'] ?? '') !== '' ? $form['slug'] : $name)),
            'year_from' => $this->nullableString($form['year_from'] ?? null),
            'year_to' => $this->nullableString($form['year_to'] ?? null),
            'msrp' => $this->nullableString($form['msrp'] ?? null),
            'description' => $this->nullableString($form['description'] ?? null),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(string $formProperty): array
    {
        return [
            $formProperty . '.model_id' => 'dòng xe',
            $formProperty . '.name' => 'tên phiên bản',
            $formProperty . '.slug' => 'đường dẫn định danh (slug)',
            $formProperty . '.year_from' => 'năm bắt đầu',
            $formProperty . '.year_to' => 'năm kết thúc',
            $formProperty . '.msrp' => 'giá niêm yết (MSRP)',
            $formProperty . '.description' => 'mô tả phiên bản',
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

    /**
     * @return array{0: string, 1: 'asc'|'desc'}
     */
    private function resolveSort(): array
    {
        return match ($this->sort) {
            'name_asc' => ['name', 'asc'],
            'name_desc' => ['name', 'desc'],
            'msrp_desc' => ['msrp', 'desc'],
            'msrp_asc' => ['msrp', 'asc'],
            'year_desc' => ['year_from', 'desc'],
            'year_asc' => ['year_from', 'asc'],
            'updated_asc' => ['updated_at', 'asc'],
            default => ['updated_at', 'desc'],
        };
    }
}
