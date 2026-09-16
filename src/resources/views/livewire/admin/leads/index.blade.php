@php
    $statusLabels = [
        'new' => 'Mới tiếp nhận',
        'contacted' => 'Đang tư vấn',
        'qualified' => 'Khách tiềm năng',
        'booked' => 'Đặt hẹn / Thương thảo',
        'closed' => 'Chốt giao dịch',
        'lost' => 'Đã hủy',
    ];
    $sourceLabels = [
        'unit_detail' => 'Trang chi tiết xe',
        'trim_page' => 'Trang phiên bản',
        'finance' => 'Hỗ trợ trả góp',
        'trade_in' => 'Thu cũ đổi mới',
        'contact' => 'Form liên hệ',
    ];
    $kanbanColumns = [
        'new' => ['order' => 1, 'title' => 'Mới tiếp nhận', 'color' => '#2563eb', 'pill' => 'c1-pill-blue', 'context' => 'Quan tâm', 'fallback' => 'Liên hệ chung'],
        'consulting' => ['order' => 2, 'title' => 'Đang tư vấn / Lái thử', 'color' => '#4f46e5', 'pill' => 'c1-pill-indigo', 'context' => 'Xe tư vấn', 'fallback' => 'Tư vấn dòng xe'],
        'negotiating' => ['order' => 3, 'title' => 'Thương thảo hợp đồng', 'color' => '#d97706', 'pill' => 'c1-pill-amber', 'context' => 'Chuẩn bị cọc', 'fallback' => 'Hợp đồng'],
        'closed' => ['order' => 4, 'title' => 'Chốt giao dịch', 'color' => '#16a34a', 'pill' => 'c1-pill-green', 'context' => 'Đã mua xe', 'fallback' => 'Giao dịch thành công'],
    ];
@endphp

<div class="c1-dash-wrapper">
    {{-- Header --}}
    <div class="c1-page-header">
        <div>
            <h1 class="c1-page-title">Khách hàng & Leads (CRM)</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                Quản lý phễu khách hàng tiềm năng, theo dõi tiến độ tư vấn và phân công chuyên viên.
            </div>
        </div>
        <div class="c1-dash-quick-actions">
            <div class="c1-view-switcher">
                <button
                    type="button"
                    wire:click="setViewMode('kanban')"
                    class="c1-view-switcher-btn {{ $viewMode === 'kanban' ? 'active' : '' }}"
                    title="Dạng phễu Kanban"
                >
                    <i class="fa fa-columns"></i>
                    <span>Phễu (Kanban)</span>
                </button>
                <button
                    type="button"
                    wire:click="setViewMode('table')"
                    class="c1-view-switcher-btn {{ $viewMode === 'table' ? 'active' : '' }}"
                    title="Dạng bảng danh sách"
                >
                    <i class="fa fa-list"></i>
                    <span>Bảng danh sách</span>
                </button>
            </div>
        </div>
    </div>

    {{-- KPI Cards Filter Bar --}}
    <div class="c1-kpi-grid mb-4">
        <button type="button" wire:click="filterByStatus('new')" class="c1-kpi-card {{ $status === 'new' ? 'active' : '' }}" style="text-align: left; width: 100%; cursor: pointer;">
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Mới tiếp nhận</span>
                <span class="c1-badge" style="background: #eff6ff; color: #2563eb;">Mới</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">{{ $stageCounts['new'] }}</span>
                    <span class="c1-kpi-unit">Khách</span>
                </div>
            </div>
        </button>
        <button type="button" wire:click="filterByStatus('consulting')" class="c1-kpi-card {{ $status === 'consulting' ? 'active' : '' }}" style="text-align: left; width: 100%; cursor: pointer;">
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Đang tư vấn / Lái thử</span>
                <span class="c1-badge" style="background: #e0e7ff; color: #3730a3;">Tư vấn</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">{{ $stageCounts['consulting'] }}</span>
                    <span class="c1-kpi-unit">Khách</span>
                </div>
            </div>
        </button>
        <button type="button" wire:click="filterByStatus('booked')" class="c1-kpi-card {{ $status === 'booked' ? 'active' : '' }}" style="text-align: left; width: 100%; cursor: pointer;">
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Thương thảo hợp đồng</span>
                <span class="c1-badge" style="background: #fef3c7; color: #92400e;">Đàm phán</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">{{ $stageCounts['negotiating'] }}</span>
                    <span class="c1-kpi-unit">Hợp đồng</span>
                </div>
            </div>
        </button>
        <button type="button" wire:click="filterByStatus('closed')" class="c1-kpi-card {{ $status === 'closed' ? 'active' : '' }}" style="text-align: left; width: 100%; cursor: pointer;">
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Đã chốt xe</span>
                <span class="c1-badge" style="background: #dcfce7; color: #166534;">Thành công</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">{{ $stageCounts['closed'] }}</span>
                    <span class="c1-kpi-unit">Giao dịch</span>
                </div>
            </div>
        </button>
    </div>

    {{-- Filter Toolbar --}}
    <div class="c1-panel c1-table-panel" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="c1-filter-toolbar">
            <div class="c1-filter-search">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Tìm theo tên khách hàng, SĐT, email...">
            </div>

            <select wire:model.live="status" class="c1-select" aria-label="Lọc trạng thái lead">
                <option value="">Tất cả trạng thái</option>
                <option value="consulting">Đang tư vấn / Lái thử (Cả 2 bước)</option>
                @foreach ($statusOptions as $statusOption)
                    <option value="{{ $statusOption }}">{{ $statusLabels[$statusOption] }}</option>
                @endforeach
            </select>

            <select wire:model.live="source" class="c1-select" aria-label="Lọc nguồn lead">
                <option value="">Tất cả nguồn</option>
                @foreach ($sourceOptions as $sourceOption)
                    <option value="{{ $sourceOption }}">{{ $sourceLabels[$sourceOption] }}</option>
                @endforeach
            </select>

            <select wire:model.live="assignedTo" class="c1-select" aria-label="Lọc nhân viên phụ trách">
                <option value="">Tất cả nhân viên</option>
                @foreach ($staffUsers as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                @endforeach
            </select>

            <button type="button" wire:click="resetFilters" class="c1-btn c1-btn-secondary" style="height: 38px;">
                <i class="fa fa-refresh"></i>
                <span>Đặt lại</span>
            </button>
        </div>
        <div class="text-muted small mt-2" wire:loading>Đang cập nhật danh sách...</div>
    </div>

    {{-- VIEW MODE: KANBAN --}}
    @if ($viewMode === 'kanban')
        <div class="c1-kanban-board">
            @foreach ($kanbanColumns as $columnKey => $column)
                <div class="c1-kanban-col" wire:key="lead-column-{{ $columnKey }}">
                    <div class="c1-kanban-col-head">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: {{ $column['color'] }}; color: #fff; font-size: 11px; font-weight: 700;">
                                {{ $column['order'] }}
                            </span>
                            <span>{{ $column['title'] }}</span>
                        </span>
                        <span class="c1-pill {{ $column['pill'] }}">{{ count($kanbanLeads[$columnKey]) }}</span>
                    </div>

                    @forelse ($kanbanLeads[$columnKey] as $lead)
                        @php
                            $contextTrim = $lead->carUnit?->trim ?? $lead->trim;
                            $contextName = trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' '));
                            $contextMedia = $lead->carUnit?->primaryMedia ?? $contextTrim?->carUnits?->first()?->primaryMedia;
                            $rawPath = $contextMedia?->path_or_url;
                            $thumbUrl = filled($rawPath)
                                ? ((str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) ? $rawPath : asset(ltrim($rawPath, '/')))
                                : null;
                        @endphp
                        <a href="{{ route('admin.leads.show', $lead) }}" wire:navigate class="c1-kanban-card" wire:key="kanban-lead-{{ $lead->id }}">
                            {{-- Header thẻ: Avatar & Tên --}}
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="c1-user-avatar" style="width: 32px; height: 32px; font-size: 12px; background: {{ $column['color'] }}; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">
                                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div class="c1-cell-primary" style="font-size: 13.5px; font-weight: 600;">{{ $lead->name }}</div>
                                    <div class="c1-cell-sub" style="font-size: 12px; color: var(--c1-text-muted);">{{ $lead->phone }}</div>
                                </div>
                                @if ($lead->carUnit)
                                    <span class="c1-vehicle-tag" style="font-size: 10px; background: #e0f2fe; color: #0369a1;">#{{ $lead->carUnit->stock_code }}</span>
                                @endif
                            </div>

                            {{-- Context xe quan tâm --}}
                            <div style="display: flex; align-items: center; gap: 8px; padding: 6px 8px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px;">
                                @if ($thumbUrl)
                                    <img src="{{ $thumbUrl }}" alt="Thumb" style="width: 38px; height: 26px; object-fit: cover; border-radius: 4px; flex-shrink: 0;">
                                @else
                                    <span style="width: 38px; height: 26px; border-radius: 4px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #94a3b8; flex-shrink: 0;">
                                        <i class="fa fa-car"></i>
                                    </span>
                                @endif
                                <div style="font-size: 12px; color: var(--c1-text-heading); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $contextName ?: $column['fallback'] }}
                                </div>
                            </div>

                            {{-- Footer thẻ: Phân công & Ghi chú --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--c1-text-muted); padding-top: 6px; border-top: 1px dashed #e2e8f0;">
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <i class="fa fa-user-circle" style="color: #94a3b8;"></i>
                                    <span>{{ $lead->assignedTo?->name ?? 'Chưa gán NV' }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if ($lead->notes_count > 0)
                                        <span title="{{ $lead->notes_count }} ghi chú"><i class="fa fa-comment-o"></i> {{ $lead->notes_count }}</span>
                                    @endif
                                    @if ($lead->appointments_count > 0)
                                        <span title="{{ $lead->appointments_count }} lịch hẹn" style="color: #d97706;"><i class="fa fa-calendar-check-o"></i> {{ $lead->appointments_count }}</span>
                                    @endif
                                    <span>{{ $lead->created_at?->diffForHumans(null, true) ?? 'Mới' }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="c1-empty-cell" style="padding: 32px 12px !important; color: #94a3b8; text-align: center; font-size: 12.5px;">
                            Chưa có lead trong giai đoạn này.
                        </div>
                    @endforelse
                </div>
            @endforeach
        </div>
    @else
        {{-- VIEW MODE: TABLE --}}
        <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
            <div class="c1-table-wrap">
                <table class="c1-table">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th>Nguồn</th>
                            <th>Xe quan tâm</th>
                            <th>Trạng thái</th>
                            <th>Chuyên viên phụ trách</th>
                            <th>Ghi chú / Lịch</th>
                            <th class="text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leads as $lead)
                            @php
                                $contextTrim = $lead->carUnit?->trim ?? $lead->trim;
                                $contextName = trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' '));
                                $contextMedia = $lead->carUnit?->primaryMedia ?? $contextTrim?->carUnits?->first()?->primaryMedia;
                                $rawPath = $contextMedia?->path_or_url;
                                $thumbUrl = filled($rawPath)
                                    ? ((str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) ? $rawPath : asset(ltrim($rawPath, '/')))
                                    : null;
                            @endphp
                            <tr wire:key="lead-row-{{ $lead->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="c1-user-avatar" style="width: 32px; height: 32px; font-size: 12px; background: #2563eb; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                            {{ strtoupper(substr($lead->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="c1-cell-primary">{{ $lead->name }}</div>
                                            <div class="c1-cell-sub">{{ $lead->phone }}{{ $lead->email ? ' • ' . $lead->email : '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="c1-vehicle-tag">{{ $sourceLabels[$lead->source] ?? ucfirst($lead->source ?? 'Web') }}</span></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        @if ($thumbUrl)
                                            <img src="{{ $thumbUrl }}" alt="Thumb" style="width: 40px; height: 26px; object-fit: cover; border-radius: 4px; flex-shrink: 0;">
                                        @endif
                                        <div>
                                            <span class="c1-cell-primary">{{ $contextName ?: 'Liên hệ chung' }}</span>
                                            @if ($lead->carUnit)
                                                <div class="c1-cell-sub">Mã kho: <strong>#{{ $lead->carUnit->stock_code }}</strong></div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($lead->status === 'new')
                                        <span class="c1-pill c1-pill-blue">Mới tiếp nhận</span>
                                    @elseif (in_array($lead->status, ['contacted', 'qualified'], true))
                                        <span class="c1-pill c1-pill-indigo">{{ $statusLabels[$lead->status] ?? 'Đang tư vấn' }}</span>
                                    @elseif ($lead->status === 'booked')
                                        <span class="c1-pill c1-pill-amber">Đặt hẹn / Đàm phán</span>
                                    @elseif ($lead->status === 'closed')
                                        <span class="c1-pill c1-pill-green">Chốt thành công</span>
                                    @else
                                        <span class="c1-pill c1-pill-gray">{{ $statusLabels[$lead->status] ?? strtoupper($lead->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 13px; color: {{ $lead->assignedTo ? 'var(--c1-text-heading)' : 'var(--c1-text-muted)' }}; font-weight: {{ $lead->assignedTo ? '500' : '400' }};">
                                        {{ $lead->assignedTo?->name ?? 'Chưa phân công' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 12.5px; color: var(--c1-text-body);">
                                        <span><i class="fa fa-comment-o me-1"></i>{{ $lead->notes_count }} note</span>
                                        <span class="mx-1">•</span>
                                        <span><i class="fa fa-calendar-check-o me-1 text-warning"></i>{{ $lead->appointments_count }} lịch</span>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.leads.show', $lead) }}" wire:navigate class="c1-action-btn c1-action-btn-primary" title="Xem hồ sơ chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <span>Chi tiết</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="c1-empty-cell">Chưa có lead nào phù hợp với bộ lọc hiện tại.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $leads->links('admin.partials.pagination') }}
            </div>
        </div>
    @endif
</div>
