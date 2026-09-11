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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;

class Form extends AdminPageComponent
{
    use WithFileUploads;

    #[Locked]
    public ?int $carUnitId = null;

    /** @var array<string, mixed> */
    public array $form = [];

    /** @var array<int, array{id: ?int, type: string, path_or_url: string, caption: ?string, sort_order: int, is_cover: bool}> */
    public array $media = [];

    /** @var array<int, mixed> */
    public array $uploads = [];

    public string $newMediaUrl = '';

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function mount(?CarUnit $carUnit = null): void
    {
        $this->carUnitId = $carUnit?->id;
        $this->fillForm($carUnit);

        $feedback = session()->pull('inventory_feedback');
        if (is_array($feedback)) {
            $this->feedback = $feedback;
        }
    }

    public function updatedUploads(): void
    {
        $this->validate([
            'uploads.*' => ['image', 'max:10240'],
        ]);

        foreach ($this->uploads as $file) {
            $path = $file->store('inventory-media', 'public');
            $this->media[] = [
                'id' => null,
                'type' => 'image',
                'path_or_url' => '/storage/' . $path,
                'caption' => $file->getClientOriginalName(),
                'sort_order' => count($this->media),
                'is_cover' => count($this->media) === 0,
            ];
        }

        $this->uploads = [];
    }

    public function addMediaUrl(): void
    {
        $url = trim($this->newMediaUrl);
        if ($url === '') {
            return;
        }

        $this->media[] = [
            'id' => null,
            'type' => 'image',
            'path_or_url' => $url,
            'caption' => 'Ảnh nhập từ URL',
            'sort_order' => count($this->media),
            'is_cover' => count($this->media) === 0,
        ];

        $this->newMediaUrl = '';
    }

    public function setCover(int $index): void
    {
        foreach ($this->media as $i => $row) {
            $this->media[$i]['is_cover'] = ($i === $index);
        }
    }

    public function removeMedia(int $index): void
    {
        if (! isset($this->media[$index])) {
            return;
        }

        $wasCover = $this->media[$index]['is_cover'];
        array_splice($this->media, $index, 1);

        foreach ($this->media as $i => $row) {
            $this->media[$i]['sort_order'] = $i;
        }

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

        $this->reorderMedia();
    }

    public function moveMediaDown(int $index): void
    {
        if ($index >= count($this->media) - 1 || ! isset($this->media[$index])) {
            return;
        }

        $temp = $this->media[$index];
        $this->media[$index] = $this->media[$index + 1];
        $this->media[$index + 1] = $temp;

        $this->reorderMedia();
    }

    public function setCondition(string $condition): void
    {
        if (in_array($condition, ['new', 'used', 'cpo'], true)) {
            $this->form['condition'] = $condition;
        }
    }

    public function saveWithStatus(string $status, InventoryWorkflowService $service): void
    {
        if (in_array($status, ['draft', 'available', 'on_hold', 'sold', 'archived'], true)) {
            $this->form['status'] = $status;
        }

        $this->save($service);
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function save(InventoryWorkflowService $service): void
    {
        $this->feedback = [];
        $this->resetErrorBag();
        $this->form = $this->normalizeForm($this->form);

        $validated = $this->validate(
            $this->rules(),
            attributes: $this->validationAttributes(),
        );

        $carUnit = $this->carUnitId !== null
            ? CarUnit::query()->with('sale')->findOrFail($this->carUnitId)
            : null;

        $status = (string) $this->form['status'];
        if ($status === 'sold' && ($carUnit === null || ! $carUnit->sale()->exists())) {
            $this->addError('form.status', 'Trạng thái Đã bán (sold) phải được tạo từ module Quản lý bán hàng (Sales).');

            return;
        }

        if ($status === 'on_hold' && ($carUnit === null || $carUnit->status !== 'on_hold')) {
            $this->addError('form.status', 'Hãy dùng workflow giữ cọc để cập nhật trạng thái giữ xe.');

            return;
        }

        $user = $this->authorizeAdminAccess($this->requiredPermission());

        $payload = array_merge($validated['form'], [
            'media' => $this->media,
        ]);

        $saved = $service->save($payload, $user, $carUnit);

        if ($this->carUnitId === null) {
            session()->flash('inventory_feedback', [
                'type' => 'success',
                'message' => "Đã tạo xe mới [{$saved->stock_code}] trong kho thành công.",
            ]);

            $this->redirectRoute('admin.inventory.edit', $saved, navigate: true);

            return;
        }

        $this->fillForm($saved);
        $this->feedback = [
            'type' => 'success',
            'message' => "Đã cập nhật thông tin xe [{$saved->stock_code}] thành công.",
        ];
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
            'form.exterior_color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'form.interior_color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'form.price' => ['nullable', 'integer', 'min:0'],
            'form.currency' => ['required', 'string', 'size:3'],
            'form.status' => ['required', Rule::in(['draft', 'available', 'on_hold', 'sold', 'archived'])],
            'form.notes_internal' => ['nullable', 'string'],
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
                ->orderBy('sort_order')
                ->get()
                ->map(static function (CarUnitMedia $m): array {
                    return [
                        'id' => (int) $m->id,
                        'type' => (string) $m->type,
                        'path_or_url' => (string) $m->path_or_url,
                        'caption' => (string) ($m->caption ?? ''),
                        'sort_order' => (int) $m->sort_order,
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
            'stock_code' => 'STK-' . date('Y') . '-' . rand(100, 999),
            'year' => (int) date('Y'),
            'mileage' => null,
            'body_type_id' => null,
            'fuel_type_id' => null,
            'transmission_id' => null,
            'drivetrain_id' => null,
            'exterior_color_id' => null,
            'interior_color_id' => null,
            'price' => null,
            'currency' => 'VND',
            'status' => 'available',
            'notes_internal' => '',
        ];

        $this->media = [];
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
            'year' => ! empty($data['year']) ? (int) $data['year'] : (int) date('Y'),
            'mileage' => isset($data['mileage']) && $data['mileage'] !== '' ? (int) $data['mileage'] : null,
            'body_type_id' => ! empty($data['body_type_id']) ? (int) $data['body_type_id'] : null,
            'fuel_type_id' => ! empty($data['fuel_type_id']) ? (int) $data['fuel_type_id'] : null,
            'transmission_id' => ! empty($data['transmission_id']) ? (int) $data['transmission_id'] : null,
            'drivetrain_id' => ! empty($data['drivetrain_id']) ? (int) $data['drivetrain_id'] : null,
            'exterior_color_id' => ! empty($data['exterior_color_id']) ? (int) $data['exterior_color_id'] : null,
            'interior_color_id' => ! empty($data['interior_color_id']) ? (int) $data['interior_color_id'] : null,
            'price' => isset($data['price']) && $data['price'] !== '' ? (int) $data['price'] : null,
            'currency' => 'VND',
            'status' => trim((string) ($data['status'] ?? 'available')),
            'notes_internal' => ! empty($data['notes_internal']) ? trim((string) $data['notes_internal']) : null,
        ];
    }

    private function reorderMedia(): void
    {
        foreach ($this->media as $i => $row) {
            $this->media[$i]['sort_order'] = $i;
        }
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
