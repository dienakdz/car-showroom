<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class CarSearchService
{
    /**
     * @return list<int>|null Null means the search service was unavailable or returned an invalid response.
     */
    public function rankedCarIds(string $query): ?array
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
        if (! is_array($hits)) {
            Log::warning('Car search service returned an invalid response.');

            return null;
        }

        $rankedIds = [];
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

            $rankedIds[] = (int) $carUnitId;
        }

        return array_values(array_unique($rankedIds));
    }
}
