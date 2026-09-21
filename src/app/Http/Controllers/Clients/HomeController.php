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

        $allMakesWithCount = Make::query()
            ->select([
                'makes.id',
                'makes.name',
                'makes.slug',
                'makes.logo_path',
            ])
            ->leftJoin('models', 'models.make_id', '=', 'makes.id')
            ->leftJoin('trims', 'trims.model_id', '=', 'models.id')
            ->leftJoin('car_units', function ($join): void {
                $join->on('car_units.trim_id', '=', 'trims.id')
                    ->where('car_units.status', '=', 'available')
                    ->whereNotNull('car_units.published_at')
                    ->whereNull('car_units.deleted_at');
            })
            ->groupBy('makes.id', 'makes.name', 'makes.slug', 'makes.logo_path')
            ->orderByDesc('total_units')
            ->selectRaw('COUNT(car_units.id) as total_units')
            ->get();

        $topMakes = $allMakesWithCount
            ->filter(fn (object $make): bool => ((int) ($make->total_units ?? 0)) > 0)
            ->take(3)
            ->values();

        $topMakeSlugs = $topMakes->pluck('slug')->all();

        $carsByMake = (clone $availableCars)
            ->whereIn('makes.slug', $topMakeSlugs)
            ->orderByDesc('car_units.published_at')
            ->orderByDesc('car_units.id')
            ->get()
            ->map(fn (object $car) => $this->decorateCar($car))
            ->groupBy('make_slug');

        $popularMakes = $topMakes->map(fn (object $make): object => (object) [
            'name' => $make->name,
            'slug' => $make->slug,
            'cars' => $carsByMake->get($make->slug, collect())->take(6)->values(),
        ]);

        $unitCounts = CarUnit::query()
            ->available()
            ->whereNotNull('published_at')
            ->selectRaw("
                COUNT(*) as available,
                COUNT(CASE WHEN `condition` = 'new' THEN 1 END) as `new`,
                COUNT(CASE WHEN `condition` IN ('used', 'cpo') THEN 1 END) as `used`
            ")
            ->first();

        $stats = [
            'available' => (int) ($unitCounts->available ?? 0),
            'new' => (int) ($unitCounts->new ?? 0),
            'used' => (int) ($unitCounts->used ?? 0),
            'makes' => $allMakesWithCount->count(),
        ];

        $bodyTypes = BodyType::query()
            ->orderBy('name')
            ->get(['id', 'slug', 'name']);

        return $this->viewWithSharedData('client.home', [
            'featuredCars' => $featuredCars,
            'newCars' => $newCars,
            'usedCars' => $usedCars,
            'makes' => $allMakesWithCount,
            'popularMakes' => $popularMakes,
            'bodyTypes' => $bodyTypes,
            'fuelTypes' => FuelType::query()->orderBy('name')->get(['slug', 'name']),
            'stats' => $stats,
        ]);
    }
}
