<div>
    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span><i class="fa {{ ($feedback['type'] ?? 'success') === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle' }} me-2"></i>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="c1-dash-wrapper">
        {{-- Page Header --}}
        <div class="c1-page-header">
            <div>
                <h1 class="c1-page-title">Quản lý Bán hàng & Hợp đồng</h1>
                <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                    Theo dõi chi tiết hợp đồng bán xe, doanh thu và đối soát thanh toán.
                </div>
            </div>
            <div class="c1-dash-quick-actions">
                <a href="{{ route('admin.sales.create') }}" wire:navigate.hover class="c1-btn c1-btn-primary">
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
                    <span class="c1-badge c1-badge-green">Doanh thu</span>
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
            <div class="c1-panel-head" style="margin-bottom: 16px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px;">
                <div>
                    <h3 class="c1-panel-title">Danh sách hợp đồng bán xe</h3>
                    <span class="c1-cell-sub">Tổng cộng {{ $sales->total() }} giao dịch</span>
                </div>

                {{-- Filter Toolbar --}}
                <div style="display: flex; gap: 8px; align-items: center; min-width: 280px;">
                    <div style="position: relative; width: 100%;">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control"
                            placeholder="Tìm khách hàng, mã HĐ, xe..."
                            style="padding-left: 32px; font-size: 13px; height: 38px; border-radius: 8px;"
                        >
                        <i class="fa fa-search" style="position: absolute; left: 10px; top: 12px; color: var(--c1-text-muted);"></i>
                    </div>

                    @if ($search !== '')
                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="c1-btn c1-btn-ghost c1-btn-sm"
                            title="Xóa tìm kiếm"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="c1-table-wrap">
                <table class="c1-table">
                    <thead>
                        <tr>
                            <th style="width: 110px;">Mã HĐ</th>
                            <th>Khách hàng</th>
                            <th>Dòng xe & Mã kho</th>
                            <th>Giá chốt bán</th>
                            <th>Người thực hiện</th>
                            <th>Trạng thái</th>
                            <th>Ngày ký</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            @php($trim = $sale->carUnit?->trim)
                            <tr wire:key="sale-row-{{ $sale->id }}">
                                <td>
                                    <span class="c1-vehicle-tag" style="font-weight: 700; color: var(--c1-primary);">
                                        #HD-{{ str_pad((string) $sale->id, 3, '0', STR_PAD_LEFT) }}
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
                                        <div class="c1-cell-sub">Mã kho: {{ $sale->carUnit?->stock_code ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="c1-price-cell">
                                        {{ $sale->sold_price ? number_format($sale->sold_price, 0, ',', '.') . ' VNĐ' : 'Theo thỏa thuận' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="c1-cell-primary">{{ $sale->createdBy?->name ?? 'Nhân viên' }}</div>
                                </td>
                                <td>
                                    <span class="c1-pill c1-pill-green">Đã thanh toán đủ</span>
                                </td>
                                <td>
                                    <span class="c1-cell-sub">{{ optional($sale->sold_at)->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="c1-empty-cell" style="text-align: center; padding: 36px 16px; color: var(--c1-text-muted);">
                                    <i class="fa fa-file-text-o fa-2x mb-2 d-block" style="opacity: 0.5;"></i>
                                    @if ($search !== '')
                                        Không tìm thấy giao dịch nào phù hợp với từ khóa "{{ $search }}".
                                    @else
                                        Chưa có giao dịch bán hàng nào được ghi nhận.
                                    @endif
                                </td>
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
</div>
