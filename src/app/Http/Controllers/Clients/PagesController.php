<?php

namespace App\Http\Controllers\Clients;

use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Make;
use App\Models\Trim;
use App\Models\TrimReview;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PagesController extends ClientBaseController
{
    public function about(): View
    {
        $showroom = $this->sharedShowroom();

        $leadCount = Lead::query()->count();
        $stats = [
            'cars_for_sale' => CarUnit::query()->available()->whereNotNull('published_at')->count(),
            'trims' => Trim::query()->count(),
            'reviews' => TrimReview::query()->approved()->count(),
            'leads' => $leadCount,
            'makes' => Make::query()->count(),
            'years_in_business' => 10,
            'satisfied_customers' => max(1500, ($leadCount * 12) + 1200),
            'satisfaction_rate' => 99,
        ];

        return $this->viewWithSharedData('client.about', [
            'showroom' => $showroom,
            'stats' => $stats,
        ]);
    }

    public function contact(): View
    {
        return $this->viewWithSharedData('client.contact', [
            'showroom' => $this->sharedShowroom(),
            'availableCars' => $this->getAvailableCarsForSelector(),
        ]);
    }

    public function finance(): View
    {
        return $this->viewWithSharedData('client.finance', [
            'showroom' => $this->sharedShowroom(),
            'availableCars' => $this->getAvailableCarsForSelector(),
        ]);
    }

    public function tradeIn(): View
    {
        $popularMakes = Make::query()->orderBy('name')->pluck('name');

        return $this->viewWithSharedData('client.trade-in', [
            'showroom' => $this->sharedShowroom(),
            'availableCars' => $this->getAvailableCarsForSelector(),
            'popularMakes' => $popularMakes,
        ]);
    }

    /**
     * @return Collection<int, object>
     */
    protected function getAvailableCarsForSelector(): Collection
    {
        return $this->publicVisibleCarQuery()
            ->toBase()
            ->orderByDesc('car_units.id')
            ->limit(50)
            ->get()
            ->map(function (object $car): object {
                $car = $this->decorateCar($car);
                $car->label = $car->make_name . ' ' . $car->model_name . ' ' . $car->trim_name . ' (' . $car->stock_code . ')';

                return $car;
            });
    }
}
