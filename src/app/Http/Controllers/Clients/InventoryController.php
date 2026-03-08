<?php

namespace App\Http\Controllers\Clients;

use App\Models\BodyType;
use App\Models\CarModel;
use App\Models\CarUnit;
use App\Models\CarUnitMedia;
use App\Models\FuelType;
use App\Models\Make;
use App\Models\Transmission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends ClientBaseController
{
    public function index(Request $request, ?string $condition = null): View
    {
        $allowedConditions = ['new', 'used', 'cpo'];
        if ($condition !== null && !in_array($condition, $allowedConditions, true)) {
            abort(404);
        }

        $query = $this->baseCarQuery();
        $effectiveCondition = $condition;

        if ($effectiveCondition === null && in_array((string) $request->query('condition'), $allowedConditions, true)) {
            $effectiveCondition = (string) $request->query('condition');
        }

        $status = $request->query('status', 'available');
        if ($status !== 'all') {
            $query->where('car_units.status', $status);
        }

        if ($effectiveCondition !== null) {
            $query->where('car_units.condition', $effectiveCondition);
        }

        $keyword = trim((string) $request->query('q', ''));
        if ($keyword !== '') {
            $query->where(function ($queryBuilder) use ($keyword): void {
                $queryBuilder->where('makes.name', 'like', '%' . $keyword . '%')
                    ->orWhere('models.name', 'like', '%' . $keyword . '%')
                    ->orWhere('trims.name', 'like', '%' . $keyword . '%')
                    ->orWhere('car_units.stock_code', 'like', '%' . $keyword . '%')
                    ->orWhere('car_units.vin', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('make')) {
            $query->where('makes.slug', $request->query('make'));
        }
        if ($request->filled('model')) {
            $query->where('models.slug', $request->query('model'));
        }
        if ($request->filled('body_type')) {
            $query->where('body_types.slug', $request->query('body_type'));
        }
        if ($request->filled('fuel_type')) {
            $query->where('fuel_types.slug', $request->query('fuel_type'));
        }
        if ($request->filled('year')) {
            $query->where('car_units.year', (int) $request->query('year'));
        }
        if ($request->filled('transmission')) {
            $query->where('transmissions.slug', $request->query('transmission'));
        }
        if ($request->filled('min_price')) {
            $query->where('car_units.price', '>=', (int) $request->query('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('car_units.price', '<=', (int) $request->query('max_price'));
        }

        $sort = $request->query('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('car_units.price IS NULL, car_units.price ASC');
                break;
            case 'price_desc':
                $query->orderByDesc('car_units.price');
                break;
            case 'year_asc':
                $query->orderBy('car_units.year');
                break;
            case 'year_desc':
                $query->orderByDesc('car_units.year');
                break;
            default:
                $query->orderByDesc('car_units.published_at')->orderByDesc('car_units.id');
                break;
        }

        $cars = $query->paginate(12)->withQueryString();
        $cars->getCollection()->transform(fn (object $car) => $this->decorateCar($car));

        $pageTitle = match ($effectiveCondition) {
            'new' => 'Xe moi',
            'used' => 'Xe cu',
            'cpo' => 'Xe CPO',
            default => 'Kho xe',
        };

        return $this->viewWithSharedData('client.inventory', [
            'cars' => $cars,
            'pageTitle' => $pageTitle,
            'currentCondition' => $effectiveCondition,
            'filters' => [
                'makes' => Make::query()->orderBy('name')->get(['slug', 'name']),
                'models' => CarModel::query()
                    ->select([
                        'models.slug',
                        'models.name',
                        'makes.name as make_name',
                    ])
                    ->join('makes', 'makes.id', '=', 'models.make_id')
                    ->orderBy('makes.name')
                    ->orderBy('models.name')
                    ->get()
                    ->map(fn (CarModel $model): object => (object) [
                        'slug' => $model->slug,
                        'name' => $model->make_name . ' ' . $model->name,
                    ]),
                'bodyTypes' => BodyType::query()->orderBy('name')->get(['slug', 'name']),
                'fuelTypes' => FuelType::query()->orderBy('name')->get(['slug', 'name']),
                'transmissions' => Transmission::query()->orderBy('name')->get(['slug', 'name']),
                'years' => CarUnit::query()
                    ->whereNotNull('year')
                    ->select('year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->get()
                    ->map(fn (object $item): object => (object) [
                        'value' => (string) $item->year,
                        'label' => (string) $item->year,
                    ]),
            ],
        ]);
    }

    public function show(string $stockCode): View
    {
        $car = $this->baseCarQuery()
            ->where('car_units.stock_code', $stockCode)
            ->first();

        if ($car === null) {
            abort(404);
        }

        $car = $this->decorateCar($car);

        $media = CarUnitMedia::query()
            ->where('car_unit_id', $car->id)
            ->orderByDesc('is_cover')
            ->orderBy('sort_order')
            ->get()
            ->map(function (CarUnitMedia $item): object {
                $item->url = $this->resolveMediaPath($item->path_or_url);
                return $item;
            });

        if ($media->isEmpty()) {
            $media = collect([(object) ['url' => $this->resolveMediaPath(null)]]);
        }

        $features = $this->loadTrimFeatures($car->trim_id);
        $attributes = $this->loadTrimAttributes($car->trim_id);
        $reviewSummary = $this->loadTrimReviewSummary($car->trim_id);
        $reviews = $this->loadApprovedTrimReviews($car->trim_id, 6);

        $relatedCars = $this->baseCarQuery()
            ->where('car_units.trim_id', $car->trim_id)
            ->where('car_units.stock_code', '!=', $car->stock_code)
            ->where('car_units.status', 'available')
            ->limit(4)
            ->get()
            ->map(fn (object $related) => $this->decorateCar($related));

        return $this->viewWithSharedData('client.car-detail', [
            'car' => $car,
            'media' => $media,
            'features' => $features,
            'attributes' => $attributes,
            'reviewSummary' => $reviewSummary,
            'reviews' => $reviews,
            'relatedCars' => $relatedCars,
        ]);
    }
}
