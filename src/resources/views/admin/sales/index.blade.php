@extends('admin.layouts.app')

@section('title', 'Quản lý Bán hàng & Hợp đồng | Admin')

@section('admin-content')
    <div class="c1-dash-wrapper">
        {{-- Page Header --}}
        <div class="c1-page-header">
            <h1 class="c1-page-title">Quản lý Bán hàng & Hợp đồng</h1>
            <div class="c1-dash-quick-actions">
                <a href="{{ route('admin.sales.create') }}" class="c1-btn c1-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Tạo hợp đồng mới</span>
                </a>
            </div>
        </div>

        {{-- 4 Sales Metric Cards --}}
        <div class="c1-kpi-grid">
            <div class="c1-kpi-card">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Doanh số đã chốt</span>
                    <span class="c1-badge c1-badge-green">+100%</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value c1-kpi-val-money" style="font-size: 22px;">
                            {{ number_format($totalRevenue, 0, ',', '.') }}
                        </span>
                        <span class="c1-kpi-unit">VNĐ</span>
                    </div>
                </div>
            </div>

            <div class="c1-kpi-card">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Hợp đồng đã ký</span>
                    <span class="c1-badge" style="background:#e0e7ff; color:#3730a3;">Đã ký</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $totalCount }}</span>
                        <span class="c1-kpi-unit">Hợp đồng</span>
                    </div>
                </div>
            </div>

            <div class="c1-kpi-card">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Giao dịch tháng này</span>
                    <span class="c1-badge c1-badge-green">Tháng này</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $monthlyCount }}</span>
                        <span class="c1-kpi-unit">Đơn chốt</span>
                    </div>
                </div>
            </div>

            <div class="c1-kpi-card">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Tỷ lệ hoàn thành</span>
                    <span class="c1-badge c1-badge-green">Hoàn tất</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">100%</span>
                        <span class="c1-kpi-unit">Đã bàn giao</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contracts Table Panel --}}
        <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
            <div class="c1-panel-head" style="margin-bottom: 16px;">
                <h3 class="c1-panel-title">Danh sách hợp đồng bán xe</h3>
                <span class="c1-cell-sub">Tổng cộng {{ $sales->total() }} giao dịch</span>
            </div>

            <div class="c1-table-wrap">
                <table class="c1-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Mã HĐ</th>
                            <th>Khách hàng</th>
                            <th>Dòng xe</th>
                            <th>Giá chốt bán</th>
                            <th>Hình thức</th>
                            <th>Trạng thái thanh toán</th>
                            <th>Ngày ký</th>
                            <th class="text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            @php($trim = $sale->carUnit?->trim)
                            <tr>
                                <td>
                                    <span class="c1-vehicle-tag" style="font-weight: 700; color: var(--c1-primary);">
                                        #HD-{{ str_pad($sale->id, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <div class="c1-cell-primary">{{ $sale->buyer?->name ?? 'Khách hàng' }}</div>
                                        <div class="c1-cell-sub">{{ $sale->buyer?->phone ?: $sale->buyer?->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="c1-cell-primary">
                                            {{ trim(collect([$trim?->model?->make?->name, $trim?->model?->name, $trim?->name])->filter()->implode(' ')) }}
                                        </div>
                                        <div class="c1-cell-sub">Mã kho: {{ $sale->carUnit?->stock_code }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="c1-price-cell">
                                        {{ $sale->sold_price ? number_format($sale->sold_price, 0, ',', '.') . ' VNĐ' : 'Theo thỏa thuận' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="c1-cell-sub" style="font-weight: 500;">Trả thẳng 100%</span>
                                </td>
                                <td>
                                    <span class="c1-pill c1-pill-green">Đã thanh toán đủ</span>
                                </td>
                                <td>
                                    <span class="c1-cell-sub">{{ optional($sale->sold_at)->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="text-right">
                                    <span class="c1-row-action" title="Tùy chọn">•••</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="c1-empty-cell">Chưa có giao dịch bán hàng nào được ghi nhận.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px;">
                {{ $sales->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
@endsection

