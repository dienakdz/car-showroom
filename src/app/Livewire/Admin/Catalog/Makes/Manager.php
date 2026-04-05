<?php

namespace App\Livewire\Admin\Catalog\Makes;

use App\Models\Make;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const SORTABLE_FIELDS = ['name', 'slug', 'models_count', 'updated_at'];

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'make_q', except: '')]
    public string $search = '';

    public int $perPage = 10;

    public string $sortField = 'updated_at';

    public string $sortDirection = 'desc';

    public array $createForm = [];

    public array $editForm = [];

    public array $feedback = [];

    public ?int $editingId = null;

    public mixed $logoUpload = null;

    public mixed $editLogoUpload = null;

    public function mount(): void
    {
        $this->resetCreateForm();
        $this->resetEditState();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('makesPage');
    }

    public function updatedPerPage(): void
    {
        $this->resetPage('makesPage');
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORTABLE_FIELDS, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = in_array($field, ['models_count', 'updated_at'], true) ? 'desc' : 'asc';
    }

    public function create(): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->createForm = $this->normalizeForm($this->createForm);

        $validated = $this->validate([
            'createForm.name' => ['required', 'string', 'max:255'],
            'createForm.slug' => ['required', 'string', 'max:255', Rule::unique('makes', 'slug')],
            'createForm.manual_logo_path' => ['nullable', 'string', 'max:2048'],
            'logoUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
        ], attributes: $this->validationAttributes('createForm', 'logoUpload'));

        $payload = $validated['createForm'];
        $payload['logo_path'] = $this->prepareCreateLogoPath(
            $payload['manual_logo_path'] ?? null,
            $validated['logoUpload'] ?? null,
            $payload['name']
        );
        unset($payload['manual_logo_path']);

        Make::query()->create($payload);

        $this->logoUpload = null;
        $this->resetCreateForm();
        $this->dispatch('catalog-updated');
        $this->setFeedback('success', 'Da tao make moi.');
        $this->resetPage('makesPage');
    }

    public function startEdit(int $makeId): void
    {
        $make = Make::query()->findOrFail($makeId);

        $this->resetErrorBag();
        $this->feedback = [];
        $this->editingId = $make->id;
        $this->editForm = [
            'name' => $make->name,
            'slug' => $make->slug,
            'manual_logo_path' => $this->isManagedLogoPath($make->logo_path) ? '' : (string) ($make->logo_path ?? ''),
        ];
        $this->editLogoUpload = null;
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

        $make = Make::query()->findOrFail($this->editingId);

        $this->feedback = [];
        $this->resetErrorBag();
        $this->editForm = $this->normalizeForm($this->editForm);

        $validated = $this->validate([
            'editForm.name' => ['required', 'string', 'max:255'],
            'editForm.slug' => ['required', 'string', 'max:255', Rule::unique('makes', 'slug')->ignore($make->id)],
            'editForm.manual_logo_path' => ['nullable', 'string', 'max:2048'],
            'editLogoUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
        ], attributes: $this->validationAttributes('editForm', 'editLogoUpload'));

        $payload = $validated['editForm'];
        $payload['logo_path'] = $this->prepareUpdateLogoPath(
            $make->logo_path,
            $payload['manual_logo_path'] ?? null,
            $validated['editLogoUpload'] ?? null,
            $payload['name']
        );
        unset($payload['manual_logo_path']);

        $make->update($payload);

        $this->dispatch('catalog-updated');
        $this->resetEditState();
        $this->setFeedback('success', 'Da cap nhat make.');
    }

    public function delete(int $makeId): void
    {
        $make = Make::query()->withCount('models')->findOrFail($makeId);

        if ($make->models_count > 0) {
            $this->setFeedback('error', 'Khong the xoa make da co model lien ket.');

            return;
        }

        $this->deleteManagedLogo($make->logo_path);
        $make->delete();

        if ($this->editingId === $makeId) {
            $this->resetEditState();
        }

        $this->dispatch('catalog-updated');
        $this->setFeedback('success', 'Da xoa make.');
    }

    public function render(): View
    {
        return view('livewire.admin.catalog.makes.manager', [
            'makes' => Make::query()
                ->withCount('models')
                ->when($this->search !== '', function ($query): void {
                    $query->where(function ($innerQuery): void {
                        $innerQuery
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('slug', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage, ['*'], 'makesPage'),
        ]);
    }

    private function resetCreateForm(): void
    {
        $this->createForm = [
            'name' => '',
            'slug' => '',
            'manual_logo_path' => '',
        ];
    }

    private function resetEditState(): void
    {
        $this->editingId = null;
        $this->editForm = [
            'name' => '',
            'slug' => '',
            'manual_logo_path' => '',
        ];
        $this->editLogoUpload = null;
    }

    /**
     * @param  array<string, mixed>  $form
     * @return array<string, string>
     */
    private function normalizeForm(array $form): array
    {
        $name = trim((string) ($form['name'] ?? ''));

        return [
            'name' => $name,
            'slug' => Str::slug((string) (($form['slug'] ?? '') !== '' ? $form['slug'] : $name)),
            'manual_logo_path' => trim((string) ($form['manual_logo_path'] ?? '')),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(string $formProperty, string $uploadProperty): array
    {
        return [
            $formProperty . '.name' => 'ten hang xe',
            $formProperty . '.slug' => 'slug make',
            $formProperty . '.manual_logo_path' => 'duong dan logo',
            $uploadProperty => 'file logo',
        ];
    }

    private function prepareCreateLogoPath(?string $manualLogoPath, mixed $logoUpload, string $name): ?string
    {
        $manualLogoPath = $this->nullableString($manualLogoPath);

        if ($logoUpload instanceof UploadedFile) {
            return $this->storeLogoFile($logoUpload, $name);
        }

        return $manualLogoPath;
    }

    private function prepareUpdateLogoPath(?string $currentPath, ?string $manualLogoPath, mixed $logoUpload, string $name): ?string
    {
        $manualLogoPath = $this->nullableString($manualLogoPath);

        if ($logoUpload instanceof UploadedFile) {
            $newPath = $this->storeLogoFile($logoUpload, $name);
            $this->deleteManagedLogo($currentPath);

            return $newPath;
        }

        if ($manualLogoPath !== null) {
            if ($manualLogoPath !== $currentPath) {
                $this->deleteManagedLogo($currentPath);
            }

            return $manualLogoPath;
        }

        if ($this->isManagedLogoPath($currentPath)) {
            return $currentPath;
        }

        $this->deleteManagedLogo($currentPath);

        return null;
    }

    private function storeLogoFile(UploadedFile $file, string $name): string
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());

        if ($extension === '') {
            $extension = $file->extension() ?: 'png';
        }

        $fileName = Str::slug($name ?: 'make') . '-' . Str::lower(Str::random(8)) . '.' . $extension;

        return $file->storeAs('catalog/makes', $fileName, 'public');
    }

    private function deleteManagedLogo(?string $path): void
    {
        if (! $this->isManagedLogoPath($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function isManagedLogoPath(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        if (preg_match('/^https?:\\/\\//i', $path) === 1) {
            return false;
        }

        if (str_starts_with(ltrim($path, '/'), 'boxcar/')) {
            return false;
        }

        return Storage::disk('public')->exists($path);
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
