@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('page-actions')
    <a href="{{ route('admin.inventory.create') }}" class="admin-action-btn">Them xe vao kho</a>
    <a href="{{ route('admin.sales.create') }}" class="admin-action-btn admin-action-btn-secondary">Tao sale</a>
@endsection

@section('admin-content')
    <div class="row">
        @foreach ($summaryCards as $card)
            <div class="col-xl-3 col-md-6">
                <div class="uii-item admin-kpi-card admin-kpi-{{ $card['tone'] }}">
                    <span>{{ $card['label'] }}</span>
                    <h3>{{ number_format($card['value']) }}</h3>
                    <p>{{ $card['note'] }}</p>
                    <div class="ui-icon">
                        <img src="{{ $card['icon'] }}" alt="{{ $card['label'] }}">
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="graph-content">
        <div class="row">
            <div class="col-xl-8">
                <div class="widget-graph admin-panel-card">
                    <div class="graph-head">
                        <h3>Lead trend 6 thang gan day</h3>
                        <div class="text-box admin-inline-metrics">
                            <div class="admin-metric-pill">
                                <small>Gia tri xe dang available</small>
                                <strong>{{ $availableInventoryValueLabel }}</strong>
                            </div>
                            <div class="admin-metric-pill">
                                <small>Trang thai inventory</small>
                                <strong>{{ $inventoryCounts['available'] }} available / {{ $inventoryCounts['on_hold'] }} hold</strong>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content">
                        <canvas id="admin-lead-chart" width="100" height="45"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="notification-widget ls-widget admin-panel-card">
                    <div class="widget-title">
                        <h4>Inventory status</h4>
                    </div>
                    <div class="widget-content">
                        <ul class="notification-list admin-status-list">
                            <li><span class="icon admin-status-dot admin-status-draft"></span><strong>Draft</strong><span>{{ $inventoryCounts['draft'] }} xe</span></li>
                            <li><span class="icon admin-status-dot admin-status-available"></span><strong>Available</strong><span>{{ $inventoryCounts['available'] }} xe</span></li>
                            <li><span class="icon admin-status-dot admin-status-hold"></span><strong>On hold</strong><span>{{ $inventoryCounts['on_hold'] }} xe</span></li>
                            <li><span class="icon admin-status-dot admin-status-sold"></span><strong>Sold</strong><span>{{ $inventoryCounts['sold'] }} xe</span></li>
                            <li><span class="icon admin-status-dot admin-status-archived"></span><strong>Archived</strong><span>{{ $inventoryCounts['archived'] }} xe</span></li>
                        </ul>
                    </div>
                    <div class="dash-btn-box">
                        <a href="{{ route('admin.inventory.index') }}" class="dash-btn">Mo inventory</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row admin-dashboard-grids">
        <div class="col-xl-4">
            <div class="admin-panel-card">
                <div class="admin-section-head">
                    <h4>Lead moi</h4>
                    <a href="{{ route('admin.leads.index') }}">Xem tat ca</a>
                </div>
                <div class="admin-list-stack">
                    @forelse ($recentLeads as $lead)
                        <a href="{{ $lead->url }}" class="admin-list-item">
                            <div>
                                <strong>{{ $lead->name }}</strong>
                                <p>{{ $lead->context }}</p>
                            </div>
                            <div class="admin-list-meta">
                                <span class="admin-badge admin-badge-{{ $lead->status }}">{{ strtoupper($lead->status) }}</span>
                                <small>{{ $lead->created_at_label }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="admin-empty-state">Chua co lead nao duoc tao.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="admin-panel-card">
                <div class="admin-section-head">
                    <h4>Lich hen sap toi</h4>
                    <a href="{{ route('admin.appointments.index') }}">Mo lich hen</a>
                </div>
                <div class="admin-list-stack">
                    @forelse ($upcomingAppointments as $appointment)
                        <a href="{{ $appointment->url }}" class="admin-list-item">
                            <div>
                                <strong>{{ $appointment->scheduled_at_label }}</strong>
                                <p>{{ $appointment->context }}</p>
                            </div>
                            <div class="admin-list-meta">
                                <span class="admin-badge admin-badge-{{ $appointment->status }}">{{ strtoupper($appointment->status) }}</span>
                                <small>{{ $appointment->handled_by }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="admin-empty-state">Khong co lich hen nao trong sap toi.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="admin-panel-card">
                <div class="admin-section-head">
                    <h4>Sale gan day</h4>
                    <a href="{{ route('admin.sales.index') }}">Mo sale log</a>
                </div>
                <div class="admin-list-stack">
                    @forelse ($recentSales as $sale)
                        <a href="{{ $sale->url }}" class="admin-list-item">
                            <div>
                                <strong>{{ $sale->buyer_name }}</strong>
                                <p>{{ $sale->car_name }}</p>
                            </div>
                            <div class="admin-list-meta">
                                <span class="admin-price">{{ $sale->sold_price_label }}</span>
                                <small>{{ $sale->sold_at_label }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="admin-empty-state">Chua co giao dich nao duoc ghi nhan.</div>
                    @endforelse
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

            Chart.defaults.global.defaultFontFamily = 'Sofia Pro';
            Chart.defaults.global.defaultFontColor = '#67728a';
            Chart.defaults.global.defaultFontSize = 13;

            new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($leadTrendLabels),
                    datasets: [{
                        label: 'Leads',
                        backgroundColor: 'rgba(25, 103, 210, 0.08)',
                        borderColor: '#1967D2',
                        borderWidth: 2,
                        data: @json($leadTrendValues),
                        pointRadius: 3,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#1967D2',
                        pointHoverBackgroundColor: '#1967D2',
                        pointBorderWidth: 0,
                        lineTension: 0.35
                    }]
                },
                options: {
                    legend: { display: false },
                    scales: {
                        yAxes: [{
                            ticks: { precision: 0, beginAtZero: true },
                            gridLines: {
                                borderDash: [6, 10],
                                color: '#dbe4f0',
                                lineWidth: 1
                            }
                        }],
                        xAxes: [{
                            gridLines: { display: false }
                        }]
                    },
                    tooltips: {
                        backgroundColor: '#0f172a',
                        titleFontColor: '#fff',
                        bodyFontColor: '#fff',
                        displayColors: false,
                        xPadding: 12,
                        yPadding: 10,
                        intersect: false
                    }
                }
            });
        });
    </script>
@endpush
