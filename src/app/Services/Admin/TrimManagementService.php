<?php

namespace App\Services\Admin;

use App\Models\CarAttribute;
use App\Models\Trim;
use App\Models\TrimAttributeValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TrimManagementService
{
    public function save(array $validated, ?Trim $trim = null): Trim
    {
        return DB::transaction(function () use ($validated, $trim): Trim {
            $trim ??= new Trim();

            $trim->fill(Arr::only($validated, [
                'model_id',
                'name',
                'slug',
                'year_from',
                'year_to',
                'msrp',
                'description',
            ]));
            $trim->save();

            $trim->features()->sync($validated['feature_ids'] ?? []);
            $this->syncAttributes($trim, $validated['attributes'] ?? []);

            return $trim->fresh([
                'model.make',
                'features',
                'attributeValues.attribute',
            ]);
        });
    }

    protected function syncAttributes(Trim $trim, array $rawAttributes): void
    {
        TrimAttributeValue::query()
            ->where('trim_id', $trim->id)
            ->delete();

        if ($rawAttributes === []) {
            return;
        }

        $attributes = CarAttribute::query()
            ->whereIn('id', array_keys($rawAttributes))
            ->get()
            ->keyBy('id');

        foreach ($rawAttributes as $attributeId => $values) {
            $attribute = $attributes->get((int) $attributeId);

            if ($attribute === null || ! is_array($values)) {
                continue;
            }

            $payload = [
                'trim_id' => $trim->id,
                'attribute_id' => $attribute->id,
                'value_string' => null,
                'value_number' => null,
                'value_boolean' => null,
            ];

            if ($attribute->type === 'string') {
                $stringValue = trim((string) ($values['value_string'] ?? ''));

                if ($stringValue === '') {
                    continue;
                }

                $payload['value_string'] = $stringValue;
            }

            if ($attribute->type === 'number') {
                $numberValue = $values['value_number'] ?? null;

                if ($numberValue === null || $numberValue === '') {
                    continue;
                }

                $payload['value_number'] = (float) $numberValue;
            }

            if ($attribute->type === 'boolean') {
                $booleanValue = $values['value_boolean'] ?? null;

                if ($booleanValue === null || $booleanValue === '') {
                    continue;
                }

                $payload['value_boolean'] = (bool) (int) $booleanValue;
            }

            TrimAttributeValue::query()->create($payload);
        }
    }
}
