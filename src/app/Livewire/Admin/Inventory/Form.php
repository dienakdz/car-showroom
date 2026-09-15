<?php

namespace App\Livewire\Admin\Inventory;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\BodyType;
use App\Models\CarUnit;
use App\Models\CarUnitMedia;
use App\Models\Color;
use App\Models\Drivetrain;
use App\Models\FuelType;
use App\Models\Transmission;
use App\Models\Trim;
use App\Services\Admin\InventoryWorkflowService;
use App\Support\Admin\AdminContextResolver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Throwable;

class Form extends AdminPageComponent
{
    use WithFileUploads;

    #[Locked]
    public ?int $carUnitId = null;

    /** @var array<string, mixed> */
    public array $form = [];

    /** @var array<int, array{id: ?int, path_or_url: string, caption: ?string, is_cover: bool, upload_key?: string}> */
    public array $media = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $uploads = [];

    /** @var array<string, TemporaryUploadedFile> */
    public array $pendingUploads = [];

    public function mount(?CarUnit $carUnit = null): void
    {
        $this->carUnitId = $carUnit?->id;
        $this->fillForm($carUnit);
    }

    public function updatedUploads(): void
    {
        $this->validate([
            'uploads.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], attributes: [
            'uploads.*' => 'hình ảnh tải lên',
        ]);

        foreach ($this->uploads as $file) {
            $uploadKey = $file->getFilename();
            $this->pendingUploads[$uploadKey] = $file;
            $this->media[] = [
                'id' => null,
                'path_or_url' => '',
                'caption' => Str::limit($file->getClientOriginalName(), 255, ''),
                'is_cover' => count($this->media) === 0,
                'upload_key' => $uploadKey,
            ];
        }

        $this->uploads = [];
    }

    public function setCover(int $index): void
    {
        if (! isset($this->media[$index])) {
            return;
        }

        foreach ($this->media as $i => $row) {
            $this->media[$i]['is_cover'] = ($i === $index);
        }
    }

    public function removeMedia(int $index): void
    {
        if (! isset($this->media[$index])) {
            return;
        }

        $this->removePendingUpload($this->media[$index]);
        $wasCover = (bool) ($this->media[$index]['is_cover'] ?? false);
        array_splice($this->media, $index, 1);

        if ($wasCover && count($this->media) > 0) {
            $this->media[0]['is_cover'] = true;
        }
    }

    public function moveMediaUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->media[$index])) {
            return;
        }

        $temp = $this->media[$index];
        $this->media[$index] = $this->media[$index - 1];
        $this->media[$index - 1] = $temp;
    }

    public function moveMediaDown(int $index): void
    {
        if ($index >= count($this->media) - 1 || ! isset($this->media[$index])) {
            return;
        }

        $temp = $this->media[$index];
        $this->media[$index] = $this->media[$index + 1];
        $this->media[$index + 1] = $temp;
    }

    public function setCondition(string $condition): void
    {
        if (in_array($condition, ['new', 'used', 'cpo'], true)) {
            $this->form['condition'] = $condition;
        }
    }

    public function saveWithStatus(string $status, InventoryWorkflowService $service): void
    {
        if (! in_array($status, ['draft', 'available'], true)) {
            return;
        }

        $this->form['status'] = $status;
        $this->save($service);
    }

    public function save(InventoryWorkflowService $service): void
    {
        $this->resetErrorBag();
        $this->form = $this->normalizeForm($this->form);

        $validated = $this->validate(
            $this->rules(),
            attributes: $this->validationAttributes(),
        );

        $carUnit = $this->carUnitId !== null
            ? CarUnit::query()->findOrFail($this->carUnitId)
            : null;

        $user = $this->authorizeAdminAccess($this->requiredPermission());

        $storedPaths = [];

        try {
            $media = $this->storePendingUploads($validated['media'], $storedPaths);

            $payload = array_merge($validated['form'], [
                'media' => $media,
            ]);

            $saved = $service->save($payload, $user, $carUnit);
        } catch (Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('public')->delete($storedPaths);
            }

            throw $exception;
        }

        $this->clearPendingUploads();

        if ($this->carUnitId === null) {
            $this->flashToast('success', "Đã tạo xe mới [{$saved->stock_code}] trong kho thành công.");

            $this->redirectRoute('admin.inventory.edit', $saved, navigate: true);

            return;
        }

        $this->fillForm($saved);
        $this->toast('success', "Đã cập nhật thông tin xe [{$saved->stock_code}] thành công.");
    }

    public function render(): View
    {
        $carUnit = $this->carUnitId !== null
            ? CarUnit::query()->with(['trim.model.make', 'media'])->findOrFail($this->carUnitId)
            : new CarUnit;

        return view('livewire.admin.inventory.form', [
            'carUnit' => $carUnit,
            'trims' => $this->availableTrims(),
            'bodyTypes' => BodyType::query()->orderBy('name')->get(),
            'fuelTypes' => FuelType::query()->orderBy('name')->get(),
            'transmissions' => Transmission::query()->orderBy('name')->get(),
            'drivetrains' => Drivetrain::query()->orderBy('name')->get(),
            'exteriorColors' => Color::query()->where('type', 'exterior')->orderBy('name')->get(),
            'interiorColors' => Color::query()->where('type', 'interior')->orderBy('name')->get(),
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => $this->carUnitId !== null ? 'Cập nhật thông tin xe' : 'Thêm xe mới vào kho',
            'adminPageDescription' => $this->carUnitId !== null
                ? 'Chỉnh sửa thông tin định danh, thông số kỹ thuật, hình ảnh và quản lý lịch sử trạng thái của xe.'
                : 'Khai báo thông tin định danh, thông số kỹ thuật, hình ảnh và định giá cho xe mới trong kho.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'inventory.manage';
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $currentYear = (int) now()->addYear()->format('Y');

        return [
            'form.trim_id' => ['required', 'integer', 'exists:trims,id'],
            'form.condition' => ['required', Rule::in(['new', 'used', 'cpo'])],
            'form.vin' => ['nullable', 'string', 'max:255', Rule::unique('car_units', 'vin')->ignore($this->carUnitId)],
            'form.stock_code' => ['required', 'string', 'max:255', Rule::unique('car_units', 'stock_code')->ignore($this->carUnitId)],
            'form.year' => ['required', 'integer', 'min:1900', 'max:' . $currentYear],
            'form.mileage' => [
                Rule::requiredIf(in_array($this->form['condition'] ?? '', ['used', 'cpo'], true)),
                'nullable',
                'integer',
                'min:0',
            ],
            'form.body_type_id' => ['nullable', 'integer', 'exists:body_types,id'],
            'form.fuel_type_id' => ['nullable', 'integer', 'exists:fuel_types,id'],
            'form.transmission_id' => ['nullable', 'integer', 'exists:transmissions,id'],
            'form.drivetrain_id' => ['nullable', 'integer', 'exists:drivetrains,id'],
            'form.exterior_color_id' => ['nullable', 'integer', Rule::exists('colors', 'id')->where('type', 'exterior')],
            'form.interior_color_id' => ['nullable', 'integer', Rule::exists('colors', 'id')->where('type', 'interior')],
            'form.price' => ['nullable', 'integer', 'min:0'],
            'form.currency' => ['required', 'string', 'size:3'],
            'form.status' => ['required', Rule::in(['draft', 'available', 'on_hold', 'sold', 'archived'])],
            'form.notes_internal' => ['nullable', 'string'],
            'media' => ['array', 'max:30'],
            'media.*.id' => ['nullable', 'integer', 'distinct'],
            'media.*.path_or_url' => ['nullable', 'string', 'max:2048'],
            'media.*.caption' => ['nullable', 'string', 'max:255'],
            'media.*.is_cover' => ['required', 'boolean'],
            'media.*.upload_key' => ['nullable', 'string'],
            'pendingUploads' => ['array'],
            'pendingUploads.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'form.trim_id' => 'phiên bản xe (Trim)',
            'form.condition' => 'tình trạng xe',
            'form.vin' => 'số khung VIN',
            'form.stock_code' => 'mã quản lý kho (Stock code)',
            'form.year' => 'năm sản xuất',
            'form.mileage' => 'số km đã đi (ODO)',
            'form.body_type_id' => 'kiểu dáng thân xe',
            'form.fuel_type_id' => 'loại nhiên liệu',
            'form.transmission_id' => 'hộp số',
            'form.drivetrain_id' => 'hệ dẫn động',
            'form.exterior_color_id' => 'màu ngoại thất',
            'form.interior_color_id' => 'màu nội thất',
            'form.price' => 'giá bán niêm yết',
            'form.currency' => 'đơn vị tiền tệ',
            'form.status' => 'trạng thái xe',
            'form.notes_internal' => 'ghi chú nội bộ',
            'media' => 'danh sách hình ảnh',
            'media.*.path_or_url' => 'đường dẫn hình ảnh',
            'media.*.caption' => 'mô tả hình ảnh',
            'pendingUploads.*' => 'hình ảnh chờ lưu',
        ];
    }

    private function fillForm(?CarUnit $carUnit): void
    {
        if ($carUnit !== null && $carUnit->exists) {
            $this->form = [
                'trim_id' => $carUnit->trim_id,
                'condition' => (string) $carUnit->condition,
                'vin' => (string) ($carUnit->vin ?? ''),
                'stock_code' => (string) $carUnit->stock_code,
                'year' => (int) $carUnit->year,
                'mileage' => $carUnit->mileage,
                'body_type_id' => $carUnit->body_type_id,
                'fuel_type_id' => $carUnit->fuel_type_id,
                'transmission_id' => $carUnit->transmission_id,
                'drivetrain_id' => $carUnit->drivetrain_id,
                'exterior_color_id' => $carUnit->exterior_color_id,
                'interior_color_id' => $carUnit->interior_color_id,
                'price' => $carUnit->price,
                'currency' => (string) ($carUnit->currency ?? 'VND'),
                'status' => (string) $carUnit->status,
                'notes_internal' => (string) ($carUnit->notes_internal ?? ''),
            ];

            $this->media = CarUnitMedia::query()
                ->where('car_unit_id', $carUnit->id)
                ->where('type', 'image')
                ->orderBy('sort_order')
                ->get()
                ->map(static function (CarUnitMedia $m): array {
                    return [
                        'id' => (int) $m->id,
                        'path_or_url' => (string) $m->path_or_url,
                        'caption' => (string) ($m->caption ?? ''),
                        'is_cover' => (bool) $m->is_cover,
                    ];
                })
                ->all();

            return;
        }

        $this->form = [
            'trim_id' => null,
            'condition' => 'new',
            'vin' => '',
            'stock_code' => 'STK-' . date('Y') . '-' . Str::upper(Str::random(6)),
            'year' => (int) date('Y'),
            'mileage' => null,
            'body_type_id' => null,
            'fuel_type_id' => null,
            'transmission_id' => null,
            'drivetrain_id' => null,
            'exterior_color_id' => null,
            'interior_color_id' => null,
            'price' => null,
            'currency' => $this->defaultCurrency(),
            'status' => 'draft',
            'notes_internal' => '',
        ];

        $this->media = [];
        $this->pendingUploads = [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeForm(array $data): array
    {
        return [
            'trim_id' => ! empty($data['trim_id']) ? (int) $data['trim_id'] : null,
            'condition' => trim((string) ($data['condition'] ?? 'new')),
            'vin' => ! empty($data['vin']) ? strtoupper(trim((string) $data['vin'])) : null,
            'stock_code' => strtoupper(trim((string) ($data['stock_code'] ?? ''))),
            'year' => isset($data['year']) && $data['year'] !== '' ? (int) $data['year'] : null,
            'mileage' => isset($data['mileage']) && $data['mileage'] !== '' ? (int) $data['mileage'] : null,
            'body_type_id' => ! empty($data['body_type_id']) ? (int) $data['body_type_id'] : null,
            'fuel_type_id' => ! empty($data['fuel_type_id']) ? (int) $data['fuel_type_id'] : null,
            'transmission_id' => ! empty($data['transmission_id']) ? (int) $data['transmission_id'] : null,
            'drivetrain_id' => ! empty($data['drivetrain_id']) ? (int) $data['drivetrain_id'] : null,
            'exterior_color_id' => ! empty($data['exterior_color_id']) ? (int) $data['exterior_color_id'] : null,
            'interior_color_id' => ! empty($data['interior_color_id']) ? (int) $data['interior_color_id'] : null,
            'price' => isset($data['price']) && $data['price'] !== '' ? (int) $data['price'] : null,
            'currency' => strtoupper(trim((string) ($data['currency'] ?? $this->defaultCurrency()))),
            'status' => trim((string) ($data['status'] ?? 'available')),
            'notes_internal' => ! empty($data['notes_internal']) ? trim((string) $data['notes_internal']) : null,
        ];
    }

    /**
     * @param  array{id?: ?int, upload_key?: string}  $media
     */
    private function removePendingUpload(array $media): void
    {
        $uploadKey = trim((string) ($media['upload_key'] ?? ''));

        if ($uploadKey === '') {
            return;
        }

        $upload = $this->pendingUploads[$uploadKey] ?? null;

        if ($upload instanceof TemporaryUploadedFile) {
            $upload->delete();
        }

        unset($this->pendingUploads[$uploadKey]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $mediaRows
     * @param  array<int, string>  $storedPaths
     * @return array<int, array<string, mixed>>
     */
    private function storePendingUploads(array $mediaRows, array &$storedPaths): array
    {
        foreach ($mediaRows as $index => $row) {
            $uploadKey = trim((string) ($row['upload_key'] ?? ''));

            if ($uploadKey !== '') {
                $upload = $this->pendingUploads[$uploadKey] ?? null;

                if (! $upload instanceof TemporaryUploadedFile) {
                    throw ValidationException::withMessages([
                        "media.{$index}.path_or_url" => 'Không tìm thấy hình ảnh tạm để lưu. Vui lòng tải ảnh lên lại.',
                    ]);
                }

                $path = $upload->store('inventory-media', 'public');

                if (! is_string($path)) {
                    throw ValidationException::withMessages([
                        "media.{$index}.path_or_url" => 'Không thể lưu hình ảnh. Vui lòng thử lại.',
                    ]);
                }

                $storedPaths[] = $path;
                $row['path_or_url'] = '/storage/' . $path;
            }

            if (trim((string) ($row['path_or_url'] ?? '')) === '') {
                throw ValidationException::withMessages([
                    "media.{$index}.path_or_url" => 'Đường dẫn hình ảnh không hợp lệ.',
                ]);
            }

            unset($row['upload_key']);
            $mediaRows[$index] = $row;
        }

        return array_values($mediaRows);
    }

    private function clearPendingUploads(): void
    {
        foreach ($this->pendingUploads as $upload) {
            $upload->delete();
        }

        $this->pendingUploads = [];
        $this->uploads = [];
    }

    private function defaultCurrency(): string
    {
        $settings = app(AdminContextResolver::class)->settings();

        return strtoupper((string) data_get($settings, 'site.default_currency.value', 'VND'));
    }

    /**
     * @return Collection<int, Trim>
     */
    private function availableTrims(): Collection
    {
        return Trim::query()
            ->with('model.make')
            ->orderBy('name')
            ->get();
    }
}
