<?php

namespace App\Http\Controllers\Clients;

use App\Models\BodyType;
use App\Models\CarUnit;
use App\Models\FuelType;
use App\Models\Make;
use Illuminate\View\View;

class HomeController extends ClientBaseController
{
    public function index(): View
    {
        $availableCars = $this->publicVisibleCarQuery();

        $featuredCars = $this->attachGalleryImages((clone $availableCars)
            ->orderByDesc('car_units.published_at')
            ->orderByDesc('car_units.id')
            ->limit(8)
            ->get()
            ->map(fn (object $car) => $this->decorateCar($car)));

        $newCars = $this->attachGalleryImages((clone $availableCars)
            ->where('car_units.condition', 'new')
            ->orderByDesc('car_units.id')
            ->limit(8)
            ->get()
            ->map(fn (object $car) => $this->decorateCar($car)));

        $usedCars = $this->attachGalleryImages((clone $availableCars)
            ->whereIn('car_units.condition', ['used', 'cpo'])
            ->orderByDesc('car_units.id')
            ->limit(8)
            ->get()
            ->map(fn (object $car) => $this->decorateCar($car)));

        $makes = Make::query()
            ->select([
                'makes.name',
                'makes.slug',
            ])
            ->leftJoin('models', 'models.make_id', '=', 'makes.id')
            ->leftJoin('trims', 'trims.model_id', '=', 'models.id')
            ->leftJoin('car_units', function ($join): void {
                $join->on('car_units.trim_id', '=', 'trims.id')
                    ->where('car_units.status', '=', 'available')
                    ->whereNotNull('car_units.published_at')
                    ->whereNull('car_units.deleted_at');
            })
            ->groupBy('makes.id', 'makes.name', 'makes.slug')
            ->orderByDesc('total_units')
            ->limit(6)
            ->selectRaw('COUNT(car_units.id) as total_units')
            ->get();

        $popularMakes = $makes->take(3)->values()->map(function (object $make) use ($availableCars): object {
            $make->cars = (clone $availableCars)
                ->where('makes.slug', $make->slug)
                ->orderByDesc('car_units.published_at')
                ->orderByDesc('car_units.id')
                ->limit(6)
                ->get()
                ->map(fn (object $car) => $this->decorateCar($car));

            return $make;
        });

        $stats = [
            'available' => CarUnit::query()->available()->whereNotNull('published_at')->count(),
            'new' => CarUnit::query()->available()->whereNotNull('published_at')->where('condition', 'new')->count(),
            'used' => CarUnit::query()->available()->whereNotNull('published_at')->whereIn('condition', ['used', 'cpo'])->count(),
            'makes' => Make::query()->count(),
        ];

        return $this->viewWithSharedData('client.home', [
            'featuredCars' => $featuredCars,
            'newCars' => $newCars,
            'usedCars' => $usedCars,
            'makes' => $makes,
            'popularMakes' => $popularMakes,
            'bodyTypes' => BodyType::query()->orderBy('name')->get(['slug', 'name']),
            'fuelTypes' => FuelType::query()->orderBy('name')->get(['slug', 'name']),
            'stats' => $stats,
        ]);
    }
}
