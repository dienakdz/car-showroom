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

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'make_q', except: '')]
    public string $search = '';

    #[Url(as: 'make_sort', except: 'updated_desc')]
    public string $sort = 'updated_desc';

    public int $perPage = 10;

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

    public function updatedSort(): void
    {
        $this->resetPage('makesPage');
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function syncEditModalClosed(): void
    {
        $this->resetEditState();
        $this->resetErrorBag();
    }

    public function create(): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->createForm = $this->normalizeForm($this->createForm);

        $validated = $this->validate([
            'createForm.name' => ['required', 'string', 'max:255'],
            'createForm.slug' => ['required', 'string', 'max:255', Rule::unique('makes', 'slug')],
            'logoUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
        ], attributes: $this->validationAttributes('createForm', 'logoUpload'));

        $payload = $validated['createForm'];
        $payload['logo_path'] = $this->prepareCreateLogoPath(
            $validated['logoUpload'] ?? null,
            $payload['name']
        );

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
        ];
        $this->editLogoUpload = null;

        $this->openEditModal();
    }

    public function cancelEdit(): void
    {
        $this->resetEditState();
        $this->resetErrorBag();

        $this->closeEditModal();
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
            'editLogoUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
        ], attributes: $this->validationAttributes('editForm', 'editLogoUpload'));

        $payload = $validated['editForm'];
        $payload['logo_path'] = $this->prepareUpdateLogoPath(
            $make->logo_path,
            $validated['editLogoUpload'] ?? null,
            $payload['name']
        );

        $make->update($payload);

        $this->dispatch('catalog-updated');
        $this->resetEditState();
        $this->setFeedback('success', 'Da cap nhat make.');
        $this->closeEditModal();
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
            $this->closeEditModal();
        }

        $this->dispatch('catalog-updated');
        $this->setFeedback('success', 'Da xoa make.');
    }

    public function render(): View
    {
        [$sortField, $sortDirection] = $this->resolveSort();

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
                ->orderBy($sortField, $sortDirection)
                ->paginate($this->perPage, ['*'], 'makesPage'),
        ]);
    }

    private function resetCreateForm(): void
    {
        $this->createForm = [
            'name' => '',
            'slug' => '',
        ];
    }

    private function resetEditState(): void
    {
        $this->editingId = null;
        $this->editForm = [
            'name' => '',
            'slug' => '',
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
            $uploadProperty => 'file logo',
        ];
    }

    public function removeCreateLogo(): void
    {
        $this->logoUpload = null;
        $this->resetValidation('logoUpload');
    }

    public function removeEditLogo(): void
    {
        $this->editLogoUpload = null;
        $this->resetValidation('editLogoUpload');
    }

    public function getCreateLogoPreviewUrlProperty(): ?string
    {
        return $this->temporaryPreviewUrl($this->logoUpload);
    }

    public function getEditLogoPreviewUrlProperty(): ?string
    {
        return $this->temporaryPreviewUrl($this->editLogoUpload);
    }

    private function prepareCreateLogoPath(mixed $logoUpload, string $name): ?string
    {
        if ($logoUpload instanceof UploadedFile) {
            return $this->storeLogoFile($logoUpload, $name);
        }

        return null;
    }

    private function prepareUpdateLogoPath(?string $currentPath, mixed $logoUpload, string $name): ?string
    {
        if ($logoUpload instanceof UploadedFile) {
            $newPath = $this->storeLogoFile($logoUpload, $name);
            $this->deleteManagedLogo($currentPath);

            return $newPath;
        }

        return $currentPath;
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

    private function temporaryPreviewUrl(mixed $upload): ?string
    {
        if (! $upload instanceof UploadedFile) {
            return null;
        }

        try {
            return $upload->temporaryUrl();
        } catch (\Throwable) {
            return null;
        }
    }

    private function setFeedback(string $type, string $message): void
    {
        $this->feedback = [];
        $this->dispatch('catalog-toast', type: $type, message: $message);
    }

    private function openEditModal(): void
    {
        $this->dispatch('catalog-make-edit-modal-opened');
    }

    private function closeEditModal(): void
    {
        $this->dispatch('catalog-make-edit-modal-closed');
    }

    /**
     * @return array{0: string, 1: 'asc'|'desc'}
     */
    private function resolveSort(): array
    {
        return match ($this->sort) {
            'name_asc' => ['name', 'asc'],
            'name_desc' => ['name', 'desc'],
            'slug_asc' => ['slug', 'asc'],
            'slug_desc' => ['slug', 'desc'],
            'models_desc' => ['models_count', 'desc'],
            'models_asc' => ['models_count', 'asc'],
            'updated_asc' => ['updated_at', 'asc'],
            default => ['updated_at', 'desc'],
        };
    }
}
