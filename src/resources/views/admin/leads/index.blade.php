@extends('admin.layouts.app')

@section('title', 'Khách hàng & Leads (CRM) | Admin')

@section('admin-content')
    <div class="c1-dash-wrapper">
        {{-- Page Header --}}
        <div class="c1-page-header">
            <h1 class="c1-page-title">Khách hàng & Leads (CRM)</h1>
            <div class="c1-dash-quick-actions">
                <div style="display: inline-flex; border-radius: var(--c1-radius-card); border: 1px solid var(--c1-border-card); overflow: hidden; background: #fff;">
                    <a href="{{ route('admin.leads.index', array_merge(request()->query(), ['view' => 'kanban'])) }}"
                       class="c1-btn c1-btn-sm {{ $viewMode === 'kanban' ? 'c1-btn-primary' : 'c1-btn-ghost' }}"
                       style="border-radius: 0; border: 0;"
                       title="Dạng phễu Kanban">
                        <i class="fa fa-columns"></i>
                        <span>Phễu</span>
                    </a>
                    <a href="{{ route('admin.leads.index', array_merge(request()->query(), ['view' => 'table'])) }}"
                       class="c1-btn c1-btn-sm {{ $viewMode === 'table' ? 'c1-btn-primary' : 'c1-btn-ghost' }}"
                       style="border-radius: 0; border: 0;"
                       title="Dạng bảng danh sách">
                        <i class="fa fa-list"></i>
                        <span>Bảng</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Pipeline Stage Metric Cards --}}
        <div class="c1-kpi-grid">
            <a href="{{ route('admin.leads.index', ['status' => 'new', 'view' => $viewMode]) }}" class="c1-kpi-card" style="text-decoration: none;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Lead mới</span>
                    <span class="c1-badge c1-badge-green">Mới</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $stageCounts['new'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Khách</span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.leads.index', ['status' => 'contacted', 'view' => $viewMode]) }}" class="c1-kpi-card" style="text-decoration: none;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Đang tư vấn / Lái thử</span>
                    <span class="c1-badge" style="background:#e0e7ff; color:#3730a3;">Tư vấn</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $stageCounts['consulting'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Khách</span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.leads.index', ['status' => 'booked', 'view' => $viewMode]) }}" class="c1-kpi-card" style="text-decoration: none;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Thương thảo hợp đồng</span>
                    <span class="c1-badge" style="background:#fef3c7; color:#92400e;">Đàm phán</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $stageCounts['negotiating'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Hợp đồng</span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.leads.index', ['status' => 'closed', 'view' => $viewMode]) }}" class="c1-kpi-card" style="text-decoration: none;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Đã chốt xe</span>
                    <span class="c1-badge c1-badge-green">Thành công</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $stageCounts['closed'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Giao dịch</span>
                    </div>
                </div>
            </a>
        </div>

        @if ($viewMode === 'kanban')
            {{-- Kanban Pipeline View (Matches concept_crm_leads.jpg) --}}
            <div class="c1-kanban-board">
                {{-- Column 1: Mới tiếp nhận --}}
                <div class="c1-kanban-col">
                    <div class="c1-kanban-col-head">
                        <span><strong style="color: var(--c1-primary);">1</strong> &nbsp;Mới tiếp nhận</span>
                        <span class="c1-pill c1-pill-green">{{ count($kanbanLeads['new'] ?? []) }}</span>
                    </div>
                    @forelse ($kanbanLeads['new'] as $lead)
                        @php($contextTrim = $lead->carUnit?->trim ?? $lead->trim)
                        <a href="{{ route('admin.leads.show', $lead) }}" class="c1-kanban-card">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div class="c1-user-avatar" style="width: 28px; height: 28px; font-size: 11px;">
                                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div class="c1-cell-primary" style="font-size: 13px;">{{ $lead->name }}</div>
                                    <div class="c1-cell-sub" style="font-size: 11.5px;">{{ $lead->phone }}</div>
                                </div>
                            </div>
                            <div style="font-size: 12.5px; color: var(--c1-text-body); font-weight: 500;">
                                Quan tâm: <strong style="color: var(--c1-text-heading);">{{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Liên hệ chung' }}</strong>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--c1-text-muted); padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                                <span class="c1-vehicle-tag" style="font-size: 10.5px;">{{ ucfirst($lead->source ?? 'Web') }}</span>
                                <span>{{ $lead->created_at?->diffForHumans() ?? 'Vừa xong' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="c1-empty-cell" style="padding: 24px 8px !important;">Không có lead mới.</div>
                    @endforelse
                </div>

                {{-- Column 2: Đang tư vấn / Lái thử --}}
                <div class="c1-kanban-col">
                    <div class="c1-kanban-col-head">
                        <span><strong style="color: #4f46e5;">2</strong> &nbsp;Đang tư vấn / Lái thử</span>
                        <span class="c1-pill c1-pill-blue">{{ count($kanbanLeads['consulting'] ?? []) }}</span>
                    </div>
                    @forelse ($kanbanLeads['consulting'] as $lead)
                        @php($contextTrim = $lead->carUnit?->trim ?? $lead->trim)
                        <a href="{{ route('admin.leads.show', $lead) }}" class="c1-kanban-card">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div class="c1-user-avatar" style="width: 28px; height: 28px; font-size: 11px; background: #4f46e5;">
                                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div class="c1-cell-primary" style="font-size: 13px;">{{ $lead->name }}</div>
                                    <div class="c1-cell-sub" style="font-size: 11.5px;">{{ $lead->phone }}</div>
                                </div>
                            </div>
                            <div style="font-size: 12.5px; color: var(--c1-text-body); font-weight: 500;">
                                Xe tư vấn: <strong style="color: var(--c1-text-heading);">{{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Tư vấn dòng xe' }}</strong>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--c1-text-muted); padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                                <span class="c1-pill c1-pill-blue" style="font-size: 10px;">{{ $lead->assignedTo?->name ?? 'Chưa gán NV' }}</span>
                                <span>{{ $lead->notes_count }} ghi chú</span>
                            </div>
                        </a>
                    @empty
                        <div class="c1-empty-cell" style="padding: 24px 8px !important;">Chưa có khách trong giai đoạn này.</div>
                    @endforelse
                </div>

                {{-- Column 3: Thương thảo hợp đồng --}}
                <div class="c1-kanban-col">
                    <div class="c1-kanban-col-head">
                        <span><strong style="color: #d97706;">3</strong> &nbsp;Thương thảo hợp đồng</span>
                        <span class="c1-pill c1-pill-amber">{{ count($kanbanLeads['negotiating'] ?? []) }}</span>
                    </div>
                    @forelse ($kanbanLeads['negotiating'] as $lead)
                        @php($contextTrim = $lead->carUnit?->trim ?? $lead->trim)
                        <a href="{{ route('admin.leads.show', $lead) }}" class="c1-kanban-card">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div class="c1-user-avatar" style="width: 28px; height: 28px; font-size: 11px; background: #d97706;">
                                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div class="c1-cell-primary" style="font-size: 13px;">{{ $lead->name }}</div>
                                    <div class="c1-cell-sub" style="font-size: 11.5px;">{{ $lead->phone }}</div>
                                </div>
                            </div>
                            <div style="font-size: 12.5px; color: var(--c1-text-body); font-weight: 500;">
                                Chuẩn bị cọc: <strong style="color: var(--c1-text-heading);">{{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Hợp đồng' }}</strong>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--c1-text-muted); padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                                <span class="c1-pill c1-pill-amber" style="font-size: 10px;">Lên hợp đồng</span>
                                <span>{{ $lead->appointments_count }} lịch hẹn</span>
                            </div>
                        </a>
                    @empty
                        <div class="c1-empty-cell" style="padding: 24px 8px !important;">Chưa có khách thương thảo.</div>
                    @endforelse
                </div>

                {{-- Column 4: Chốt giao dịch --}}
                <div class="c1-kanban-col">
                    <div class="c1-kanban-col-head">
                        <span><strong style="color: #16a34a;">4</strong> &nbsp;Chốt giao dịch</span>
                        <span class="c1-pill c1-pill-green">{{ count($kanbanLeads['closed'] ?? []) }}</span>
                    </div>
                    @forelse ($kanbanLeads['closed'] as $lead)
                        @php($contextTrim = $lead->carUnit?->trim ?? $lead->trim)
                        <a href="{{ route('admin.leads.show', $lead) }}" class="c1-kanban-card">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div class="c1-user-avatar" style="width: 28px; height: 28px; font-size: 11px; background: #16a34a;">
                                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div class="c1-cell-primary" style="font-size: 13px;">{{ $lead->name }}</div>
                                    <div class="c1-cell-sub" style="font-size: 11.5px;">{{ $lead->phone }}</div>
                                </div>
                            </div>
                            <div style="font-size: 12.5px; color: var(--c1-text-body); font-weight: 500;">
                                Đã mua xe: <strong style="color: #16a34a;">{{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Giao dịch thành công' }}</strong>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--c1-text-muted); padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                                <span class="c1-pill c1-pill-green" style="font-size: 10px;">Hoàn tất</span>
                                <span>Đã bàn giao</span>
                            </div>
                        </a>
                    @empty
                        <div class="c1-empty-cell" style="padding: 24px 8px !important;">Chưa có giao dịch chốt.</div>
                    @endforelse
                </div>
            </div>
        @else
            {{-- Table View --}}
            <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
                <form method="GET" action="{{ route('admin.leads.index') }}" class="c1-filter-toolbar">
                    <input type="hidden" name="view" value="table">
                    <div class="c1-filter-search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Tìm theo tên khách, SĐT, email...">
                    </div>

                    <select name="status" class="c1-select">
                        <option value="">Tất cả trạng thái</option>
                        @foreach (['new' => 'Mới', 'contacted' => 'Đã liên hệ', 'qualified' => 'Tiềm năng', 'booked' => 'Đã đặt hẹn', 'closed' => 'Chốt thành công', 'lost' => 'Đã hủy'] as $key => $lbl)
                            <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $lbl }}</option>
                        @endforeach
                    </select>

                    <select name="source" class="c1-select">
                        <option value="">Tất cả nguồn</option>
                        @foreach (['unit_detail' => 'Trang chi tiết xe', 'trim_page' => 'Trang phiên bản', 'finance' => 'Hỗ trợ trả góp', 'trade_in' => 'Thu cũ đổi mới', 'contact' => 'Form liên hệ'] as $srcKey => $srcLabel)
                            <option value="{{ $srcKey }}" @selected($filters['source'] === $srcKey)>{{ $srcLabel }}</option>
                        @endforeach
                    </select>

                    <select name="assigned_to" class="c1-select">
                        <option value="">Tất cả nhân viên</option>
                        @foreach ($staffUsers as $staff)
                            <option value="{{ $staff->id }}" @selected($filters['assigned_to'] === $staff->id)>{{ $staff->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="c1-btn c1-btn-secondary" style="height: 38px;">
                        <i class="fa fa-filter"></i>
                        <span>Lọc</span>
                    </button>
                </form>

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
                                @php($contextTrim = $lead->carUnit?->trim ?? $lead->trim)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="c1-user-avatar" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ strtoupper(substr($lead->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="c1-cell-primary">{{ $lead->name }}</div>
                                                <div class="c1-cell-sub">{{ $lead->phone }}{{ $lead->email ? ' • ' . $lead->email : '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="c1-vehicle-tag">{{ ucfirst($lead->source ?? 'Web') }}</span></td>
                                    <td>
                                        <span class="c1-cell-primary">
                                            {{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Liên hệ chung' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($lead->status === 'new')
                                            <span class="c1-pill c1-pill-green">Mới</span>
                                        @elseif (in_array($lead->status, ['contacted', 'qualified']))
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
                                        <a href="{{ route('admin.leads.show', $lead) }}" class="c1-btn c1-btn-sm c1-btn-ghost">
                                            <i class="fa fa-eye"></i>
                                            <span>Mở lead</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="c1-empty-cell">Chưa có lead nào trong hệ thống.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 16px;">
                    {{ $leads->links('admin.partials.pagination') }}
                </div>
            </div>
        @endif
    </div>
@endsection

