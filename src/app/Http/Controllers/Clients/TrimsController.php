<?php

namespace App\Http\Controllers\Clients;

use App\Models\Sale;
use App\Models\Trim;
use App\Models\TrimReview;
use Illuminate\View\View;

class TrimsController extends ClientBaseController
{
    public function show(string $trimSlug): View
    {
        $trim = Trim::query()
            ->with('model.make')
            ->where('slug', $trimSlug)
            ->firstOrFail();

        /** @var \App\Models\CarModel|null $carModel */
        $carModel = $trim->model;
        /** @var \App\Models\Make|null $make */
        $make = $carModel?->make;

        $trim->model_name = $carModel?->name;
        $trim->model_slug = $carModel?->slug;
        $trim->make_name = $make?->name;
        $trim->make_slug = $make?->slug;

        $features = $this->loadTrimFeatures($trim->id);

        $attributes = $this->loadTrimAttributes($trim->id);

        $availableCarsQuery = $this->publicVisibleCarQuery()
            ->where('car_units.trim_id', $trim->id);

        $availableCarsCount = (clone $availableCarsQuery)->count();

        $availableCars = $availableCarsQuery
            ->orderByDesc('car_units.published_at')
            ->orderByDesc('car_units.id')
            ->limit(12)
            ->get()
            ->map(fn (object $car) => $this->decorateCar($car));

        // Format MSRP & display title without duplicate model name
        $rawMsrp = (float) ($trim->msrp ?? 0);
        $formattedMsrp = $rawMsrp > 0 ? number_format($rawMsrp, 0, ',', '.') . ' VNĐ' : 'Liên hệ nhận báo giá';
        $estimatedMonthly = $rawMsrp > 0 ? number_format(($rawMsrp * 0.7 * 0.012), 0, ',', '.') : '10.000.000';

        $displayTitle = str_starts_with(strtolower((string) $trim->name), strtolower((string) $trim->model_name))
            ? $trim->make_name . ' ' . $trim->name
            : $trim->make_name . ' ' . $trim->model_name . ' ' . $trim->name;

        $yearRange = ($trim->year_from ?? '2024') . ($trim->year_to ? ' - ' . $trim->year_to : ' - Hiện tại');

        $heroImageFallback = file_exists(public_path('seed-media/' . $trim->slug . '.jpg'))
            ? asset('seed-media/' . $trim->slug . '.jpg')
            : asset('seed-media/placeholder-car.jpg');

        $heroImageUrl = $availableCars->first()->image_url ?? $heroImageFallback;

        $reviews = $this->loadApprovedTrimReviews($trim->id);
        $userHasPurchasedTrim = false;
        $userReview = null;
        $canSubmitReview = false;

        if (auth()->check()) {
            $userHasPurchasedTrim = Sale::hasBuyerPurchasedTrim((int) auth()->id(), $trim->id);
            $userReview = TrimReview::query()
                ->where('trim_id', $trim->id)
                ->where('user_id', (int) auth()->id())
                ->first();
            $canSubmitReview = $userHasPurchasedTrim && $userReview === null;
        }

        return $this->viewWithSharedData('client.trim-detail', [
            'trim' => $trim,
            'displayTitle' => $displayTitle,
            'yearRange' => $yearRange,
            'formattedMsrp' => $formattedMsrp,
            'rawMsrp' => $rawMsrp,
            'estimatedMonthly' => $estimatedMonthly,
            'heroImageUrl' => $heroImageUrl,
            'features' => $features,
            'attributes' => $attributes,
            'availableCarsCount' => $availableCarsCount,
            'availableCars' => $availableCars,
            'reviews' => $reviews,
            'userHasPurchasedTrim' => $userHasPurchasedTrim,
            'userReview' => $userReview,
            'canSubmitReview' => $canSubmitReview,
        ]);
    }
}
