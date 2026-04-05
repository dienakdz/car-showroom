<?php

namespace App\Http\Controllers\Clients;

use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Trim;
use App\Models\TrimReview;
use Illuminate\View\View;

class PagesController extends ClientBaseController
{
    public function about(): View
    {
        $showroom = $this->sharedShowroom();

        $stats = [
            'cars_for_sale' => CarUnit::query()->available()->whereNotNull('published_at')->count(),
            'trims' => Trim::query()->count(),
            'reviews' => TrimReview::query()->approved()->count(),
            'leads' => Lead::query()->count(),
        ];

        return $this->viewWithSharedData('client.about', [
            'showroom' => $showroom,
            'stats' => $stats,
        ]);
    }

    public function contact(string $source = 'contact'): View
    {
        $source = $this->normalizeLeadSource($source);
        $sourceTitle = $this->leadSourceTitle($source);

        $availableCars = $this->publicVisibleCarQuery()
            ->orderByDesc('car_units.id')
            ->limit(40)
            ->get()
            ->map(function (object $car): object {
                $car->label = $car->make_name . ' ' . $car->model_name . ' ' . $car->trim_name . ' (' . $car->stock_code . ')';
                return $car;
            });

        $trims = Trim::query()
            ->select([
                'trims.id',
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
            ->map(function (object $trim): object {
                $trim->label = $trim->make_name . ' ' . $trim->model_name . ' ' . $trim->name;
                return $trim;
            });

        return $this->viewWithSharedData('client.contact', [
            'showroom' => $this->sharedShowroom(),
            'source' => $source,
            'sourceTitle' => $sourceTitle,
            'availableCars' => $availableCars,
            'trims' => $trims,
        ]);
    }
}
