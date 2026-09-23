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

        $stats = [
            'cars_for_sale' => CarUnit::query()->available()->whereNotNull('published_at')->count(),
            'trims' => Trim::query()->count(),
            'reviews' => TrimReview::query()->approved()->count(),
            'leads' => Lead::query()->count(),
            'makes' => Make::query()->count(),
            'years_in_business' => 10,
            'satisfied_customers' => max(1500, (Lead::query()->count() * 12) + 1200),
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
            'source' => 'contact',
            'sourceTitle' => 'Liên Hệ Showroom & Đặt Lịch Trải Nghiệm Xe',
            'availableCars' => $this->getAvailableCarsForSelector(),
            'trims' => $this->getTrimsForSelector(),
        ]);
    }

    public function finance(): View
    {
        return $this->viewWithSharedData('client.finance', [
            'showroom' => $this->sharedShowroom(),
            'source' => 'finance',
            'sourceTitle' => 'Dự Toán Tài Chính & Gói Vay Trả Góp Ưu Đãi',
            'availableCars' => $this->getAvailableCarsForSelector(),
            'trims' => $this->getTrimsForSelector(),
        ]);
    }

    public function tradeIn(): View
    {
        $popularMakes = Make::query()->orderBy('name')->pluck('name');

        return $this->viewWithSharedData('client.trade-in', [
            'showroom' => $this->sharedShowroom(),
            'source' => 'trade_in',
            'sourceTitle' => 'Thu Cũ Đổi Mới - Lên Đời Xe Sang Nhanh Chóng',
            'availableCars' => $this->getAvailableCarsForSelector(),
            'trims' => $this->getTrimsForSelector(),
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
                $car->label = $car->make_name . ' ' . $car->model_name . ' ' . $car->trim_name . ' (' . $car->stock_code . ')';

                return $car;
            });
    }

    /**
     * @return Collection<int, object>
     */
    protected function getTrimsForSelector(): Collection
    {
        return Trim::query()
            ->toBase()
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
    }
}
