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

        $trim->model_name = $trim->model?->name;
        $trim->model_slug = $trim->model?->slug;
        $trim->make_name = $trim->model?->make?->name;
        $trim->make_slug = $trim->model?->make?->slug;

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
