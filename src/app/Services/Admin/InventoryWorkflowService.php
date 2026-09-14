<?php

namespace App\Services\Admin;

use App\Models\CarUnit;
use App\Models\CarUnitMedia;
use App\Models\CarUnitPriceHistory;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InventoryWorkflowService
{
    private const EDITABLE_FIELDS = [
        'trim_id',
        'condition',
        'vin',
        'stock_code',
        'year',
        'mileage',
        'body_type_id',
        'fuel_type_id',
        'transmission_id',
        'drivetrain_id',
        'exterior_color_id',
        'interior_color_id',
        'price',
        'currency',
        'status',
        'notes_internal',
    ];

    private const HOLD_EDITABLE_FIELDS = [
        'notes_internal',
    ];

    public function save(array $validated, User $actor, ?CarUnit $carUnit = null): CarUnit
    {
        return DB::transaction(function () use ($validated, $actor, $carUnit): CarUnit {
            $carUnit = $carUnit?->exists
                ? CarUnit::query()->lockForUpdate()->findOrFail($carUnit->id)
                : new CarUnit;

            $this->ensureStatusTransitionIsAllowed($carUnit, (string) $validated['status']);

            $wasExisting = $carUnit->exists;
            $originalPrice = $carUnit->exists ? $carUnit->price : null;

            $editableFields = $carUnit->status === 'on_hold'
                ? self::HOLD_EDITABLE_FIELDS
                : self::EDITABLE_FIELDS;

            $carUnit->fill(Arr::only($validated, $editableFields));

            if ($carUnit->status === 'available' && $carUnit->published_at === null) {
                $carUnit->published_at = now();
            }

            if ($carUnit->status !== 'on_hold') {
                $carUnit->hold_until = null;
            }

            $carUnit->save();

            if ($wasExisting && $carUnit->wasChanged('price') && $originalPrice !== $carUnit->price) {
                CarUnitPriceHistory::query()->create([
                    'car_unit_id' => $carUnit->id,
                    'changed_by' => $actor->id,
                    'old_price' => $originalPrice,
                    'new_price' => $carUnit->price,
                ]);
            }

            $this->syncMedia($carUnit, collect($validated['media'] ?? []));

            return $carUnit;
        });
    }

    public function publish(CarUnit $carUnit): void
    {
        DB::transaction(function () use ($carUnit): void {
            $carUnit = CarUnit::query()->lockForUpdate()->findOrFail($carUnit->id);

            if (in_array($carUnit->status, ['sold', 'on_hold'], true) || $carUnit->sale()->exists()) {
                throw ValidationException::withMessages([
                    'carUnit' => 'Không thể publish xe đang giữ cọc hoặc đã bán.',
                ]);
            }

            $carUnit->forceFill([
                'status' => 'available',
                'published_at' => $carUnit->published_at ?? now(),
                'hold_until' => null,
            ])->save();
        });
    }

    public function archive(CarUnit $carUnit): void
    {
        DB::transaction(function () use ($carUnit): void {
            $carUnit = CarUnit::query()->lockForUpdate()->findOrFail($carUnit->id);

            if (in_array($carUnit->status, ['sold', 'on_hold'], true) || $carUnit->sale()->exists()) {
                throw ValidationException::withMessages([
                    'carUnit' => 'Không thể archive xe đang giữ cọc hoặc đã bán.',
                ]);
            }

            $carUnit->forceFill([
                'status' => 'archived',
                'hold_until' => null,
            ])->save();
        });
    }

    protected function syncMedia(CarUnit $carUnit, Collection $mediaRows): void
    {
        $normalizedRows = $mediaRows
            ->map(function (array $row, int $index): array {
                $caption = trim((string) ($row['caption'] ?? ''));

                return [
                    'id' => $row['id'] ?? null,
                    'type' => 'image',
                    'path_or_url' => trim((string) $row['path_or_url']),
                    'caption' => $caption !== '' ? $caption : null,
                    'sort_order' => $index,
                    'is_cover' => (bool) ($row['is_cover'] ?? false),
                ];
            })
            ->filter(fn (array $row): bool => $row['path_or_url'] !== '')
            ->values();

        if ($normalizedRows->isNotEmpty() && ! $normalizedRows->contains(fn (array $row): bool => $row['is_cover'])) {
            $normalizedRows[0]['is_cover'] = true;
        }

        $existingMedia = $carUnit->media()->get()->keyBy('id');
        $keptIds = [];

        foreach ($normalizedRows as $row) {
            $media = null;

            if ($row['id'] !== null && $existingMedia->has((int) $row['id'])) {
                $media = $existingMedia->get((int) $row['id']);
                $media->update(Arr::except($row, 'id'));
            } else {
                $media = $carUnit->media()->create(Arr::except($row, 'id'));
            }

            /** @var CarUnitMedia $media */
            $keptIds[] = $media->id;
        }

        $removedPaths = $existingMedia
            ->except($keptIds)
            ->pluck('path_or_url');

        if ($keptIds !== []) {
            $carUnit->media()
                ->whereNotIn('id', $keptIds)
                ->delete();
        } else {
            $carUnit->media()->delete();
        }

        if ($carUnit->media()->exists()) {
            $coverId = $carUnit->media()
                ->where('is_cover', true)
                ->value('id');

            if ($coverId === null) {
                $coverId = $carUnit->media()
                    ->orderBy('sort_order')
                    ->value('id');
            }

            $carUnit->media()->update(['is_cover' => false]);

            if ($coverId !== null) {
                $carUnit->media()->whereKey($coverId)->update(['is_cover' => true]);
            }
        }

        DB::afterCommit(fn () => $this->deleteManagedMediaFiles($removedPaths));
    }

    private function deleteManagedMediaFiles(Collection $paths): void
    {
        $paths->each(static function (mixed $path): void {
            $path = trim((string) $path);

            if (! str_starts_with($path, '/storage/inventory-media/')) {
                return;
            }

            Storage::disk('public')->delete(ltrim(substr($path, strlen('/storage/')), '/'));
        });
    }

    private function ensureStatusTransitionIsAllowed(CarUnit $carUnit, string $nextStatus): void
    {
        if (! $carUnit->exists) {
            if (! in_array($nextStatus, ['draft', 'available'], true)) {
                throw ValidationException::withMessages([
                    'form.status' => 'Xe mới chỉ có thể bắt đầu ở trạng thái bản nháp hoặc sẵn sàng bán.',
                ]);
            }

            return;
        }

        $hasSale = $carUnit->sale()->exists();

        if ($carUnit->status === 'sold' || $hasSale) {
            throw ValidationException::withMessages([
                'form.status' => 'Xe đã bán chỉ có thể xem và không thể cập nhật trong Inventory.',
            ]);
        }

        if ($nextStatus === 'sold') {
            throw ValidationException::withMessages([
                'form.status' => 'Trạng thái Đã bán phải được tạo từ module Quản lý bán hàng.',
            ]);
        }

        if ($carUnit->status === 'on_hold' && $nextStatus !== 'on_hold') {
            throw ValidationException::withMessages([
                'form.status' => 'Xe đang giữ cọc chỉ có thể đổi trạng thái qua workflow giữ cọc.',
            ]);
        }

        if ($nextStatus === 'on_hold' && $carUnit->status !== 'on_hold') {
            throw ValidationException::withMessages([
                'form.status' => 'Hãy dùng workflow giữ cọc để chuyển xe sang trạng thái này.',
            ]);
        }
    }
}
