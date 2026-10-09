<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class CarSearchService
{
    /**
     * @return array{
     *     match_mode: 'strict'|'relaxed'|'none',
     *     ranked_ids: list<int>,
     *     relaxed_constraints_by_id: array<int, list<string>>
     * }|null Null means the search service was unavailable or returned an invalid response.
     */
    public function search(string $query): ?array
    {
        $baseUrl = rtrim((string) config('services.car_search.url'), '/');
        if ($baseUrl === '') {
            Log::warning('Car search service URL is not configured.');

            return null;
        }

        $request = Http::acceptJson()
            ->asJson()
            ->connectTimeout((int) config('services.car_search.connect_timeout', 1))
            ->timeout((int) config('services.car_search.timeout', 3));

        $token = (string) config('services.car_search.token');
        if ($token !== '') {
            $request = $request->withHeaders([
                'X-Internal-Token' => $token,
            ]);
        }

        try {
            $response = $request->post($baseUrl . '/search', [
                'query' => $query,
                'limit' => (int) config('services.car_search.result_limit', 100),
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('Car search service is unavailable.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Car search service returned an unsuccessful response.', [
                'status' => $response->status(),
            ]);

            return null;
        }

        $hits = $response->json('hits');
        $matchMode = $response->json('match_mode');
        if (! is_array($hits) || ! is_string($matchMode) || ! in_array($matchMode, ['strict', 'relaxed', 'none'], true)) {
            Log::warning('Car search service returned an invalid response.');

            return null;
        }

        $rankedIds = [];
        $relaxedConstraintsById = [];
        foreach ($hits as $hit) {
            if (! is_array($hit)) {
                continue;
            }

            $carUnitId = filter_var(
                $hit['car_unit_id'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]],
            );

            if ($carUnitId === false) {
                continue;
            }

            $carUnitId = (int) $carUnitId;
            if (isset($relaxedConstraintsById[$carUnitId])) {
                continue;
            }

            $rankedIds[] = $carUnitId;
            $relaxedConstraintsById[$carUnitId] = $this->stringList(
                $hit['relaxed_constraints'] ?? [],
            );
        }

        return [
            'match_mode' => $matchMode,
            'ranked_ids' => $rankedIds,
            'relaxed_constraints_by_id' => $relaxedConstraintsById,
        ];
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            if (! is_string($item)) {
                continue;
            }

            $item = trim($item);
            if ($item !== '') {
                $items[] = $item;
            }
        }

        return array_values(array_unique($items));
    }
}
