<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Models\CarUnitMedia;
use App\Models\Feature;
use App\Models\Showroom;
use App\Models\TrimAttributeValue;
use App\Models\TrimReview;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

abstract class ClientBaseController extends Controller
{
    protected const LEAD_SOURCES = [
        'unit_detail',
        'trim_page',
        'finance',
        'trade_in',
        'contact',
    ];

    protected function baseCarQuery(): EloquentBuilder
    {
        $coverMediaSubQuery = CarUnitMedia::query()
            ->select('path_or_url')
            ->whereColumn('car_unit_media.car_unit_id', 'car_units.id')
            ->orderByDesc('is_cover')
            ->orderBy('sort_order')
            ->limit(1);

        return CarUnit::query()
            ->join('trims', 'trims.id', '=', 'car_units.trim_id')
            ->join('models', 'models.id', '=', 'trims.model_id')
            ->join('makes', 'makes.id', '=', 'models.make_id')
            ->leftJoin('body_types', 'body_types.id', '=', 'car_units.body_type_id')
            ->leftJoin('fuel_types', 'fuel_types.id', '=', 'car_units.fuel_type_id')
            ->leftJoin('transmissions', 'transmissions.id', '=', 'car_units.transmission_id')
            ->leftJoin('drivetrains', 'drivetrains.id', '=', 'car_units.drivetrain_id')
            ->leftJoin('colors as exterior_colors', 'exterior_colors.id', '=', 'car_units.exterior_color_id')
            ->leftJoin('colors as interior_colors', 'interior_colors.id', '=', 'car_units.interior_color_id')
            ->selectSub($coverMediaSubQuery, 'cover_media')
            ->select([
                'car_units.id',
                'car_units.stock_code',
                'car_units.vin',
                'car_units.condition',
                'car_units.year',
                'car_units.mileage',
                'car_units.price',
                'car_units.currency',
                'car_units.status',
                'car_units.published_at',
                'car_units.hold_until',
                'car_units.sold_at',
                'car_units.trim_id',
                'trims.name as trim_name',
                'trims.slug as trim_slug',
                'trims.description as trim_description',
                'models.name as model_name',
                'models.slug as model_slug',
                'makes.name as make_name',
                'makes.slug as make_slug',
                'body_types.name as body_type_name',
                'body_types.slug as body_type_slug',
                'fuel_types.name as fuel_type_name',
                'fuel_types.slug as fuel_type_slug',
                'transmissions.name as transmission_name',
                'transmissions.slug as transmission_slug',
                'drivetrains.name as drivetrain_name',
                'drivetrains.slug as drivetrain_slug',
                'exterior_colors.name as exterior_color_name',
                'interior_colors.name as interior_color_name',
            ]);
    }

    protected function decorateCar(object $car): object
    {
        $car->image_url = $this->resolveMediaPath($car->cover_media ?? null);
        $car->formatted_price = $car->price === null
            ? 'Lien he'
            : number_format((float) $car->price, 0, ',', '.') . ' ' . $car->currency;

        $car->condition_label = match ($car->condition) {
            'new' => 'Moi',
            'used' => 'Da qua su dung',
            'cpo' => 'CPO',
            default => strtoupper((string) $car->condition),
        };

        return $car;
    }

    protected function attachGalleryImages(Collection $cars, int $limit = 3): Collection
    {
        if ($cars->isEmpty()) {
            return $cars;
        }

        $mediaByCar = CarUnitMedia::query()
            ->whereIn('car_unit_id', $cars->pluck('id'))
            ->where('type', 'image')
            ->orderByDesc('is_cover')
            ->orderBy('sort_order')
            ->get(['car_unit_id', 'path_or_url'])
            ->groupBy('car_unit_id');

        return $cars->map(function (object $car) use ($limit, $mediaByCar): object {
            $images = collect($mediaByCar->get($car->id, []))
                ->take($limit)
                ->map(fn (object $media): string => $this->resolveMediaPath($media->path_or_url))
                ->values();

            $fallbackImage = $car->image_url ?? $this->resolveMediaPath(null);

            if ($images->isEmpty()) {
                $images = collect([$fallbackImage]);
            }

            while ($images->count() < $limit) {
                $images->push($fallbackImage);
            }

            $car->gallery_images = $images->all();

            return $car;
        });
    }

    protected function loadTrimAttributes(int $trimId): Collection
    {
        $attributes = TrimAttributeValue::query()
            ->with('attribute')
            ->where('trim_id', $trimId)
            ->get()
            ->filter(fn (TrimAttributeValue $attributeValue): bool => $attributeValue->attribute !== null)
            ->sortBy(fn (TrimAttributeValue $attributeValue) => $attributeValue->attribute->sort_order ?? 0)
            ->values();

        return $attributes->map(function (TrimAttributeValue $attributeValue): object {
            $attribute = (object) [
                'code' => $attributeValue->attribute->code,
                'label' => $attributeValue->attribute->label,
                'type' => $attributeValue->attribute->type,
                'unit' => $attributeValue->attribute->unit,
                'value_string' => $attributeValue->value_string,
                'value_number' => $attributeValue->value_number,
                'value_boolean' => $attributeValue->value_boolean,
                'display_value' => null,
            ];

            if ($attribute->type === 'string') {
                $attribute->display_value = $attribute->value_string;
                return $attribute;
            }

            if ($attribute->type === 'number') {
                if ($attribute->value_number === null) {
                    return $attribute;
                }

                $numberValue = rtrim(rtrim(number_format((float) $attribute->value_number, 4, '.', ''), '0'), '.');
                $attribute->display_value = $numberValue . ($attribute->unit ? ' ' . $attribute->unit : '');
                return $attribute;
            }

            if ($attribute->type === 'boolean') {
                $attribute->display_value = $attribute->value_boolean ? 'Co' : 'Khong';
            }

            return $attribute;
        });
    }

    protected function loadTrimFeatures(int $trimId): Collection
    {
        return Feature::query()
            ->select([
                'features.name',
                'feature_groups.name as group_name',
            ])
            ->join('trim_feature', 'trim_feature.feature_id', '=', 'features.id')
            ->join('feature_groups', 'feature_groups.id', '=', 'features.feature_group_id')
            ->where('trim_feature.trim_id', $trimId)
            ->orderBy('feature_groups.sort_order')
            ->orderBy('features.name')
            ->get()
            ->groupBy('group_name');
    }

    protected function loadTrimReviewSummary(int $trimId): ?object
    {
        return TrimReview::query()
            ->approved()
            ->where('trim_id', $trimId)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();
    }

    protected function loadApprovedTrimReviews(int $trimId, ?int $limit = null): Collection
    {
        $query = TrimReview::query()
            ->with('user:id,name')
            ->approved()
            ->where('trim_id', $trimId)
            ->orderByDesc('created_at');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->map(function (TrimReview $review): object {
            $review->user_name = $review->user?->name ?? 'Khach hang';
            return $review;
        });
    }

    protected function resolveMediaPath(?string $path): string
    {
        if ($path === null || $path === '') {
            return asset('boxcar/images/resource/shop3-1.jpg');
        }

        if (preg_match('/^https?:\\/\\//i', $path) === 1) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');

        if (str_starts_with($cleanPath, 'boxcar/')) {
            return asset($cleanPath);
        }

        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        if (file_exists(public_path('boxcar/' . $cleanPath))) {
            return asset('boxcar/' . $cleanPath);
        }

        return asset('boxcar/images/resource/shop3-1.jpg');
    }

    protected function normalizeLeadSource(string $source): string
    {
        return in_array($source, self::LEAD_SOURCES, true) ? $source : 'contact';
    }

    protected function leadSourceTitle(string $source): string
    {
        return match ($source) {
            'finance' => 'Dang Ky Tu Van Tai Chinh',
            'trade_in' => 'Dang Ky Thu Cu Doi Moi',
            default => 'Lien He Showroom',
        };
    }

    protected function viewWithSharedData(string $viewName, array $data = []): View
    {
        $navShowroom = null;
        if (Schema::hasTable('showrooms')) {
            $navShowroom = Showroom::query()->first();
        }

        return view($viewName, array_merge(['navShowroom' => $navShowroom], $data));
    }
}
