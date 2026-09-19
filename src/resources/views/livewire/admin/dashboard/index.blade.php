<div>
    <div class="c1-dash-wrapper">
        {{-- Page Header --}}
        <div class="c1-page-header">
            <div>
                <h1 class="c1-page-title">Bảng điều khiển</h1>
                <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                    Theo dõi toàn diện kho xe, khách hàng tiềm năng, lịch hẹn và giao dịch bán hàng của showroom.
                </div>
            </div>
            <div class="c1-dash-quick-actions">
                <a href="{{ route('admin.inventory.create') }}" wire:navigate.hover class="c1-btn c1-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Thêm xe vào kho</span>
                </a>
                <a href="{{ route('admin.sales.create') }}" wire:navigate.hover class="c1-btn c1-btn-secondary">
                    <i class="fa fa-file-text"></i>
                    <span>Tạo hợp đồng bán</span>
                </a>
            </div>
        </div>

        {{-- Urgent Operational Alerts Banner --}}
        @if ($urgentAlerts['has_urgent'])
            <div class="c1-dash-alerts" style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 10px; padding: 12px 18px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="c1-pill c1-pill-blue" style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                        <i class="fa fa-bell"></i> Tác nghiệp cần chú ý
                    </span>
                    <span style="font-size: 13px; font-weight: 500; color: var(--c1-text-main);">
                        Các công việc vận hành quan trọng cần xử lý trong ngày:
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    @if ($urgentAlerts['today_appointments'] > 0)
                        <a href="{{ route('admin.appointments.index') }}" wire:navigate.hover class="c1-badge" style="background: #e0f2fe; color: #0369a1; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fa fa-calendar-check-o"></i> {{ $urgentAlerts['today_appointments'] }} lịch hẹn hôm nay
                        </a>
                    @endif
                    @if ($urgentAlerts['unassigned_leads'] > 0)
                        <a href="{{ route('admin.leads.index') }}" wire:navigate.hover class="c1-badge" style="background: #fef3c7; color: #92400e; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fa fa-user-plus"></i> {{ $urgentAlerts['unassigned_leads'] }} lead chưa phân công
                        </a>
                    @endif
                    @if ($urgentAlerts['pending_reviews'] > 0)
                        <a href="{{ route('admin.reviews.index') }}" wire:navigate.hover class="c1-badge" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fa fa-star-half-o"></i> {{ $urgentAlerts['pending_reviews'] }} đánh giá chờ duyệt
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Row 1: 4 KPI Cards (Clickable to jump to modules) --}}
        <div class="c1-kpi-grid">
            {{-- Card 1: Total Inventory --}}
            <a href="{{ route('admin.inventory.index') }}" wire:navigate.hover class="c1-kpi-card" style="text-decoration: none; display: block; color: inherit;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Tổng xe trong kho</span>
                    <span class="c1-badge" style="background: #e2e8f0; color: #334155;">Kho xe</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ number_format($summaryCards[0]['value'] ?? 0) }}</span>
                        <span class="c1-kpi-unit">Xe</span>
                    </div>
                    <span class="c1-badge c1-badge-green">{{ $summaryCards[0]['highlight'] ?? 'Sẵn sàng' }}</span>
                </div>
                <div class="c1-cell-sub" style="font-size: 11.5px; margin-top: 4px;">
                    {{ $summaryCards[0]['note'] }}
                </div>
            </a>

            {{-- Card 2: New Leads --}}
            <a href="{{ route('admin.leads.index') }}" wire:navigate.hover class="c1-kpi-card" style="text-decoration: none; display: block; color: inherit;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Lead mới tiếp nhận</span>
                    <span class="c1-badge" style="background: #e0f2fe; color: #0369a1;">CRM</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ number_format($summaryCards[1]['value'] ?? 0) }}</span>
                        <span class="c1-kpi-unit">Tuần này</span>
                    </div>
                    <span class="c1-badge" style="background: rgba(3, 105, 161, 0.12); color: #0284c7;">{{ $summaryCards[1]['highlight'] ?? 'Chờ xử lý' }}</span>
                </div>
                <div class="c1-cell-sub" style="font-size: 11.5px; margin-top: 4px;">
                    {{ $summaryCards[1]['note'] }}
                </div>
            </a>

            {{-- Card 3: Appointments --}}
            <a href="{{ route('admin.appointments.index') }}" wire:navigate.hover class="c1-kpi-card" style="text-decoration: none; display: block; color: inherit;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Lịch hẹn cần xử lý</span>
                    <span class="c1-badge" style="background: #fef3c7; color: #92400e;">Lịch hẹn</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ number_format($summaryCards[2]['value'] ?? 0) }}</span>
                        <span class="c1-kpi-unit">Sắp tới</span>
                    </div>
                    <span class="c1-badge" style="background: rgba(217, 119, 6, 0.12); color: #d97706;">{{ $summaryCards[2]['highlight'] ?? 'Gần kề' }}</span>
                </div>
                <div class="c1-cell-sub" style="font-size: 11.5px; margin-top: 4px;">
                    {{ $summaryCards[2]['note'] }}
                </div>
            </a>

            {{-- Card 4: Monthly Sales --}}
            <a href="{{ route('admin.sales.index') }}" wire:navigate.hover class="c1-kpi-card" style="text-decoration: none; display: block; color: inherit;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Giao dịch tháng này</span>
                    <span class="c1-badge c1-badge-green">Doanh số</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ number_format($summaryCards[3]['value'] ?? 0) }}</span>
                        <span class="c1-kpi-unit">Hợp đồng</span>
                    </div>
                    <span class="c1-badge c1-badge-green">{{ $summaryCards[3]['highlight'] ?? 'Doanh thu' }}</span>
                </div>
                <div class="c1-cell-sub" style="font-size: 11.5px; margin-top: 4px; font-weight: 500; color: var(--c1-primary);">
                    {{ $summaryCards[3]['note'] }}
                </div>
            </a>
        </div>

        {{-- Row 2: Analytics & Inventory Status (Grid 68% : 32%) --}}
        <div class="c1-analytics-grid">
            {{-- Sales & Leads Chart --}}
            <div class="c1-panel c1-chart-panel" style="min-width: 0;">
                <div class="c1-panel-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <h3 class="c1-panel-title">Hiệu quả kinh doanh &amp; Leads</h3>
                        <span class="c1-cell-sub">So sánh hợp đồng bán và lead tiếp nhận theo thời gian thực</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="c1-period-btn-group" role="group" aria-label="Lọc thời gian">
                            <button
                                type="button"
                                wire:click="setLeadTrendMonths(3)"
                                class="c1-period-btn {{ $leadTrendMonths === 3 ? 'active' : '' }}"
                            >3T</button>
                            <button
                                type="button"
                                wire:click="setLeadTrendMonths(6)"
                                class="c1-period-btn {{ $leadTrendMonths === 6 ? 'active' : '' }}"
                            >6T</button>
                            <button
                                type="button"
                                wire:click="setLeadTrendMonths(12)"
                                class="c1-period-btn {{ $leadTrendMonths === 12 ? 'active' : '' }}"
                            >12T</button>
                        </div>
                        <div class="c1-chart-legend">
                            <span class="c1-legend-item"><span class="c1-legend-dot" style="background: #10b981;"></span> Doanh số</span>
                            <span class="c1-legend-item"><span class="c1-legend-dot" style="background: #38bdf8;"></span> Leads</span>
                        </div>
                    </div>
                </div>
                <div class="c1-chart-container" style="position: relative; height: 230px;" wire:ignore>
                    <canvas id="admin-lead-chart" height="230"></canvas>
                </div>
            </div>

            {{-- Inventory Status --}}
            <div class="c1-panel c1-status-panel" style="min-width: 0;">
                <div class="c1-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="c1-panel-title">Trạng thái kho xe</h3>
                    <span class="c1-cell-sub" style="font-weight: 600; color: var(--c1-primary);" title="Tổng giá trị các xe sẵn sàng bán">
                        {{ $availableInventoryValueLabel }}
                    </span>
                </div>
                <div class="c1-progress-list">
                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name" style="display: inline-flex; align-items: center; gap: 6px;">
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                Sẵn sàng bán (Available)
                            </span>
                            <span class="c1-prog-pct" style="font-weight: 600;">
                                {{ $inventoryBreakdown['available']['count'] ?? 0 }} xe <span style="font-size: 11.5px; opacity: 0.85;">({{ $inventoryBreakdown['available']['percent'] ?? 0 }}%)</span>
                            </span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-inventory" style="width: {{ $inventoryBreakdown['available']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name" style="display: inline-flex; align-items: center; gap: 6px;">
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                                Đang giữ cọc (On Hold)
                            </span>
                            <span class="c1-prog-pct" style="font-weight: 600;">
                                {{ $inventoryBreakdown['on_hold']['count'] ?? 0 }} xe <span style="font-size: 11.5px; opacity: 0.85;">({{ $inventoryBreakdown['on_hold']['percent'] ?? 0 }}%)</span>
                            </span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-reserved" style="width: {{ $inventoryBreakdown['on_hold']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name" style="display: inline-flex; align-items: center; gap: 6px;">
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #6366f1; display: inline-block;"></span>
                                Bản nháp / Kiểm định (Draft)
                            </span>
                            <span class="c1-prog-pct" style="font-weight: 600;">
                                {{ $inventoryBreakdown['draft']['count'] ?? 0 }} xe <span style="font-size: 11.5px; opacity: 0.85;">({{ $inventoryBreakdown['draft']['percent'] ?? 0 }}%)</span>
                            </span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-intransit" style="width: {{ $inventoryBreakdown['draft']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name" style="display: inline-flex; align-items: center; gap: 6px;">
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #0ea5e9; display: inline-block;"></span>
                                Đã bàn giao (Sold)
                            </span>
                            <span class="c1-prog-pct" style="font-weight: 600;">
                                {{ $inventoryBreakdown['sold']['count'] ?? 0 }} xe <span style="font-size: 11.5px; opacity: 0.85;">({{ $inventoryBreakdown['sold']['percent'] ?? 0 }}%)</span>
                            </span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-distribution" style="width: {{ $inventoryBreakdown['sold']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Two Activity Tables (Grid 50% : 50%) --}}
        <div class="c1-tables-grid">
            {{-- Left Table: Recent Activity (Leads) --}}
            <div class="c1-panel c1-table-panel">
                <div class="c1-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 class="c1-panel-title">Lead mới tiếp nhận</h3>
                        <span class="c1-cell-sub">Khách hàng quan tâm và đăng ký tư vấn gần đây</span>
                    </div>
                    <a href="{{ route('admin.leads.index') }}" wire:navigate.hover class="c1-see-all">Xem tất cả ▾</a>
                </div>
                <div class="c1-table-wrap">
                    <table class="c1-table" style="table-layout: fixed; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 26%;">Xe quan tâm</th>
                                <th style="width: 28%;">Khách hàng</th>
                                <th style="width: 24%;">Nguồn / Tư vấn</th>
                                <th style="width: 22%;">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentLeads as $lead)
                                <tr wire:key="recent-lead-{{ $loop->index }}">
                                    <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <a href="{{ $lead->url }}" wire:navigate style="text-decoration: none;">
                                            <span class="c1-vehicle-tag" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; display: inline-block;">
                                                {{ $lead->car_context }}
                                            </span>
                                        </a>
                                    </td>
                                    <td style="overflow: hidden;">
                                        <div class="c1-cell-primary" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <a href="{{ $lead->url }}" wire:navigate style="color: inherit; text-decoration: none;">{{ $lead->name }}</a>
                                        </div>
                                        @if ($lead->phone)
                                            <div class="c1-cell-sub" style="font-size: 11px;">{{ $lead->phone }}</div>
                                        @endif
                                    </td>
                                    <td style="overflow: hidden;">
                                        <div class="c1-cell-primary" style="font-size: 11.5px; white-space: nowrap;">{{ $lead->source }}</div>
                                        <div class="c1-cell-sub" style="font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $lead->assigned_to }}</div>
                                    </td>
                                    <td style="overflow: hidden;">
                                        <div style="font-size: 11.5px; color: var(--c1-text-muted); white-space: nowrap;">{{ $lead->created_at_label }}</div>
                                        <span class="c1-pill c1-pill-{{ $lead->status_class }}" style="margin-top: 2px;">{{ $lead->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="c1-empty-cell">Chưa có lead mới tiếp nhận.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right Table: Recent Activity (Sales / Appointments) --}}
            <div class="c1-panel c1-table-panel">
                <div class="c1-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 class="c1-panel-title">Giao dịch &amp; Lịch hẹn gần đây</h3>
                        <span class="c1-cell-sub">Hợp đồng mua bán và lịch hẹn lái thử mới nhất</span>
                    </div>
                    <a href="{{ route('admin.sales.index') }}" wire:navigate.hover class="c1-see-all">Xem tất cả ▾</a>
                </div>
                <div class="c1-table-wrap">
                    <table class="c1-table" style="table-layout: fixed; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 44%;">Khách hàng &amp; Mẫu xe</th>
                                <th style="width: 34%;">Chi tiết &amp; Thời gian</th>
                                <th style="width: 22%;">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $activityCount = 0; @endphp
                            @foreach ($recentSales as $sale)
                                @php $activityCount++; @endphp
                                <tr wire:key="recent-sale-{{ $loop->index }}">
                                    <td style="overflow: hidden;">
                                        <div class="c1-cell-primary" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <a href="{{ $sale->url }}" wire:navigate style="color: inherit; text-decoration: none;">{{ $sale->buyer_name }}</a>
                                        </div>
                                        <div class="c1-cell-sub" style="font-size: 11.5px; color: var(--c1-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <i class="fa fa-car" style="margin-right: 3px;"></i> {{ $sale->car_name }}
                                        </div>
                                    </td>
                                    <td style="overflow: hidden;">
                                        <div style="font-weight: 600; font-size: 12px; color: var(--c1-primary); white-space: nowrap;">
                                            {{ $sale->sold_price_label }}
                                        </div>
                                        <div class="c1-cell-sub" style="font-size: 11px; white-space: nowrap;">{{ $sale->sold_at_label }}</div>
                                    </td>
                                    <td>
                                        <span class="c1-pill c1-pill-green"><i class="fa fa-check"></i> Đã bán</span>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($upcomingAppointments as $app)
                                @if ($activityCount < 6)
                                    @php $activityCount++; @endphp
                                    <tr wire:key="recent-app-{{ $loop->index }}">
                                        <td style="overflow: hidden;">
                                            <div class="c1-cell-primary" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <a href="{{ $app->url }}" wire:navigate style="color: inherit; text-decoration: none;">{{ $app->customer_name }}</a>
                                            </div>
                                            <div class="c1-cell-sub" style="font-size: 11.5px; color: var(--c1-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <i class="fa fa-calendar-check-o" style="margin-right: 3px;"></i> {{ $app->car_name }}
                                            </div>
                                        </td>
                                        <td style="overflow: hidden;">
                                            <div class="c1-cell-primary" style="font-size: 12px; white-space: nowrap;">{{ $app->scheduled_date }} - {{ $app->scheduled_time }}</div>
                                            <div class="c1-cell-sub" style="font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">TV: {{ $app->handled_by }}</div>
                                        </td>
                                        <td>
                                            <span class="c1-pill c1-pill-{{ $app->status_class }}">{{ $app->status_label }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($activityCount === 0)
                                <tr>
                                    <td colspan="3" class="c1-empty-cell">Chưa có giao dịch hoặc lịch hẹn nào.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('boxcar/js/chart.min.js') }}"></script>
@endpush

@script
<script>
    let adminLeadChartInstance = null;

    const renderLeadChart = (labels, leadData, salesData) => {
        const canvas = document.getElementById('admin-lead-chart');
        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        if (adminLeadChartInstance) {
            adminLeadChartInstance.destroy();
            adminLeadChartInstance = null;
        }

        const ctx = canvas.getContext('2d');
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

        const salesColor = isDark ? '#34d399' : '#10b981';
        const leadsColor = isDark ? '#38bdf8' : '#2563eb';

        const gradientSales = ctx.createLinearGradient(0, 0, 0, 220);
        gradientSales.addColorStop(0, isDark ? 'rgba(52, 211, 153, 0.30)' : 'rgba(16, 185, 129, 0.22)');
        gradientSales.addColorStop(0.8, isDark ? 'rgba(52, 211, 153, 0.03)' : 'rgba(16, 185, 129, 0.02)');
        gradientSales.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        const gradientLeads = ctx.createLinearGradient(0, 0, 0, 220);
        gradientLeads.addColorStop(0, isDark ? 'rgba(56, 189, 248, 0.28)' : 'rgba(37, 99, 235, 0.18)');
        gradientLeads.addColorStop(0.8, isDark ? 'rgba(56, 189, 248, 0.03)' : 'rgba(37, 99, 235, 0.02)');
        gradientLeads.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

        Chart.defaults.global.defaultFontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        Chart.defaults.global.defaultFontColor = isDark ? '#94a3b8' : '#64748b';
        Chart.defaults.global.defaultFontSize = 12;

        adminLeadChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Doanh số (Hợp đồng)',
                        backgroundColor: gradientSales,
                        borderColor: salesColor,
                        borderWidth: 2.2,
                        data: salesData,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: isDark ? '#152243' : '#ffffff',
                        pointBorderColor: salesColor,
                        pointBorderWidth: 2,
                        lineTension: 0.38,
                        fill: true
                    },
                    {
                        label: 'Leads (Khách tiềm năng)',
                        backgroundColor: gradientLeads,
                        borderColor: leadsColor,
                        borderWidth: 2,
                        data: leadData,
                        pointRadius: 3.5,
                        pointHoverRadius: 5.5,
                        pointBackgroundColor: isDark ? '#152243' : '#ffffff',
                        pointBorderColor: leadsColor,
                        pointBorderWidth: 2,
                        lineTension: 0.38,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    yAxes: [{
                        ticks: {
                            precision: 0,
                            beginAtZero: true,
                            padding: 8,
                            fontColor: isDark ? '#94a3b8' : '#64748b'
                        },
                        gridLines: {
                            borderDash: [3, 5],
                            color: isDark ? 'rgba(255, 255, 255, 0.06)' : '#f1f5f9',
                            lineWidth: 1,
                            drawBorder: false,
                            zeroLineColor: isDark ? 'rgba(255, 255, 255, 0.12)' : '#e2e8f0'
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            padding: 8,
                            fontColor: isDark ? '#94a3b8' : '#64748b',
                            fontStyle: '500'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        }
                    }]
                },
                tooltips: {
                    enabled: true,
                    mode: 'index',
                    intersect: false,
                    backgroundColor: isDark ? '#0b132b' : '#0f172a',
                    borderColor: isDark ? 'rgba(255, 255, 255, 0.15)' : 'transparent',
                    borderWidth: isDark ? 1 : 0,
                    titleFontColor: '#f8fafc',
                    titleFontSize: 12,
                    bodyFontColor: '#e2e8f0',
                    bodyFontSize: 11.5,
                    cornerRadius: 6,
                    xPadding: 12,
                    yPadding: 8,
                    displayColors: true
                }
            }
        });
    };

    const initialLabels = @json($leadTrendLabels);
    const initialLeadValues = @json($leadTrendValues);
    const initialSalesValues = @json($salesTrendValues);

    const tryInitChart = () => {
        if (typeof Chart !== 'undefined') {
            renderLeadChart(initialLabels, initialLeadValues, initialSalesValues);
        } else {
            setTimeout(tryInitChart, 50);
        }
    };

    tryInitChart();

    $wire.hook('commit', ({ succeed }) => {
        succeed(() => {
            renderLeadChart(@json($leadTrendLabels), @json($leadTrendValues), @json($salesTrendValues));
        });
    });

    window.addEventListener('admin-theme-changed', () => {
        renderLeadChart(@json($leadTrendLabels), @json($leadTrendValues), @json($salesTrendValues));
    });
</script>
@endscript
