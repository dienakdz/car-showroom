<?php

namespace App\Http\Controllers\Clients;

use App\Models\BodyType;
use App\Models\CarModel;
use App\Models\CarUnit;
use App\Models\CarUnitMedia;
use App\Models\Color;
use App\Models\Drivetrain;
use App\Models\FuelType;
use App\Models\Make;
use App\Models\Transmission;
use App\Models\Trim;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends ClientBaseController
{
    public function index(Request $request, ?string $condition = null): View
    {
        $allowedConditions = ['new', 'used', 'cpo'];
        if ($condition !== null && ! in_array($condition, $allowedConditions, true)) {
            abort(404);
        }

        $query = $this->publicVisibleCarQuery();
        $effectiveCondition = $condition;

        if ($effectiveCondition === null && in_array((string) $request->query('condition'), $allowedConditions, true)) {
            $effectiveCondition = (string) $request->query('condition');
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
        if ($request->filled('trim')) {
            $query->where('trims.slug', $request->query('trim'));
        }
        if ($request->filled('body_type')) {
            $query->where('body_types.slug', $request->query('body_type'));
        }
        if ($request->filled('fuel_type')) {
            $query->where('fuel_types.slug', $request->query('fuel_type'));
        }
        if ($request->filled('drivetrain')) {
            $query->where('drivetrains.slug', $request->query('drivetrain'));
        }
        if ($request->filled('year')) {
            $query->where('car_units.year', (int) $request->query('year'));
        }
        if ($request->filled('min_year')) {
            $query->where('car_units.year', '>=', (int) $request->query('min_year'));
        }
        if ($request->filled('max_year')) {
            $query->where('car_units.year', '<=', (int) $request->query('max_year'));
        }
        if ($request->filled('transmission')) {
            $query->where('transmissions.slug', $request->query('transmission'));
        }
        if ($request->filled('exterior_color')) {
            $query->where('exterior_colors.slug', $request->query('exterior_color'));
        }
        if ($request->filled('interior_color')) {
            $query->where('interior_colors.slug', $request->query('interior_color'));
        }
        if ($request->filled('min_mileage')) {
            $query->where('car_units.mileage', '>=', (int) $request->query('min_mileage'));
        }
        if ($request->filled('max_mileage')) {
            $query->where('car_units.mileage', '<=', (int) $request->query('max_mileage'));
        }
        if ($request->filled('price_range')) {
            match ((string) $request->query('price_range')) {
                'under_800' => $query->where('car_units.price', '<=', 800000000),
                '800_1500' => $query->whereBetween('car_units.price', [800000000, 1500000000]),
                '1500_2500' => $query->whereBetween('car_units.price', [1500000000, 2500000000]),
                'over_2500' => $query->where('car_units.price', '>=', 2500000000),
                default => null,
            };
        }

        if ($request->filled('min_price')) {
            $minPrice = (int) $request->query('min_price');
            if ($minPrice > 0 && $minPrice < 100000) {
                $minPrice *= 1000000;
            }
            $query->where('car_units.price', '>=', $minPrice);
        }
        if ($request->filled('max_price')) {
            $maxPrice = (int) $request->query('max_price');
            if ($maxPrice > 0 && $maxPrice < 100000) {
                $maxPrice *= 1000000;
            }
            $query->where('car_units.price', '<=', $maxPrice);
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
            case 'mileage_asc':
                $query->orderByRaw('car_units.mileage IS NULL, car_units.mileage ASC');
                break;
            case 'mileage_desc':
                $query->orderByDesc('car_units.mileage');
                break;
            default:
                $query->orderByDesc('car_units.published_at')->orderByDesc('car_units.id');
                break;
        }

        $cars = $query->paginate(12)->withQueryString();
        $cars->getCollection()->transform(fn (object $car) => $this->decorateCar($car));

        $pageTitle = match ($effectiveCondition) {
            'new' => 'Xe Mới 100%',
            'used' => 'Xe Siêu Lướt',
            'cpo' => 'Xe CPO Kiểm Định',
            default => 'Kho Xe Chính Hãng',
        };

        $pageSubtitle = match ($effectiveCondition) {
            'new' => 'Danh mục các mẫu xe mới 100% nguyên bản, đầy đủ sổ bảo hành chính hãng cùng nhiều ưu đãi đặc quyền.',
            'used' => 'Bộ sưu tập xe lướt tuyển chọn, lịch sử bảo dưỡng rõ ràng, cam kết không đâm đụng, không ngập nước.',
            'cpo' => 'Xe chứng nhận chính hãng (Certified Pre-Owned) đạt tiêu chuẩn 160 điểm kiểm định kỹ thuật khắt khe.',
            default => 'Hơn 100+ mẫu xe tuyển chọn khắt khe qua 160 bước kiểm định nghiêm ngặt. Cam kết pháp lý minh bạch và hỗ trợ trả góp tối đa 80%.',
        };

        return $this->viewWithSharedData('client.inventory', [
            'cars' => $cars,
            'pageTitle' => $pageTitle,
            'pageSubtitle' => $pageSubtitle,
            'currentCondition' => $effectiveCondition,
            'filters' => $this->loadInventoryFilters(),
        ]);
    }

    protected function loadInventoryFilters(): array
    {
        return [
            'makes' => Make::query()->orderBy('name')->get(['slug', 'name']),
            'models' => CarModel::query()
                ->toBase()
                ->select([
                    'models.slug',
                    'models.name',
                    'makes.name as make_name',
                ])
                ->join('makes', 'makes.id', '=', 'models.make_id')
                ->orderBy('makes.name')
                ->orderBy('models.name')
                ->get()
                ->map(fn (object $model): object => (object) [
                    'slug' => $model->slug,
                    'name' => $model->make_name . ' ' . $model->name,
                ]),
            'trims' => Trim::query()
                ->toBase()
                ->select([
                    'trims.slug',
                    'trims.name',
                    'models.name as model_name',
                    'makes.name as make_name',
                ])
                ->join('models', 'models.id', '=', 'trims.model_id')
                ->join('makes', 'makes.id', '=', 'models.make_id')
                ->orderBy('makes.name')
                ->orderBy('models.name')
                ->orderBy('trims.name')
                ->get()
                ->map(fn (object $trim): object => (object) [
                    'slug' => $trim->slug,
                    'name' => $trim->make_name . ' ' . $trim->model_name . ' ' . $trim->name,
                ]),
            'bodyTypes' => BodyType::query()->orderBy('name')->get(['slug', 'name']),
            'fuelTypes' => FuelType::query()->orderBy('name')->get(['slug', 'name']),
            'drivetrains' => Drivetrain::query()->orderBy('name')->get(['slug', 'name']),
            'transmissions' => Transmission::query()->orderBy('name')->get(['slug', 'name']),
            'colors' => Color::query()->orderBy('name')->get(['slug', 'name']),
            'years' => CarUnit::query()
                ->whereIn('status', $this->publicAllowedStatuses())
                ->whereNotNull('published_at')
                ->whereNotNull('year')
                ->select('year')
                ->distinct()
                ->orderByDesc('year')
                ->get()
                ->map(fn (object $item): object => (object) [
                    'value' => (string) $item->year,
                    'label' => (string) $item->year,
                ]),
        ];
    }

    public function show(string $stockCode): View
    {
        $car = $this->publicVisibleCarQuery()
            ->where('car_units.stock_code', $stockCode)
            ->first();

        if ($car === null) {
            abort(404);
        }

        $car = $this->decorateCar($car);

        $media = CarUnitMedia::query()
            ->where('car_unit_id', $car->id)
            ->where('type', 'image')
            ->orderByDesc('is_cover')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (CarUnitMedia $item): object => (object) [
                'url' => $this->resolveMediaPath($item->path_or_url),
            ]);

        if ($media->isEmpty()) {
            $media = collect([(object) ['url' => $this->resolveMediaPath(null)]]);
        }

        $features = $this->loadTrimFeatures($car->trim_id);
        $attributes = $this->loadTrimAttributes($car->trim_id);
        $reviewSummary = $this->loadTrimReviewSummary($car->trim_id);
        $reviews = $this->loadApprovedTrimReviews($car->trim_id, 6);

        $relatedCars = $this->publicVisibleCarQuery()
            ->where('car_units.trim_id', $car->trim_id)
            ->where('car_units.stock_code', '!=', $car->stock_code)
            ->limit(4)
            ->get();

        if ($relatedCars->count() < 4) {
            $excludeStockCodes = $relatedCars->pluck('stock_code')->push($car->stock_code)->all();
            $moreCars = $this->publicVisibleCarQuery()
                ->where('makes.slug', $car->make_slug)
                ->whereNotIn('car_units.stock_code', $excludeStockCodes)
                ->limit(4 - $relatedCars->count())
                ->get();
            $relatedCars = $relatedCars->concat($moreCars);
        }

        if ($relatedCars->count() < 4) {
            $excludeStockCodes = $relatedCars->pluck('stock_code')->push($car->stock_code)->all();
            $fallbackCars = $this->publicVisibleCarQuery()
                ->whereNotIn('car_units.stock_code', $excludeStockCodes)
                ->limit(4 - $relatedCars->count())
                ->get();
            $relatedCars = $relatedCars->concat($fallbackCars);
        }

        $relatedCars = $relatedCars->map(fn (object $related) => $this->decorateCar($related));

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
