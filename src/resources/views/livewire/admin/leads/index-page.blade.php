@php
    $statusLabels = [
        'new' => 'Mới',
        'contacted' => 'Đã liên hệ',
        'qualified' => 'Tiềm năng',
        'booked' => 'Đã đặt hẹn',
        'closed' => 'Chốt thành công',
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
        'new' => ['order' => 1, 'title' => 'Mới tiếp nhận', 'color' => 'var(--c1-primary)', 'pill' => 'c1-pill-green', 'context' => 'Quan tâm', 'fallback' => 'Liên hệ chung'],
        'consulting' => ['order' => 2, 'title' => 'Đang tư vấn / Lái thử', 'color' => '#4f46e5', 'pill' => 'c1-pill-blue', 'context' => 'Xe tư vấn', 'fallback' => 'Tư vấn dòng xe'],
        'negotiating' => ['order' => 3, 'title' => 'Thương thảo hợp đồng', 'color' => '#d97706', 'pill' => 'c1-pill-amber', 'context' => 'Chuẩn bị cọc', 'fallback' => 'Hợp đồng'],
        'closed' => ['order' => 4, 'title' => 'Chốt giao dịch', 'color' => '#16a34a', 'pill' => 'c1-pill-green', 'context' => 'Đã mua xe', 'fallback' => 'Giao dịch thành công'],
    ];
@endphp

<div class="c1-dash-wrapper">
    <div class="c1-page-header">
        <h1 class="c1-page-title">Khách hàng & Leads (CRM)</h1>
        <div class="c1-dash-quick-actions">
            <div style="display: inline-flex; border-radius: var(--c1-radius-card); border: 1px solid var(--c1-border-card); overflow: hidden; background: #fff;">
                <button
                    type="button"
                    wire:click="setViewMode('kanban')"
                    class="c1-btn c1-btn-sm {{ $viewMode === 'kanban' ? 'c1-btn-primary' : 'c1-btn-ghost' }}"
                    style="border-radius: 0; border: 0;"
                    title="Dạng phễu Kanban"
                >
                    <i class="fa fa-columns"></i>
                    <span>Phễu</span>
                </button>
                <button
                    type="button"
                    wire:click="setViewMode('table')"
                    class="c1-btn c1-btn-sm {{ $viewMode === 'table' ? 'c1-btn-primary' : 'c1-btn-ghost' }}"
                    style="border-radius: 0; border: 0;"
                    title="Dạng bảng danh sách"
                >
                    <i class="fa fa-list"></i>
                    <span>Bảng</span>
                </button>
            </div>
        </div>
    </div>

    <div class="c1-kpi-grid">
        <button type="button" wire:click="filterByStatus('new')" class="c1-kpi-card" style="text-align: left; width: 100%; cursor: pointer;">
            <div class="c1-kpi-head"><span class="c1-kpi-title">Lead mới</span><span class="c1-badge c1-badge-green">Mới</span></div>
            <div class="c1-kpi-body"><div class="c1-kpi-val-group"><span class="c1-kpi-value">{{ $stageCounts['new'] }}</span><span class="c1-kpi-unit">Khách</span></div></div>
        </button>
        <button type="button" wire:click="filterByStatus('contacted')" class="c1-kpi-card" style="text-align: left; width: 100%; cursor: pointer;">
            <div class="c1-kpi-head"><span class="c1-kpi-title">Đang tư vấn / Lái thử</span><span class="c1-badge" style="background:#e0e7ff; color:#3730a3;">Tư vấn</span></div>
            <div class="c1-kpi-body"><div class="c1-kpi-val-group"><span class="c1-kpi-value">{{ $stageCounts['consulting'] }}</span><span class="c1-kpi-unit">Khách</span></div></div>
        </button>
        <button type="button" wire:click="filterByStatus('booked')" class="c1-kpi-card" style="text-align: left; width: 100%; cursor: pointer;">
            <div class="c1-kpi-head"><span class="c1-kpi-title">Thương thảo hợp đồng</span><span class="c1-badge" style="background:#fef3c7; color:#92400e;">Đàm phán</span></div>
            <div class="c1-kpi-body"><div class="c1-kpi-val-group"><span class="c1-kpi-value">{{ $stageCounts['negotiating'] }}</span><span class="c1-kpi-unit">Hợp đồng</span></div></div>
        </button>
        <button type="button" wire:click="filterByStatus('closed')" class="c1-kpi-card" style="text-align: left; width: 100%; cursor: pointer;">
            <div class="c1-kpi-head"><span class="c1-kpi-title">Đã chốt xe</span><span class="c1-badge c1-badge-green">Thành công</span></div>
            <div class="c1-kpi-body"><div class="c1-kpi-val-group"><span class="c1-kpi-value">{{ $stageCounts['closed'] }}</span><span class="c1-kpi-unit">Giao dịch</span></div></div>
        </button>
    </div>

    <div class="c1-panel c1-table-panel" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="c1-filter-toolbar">
            <div class="c1-filter-search">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Tìm theo tên khách, SĐT, email...">
            </div>

            <select wire:model.live="status" class="c1-select" aria-label="Lọc trạng thái lead">
                <option value="">Tất cả trạng thái</option>
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
        <div class="text-muted small" wire:loading>Đang cập nhật danh sách...</div>
    </div>

    @if ($viewMode === 'kanban')
        <div class="c1-kanban-board">
            @foreach ($kanbanColumns as $columnKey => $column)
                <div class="c1-kanban-col" wire:key="lead-column-{{ $columnKey }}">
                    <div class="c1-kanban-col-head">
                        <span><strong style="color: {{ $column['color'] }};">{{ $column['order'] }}</strong> &nbsp;{{ $column['title'] }}</span>
                        <span class="c1-pill {{ $column['pill'] }}">{{ count($kanbanLeads[$columnKey]) }}</span>
                    </div>

                    @forelse ($kanbanLeads[$columnKey] as $lead)
                        @php
                            $contextTrim = $lead->carUnit?->trim ?? $lead->trim;
                            $contextName = trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' '));
                        @endphp
                        <a href="{{ route('admin.leads.show', $lead) }}" wire:navigate class="c1-kanban-card" wire:key="kanban-lead-{{ $lead->id }}">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div class="c1-user-avatar" style="width: 28px; height: 28px; font-size: 11px; background: {{ $column['color'] }};">
                                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div class="c1-cell-primary" style="font-size: 13px;">{{ $lead->name }}</div>
                                    <div class="c1-cell-sub" style="font-size: 11.5px;">{{ $lead->phone }}</div>
                                </div>
                            </div>
                            <div style="font-size: 12.5px; color: var(--c1-text-body); font-weight: 500;">
                                {{ $column['context'] }}: <strong style="color: {{ $columnKey === 'closed' ? '#16a34a' : 'var(--c1-text-heading)' }};">{{ $contextName ?: $column['fallback'] }}</strong>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--c1-text-muted); padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                                @switch($columnKey)
                                    @case('new')
                                        <span class="c1-vehicle-tag" style="font-size: 10.5px;">{{ ucfirst($lead->source ?? 'Web') }}</span>
                                        <span>{{ $lead->created_at?->diffForHumans() ?? 'Vừa xong' }}</span>
                                        @break
                                    @case('consulting')
                                        <span class="c1-pill c1-pill-blue" style="font-size: 10px;">{{ $lead->assignedTo?->name ?? 'Chưa gán NV' }}</span>
                                        <span>{{ $lead->notes_count }} ghi chú</span>
                                        @break
                                    @case('negotiating')
                                        <span class="c1-pill c1-pill-amber" style="font-size: 10px;">Lên hợp đồng</span>
                                        <span>{{ $lead->appointments_count }} lịch hẹn</span>
                                        @break
                                    @default
                                        <span class="c1-pill c1-pill-green" style="font-size: 10px;">Hoàn tất</span>
                                        <span>Đã bàn giao</span>
                                @endswitch
                            </div>
                        </a>
                    @empty
                        <div class="c1-empty-cell" style="padding: 24px 8px !important;">Không có lead trong giai đoạn này.</div>
                    @endforelse
                </div>
            @endforeach
        </div>
    @else
        <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
            <div class="c1-table-wrap">
                <table class="c1-table">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th>Nguồn</th>
                            <th>Mẫu xe quan tâm</th>
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
                            @endphp
                            <tr wire:key="lead-row-{{ $lead->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="c1-user-avatar" style="width: 30px; height: 30px; font-size: 12px;">{{ strtoupper(substr($lead->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="c1-cell-primary">{{ $lead->name }}</div>
                                            <div class="c1-cell-sub">{{ $lead->phone }}{{ $lead->email ? ' • ' . $lead->email : '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="c1-vehicle-tag">{{ ucfirst($lead->source ?? 'Web') }}</span></td>
                                <td><span class="c1-cell-primary">{{ $contextName ?: 'Liên hệ chung' }}</span></td>
                                <td>
                                    @if ($lead->status === 'new')
                                        <span class="c1-pill c1-pill-green">Mới</span>
                                    @elseif (in_array($lead->status, ['contacted', 'qualified'], true))
                                        <span class="c1-pill c1-pill-blue">Đang tư vấn</span>
                                    @elseif ($lead->status === 'booked')
                                        <span class="c1-pill c1-pill-amber">Lịch hẹn</span>
                                    @elseif ($lead->status === 'closed')
                                        <span class="c1-pill c1-pill-green">Chốt thành công</span>
                                    @else
                                        <span class="c1-pill c1-pill-gray">{{ strtoupper($lead->status) }}</span>
                                    @endif
                                </td>
                                <td><span>{{ $lead->assignedTo?->name ?? 'Chưa phân công' }}</span></td>
                                <td><span>{{ $lead->notes_count }} note / {{ $lead->appointments_count }} lịch</span></td>
                                <td class="text-right">
                                    <a href="{{ route('admin.leads.show', $lead) }}" wire:navigate class="c1-btn c1-btn-sm c1-btn-ghost">
                                        <i class="fa fa-eye"></i><span>Mở lead</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="c1-empty-cell">Chưa có lead nào phù hợp với bộ lọc.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px;">{{ $leads->links('admin.partials.pagination') }}</div>
        </div>
    @endif
</div>
