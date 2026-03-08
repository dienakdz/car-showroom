<?php

namespace App\Http\Controllers\Clients;

use App\Models\Trim;
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

        $availableCars = $this->baseCarQuery()
            ->where('car_units.trim_id', $trim->id)
            ->where('car_units.status', 'available')
            ->orderByDesc('car_units.published_at')
            ->orderByDesc('car_units.id')
            ->limit(12)
            ->get()
            ->map(fn (object $car) => $this->decorateCar($car));

        $reviews = $this->loadApprovedTrimReviews($trim->id);

        return $this->viewWithSharedData('client.trim-detail', [
            'trim' => $trim,
            'features' => $features,
            'attributes' => $attributes,
            'availableCars' => $availableCars,
            'reviews' => $reviews,
        ]);
    }
}
