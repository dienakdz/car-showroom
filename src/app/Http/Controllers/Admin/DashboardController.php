<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\Trim;
use App\Models\TrimReview;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends AdminBaseController
{
    public function __invoke(): View
    {
        $settings = $this->loadAdminSettings();
        $currency = (string) data_get($settings, 'site.default_currency.value', 'VND');

        $inventoryCounts = CarUnit::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $leadTrendPeriodStart = now()->startOfMonth()->subMonths(5);
        $leadTrendPeriodExpression = match (DB::getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };
        $leadTrendCounts = Lead::query()
            ->selectRaw($leadTrendPeriodExpression . ' as period_key, COUNT(*) as total')
            ->whereBetween('created_at', [$leadTrendPeriodStart, now()->endOfMonth()])
            ->groupBy('period_key')
            ->pluck('total', 'period_key');

        $leadTrend = collect(range(5, 0))
            ->map(function (int $offset) use ($leadTrendCounts): array {
                $periodStart = now()->startOfMonth()->subMonths($offset);

                return [
                    'label' => $periodStart->translatedFormat('M Y'),
                    'total' => (int) $leadTrendCounts->get($periodStart->format('Y-m'), 0),
                ];
            });

        $recentLeadRows = Lead::query()
            ->with([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
            ])
            ->latest()
            ->limit(6)
            ->get();

        $recentLeadFallbackTrims = $this->loadContextTrims(
            $recentLeadRows->whereNull('car_unit_id')->pluck('trim_id')
        );

        $summaryCards = [
            [
                'label' => 'Tong xe trong kho',
                'value' => (int) $inventoryCounts->sum(),
                'note' => $inventoryCounts->get('available', 0) . ' xe dang san sang len public',
                'icon' => asset('boxcar/images/icons/cart1.svg'),
                'tone' => 'primary',
            ],
            [
                'label' => 'Lead moi 7 ngay',
                'value' => Lead::query()
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
                'note' => Lead::query()->where('status', 'new')->count() . ' lead dang cho phan hoi',
                'icon' => asset('boxcar/images/icons/cart2.svg'),
                'tone' => 'info',
            ],
            [
                'label' => 'Lich hen can xu ly',
                'value' => Appointment::query()
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count(),
                'note' => Appointment::query()
                    ->where('scheduled_at', '>=', now())
                    ->where('scheduled_at', '<=', now()->addDays(3))
                    ->count() . ' lich trong 3 ngay toi',
                'icon' => asset('boxcar/images/icons/cart3.svg'),
                'tone' => 'warning',
            ],
            [
                'label' => 'Sale thang nay',
                'value' => Sale::query()
                    ->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'note' => TrimReview::query()->where('status', 'pending')->count() . ' review dang cho duyet',
                'icon' => asset('boxcar/images/icons/cart4.svg'),
                'tone' => 'success',
            ],
        ];

        $recentLeads = $recentLeadRows
            ->map(function (Lead $lead) use ($recentLeadFallbackTrims): object {
                $context = $lead->carUnit?->trim;

                if ($context === null) {
                    $context = $recentLeadFallbackTrims->get($lead->trim_id);
                }

                return (object) [
                    'name' => $lead->name,
                    'source' => $lead->source,
                    'status' => $lead->status,
                    'assigned_to' => $lead->assignedTo?->name ?? 'Chua phan cong',
                    'context' => trim(collect([
                        $context?->model?->make?->name,
                        $context?->model?->name,
                        $context?->name,
                    ])->filter()->implode(' ')) ?: 'Lien he chung',
                    'created_at_label' => $this->formatRelativeDate($lead->created_at),
                    'url' => route('admin.leads.show', $lead),
                ];
            });

        $upcomingAppointmentRows = Appointment::query()
            ->with([
                'handledBy:id,name',
                'carUnit.trim.model.make',
            ])
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->orderBy('scheduled_at')
            ->limit(6)
            ->get();

        $upcomingAppointmentFallbackTrims = $this->loadContextTrims(
            $upcomingAppointmentRows->whereNull('car_unit_id')->pluck('trim_id')
        );

        $upcomingAppointments = $upcomingAppointmentRows
            ->map(function (Appointment $appointment) use ($upcomingAppointmentFallbackTrims): object {
                $context = $appointment->carUnit?->trim;

                if ($context === null) {
                    $context = $upcomingAppointmentFallbackTrims->get($appointment->trim_id);
                }

                return (object) [
                    'scheduled_at_label' => optional($appointment->scheduled_at)->format('d/m/Y H:i') ?? 'Dang cap nhat',
                    'status' => $appointment->status,
                    'handled_by' => $appointment->handledBy?->name ?? 'Chua gan staff',
                    'context' => trim(collect([
                        $context?->model?->make?->name,
                        $context?->model?->name,
                        $context?->name,
                    ])->filter()->implode(' ')) ?: 'Khong co context xe',
                    'url' => route('admin.appointments.edit', $appointment),
                ];
            });

        $recentSales = Sale::query()
            ->with([
                'buyer:id,name',
                'carUnit.trim.model.make',
            ])
            ->orderByDesc('sold_at')
            ->limit(6)
            ->get()
            ->map(function (Sale $sale) use ($currency): object {
                $trim = $sale->carUnit?->trim;

                return (object) [
                    'buyer_name' => $sale->buyer?->name ?? 'Khach hang an danh',
                    'car_name' => trim(collect([
                        $trim?->model?->make?->name,
                        $trim?->model?->name,
                        $trim?->name,
                    ])->filter()->implode(' ')) ?: 'Dang cap nhat phien ban',
                    'sold_at_label' => optional($sale->sold_at)->format('d/m/Y') ?? 'Dang cap nhat',
                    'sold_price_label' => $this->formatCurrency($sale->sold_price, $currency),
                    'url' => route('admin.sales.index'),
                ];
            });

        return $this->adminView('admin.dashboard', [
            'adminPageTitle' => 'Dashboard',
            'adminPageDescription' => 'Theo doi inventory, CRM va cac giao dich quan trong cua showroom.',
            'summaryCards' => $summaryCards,
            'inventoryCounts' => [
                'draft' => (int) $inventoryCounts->get('draft', 0),
                'available' => (int) $inventoryCounts->get('available', 0),
                'on_hold' => (int) $inventoryCounts->get('on_hold', 0),
                'sold' => (int) $inventoryCounts->get('sold', 0),
                'archived' => (int) $inventoryCounts->get('archived', 0),
            ],
            'availableInventoryValueLabel' => $this->formatCurrency(
                (int) CarUnit::query()->where('status', 'available')->sum('price'),
                $currency
            ),
            'leadTrendLabels' => $leadTrend->pluck('label')->all(),
            'leadTrendValues' => $leadTrend->pluck('total')->all(),
            'recentLeads' => $recentLeads,
            'upcomingAppointments' => $upcomingAppointments,
            'recentSales' => $recentSales,
        ]);
    }

    protected function loadContextTrims(Collection $trimIds): Collection
    {
        $trimIds = $trimIds
            ->filter()
            ->unique()
            ->values();

        if ($trimIds->isEmpty()) {
            return collect();
        }

        return Trim::query()
            ->with('model.make')
            ->whereIn('id', $trimIds)
            ->get()
            ->keyBy('id');
    }

    protected function formatRelativeDate(?CarbonInterface $dateTime): string
    {
        if ($dateTime === null) {
            return 'Vua cap nhat';
        }

        $now = Carbon::now();

        if ($dateTime->isSameDay($now)) {
            return 'Hom nay, ' . $dateTime->format('H:i');
        }

        return $dateTime->format('d/m/Y H:i');
    }
}
