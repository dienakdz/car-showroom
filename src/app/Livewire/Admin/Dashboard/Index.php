<?php

namespace App\Livewire\Admin\Dashboard;

use App\Livewire\Admin\AdminPageComponent;
use App\Services\Admin\DashboardService;
use Illuminate\View\View;

class Index extends AdminPageComponent
{
    public int $leadTrendMonths = 6;

    public function setLeadTrendMonths(int $months): void
    {
        if (in_array($months, [3, 6, 12], true)) {
            $this->leadTrendMonths = $months;
        }
    }

    public function render(DashboardService $dashboardService): View
    {
        $adminData = $this->adminLayoutData();
        $settings = $adminData['adminSettings'] ?? collect();
        $currency = (string) data_get($settings, 'site.default_currency.value', 'VND');

        $inventoryStats = $dashboardService->getInventoryStats($currency);
        $trends = $dashboardService->getTrends($this->leadTrendMonths);

        return view('livewire.admin.dashboard.index', [
            'summaryCards' => $dashboardService->getSummaryCards($currency),
            'urgentAlerts' => $dashboardService->getUrgentAlerts(),
            'totalInventory' => $inventoryStats['total'],
            'inventoryBreakdown' => $inventoryStats['breakdown'],
            'inventoryCounts' => $inventoryStats['counts'],
            'availableInventoryValueLabel' => $inventoryStats['available_value_label'],
            'leadTrendLabels' => $trends['labels'],
            'leadTrendValues' => $trends['leadValues'],
            'salesTrendValues' => $trends['salesValues'],
            'revenueTrendValues' => $trends['revenueValues'],
            'recentLeads' => $dashboardService->getRecentLeads(6),
            'recentSales' => $dashboardService->getRecentSales(4, $currency),
            'upcomingAppointments' => $dashboardService->getUpcomingAppointments(4),
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Bảng điều khiển tổng quan',
            'adminPageDescription' => 'Theo dõi toàn diện kho xe, khách hàng tiềm năng, lịch hẹn và giao dịch bán hàng của showroom.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return null;
    }
}
