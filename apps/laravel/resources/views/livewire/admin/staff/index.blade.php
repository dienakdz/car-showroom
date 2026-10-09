<div class="c1-dash-wrapper">
    {{-- Feedback Alert --}}
    @if (($feedback['message'] ?? '') !== '')
        @php
            $isError = ($feedback['type'] ?? 'success') === 'error';
        @endphp
        <div class="c1-alert {{ $isError ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 18px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <div class="d-flex align-items-center gap-2">
                <i class="fa {{ $isError ? 'fa-exclamation-circle' : 'fa-check-circle' }}" aria-hidden="true"></i>
                <span>{{ $feedback['message'] }}</span>
            </div>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="c1-page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="c1-page-title">Quản lý Nhân viên & Phân quyền</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                Quản lý tài khoản đội ngũ showroom, thiết lập vai trò và phân quyền hạn vận hành hệ thống.
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <button
                type="button"
                wire:click="openRoleModal(1)"
                class="c1-btn c1-btn-secondary"
                style="height: 40px; padding: 0 16px; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;"
            >
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                <span>Phân quyền vai trò</span>
            </button>
            <button
                type="button"
                wire:click="openCreateModal"
                class="c1-btn c1-btn-primary"
                style="height: 40px; padding: 0 16px; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;"
            >
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Thêm nhân viên</span>
            </button>
        </div>
    </div>

    {{-- 4 Metric Cards --}}
    <div class="c1-kpi-grid">
        <button
            type="button"
            wire:click="resetFilters"
            class="c1-kpi-card {{ $roleFilter === '' && $statusFilter === '' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Tổng nhân sự</span>
                <span class="c1-pill-badge c1-pill-blue">Đội ngũ</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">{{ number_format($stats['total']) }}</span>
                    <span class="c1-kpi-unit">Tài khoản</span>
                </div>
            </div>
        </button>

        <button
            type="button"
            wire:click="$set('roleFilter', 'admin')"
            class="c1-kpi-card {{ $roleFilter === 'admin' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Quản trị viên</span>
                <span class="c1-pill-badge c1-pill-purple">Admin</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value" style="color: #6366f1;">{{ number_format($stats['admins']) }}</span>
                    <span class="c1-kpi-unit">Toàn quyền</span>
                </div>
            </div>
        </button>

        <button
            type="button"
            wire:click="$set('roleFilter', 'staff')"
            class="c1-kpi-card {{ $roleFilter === 'staff' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Nhân viên vận hành</span>
                <span class="c1-pill-badge c1-pill-green">Staff</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value text-success">{{ number_format($stats['staff']) }}</span>
                    <span class="c1-kpi-unit">Kinh doanh & Kho</span>
                </div>
            </div>
        </button>

        <button
            type="button"
            wire:click="$set('statusFilter', 'active')"
            class="c1-kpi-card {{ $statusFilter === 'active' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Đang hoạt động</span>
                <span class="c1-pill-badge c1-pill-amber">Khả dụng</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value" style="color: #f59e0b;">{{ number_format($stats['active']) }}</span>
                    <span class="c1-kpi-unit">/ {{ $stats['total'] }} sẵn sàng</span>
                </div>
            </div>
        </button>
    </div>

    {{-- Main Panel: Filter Toolbar & Data Table --}}
    <div class="c1-panel c1-table-panel" style="padding: 20px;">
        {{-- Toolbar --}}
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; margin-bottom: 20px;">
            {{-- Search Box --}}
            <div style="position: relative; width: 340px; max-width: 100%;">
                <i class="fa fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--c1-text-light); font-size: 13px;"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    class="form-control"
                    placeholder="Tìm theo tên, email, số điện thoại..."
                    style="padding-left: 34px; font-size: 13.5px; height: 40px; border-radius: 8px; border: 1px solid var(--c1-border-card); background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                >
            </div>

            {{-- Select Filters --}}
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                {{-- Role Select --}}
                <select
                    wire:model.live="roleFilter"
                    class="form-select"
                    style="width: 170px; font-size: 13.5px; height: 40px; border-radius: 8px; border: 1px solid var(--c1-border-card); background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                    aria-label="Lọc theo vai trò"
                >
                    <option value="">Tất cả vai trò</option>
                    <option value="admin">Quản trị viên (Admin)</option>
                    <option value="staff">Nhân viên (Staff)</option>
                </select>

                {{-- Status Select --}}
                <select
                    wire:model.live="statusFilter"
                    class="form-select"
                    style="width: 170px; font-size: 13.5px; height: 40px; border-radius: 8px; border: 1px solid var(--c1-border-card); background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                    aria-label="Lọc theo trạng thái"
                >
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Đang hoạt động</option>
                    <option value="inactive">Đang bị khóa</option>
                </select>

                {{-- Reset Button --}}
                @if ($search !== '' || $roleFilter !== '' || $statusFilter !== '')
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="c1-action-btn"
                        style="height: 40px; padding: 0 14px; font-size: 13px;"
                        title="Đặt lại bộ lọc"
                    >
                        <i class="fa fa-refresh me-1"></i> Đặt lại
                    </button>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="c1-table c1-staff-table">
                <thead>
                    <tr>
                        <th style="width: 22%;">Nhân viên</th>
                        <th style="width: 16%;">Liên hệ</th>
                        <th style="width: 13%; text-align: center;">Vai trò</th>
                        <th style="width: 21%;">Hiệu suất vận hành</th>
                        <th style="width: 12%; text-align: center;">Trạng thái</th>
                        <th style="width: 16%; text-align: center; white-space: nowrap;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staffList as $staff)
                        @php
                            $role = $staff->roles->first();
                            $roleName = $role?->name ?? 'staff';
                            $isAdmin = $roleName === 'admin';
                            $initials = mb_strtoupper(mb_substr($staff->name, 0, 1));
                            $isSelf = auth()->id() === $staff->id;

                            $gradients = [
                                'linear-gradient(135deg, #3b82f6, #1d4ed8)', // Blue
                                'linear-gradient(135deg, #8b5cf6, #6d28d9)', // Indigo
                                'linear-gradient(135deg, #10b981, #059669)', // Emerald
                                'linear-gradient(135deg, #f59e0b, #d97706)', // Amber
                                'linear-gradient(135deg, #ec4899, #be185d)', // Pink
                                'linear-gradient(135deg, #06b6d4, #0891b2)', // Cyan
                            ];
                            $gradient = $gradients[$staff->id % count($gradients)];
                        @endphp
                        <tr wire:key="staff-row-{{ $staff->id }}" style="{{ ! $staff->is_active ? 'opacity: 0.65;' : '' }}">
                            {{-- Staff Name & Avatar --}}
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="c1-staff-avatar" style="background: {{ $gradient }};">
                                        {{ $initials }}
                                    </div>
                                    <div style="min-width: 0; max-width: 160px;">
                                        <div class="c1-cell-primary" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; font-size: 13.5px;" title="{{ $staff->name }}">
                                            {{ $staff->name }}
                                            @if ($isSelf)
                                                <span class="badge bg-primary ms-1" style="font-size: 10px; padding: 2px 6px;">Tôi</span>
                                            @endif
                                        </div>
                                        <div class="c1-cell-sub" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 11.5px;" title="{{ $staff->email }}">
                                            {{ $staff->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td>
                                <div>
                                    <div style="font-size: 13px; font-weight: 500; color: var(--c1-text-heading);">
                                        <i class="fa fa-phone text-muted me-1" style="font-size: 11px;"></i>
                                        {{ $staff->phone ?: 'Chưa cập nhật' }}
                                    </div>
                                    <div class="c1-cell-sub" style="font-size: 11px; margin-top: 2px;">
                                        Tham gia: {{ $staff->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </td>

                            {{-- Role Badge --}}
                            <td style="text-align: center;">
                                @if ($isAdmin)
                                    <span class="c1-pill-badge c1-pill-purple" style="font-weight: 600;">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                        <span>Quản trị viên</span>
                                    </span>
                                @else
                                    <span class="c1-pill-badge c1-pill-blue" style="font-weight: 600;">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        <span>Nhân viên</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Work Performance Indicators --}}
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center;">
                                    <span class="c1-badge" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 11px; padding: 2px 7px;" title="Số xe đã chốt đơn bán thành công">
                                        <i class="fa fa-car me-1"></i>{{ $staff->sales_count }} xe bán
                                    </span>
                                    <span class="c1-badge" style="background: rgba(59, 130, 246, 0.1); color: #2563eb; font-size: 11px; padding: 2px 7px;" title="Số lịch hẹn tư vấn/lái thử đã phụ trách">
                                        <i class="fa fa-calendar-check-o me-1"></i>{{ $staff->handled_appointments_count }} hẹn
                                    </span>
                                    <span class="c1-badge" style="background: rgba(245, 158, 11, 0.1); color: #d97706; font-size: 11px; padding: 2px 7px;" title="Số khách tiềm năng (Leads) được phân công">
                                        <i class="fa fa-users me-1"></i>{{ $staff->assigned_leads_count }} leads
                                    </span>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td style="text-align: center;">
                                @if ($staff->is_active)
                                    <span class="c1-pill-badge c1-pill-green">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Hoạt động</span>
                                    </span>
                                @else
                                    <span class="c1-pill-badge c1-pill-slate">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                        <span>Bị khóa</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Row Actions --}}
                            <td style="text-align: center; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; gap: 5px;">
                                    {{-- Edit Button --}}
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $staff->id }})"
                                        class="c1-action-btn c1-action-btn-primary"
                                        style="height: 30px; padding: 0 9px; font-size: 12px;"
                                        title="Chỉnh sửa thông tin nhân viên"
                                    >
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        <span>Sửa</span>
                                    </button>

                                    {{-- Role Permissions Matrix Modal Button --}}
                                    @if ($role)
                                        <button
                                            type="button"
                                            wire:click="openRoleModal({{ $role->id }})"
                                            class="c1-action-btn c1-action-btn-icon c1-action-btn-secondary"
                                            style="width: 30px; height: 30px;"
                                            title="Xem ma trận quyền của vai trò {{ $role->name }}"
                                        >
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                        </button>
                                    @endif

                                    {{-- Lock / Unlock Button --}}
                                    @if (! $isSelf)
                                        <button
                                            type="button"
                                            wire:click="toggleStaffStatus({{ $staff->id }})"
                                            wire:loading.attr="disabled"
                                            class="c1-action-btn c1-action-btn-icon {{ $staff->is_active ? 'c1-action-btn-warning' : 'c1-action-btn-success' }}"
                                            style="width: 30px; height: 30px;"
                                            title="{{ $staff->is_active ? 'Tạm khóa tài khoản' : 'Mở khóa tài khoản' }}"
                                        >
                                            @if ($staff->is_active)
                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                            @else
                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                            @endif
                                        </button>

                                        {{-- Delete Button --}}
                                        <button
                                            type="button"
                                            wire:click="deleteStaff({{ $staff->id }})"
                                            wire:loading.attr="disabled"
                                            wire:confirm="Bạn có chắc chắn muốn xóa tài khoản nhân viên {{ $staff->name }}?"
                                            class="c1-action-btn c1-action-btn-icon c1-action-btn-danger"
                                            style="width: 30px; height: 30px;"
                                            title="Xóa vĩnh viễn tài khoản nhân viên"
                                        >
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px 20px;">
                                <div style="color: var(--c1-text-light); font-size: 36px; margin-bottom: 12px;">
                                    <i class="fa fa-users" style="opacity: 0.3;"></i>
                                </div>
                                <h4 style="color: var(--c1-text-heading); font-size: 16px; margin-bottom: 6px;">Không tìm thấy nhân viên nào</h4>
                                <p style="color: var(--c1-text-muted); font-size: 13.5px; margin: 0;">
                                    Thử thay đổi từ khóa tìm kiếm hoặc bấm Đặt lại bộ lọc.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($staffList->hasPages())
            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--c1-border-card);">
                {{ $staffList->links('admin.partials.pagination') }}
            </div>
        @endif
    </div>

    {{-- Modal 1: Create / Edit Staff Modal --}}
    @if ($showFormModal)
        <div
            class="modal fade show"
            style="display: block; background-color: rgba(15, 23, 42, 0.65); z-index: 1060; backdrop-filter: blur(2px);"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
        >
            <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
                <div class="modal-content" style="background-color: var(--c1-bg-card); border: 1px solid var(--c1-border-card); border-radius: 12px; box-shadow: var(--c1-shadow-md); color: var(--c1-text-heading); overflow: hidden;">
                    <div class="modal-header" style="border-bottom: 1px solid var(--c1-border-card); padding: 16px 20px;">
                        <h5 class="modal-title fw-bold" style="font-size: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-user-circle text-primary"></i>
                            <span>{{ $editingStaffId ? 'Chỉnh sửa tài khoản nhân viên' : 'Thêm mới tài khoản nhân viên' }}</span>
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeFormModal" aria-label="Đóng"></button>
                    </div>

                    <form wire:submit.prevent="saveStaff">
                        <div class="modal-body" style="padding: 20px;">
                            @error('form.general')
                                <div class="alert alert-danger py-2 px-3 mb-3 small">
                                    {{ $message }}
                                </div>
                            @enderror

                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="font-size: 13px;">Họ và tên <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    wire:model="form.name"
                                    class="form-control @error('form.name') is-invalid @enderror"
                                    placeholder="Ví dụ: Nguyễn Văn Hoàng"
                                    style="font-size: 13.5px; height: 38px; border-radius: 6px; background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                                >
                                @error('form.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Email & Phone --}}
                            <div class="row g-2 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold" style="font-size: 13px;">Địa chỉ Email <span class="text-danger">*</span></label>
                                    <input
                                        type="email"
                                        wire:model="form.email"
                                        class="form-control @error('form.email') is-invalid @enderror"
                                        placeholder="sales@showroom.test"
                                        style="font-size: 13.5px; height: 38px; border-radius: 6px; background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                                    >
                                    @error('form.email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold" style="font-size: 13px;">Số điện thoại</label>
                                    <input
                                        type="text"
                                        wire:model="form.phone"
                                        class="form-control @error('form.phone') is-invalid @enderror"
                                        placeholder="0912345678"
                                        style="font-size: 13.5px; height: 38px; border-radius: 6px; background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                                    >
                                    @error('form.phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="font-size: 13px;">
                                    Mật khẩu {{ $editingStaffId ? '(Để trống nếu không đổi)' : '' }}
                                    @if (! $editingStaffId) <span class="text-danger">*</span> @endif
                                </label>
                                <input
                                    type="password"
                                    wire:model="form.password"
                                    class="form-control @error('form.password') is-invalid @enderror"
                                    placeholder="{{ $editingStaffId ? '••••••••' : 'Tối thiểu 6 ký tự' }}"
                                    style="font-size: 13.5px; height: 38px; border-radius: 6px; background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                                >
                                @error('form.password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Role Selection --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="font-size: 13px;">Vai trò quyền hạn <span class="text-danger">*</span></label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    @foreach ($roles as $r)
                                        <label
                                            class="c1-role-option {{ (int)$form['role_id'] === (int)$r->id ? 'is-selected' : '' }}"
                                            style="padding: 12px; border-radius: 8px; border: 1.5px solid var(--c1-border-card); cursor: pointer; display: flex; flex-direction: column; gap: 4px; background: var(--c1-bg-page);"
                                        >
                                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                                <span class="fw-bold" style="font-size: 13px; color: var(--c1-text-heading);">
                                                    {{ $r->name === 'admin' ? 'Quản trị viên (Admin)' : 'Nhân viên (Staff)' }}
                                                </span>
                                                <input
                                                    type="radio"
                                                    wire:model="form.role_id"
                                                    value="{{ $r->id }}"
                                                    style="cursor: pointer;"
                                                >
                                            </div>
                                            <span style="font-size: 11.5px; color: var(--c1-text-muted);">
                                                {{ $r->description }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('form.role_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Status Toggle --}}
                            <div class="form-check form-switch pt-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="staffIsActiveSwitch"
                                    wire:model="form.is_active"
                                    style="cursor: pointer;"
                                >
                                <label class="form-check-label fw-bold ms-1" for="staffIsActiveSwitch" style="font-size: 13px; cursor: pointer;">
                                    Tài khoản đang hoạt động (Được phép đăng nhập)
                                </label>
                            </div>
                        </div>

                        <div class="modal-footer" style="border-top: 1px solid var(--c1-border-card); padding: 14px 20px; display: flex; justify-content: space-between;">
                            <button
                                type="button"
                                class="c1-action-btn"
                                wire:click="closeFormModal"
                            >
                                Hủy
                            </button>

                            <button
                                type="submit"
                                class="c1-btn c1-btn-primary"
                                style="height: 36px; padding: 0 18px; font-size: 13px; font-weight: 600;"
                                wire:loading.attr="disabled"
                            >
                                <i class="fa fa-check me-1"></i>
                                <span>{{ $editingStaffId ? 'Cập nhật tài khoản' : 'Tạo tài khoản' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal 2: Role Permissions Matrix Modal --}}
    @if ($showRoleModal && $selectedRole)
        <div
            class="modal fade show"
            style="display: block; background-color: rgba(15, 23, 42, 0.65); z-index: 1060; backdrop-filter: blur(2px);"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
        >
            <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
                <div class="modal-content" style="background-color: var(--c1-bg-card); border: 1px solid var(--c1-border-card); border-radius: 12px; box-shadow: var(--c1-shadow-md); color: var(--c1-text-heading); overflow: hidden;">
                    <div class="modal-header" style="border-bottom: 1px solid var(--c1-border-card); padding: 16px 20px;">
                        <h5 class="modal-title fw-bold" style="font-size: 16px; display: flex; align-items: center; gap: 8px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                            <span>Phân quyền vai trò: <strong>{{ $selectedRole->name === 'admin' ? 'Quản trị viên (Admin)' : 'Nhân viên (Staff)' }}</strong></span>
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeRoleModal" aria-label="Đóng"></button>
                    </div>

                    <form wire:submit.prevent="saveRolePermissions">
                        <div class="modal-body" style="padding: 20px; max-height: 480px; overflow-y: auto;">
                            {{-- Role Selector Tabs --}}
                            <div style="display: flex; gap: 8px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--c1-border-card);">
                                @foreach ($roles as $r)
                                    <button
                                        type="button"
                                        wire:click="openRoleModal({{ $r->id }})"
                                        class="c1-action-btn {{ $selectedRole->id === $r->id ? 'c1-action-btn-primary active' : '' }}"
                                        style="padding: 6px 14px; font-size: 12.5px; font-weight: 600; border-radius: 6px;"
                                    >
                                        <i class="fa {{ $r->name === 'admin' ? 'fa-shield' : 'fa-user' }} me-1"></i>
                                        <span>{{ $r->name === 'admin' ? 'Quản trị viên (Admin)' : 'Nhân viên (Staff)' }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <p style="font-size: 12.5px; color: var(--c1-text-muted); margin-bottom: 16px;">
                                Đánh dấu các quyền hạn cho phép vai trò <strong>{{ $selectedRole->name === 'admin' ? 'Admin' : 'Staff' }}</strong> thao tác trên hệ thống quản trị:
                            </p>

                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach ($permissions as $perm)
                                    <label
                                        class="c1-perm-item"
                                        style="display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--c1-border-card); background: var(--c1-bg-page); cursor: pointer;"
                                    >
                                        <input
                                            type="checkbox"
                                            wire:model="selectedRolePermissions"
                                            value="{{ $perm->id }}"
                                            style="margin-top: 3px; cursor: pointer;"
                                        >
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-size: 13px; font-weight: 600; color: var(--c1-text-heading);">
                                                <code>{{ $perm->name }}</code>
                                            </div>
                                            <div style="font-size: 12px; color: var(--c1-text-muted); margin-top: 2px;">
                                                {{ $perm->description }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer" style="border-top: 1px solid var(--c1-border-card); padding: 14px 20px; display: flex; justify-content: space-between;">
                            <button
                                type="button"
                                class="c1-action-btn"
                                wire:click="closeRoleModal"
                            >
                                Đóng
                            </button>

                            <button
                                type="submit"
                                class="c1-btn c1-btn-primary"
                                style="height: 36px; padding: 0 18px; font-size: 13px; font-weight: 600;"
                                wire:loading.attr="disabled"
                            >
                                <i class="fa fa-save me-1"></i>
                                <span>Lưu quyền hạn vai trò</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .c1-staff-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: #ffffff;
            font-weight: 700;
            font-size: 13.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        }
        .c1-staff-table th,
        .c1-staff-table td {
            padding: 13px 12px;
        }
        .c1-staff-table th:first-child,
        .c1-staff-table td:first-child {
            padding-left: 20px;
        }
        .c1-staff-table th:last-child,
        .c1-staff-table td:last-child {
            padding-right: 20px;
        }
        .c1-role-option.is-selected {
            border-color: var(--c1-primary) !important;
            background-color: rgba(59, 130, 246, 0.05) !important;
        }
        [data-theme="dark"] .c1-role-option.is-selected {
            background-color: rgba(59, 130, 246, 0.15) !important;
        }
        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
</div>
