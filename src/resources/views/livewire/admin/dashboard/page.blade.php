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
                    <span class="c1-badge c1-badge-green">+3%</span>
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
                    <span class="c1-badge c1-badge-green">+12%</span>
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
                    <span class="c1-badge c1-badge-red">-1%</span>
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
                        <span class="c1-kpi-unit">Đơn chốt</span>
                    </div>
                    <span class="c1-badge c1-badge-green">+8%</span>
                </div>
                <div class="c1-cell-sub" style="font-size: 11.5px; margin-top: 4px;">
                    {{ $summaryCards[3]['note'] }}
                </div>
            </a>
        </div>

        {{-- Row 2: Analytics & Inventory Status (Grid 68% : 32%) --}}
        <div class="c1-analytics-grid">
            {{-- Sales & Leads Chart --}}
            <div class="c1-panel c1-chart-panel">
                <div class="c1-panel-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <h3 class="c1-panel-title">Hiệu quả kinh doanh & Leads</h3>
                        <span class="c1-cell-sub">Biểu đồ tăng trưởng tương tác khách hàng theo thời gian</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Lọc thời gian">
                            <button
                                type="button"
                                wire:click="setLeadTrendMonths(3)"
                                class="btn {{ $leadTrendMonths === 3 ? 'btn-primary' : 'btn-outline-secondary' }}"
                                style="font-size: 12px; padding: 3px 10px;"
                            >3T</button>
                            <button
                                type="button"
                                wire:click="setLeadTrendMonths(6)"
                                class="btn {{ $leadTrendMonths === 6 ? 'btn-primary' : 'btn-outline-secondary' }}"
                                style="font-size: 12px; padding: 3px 10px;"
                            >6T</button>
                            <button
                                type="button"
                                wire:click="setLeadTrendMonths(12)"
                                class="btn {{ $leadTrendMonths === 12 ? 'btn-primary' : 'btn-outline-secondary' }}"
                                style="font-size: 12px; padding: 3px 10px;"
                            >12T</button>
                        </div>
                        <div class="c1-chart-legend">
                            <span class="c1-legend-item"><span class="c1-legend-dot dot-sales"></span> Doanh số</span>
                            <span class="c1-legend-item"><span class="c1-legend-dot dot-leads"></span> Leads</span>
                        </div>
                    </div>
                </div>
                <div class="c1-chart-container" style="position: relative; height: 230px;" wire:ignore>
                    <canvas id="admin-lead-chart" height="230"></canvas>
                </div>
            </div>

            {{-- Inventory Status --}}
            <div class="c1-panel c1-status-panel">
                <div class="c1-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="c1-panel-title">Trạng thái kho xe</h3>
                    <span class="c1-cell-sub" style="font-weight: 600; color: var(--c1-primary);">
                        {{ $availableInventoryValueLabel }}
                    </span>
                </div>
                <div class="c1-progress-list">
                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name">Sẵn sàng bán (Available)</span>
                            <span class="c1-prog-pct">{{ $inventoryBreakdown['available']['count'] ?? 0 }} xe ({{ $inventoryBreakdown['available']['percent'] ?? 0 }}%)</span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-inventory" style="width: {{ $inventoryBreakdown['available']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name">Đang giữ cọc (On Hold)</span>
                            <span class="c1-prog-pct">{{ $inventoryBreakdown['on_hold']['count'] ?? 0 }} xe ({{ $inventoryBreakdown['on_hold']['percent'] ?? 0 }}%)</span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-reserved" style="width: {{ $inventoryBreakdown['on_hold']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name">Bản nháp / Chờ duyệt (Draft)</span>
                            <span class="c1-prog-pct">{{ $inventoryBreakdown['draft']['count'] ?? 0 }} xe ({{ $inventoryBreakdown['draft']['percent'] ?? 0 }}%)</span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-intransit" style="width: {{ $inventoryBreakdown['draft']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name">Đã bàn giao (Sold)</span>
                            <span class="c1-prog-pct">{{ $inventoryBreakdown['sold']['count'] ?? 0 }} xe ({{ $inventoryBreakdown['sold']['percent'] ?? 0 }}%)</span>
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
                    <h3 class="c1-panel-title">Lead mới tiếp nhận</h3>
                    <a href="{{ route('admin.leads.index') }}" wire:navigate.hover class="c1-see-all">Xem tất cả ▾</a>
                </div>
                <div class="c1-table-wrap">
                    <table class="c1-table">
                        <thead>
                            <tr>
                                <th>Xe quan tâm</th>
                                <th>Khách hàng</th>
                                <th>Nguồn</th>
                                <th>Ngày tạo</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentLeads as $lead)
                                <tr wire:key="recent-lead-{{ $loop->index }}">
                                     <td>
                                         <span class="c1-vehicle-tag">{{ \Illuminate\Support\Str::limit($lead->context, 20) }}</span>
                                     </td>
                                     <td class="c1-cell-primary">{{ $lead->name }}</td>
                                     <td><span class="c1-cell-sub">{{ ucfirst($lead->source ?? 'Web') }}</span></td>
                                     <td><span class="c1-cell-sub">{{ $lead->created_at_label }}</span></td>
                                     <td class="text-right">
                                         <a href="{{ $lead->url }}" wire:navigate class="c1-row-action" title="Xem chi tiết lead">•••</a>
                                     </td>
                                 </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="c1-empty-cell">Chưa có lead mới tiếp nhận.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right Table: Recent Activity (Sales / Appointments) --}}
            <div class="c1-panel c1-table-panel">
                <div class="c1-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="c1-panel-title">Giao dịch & Lịch hẹn gần đây</h3>
                    <a href="{{ route('admin.sales.index') }}" wire:navigate.hover class="c1-see-all">Xem tất cả ▾</a>
                </div>
                <div class="c1-table-wrap">
                    <table class="c1-table">
                        <thead>
                            <tr>
                                <th>Khách hàng / Liên hệ</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $activityCount = 0; @endphp
                            @foreach ($recentSales as $sale)
                                @php $activityCount++; @endphp
                                <tr wire:key="recent-sale-{{ $loop->index }}">
                                    <td class="c1-cell-primary">{{ $sale->buyer_name }}</td>
                                    <td><span class="c1-cell-sub">{{ $sale->sold_at_label }}</span></td>
                                    <td>
                                        <span class="c1-pill c1-pill-green">Đã bán</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ $sale->url }}" wire:navigate class="c1-row-action" title="Xem hợp đồng">•••</a>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($upcomingAppointments as $app)
                                @if ($activityCount < 5)
                                    @php $activityCount++; @endphp
                                    <tr wire:key="recent-app-{{ $loop->index }}">
                                        <td class="c1-cell-primary">{{ $app->handled_by }}</td>
                                        <td><span class="c1-cell-sub">{{ $app->scheduled_date }} {{ $app->scheduled_time }}</span></td>
                                        <td>
                                            <span class="c1-pill c1-pill-blue">Lịch hẹn</span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ $app->url }}" wire:navigate class="c1-row-action" title="Xem lịch hẹn">•••</a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($activityCount === 0)
                                <tr>
                                    <td colspan="4" class="c1-empty-cell">Chưa có giao dịch hoặc lịch hẹn nào.</td>
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

    const renderLeadChart = (labels, leadData) => {
        const canvas = document.getElementById('admin-lead-chart');
        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        if (adminLeadChartInstance) {
            adminLeadChartInstance.destroy();
            adminLeadChartInstance = null;
        }

        const ctx = canvas.getContext('2d');

        const gradientSales = ctx.createLinearGradient(0, 0, 0, 220);
        gradientSales.addColorStop(0, 'rgba(51, 65, 85, 0.22)');
        gradientSales.addColorStop(0.8, 'rgba(51, 65, 85, 0.03)');
        gradientSales.addColorStop(1, 'rgba(51, 65, 85, 0.0)');

        const gradientLeads = ctx.createLinearGradient(0, 0, 0, 220);
        gradientLeads.addColorStop(0, 'rgba(148, 163, 184, 0.18)');
        gradientLeads.addColorStop(0.8, 'rgba(148, 163, 184, 0.02)');
        gradientLeads.addColorStop(1, 'rgba(148, 163, 184, 0.0)');

        Chart.defaults.global.defaultFontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        Chart.defaults.global.defaultFontColor = '#94a3b8';
        Chart.defaults.global.defaultFontSize = 12;

        const salesData = leadData.map(val => Math.round(val * 1.35) + 1);

        adminLeadChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Sales',
                        backgroundColor: gradientSales,
                        borderColor: '#334155',
                        borderWidth: 2,
                        data: salesData,
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#334155',
                        pointBorderWidth: 2,
                        lineTension: 0.42,
                        fill: true
                    },
                    {
                        label: 'Leads',
                        backgroundColor: gradientLeads,
                        borderColor: '#94a3b8',
                        borderWidth: 1.8,
                        data: leadData,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#94a3b8',
                        pointBorderWidth: 1.8,
                        lineTension: 0.42,
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
                            fontColor: '#94a3b8'
                        },
                        gridLines: {
                            borderDash: [3, 5],
                            color: '#f1f5f9',
                            lineWidth: 1,
                            drawBorder: false,
                            zeroLineColor: '#e2e8f0'
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            padding: 8,
                            fontColor: '#64748b',
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
                    backgroundColor: '#0f172a',
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
    const initialValues = @json($leadTrendValues);

    const tryInitChart = () => {
        if (typeof Chart !== 'undefined') {
            renderLeadChart(initialLabels, initialValues);
        } else {
            setTimeout(tryInitChart, 50);
        }
    };

    tryInitChart();

    $wire.hook('commit', ({ succeed }) => {
        succeed(() => {
            renderLeadChart(@json($leadTrendLabels), @json($leadTrendValues));
        });
    });
</script>
@endscript
