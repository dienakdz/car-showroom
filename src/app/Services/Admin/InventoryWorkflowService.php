<?php

namespace App\Services\Admin;

use App\Models\CarUnit;
use App\Models\CarUnitHold;
use App\Models\CarUnitPriceHistory;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryWorkflowService
{
    public function save(array $validated, User $actor, ?CarUnit $carUnit = null): CarUnit
    {
        return DB::transaction(function () use ($validated, $actor, $carUnit): CarUnit {
            $carUnit ??= new CarUnit();

            $wasExisting = $carUnit->exists;
            $originalPrice = $carUnit->exists ? $carUnit->price : null;

            $carUnit->fill(Arr::only($validated, [
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
            ]));

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

            return $carUnit->fresh([
                'trim.model.make',
                'media',
                'holds.createdBy',
                'priceHistories.changedBy',
                'sale',
            ]);
        });
    }

    public function publish(CarUnit $carUnit): void
    {
        if ($carUnit->status === 'sold') {
            throw ValidationException::withMessages([
                'carUnit' => 'Khong the publish mot xe da sold.',
            ]);
        }

        $carUnit->forceFill([
            'status' => 'available',
            'published_at' => $carUnit->published_at ?? now(),
            'hold_until' => null,
        ])->save();
    }

    public function archive(CarUnit $carUnit): void
    {
        $carUnit->forceFill([
            'status' => 'archived',
            'hold_until' => null,
        ])->save();
    }

    public function hold(CarUnit $carUnit, Carbon $holdUntil, ?string $reason, User $actor): void
    {
        if ($carUnit->status === 'sold') {
            throw ValidationException::withMessages([
                'carUnit' => 'Khong the giu mot xe da sold.',
            ]);
        }

        DB::transaction(function () use ($carUnit, $holdUntil, $reason, $actor): void {
            $carUnit->forceFill([
                'status' => 'on_hold',
                'hold_until' => $holdUntil,
            ])->save();

            CarUnitHold::query()->create([
                'car_unit_id' => $carUnit->id,
                'created_by' => $actor->id,
                'hold_until' => $holdUntil,
                'reason' => $reason,
            ]);
        });
    }

    public function release(CarUnit $carUnit): void
    {
        if ($carUnit->status !== 'on_hold') {
            return;
        }

        $carUnit->forceFill([
            'status' => 'available',
            'hold_until' => null,
            'published_at' => $carUnit->published_at ?? now(),
        ])->save();
    }

    public function updatePrice(CarUnit $carUnit, ?int $price, User $actor): void
    {
        if ($carUnit->price === $price) {
            return;
        }

        DB::transaction(function () use ($carUnit, $price, $actor): void {
            CarUnitPriceHistory::query()->create([
                'car_unit_id' => $carUnit->id,
                'changed_by' => $actor->id,
                'old_price' => $carUnit->price,
                'new_price' => $price,
            ]);

            $carUnit->update(['price' => $price]);
        });
    }

    protected function syncMedia(CarUnit $carUnit, Collection $mediaRows): void
    {
        $normalizedRows = $mediaRows
            ->map(function (array $row, int $index): array {
                return [
                    'id' => $row['id'] ?? null,
                    'type' => $row['type'],
                    'path_or_url' => trim((string) $row['path_or_url']),
                    'caption' => $row['caption'] !== '' ? $row['caption'] : null,
                    'sort_order' => (int) ($row['sort_order'] ?? $index),
                    'is_cover' => (bool) ($row['is_cover'] ?? false),
                ];
            })
            ->filter(fn (array $row): bool => $row['path_or_url'] !== '')
            ->values();

        if ($normalizedRows->isNotEmpty() && ! $normalizedRows->contains(fn (array $row): bool => $row['is_cover'])) {
            $firstIndex = $normalizedRows->search(fn (array $row): bool => $row['type'] === 'image');
            $targetIndex = $firstIndex !== false ? $firstIndex : 0;
            $normalizedRows[$targetIndex]['is_cover'] = true;
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

            $keptIds[] = $media->id;
        }

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
                $coverId = $carUnit->media()->orderBy('sort_order')->value('id');
            }

            $carUnit->media()->update(['is_cover' => false]);
            $carUnit->media()->whereKey($coverId)->update(['is_cover' => true]);
        }
    }
}
