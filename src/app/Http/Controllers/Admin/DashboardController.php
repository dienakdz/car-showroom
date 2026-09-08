<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\CarModel;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\Make;
use App\Models\Sale;
use App\Models\Trim;
use App\Models\TrimReview;
use App\Models\User;
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

        $totalInventory = (int) $inventoryCounts->sum();
        $inventoryBreakdown = collect(['available', 'on_hold', 'draft', 'sold', 'archived'])
            ->mapWithKeys(function (string $status) use ($inventoryCounts, $totalInventory): array {
                $count = (int) $inventoryCounts->get($status, 0);
                $percent = $totalInventory > 0 ? round(($count / $totalInventory) * 100, 1) : 0;

                return [$status => [
                    'count' => $count,
                    'percent' => $percent,
                ]];
            })
            ->all();

        $summaryCards = [
            [
                'badge' => 'Kho xe',
                'label' => 'Tổng xe trong kho',
                'value' => $totalInventory,
                'note' => $inventoryCounts->get('available', 0) . ' xe sẵn sàng lên sàn',
                'icon' => asset('boxcar/images/icons/cart1.svg'),
                'fa_icon' => 'fa-car',
                'tone' => 'primary',
            ],
            [
                'badge' => 'CRM 7 ngày',
                'label' => 'Lead mới tiếp nhận',
                'value' => Lead::query()
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
                'note' => Lead::query()->where('status', 'new')->count() . ' lead đang chờ phản hồi',
                'icon' => asset('boxcar/images/icons/cart2.svg'),
                'fa_icon' => 'fa-users',
                'tone' => 'info',
            ],
            [
                'badge' => 'Lịch hẹn',
                'label' => 'Lịch hẹn cần xử lý',
                'value' => Appointment::query()
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count(),
                'note' => Appointment::query()
                    ->where('scheduled_at', '>=', now())
                    ->where('scheduled_at', '<=', now()->addDays(3))
                    ->count() . ' lịch trong 3 ngày tới',
                'icon' => asset('boxcar/images/icons/cart3.svg'),
                'fa_icon' => 'fa-calendar-check',
                'tone' => 'warning',
            ],
            [
                'badge' => 'Doanh số',
                'label' => 'Giao dịch tháng này',
                'value' => Sale::query()
                    ->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'note' => TrimReview::query()->where('status', 'pending')->count() . ' review đang chờ duyệt',
                'icon' => asset('boxcar/images/icons/cart4.svg'),
                'fa_icon' => 'fa-line-chart',
                'tone' => 'success',
            ],
        ];

        $recentLeads = $recentLeadRows
            ->map(function (Lead $lead) use ($recentLeadFallbackTrims): object {
                /** @var CarUnit|null $carUnit */
                $carUnit = $lead->carUnit;
                /** @var Trim|null $context */
                $context = $carUnit !== null ? $carUnit->trim : $recentLeadFallbackTrims->get($lead->trim_id);
                /** @var User|null $assignedTo */
                $assignedTo = $lead->assignedTo;

                return (object) [
                    'name' => $lead->name,
                    'source' => $lead->source,
                    'status' => $lead->status,
                    'status_label' => $this->formatLeadStatus($lead->status),
                    'assigned_to' => $assignedTo ? $assignedTo->name : 'Chưa phân công',
                    'context' => $this->formatCarContext($context) ?: 'Liên hệ chung',
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
                /** @var CarUnit|null $carUnit */
                $carUnit = $appointment->carUnit;
                /** @var Trim|null $context */
                $context = $carUnit !== null ? $carUnit->trim : $upcomingAppointmentFallbackTrims->get($appointment->trim_id);
                /** @var User|null $handledBy */
                $handledBy = $appointment->handledBy;

                return (object) [
                    'scheduled_at_label' => optional($appointment->scheduled_at)->format('d/m/Y H:i') ?? 'Đang cập nhật',
                    'scheduled_date' => optional($appointment->scheduled_at)->format('d/m') ?? '--',
                    'scheduled_time' => optional($appointment->scheduled_at)->format('H:i') ?? '--',
                    'status' => $appointment->status,
                    'status_label' => $this->formatAppointmentStatus($appointment->status),
                    'handled_by' => $handledBy ? $handledBy->name : 'Chưa phân công nhân viên',
                    'context' => $this->formatCarContext($context) ?: 'Không có thông tin xe',
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
                /** @var CarUnit|null $carUnit */
                $carUnit = $sale->carUnit;
                /** @var Trim|null $trim */
                $trim = $carUnit?->trim;
                /** @var User|null $buyer */
                $buyer = $sale->buyer;

                return (object) [
                    'buyer_name' => $buyer ? $buyer->name : 'Khách hàng ẩn danh',
                    'car_name' => $this->formatCarContext($trim) ?: 'Đang cập nhật phiên bản',
                    'sold_at_label' => optional($sale->sold_at)->format('d/m/Y') ?? 'Đang cập nhật',
                    'sold_price_label' => $this->formatCurrency($sale->sold_price, $currency),
                    'url' => route('admin.sales.index'),
                ];
            });

        return $this->adminView('admin.dashboard', [
            'adminPageTitle' => 'Bảng điều khiển tổng quan',
            'adminPageDescription' => 'Theo dõi toàn diện kho xe, khách hàng tiềm năng, lịch hẹn và giao dịch bán hàng của showroom.',
            'summaryCards' => $summaryCards,
            'totalInventory' => $totalInventory,
            'inventoryBreakdown' => $inventoryBreakdown,
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

    protected function formatCarContext(?Trim $trim): string
    {
        if ($trim === null) {
            return '';
        }

        /** @var CarModel|null $model */
        $model = $trim->model;
        /** @var Make|null $make */
        $make = $model?->make;

        return trim(collect([
            $make?->name,
            $model?->name,
            $trim->name,
        ])->filter()->implode(' '));
    }

    /**
     * @param  Collection<int, mixed>  $trimIds
     * @return Collection<int|string, Trim>
     */
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

    protected function formatLeadStatus(string $status): string
    {
        return match ($status) {
            'new' => 'Mới',
            'contacted' => 'Đã liên hệ',
            'qualified' => 'Tiềm năng',
            'lost' => 'Đã hủy',
            default => mb_strtoupper($status),
        };
    }

    protected function formatAppointmentStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ duyệt',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
            default => mb_strtoupper($status),
        };
    }

    protected function formatRelativeDate(?CarbonInterface $dateTime): string
    {
        if ($dateTime === null) {
            return 'Vừa cập nhật';
        }

        $now = Carbon::now();

        if ($dateTime->isSameDay($now)) {
            return 'Hôm nay, ' . $dateTime->format('H:i');
        }

        return $dateTime->format('d/m/Y H:i');
    }
}
