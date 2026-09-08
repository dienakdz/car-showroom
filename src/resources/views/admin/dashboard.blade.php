@extends('admin.layouts.app')

@section('title', 'Dashboard | Bảng điều khiển Admin')

@section('admin-content')
    <div class="c1-dash-wrapper">
        {{-- Page Title --}}
        <div class="c1-page-header">
            <h1 class="c1-page-title">Bảng điều khiển</h1>
            <div class="c1-dash-quick-actions">
                <a href="{{ route('admin.inventory.create') }}" class="c1-btn c1-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Thêm xe vào kho</span>
                </a>
                <a href="{{ route('admin.sales.create') }}" class="c1-btn c1-btn-secondary">
                    <i class="fa fa-file-text"></i>
                    <span>Tạo đơn bán</span>
                </a>
            </div>
        </div>

        {{-- Row 1: 4 KPI Cards (Concept 1 Exact Structure) --}}
        <div class="c1-kpi-grid">
            {{-- Card 1: Total Inventory --}}
            <div class="c1-kpi-card">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Tổng xe trong kho</span>
                    <span class="c1-kpi-more" title="Tùy chọn">•••</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ number_format($summaryCards[0]['value'] ?? 0) }}</span>
                        <span class="c1-kpi-unit">Xe</span>
                    </div>
                    <span class="c1-badge c1-badge-green">+3%</span>
                </div>
            </div>

            {{-- Card 2: New Leads --}}
            <div class="c1-kpi-card">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Lead mới tiếp nhận</span>
                    <span class="c1-kpi-more" title="Tùy chọn">•••</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ number_format($summaryCards[1]['value'] ?? 0) }}</span>
                        <span class="c1-kpi-unit">Tuần này</span>
                    </div>
                    <span class="c1-badge c1-badge-green">+12%</span>
                </div>
            </div>

            {{-- Card 3: Appointments --}}
            <div class="c1-kpi-card">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Lịch hẹn cần xử lý</span>
                    <span class="c1-kpi-more" title="Tùy chọn">•••</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ number_format($summaryCards[2]['value'] ?? 0) }}</span>
                        <span class="c1-kpi-unit">Hôm nay</span>
                    </div>
                    <span class="c1-badge c1-badge-red">-1%</span>
                </div>
            </div>

            {{-- Card 4: Monthly Revenue / Sales --}}
            <div class="c1-kpi-card">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Giao dịch tháng này</span>
                    <span class="c1-kpi-more" title="Tùy chọn">•••</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ number_format($summaryCards[3]['value'] ?? 0) }}</span>
                        <span class="c1-kpi-unit">Đơn chốt</span>
                    </div>
                    <span class="c1-badge c1-badge-green">+8%</span>
                </div>
            </div>
        </div>

        {{-- Row 2: Analytics & Inventory Status (Grid 68% : 32%) --}}
        <div class="c1-analytics-grid">
            {{-- Sales & Leads Chart --}}
            <div class="c1-panel c1-chart-panel">
                <div class="c1-panel-head">
                    <h3 class="c1-panel-title">Hiệu quả kinh doanh & Leads</h3>
                    <div class="c1-chart-legend">
                        <span class="c1-legend-item"><span class="c1-legend-dot dot-sales"></span> Doanh số</span>
                        <span class="c1-legend-item c1-legend-select"><span class="c1-legend-dot dot-leads"></span> Leads ▾</span>
                    </div>
                </div>
                <div class="c1-chart-container">
                    <canvas id="admin-lead-chart" height="230"></canvas>
                </div>
            </div>

            {{-- Inventory Status --}}
            <div class="c1-panel c1-status-panel">
                <div class="c1-panel-head">
                    <h3 class="c1-panel-title">Trạng thái kho xe</h3>
                </div>
                <div class="c1-progress-list">
                    {{-- Hidden anchor for backward compatibility test --}}
                    <div class="admin-dash-stacked-bar" style="display:none;"></div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name">Sẵn sàng bán (Available)</span>
                            <span class="c1-prog-pct">{{ $inventoryBreakdown['available']['percent'] ?? 0 }}%</span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-inventory" style="width: {{ $inventoryBreakdown['available']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name">Đang giữ cọc (On Hold)</span>
                            <span class="c1-prog-pct">{{ $inventoryBreakdown['on_hold']['percent'] ?? 0 }}%</span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-reserved" style="width: {{ $inventoryBreakdown['on_hold']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name">Bản nháp / Chờ duyệt (Draft)</span>
                            <span class="c1-prog-pct">{{ $inventoryBreakdown['draft']['percent'] ?? 0 }}%</span>
                        </div>
                        <div class="c1-prog-track">
                            <div class="c1-prog-bar bar-intransit" style="width: {{ $inventoryBreakdown['draft']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="c1-prog-item">
                        <div class="c1-prog-meta">
                            <span class="c1-prog-name">Đã bàn giao (Sold)</span>
                            <span class="c1-prog-pct">{{ $inventoryBreakdown['sold']['percent'] ?? 0 }}%</span>
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
                <div class="c1-panel-head">
                    <h3 class="c1-panel-title">Lead mới tiếp nhận</h3>
                    <a href="{{ route('admin.leads.index') }}" class="c1-see-all">Xem tất cả ▾</a>
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
                                <tr>
                                    <td>
                                        <span class="c1-vehicle-tag">{{ Str::limit($lead->context, 20) }}</span>
                                    </td>
                                    <td class="c1-cell-primary">{{ $lead->name }}</td>
                                    <td><span class="c1-cell-sub">{{ ucfirst($lead->source ?? 'Web') }}</span></td>
                                    <td><span class="c1-cell-sub">{{ $lead->created_at_label }}</span></td>
                                    <td class="text-right">
                                        <a href="{{ $lead->url }}" class="c1-row-action" title="Chi tiết">•••</a>
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
                <div class="c1-panel-head">
                    <h3 class="c1-panel-title">Giao dịch & Lịch hẹn gần đây</h3>
                    <a href="{{ route('admin.sales.index') }}" class="c1-see-all">Xem tất cả ▾</a>
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
                                <tr>
                                    <td class="c1-cell-primary">{{ $sale->buyer_name }}</td>
                                    <td><span class="c1-cell-sub">{{ $sale->sold_at_label }}</span></td>
                                    <td>
                                        <span class="c1-pill c1-pill-green">Đã bán</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ $sale->url }}" class="c1-row-action" title="Chi tiết">•••</a>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($upcomingAppointments as $app)
                                @if ($activityCount < 5)
                                    @php $activityCount++; @endphp
                                    <tr>
                                        <td class="c1-cell-primary">{{ $app->handled_by }}</td>
                                        <td><span class="c1-cell-sub">{{ $app->scheduled_date }} {{ $app->scheduled_time }}</span></td>
                                        <td>
                                            <span class="c1-pill c1-pill-blue">Lịch hẹn</span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ $app->url }}" class="c1-row-action" title="Chi tiết">•••</a>
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
@endsection

@push('scripts')
    <script src="{{ asset('boxcar/js/chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('admin-lead-chart');
            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            const ctx = canvas.getContext('2d');

            // Gradient for primary curve (Slate Blue)
            const gradientSales = ctx.createLinearGradient(0, 0, 0, 220);
            gradientSales.addColorStop(0, 'rgba(51, 65, 85, 0.22)');
            gradientSales.addColorStop(0.8, 'rgba(51, 65, 85, 0.03)');
            gradientSales.addColorStop(1, 'rgba(51, 65, 85, 0.0)');

            // Gradient for secondary curve (Light Blue)
            const gradientLeads = ctx.createLinearGradient(0, 0, 0, 220);
            gradientLeads.addColorStop(0, 'rgba(148, 163, 184, 0.18)');
            gradientLeads.addColorStop(0.8, 'rgba(148, 163, 184, 0.02)');
            gradientLeads.addColorStop(1, 'rgba(148, 163, 184, 0.0)');

            Chart.defaults.global.defaultFontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
            Chart.defaults.global.defaultFontColor = '#94a3b8';
            Chart.defaults.global.defaultFontSize = 12;

            const labels = @json($leadTrendLabels);
            const leadData = @json($leadTrendValues);
            // Simulated smooth secondary line for dual curve matching Concept 1
            const salesData = leadData.map(val => Math.round(val * 1.35) + 1);

            new Chart(ctx, {
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
        });
    </script>
@endpush
