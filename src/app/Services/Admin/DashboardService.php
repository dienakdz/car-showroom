<?php

namespace App\Services\Admin;

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

class DashboardService
{
    /**
     * @return array<int, array{
     *     badge: string,
     *     label: string,
     *     value: int,
     *     note: string,
     *     url: string,
     *     tone: string,
     *     unit: string,
     *     highlight: string
     * }>
     */
    public function getSummaryCards(string $currency = 'VND'): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. Inventory counts
        $totalInventory = (int) CarUnit::query()->count();
        $availableInventory = (int) CarUnit::query()->where('status', 'available')->count();

        // 2. Leads (7 days)
        $newLeads7Days = (int) Lead::query()->where('created_at', '>=', $now->copy()->subDays(7))->count();
        $pendingLeads = (int) Lead::query()->where('status', 'new')->count();

        // 3. Appointments
        $activeAppointments = (int) Appointment::query()->whereIn('status', ['pending', 'confirmed'])->count();
        $upcoming3DaysAppointments = (int) Appointment::query()
            ->where('scheduled_at', '>=', $now)
            ->where('scheduled_at', '<=', $now->copy()->addDays(3))
            ->count();

        // 4. Sales & Revenue this month
        $monthlySalesCount = (int) Sale::query()
            ->whereBetween('sold_at', [$startOfMonth, $endOfMonth])
            ->count();
        $monthlyRevenue = (int) Sale::query()
            ->whereBetween('sold_at', [$startOfMonth, $endOfMonth])
            ->sum('sold_price');

        $revenueFormatted = $this->formatShortRevenue($monthlyRevenue, $currency);

        return [
            [
                'badge' => 'Kho xe',
                'label' => 'Tổng xe trong kho',
                'value' => $totalInventory,
                'note' => $availableInventory . ' xe sẵn sàng lên sàn',
                'url' => route('admin.inventory.index'),
                'tone' => 'primary',
                'unit' => 'Xe',
                'highlight' => $availableInventory . ' xe sẵn sàng',
            ],
            [
                'badge' => 'CRM 7 ngày',
                'label' => 'Lead mới tiếp nhận',
                'value' => $newLeads7Days,
                'note' => $pendingLeads . ' lead đang chờ phản hồi',
                'url' => route('admin.leads.index'),
                'tone' => 'info',
                'unit' => 'Tuần này',
                'highlight' => $pendingLeads . ' chờ xử lý',
            ],
            [
                'badge' => 'Lịch hẹn',
                'label' => 'Lịch hẹn cần xử lý',
                'value' => $activeAppointments,
                'note' => $upcoming3DaysAppointments . ' lịch trong 3 ngày tới',
                'url' => route('admin.appointments.index'),
                'tone' => 'warning',
                'unit' => 'Sắp tới',
                'highlight' => $upcoming3DaysAppointments . ' lịch gần kề',
            ],
            [
                'badge' => 'Doanh số',
                'label' => 'Giao dịch tháng này',
                'value' => $monthlySalesCount,
                'note' => 'Doanh thu: ' . $revenueFormatted,
                'url' => route('admin.sales.index'),
                'tone' => 'success',
                'unit' => 'Hợp đồng',
                'highlight' => $revenueFormatted,
            ],
        ];
    }

    /**
     * @return array{
     *     today_appointments: int,
     *     unassigned_leads: int,
     *     pending_reviews: int,
     *     has_urgent: bool
     * }
     */
    public function getUrgentAlerts(): array
    {
        $todayAppointments = (int) Appointment::query()
            ->whereDate('scheduled_at', Carbon::today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $unassignedLeads = (int) Lead::query()
            ->whereNull('assigned_to')
            ->where('status', 'new')
            ->count();

        $pendingReviews = (int) TrimReview::query()
            ->where('status', 'pending')
            ->count();

        return [
            'today_appointments' => $todayAppointments,
            'unassigned_leads' => $unassignedLeads,
            'pending_reviews' => $pendingReviews,
            'has_urgent' => ($todayAppointments > 0 || $unassignedLeads > 0 || $pendingReviews > 0),
        ];
    }

    /**
     * @return array{
     *     total: int,
     *     breakdown: array<string, array{count: int, percent: float}>,
     *     counts: array<string, int>,
     *     available_value: int,
     *     available_value_label: string
     * }
     */
    public function getInventoryStats(string $currency = 'VND'): array
    {
        $inventoryCounts = CarUnit::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $totalInventory = (int) $inventoryCounts->sum();

        $inventoryBreakdown = collect(['available', 'on_hold', 'draft', 'sold', 'archived'])
            ->mapWithKeys(function (string $status) use ($inventoryCounts, $totalInventory): array {
                $count = (int) $inventoryCounts->get($status, 0);
                $percent = $totalInventory > 0 ? round(($count / $totalInventory) * 100, 1) : 0.0;

                return [$status => [
                    'count' => $count,
                    'percent' => $percent,
                ]];
            })
            ->all();

        $availableValue = (int) CarUnit::query()->where('status', 'available')->sum('price');

        return [
            'total' => $totalInventory,
            'breakdown' => $inventoryBreakdown,
            'counts' => [
                'draft' => (int) $inventoryCounts->get('draft', 0),
                'available' => (int) $inventoryCounts->get('available', 0),
                'on_hold' => (int) $inventoryCounts->get('on_hold', 0),
                'sold' => (int) $inventoryCounts->get('sold', 0),
                'archived' => (int) $inventoryCounts->get('archived', 0),
            ],
            'available_value' => $availableValue,
            'available_value_label' => $this->formatCurrency($availableValue, $currency),
        ];
    }

    /**
     * @return array{
     *     labels: array<int, string>,
     *     leadValues: array<int, int>,
     *     salesValues: array<int, int>,
     *     revenueValues: array<int, float>
     * }
     */
    public function getTrends(int $months = 6): array
    {
        $offsetRange = max(2, min(12, $months)) - 1;
        $trendPeriodStart = Carbon::now()->startOfMonth()->subMonths($offsetRange);
        $trendPeriodEnd = Carbon::now()->endOfMonth();

        $leadPeriodExpression = match (DB::getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $salePeriodExpression = match (DB::getDriverName()) {
            'sqlite' => "strftime('%Y-%m', sold_at)",
            'pgsql' => "to_char(sold_at, 'YYYY-MM')",
            default => "DATE_FORMAT(sold_at, '%Y-%m')",
        };

        $leadCounts = Lead::query()
            ->selectRaw($leadPeriodExpression . ' as period_key, COUNT(*) as total')
            ->whereBetween('created_at', [$trendPeriodStart, $trendPeriodEnd])
            ->groupBy('period_key')
            ->pluck('total', 'period_key');

        $saleStats = DB::table('sales')
            ->selectRaw($salePeriodExpression . ' as period_key, COUNT(*) as total, SUM(sold_price) as revenue')
            ->whereBetween('sold_at', [$trendPeriodStart, $trendPeriodEnd])
            ->groupBy('period_key')
            ->get()
            ->keyBy('period_key');

        $labels = [];
        $leadValues = [];
        $salesValues = [];
        $revenueValues = [];

        foreach (range($offsetRange, 0) as $offset) {
            $period = Carbon::now()->startOfMonth()->subMonths($offset);
            $key = $period->format('Y-m');

            $labels[] = 'Thg ' . $period->format('m/y');
            $leadValues[] = (int) $leadCounts->get($key, 0);

            $saleRow = $saleStats->get($key);
            $salesValues[] = $saleRow ? (int) $saleRow->total : 0;
            $revenueValues[] = $saleRow ? (float) ($saleRow->revenue ?? 0) : 0.0;
        }

        return [
            'labels' => $labels,
            'leadValues' => $leadValues,
            'salesValues' => $salesValues,
            'revenueValues' => $revenueValues,
        ];
    }

    /**
     * @return Collection<int, mixed>
     */
    public function getRecentLeads(int $limit = 6): Collection
    {
        $leads = Lead::query()
            ->with([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
                'trim.model.make',
            ])
            ->latest()
            ->limit($limit)
            ->get();

        return $leads->map(function (Lead $lead): object {
            /** @var CarUnit|null $carUnit */
            $carUnit = $lead->carUnit;
            /** @var Trim|null $trim */
            $trim = $carUnit !== null ? $carUnit->trim : $lead->trim;
            /** @var User|null $assignedTo */
            $assignedTo = $lead->assignedTo;

            return (object) [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'source' => ucfirst($lead->source ?? 'Web'),
                'status' => $lead->status,
                'status_label' => $this->leadStatusLabel($lead->status),
                'status_class' => $this->leadStatusClass($lead->status),
                'assigned_to' => $assignedTo ? $assignedTo->name : 'Chưa phân công',
                'car_context' => $this->formatCarContext($trim) ?: 'Liên hệ chung',
                'created_at_label' => $this->formatRelativeDate($lead->created_at),
                'url' => route('admin.leads.show', $lead),
            ];
        });
    }

    /**
     * @return Collection<int, mixed>
     */
    public function getRecentSales(int $limit = 4, string $currency = 'VND'): Collection
    {
        $sales = Sale::query()
            ->with([
                'buyer:id,name',
                'carUnit.trim.model.make',
            ])
            ->orderByDesc('sold_at')
            ->limit($limit)
            ->get();

        return $sales->map(function (Sale $sale) use ($currency): object {
            /** @var CarUnit|null $carUnit */
            $carUnit = $sale->carUnit;
            /** @var Trim|null $trim */
            $trim = $carUnit?->trim;
            /** @var User|null $buyer */
            $buyer = $sale->buyer;

            return (object) [
                'buyer_name' => $buyer ? $buyer->name : 'Khách hàng showroom',
                'car_name' => $this->formatCarContext($trim) ?: 'Xe showroom',
                'sold_at_label' => optional($sale->sold_at)->format('d/m/Y') ?? 'Đang cập nhật',
                'sold_price_label' => $this->formatCurrency($sale->sold_price, $currency),
                'url' => route('admin.sales.index'),
            ];
        });
    }

    /**
     * @return Collection<int, mixed>
     */
    public function getUpcomingAppointments(int $limit = 4): Collection
    {
        $appointments = Appointment::query()
            ->with([
                'user:id,name',
                'lead:id,name',
                'handledBy:id,name',
                'carUnit.trim.model.make',
                'trim.model.make',
            ])
            ->where('scheduled_at', '>=', Carbon::now()->startOfDay())
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();

        return $appointments->map(function (Appointment $appointment): object {
            /** @var CarUnit|null $carUnit */
            $carUnit = $appointment->carUnit;
            /** @var Trim|null $trim */
            $trim = $carUnit !== null ? $carUnit->trim : $appointment->trim;
            /** @var User|null $user */
            $user = $appointment->user;
            /** @var Lead|null $lead */
            $lead = $appointment->lead;
            /** @var User|null $handledBy */
            $handledBy = $appointment->handledBy;

            $customerName = $user !== null ? $user->name : ($lead !== null ? $lead->name : 'Khách hẹn tư vấn');

            return (object) [
                'customer_name' => $customerName,
                'car_name' => $this->formatCarContext($trim) ?: 'Không chọn xe trước',
                'scheduled_date' => optional($appointment->scheduled_at)->format('d/m') ?? '--',
                'scheduled_time' => optional($appointment->scheduled_at)->format('H:i') ?? '--',
                'scheduled_at_label' => optional($appointment->scheduled_at)->format('d/m/Y H:i') ?? 'Đang cập nhật',
                'status' => $appointment->status,
                'status_label' => $this->appointmentStatusLabel($appointment->status),
                'status_class' => $this->appointmentStatusClass($appointment->status),
                'handled_by' => $handledBy ? $handledBy->name : 'Chưa phân công',
                'url' => route('admin.appointments.edit', $appointment),
            ];
        });
    }

    public function formatCarContext(?Trim $trim): string
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

    public function formatCurrency(int|float|null $value, string $currency = 'VND'): string
    {
        if ($value === null || $value <= 0) {
            return '0 ' . $currency;
        }

        return number_format((float) $value, 0, ',', '.') . ' ' . $currency;
    }

    public function formatShortRevenue(int|float|null $value, string $currency = 'VND'): string
    {
        if ($value === null || $value <= 0) {
            return '0 ' . $currency;
        }

        if ($value >= 1_000_000_000) {
            return round($value / 1_000_000_000, 2) . ' tỷ ' . $currency;
        }

        if ($value >= 1_000_000) {
            return round($value / 1_000_000, 1) . ' triệu ' . $currency;
        }

        return number_format((float) $value, 0, ',', '.') . ' ' . $currency;
    }

    public function formatRelativeDate(?CarbonInterface $dateTime): string
    {
        if ($dateTime === null) {
            return 'Vừa cập nhật';
        }

        $now = Carbon::now();

        if ($dateTime->isSameDay($now)) {
            return 'Hôm nay, ' . $dateTime->format('H:i');
        }

        if ($dateTime->isYesterday()) {
            return 'Hôm qua, ' . $dateTime->format('H:i');
        }

        return $dateTime->format('d/m/Y H:i');
    }

    public function leadStatusLabel(string $status): string
    {
        return match ($status) {
            'new' => 'Mới',
            'contacted' => 'Đã liên hệ',
            'qualified' => 'Tiềm năng',
            'closed' => 'Đã chốt',
            'lost' => 'Đã hủy',
            default => mb_strtoupper($status),
        };
    }

    public function leadStatusClass(string $status): string
    {
        return match ($status) {
            'new' => 'blue',
            'contacted' => 'cyan',
            'qualified' => 'green',
            'closed' => 'indigo',
            'lost' => 'red',
            default => 'gray',
        };
    }

    public function appointmentStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ duyệt',
            'confirmed' => 'Đã duyệt',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
            default => mb_strtoupper($status),
        };
    }

    public function appointmentStatusClass(string $status): string
    {
        return match ($status) {
            'pending' => 'amber',
            'confirmed' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
