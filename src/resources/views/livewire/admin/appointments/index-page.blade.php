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
                <h1 class="c1-page-title">Lịch hẹn xem xe & Lái thử</h1>
                <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                    Điều phối lịch lái thử, đón tiếp khách hàng và phân công chuyên viên tư vấn.
                </div>
            </div>
            <div class="c1-dash-quick-actions">
                <a href="{{ route('admin.appointments.create') }}" wire:navigate class="c1-btn c1-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Đặt lịch hẹn mới</span>
                </a>
            </div>
        </div>

        {{-- 4 Appointments Metric Cards with Reactive Filtering --}}
        <div class="c1-kpi-grid">
            <div
                wire:click="resetFilters"
                class="c1-kpi-card {{ $status === '' ? 'is-active' : '' }}"
                style="cursor: pointer; user-select: none;"
                title="Bấm để xem tất cả lịch hẹn"
            >
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Tổng lịch hẹn</span>
                    <span class="c1-badge" style="background:#e2e8f0; color:#334155;">Tất cả</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $appointmentCounts['all'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Lịch hẹn</span>
                    </div>
                </div>
            </div>

            <div
                class="c1-kpi-card"
                style="user-select: none;"
            >
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Lịch hôm nay</span>
                    <span class="c1-badge c1-badge-green">Hôm nay</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $appointmentCounts['today'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Lịch hẹn</span>
                    </div>
                </div>
            </div>

            <div
                wire:click="filterByStatus('pending')"
                class="c1-kpi-card {{ $status === 'pending' ? 'is-active' : '' }}"
                style="cursor: pointer; user-select: none; {{ $status === 'pending' ? 'border: 2px solid #f59e0b;' : '' }}"
                title="Bấm để lọc lịch chờ xác nhận"
            >
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Chờ xác nhận</span>
                    <span class="c1-badge" style="background:#fef3c7; color:#92400e;">Chờ duyệt</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $appointmentCounts['pending'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Cần xử lý</span>
                    </div>
                </div>
            </div>

            <div
                wire:click="filterByStatus('done')"
                class="c1-kpi-card {{ $status === 'done' ? 'is-active' : '' }}"
                style="cursor: pointer; user-select: none; {{ $status === 'done' ? 'border: 2px solid #10b981;' : '' }}"
                title="Bấm để lọc lịch đã hoàn tất"
            >
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Đã hoàn tất</span>
                    <span class="c1-badge c1-badge-green">Xong</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $appointmentCounts['done'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Đã tiếp đón</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Appointments Table Panel --}}
        <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
            <div class="c1-filter-toolbar" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <div style="flex: 1; min-width: 220px;">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="c1-input"
                        placeholder="Tìm kiếm khách hàng, SĐT, ghi chú..."
                        style="height: 38px; width: 100%;"
                        aria-label="Tìm kiếm lịch hẹn"
                    >
                </div>

                <select wire:model.live="status" class="c1-select" style="min-width: 170px; height: 38px;" aria-label="Lọc trạng thái">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statusOptions as $stKey => $stLbl)
                        <option value="{{ $stKey }}">{{ $stLbl }}</option>
                    @endforeach
                </select>

                <select wire:model.live="handledBy" class="c1-select" style="min-width: 200px; height: 38px;" aria-label="Lọc chuyên viên">
                    <option value="0">Tất cả chuyên viên đón tiếp</option>
                    @foreach ($staffUsers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>

                @if ($search !== '' || $status !== '' || $handledBy > 0)
                    <button type="button" wire:click="resetFilters" class="c1-btn c1-btn-secondary" style="height: 38px;" title="Xóa toàn bộ bộ lọc">
                        <i class="fa fa-times me-1"></i>
                        <span>Đặt lại</span>
                    </button>
                @endif

                <span class="text-muted small ms-auto" wire:loading wire:target="search,status,handledBy,filterByStatus,resetFilters">
                    <i class="fa fa-spinner fa-spin me-1"></i>Đang tải dữ liệu...
                </span>
            </div>

            <div class="c1-table-wrap" style="margin-top: 16px;">
                <table class="c1-table">
                    <thead>
                        <tr>
                            <th style="width: 180px;">Khung giờ</th>
                            <th>Khách hàng</th>
                            <th>Mẫu xe lái thử</th>
                            <th>Chuyên viên đón tiếp</th>
                            <th style="width: 190px;">Trạng thái</th>
                            <th class="text-right" style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $appointment)
                            @php
                                $contextTrim = $appointment->carUnit?->trim ?? $appointment->trim;
                                $timeLabel = optional($appointment->scheduled_at)->format('H:i • d/m/Y') ?? 'Chưa hẹn giờ';
                                $customerName = $appointment->user?->name ?: ($appointment->lead?->name ?: 'Khách hàng đặt lịch');
                                $customerContact = $appointment->user?->phone ?: ($appointment->lead?->phone ?: $appointment->user?->email);
                            @endphp
                            <tr wire:key="appointment-row-{{ $appointment->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="c1-user-avatar" style="width: 32px; height: 32px; font-size: 11px; background: #f1f5f9; color: var(--c1-text-body); border: 1px solid var(--c1-border-card);">
                                            <i class="fa fa-calendar-check" aria-hidden="true"></i>
                                        </div>
                                        <div class="c1-cell-primary" style="font-size: 12.5px;">{{ $timeLabel }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="c1-cell-primary">{{ $customerName }}</div>
                                        @if ($customerContact)
                                            <div class="c1-cell-sub">{{ $customerContact }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="c1-vehicle-tag">
                                        {{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Tư vấn trực tiếp tại Showroom' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="c1-cell-primary">{{ $appointment->handledBy?->name ?? 'Chưa gán chuyên viên' }}</span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <select
                                            wire:change="updateStatus({{ $appointment->id }}, $event.target.value)"
                                            wire:loading.attr="disabled"
                                            class="c1-select"
                                            style="height: 30px; font-size: 12px; padding: 2px 8px; border-radius: 6px; {{ $appointment->status === 'confirmed' ? 'border-color: #10b981; background: #ecfdf5;' : ($appointment->status === 'done' ? 'border-color: #3b82f6; background: #eff6ff;' : ($appointment->status === 'cancelled' ? 'border-color: #ef4444; background: #fef2f2;' : 'border-color: #f59e0b; background: #fffbeb;')) }}"
                                            aria-label="Cập nhật trạng thái lịch hẹn #{{ $appointment->id }}"
                                        >
                                            @foreach ($statusOptions as $stKey => $stLbl)
                                                <option value="{{ $stKey }}" @selected($appointment->status === $stKey)>{{ $stLbl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.appointments.edit', $appointment) }}" wire:navigate class="c1-btn c1-btn-sm c1-btn-ghost" title="Xem và chỉnh sửa chi tiết">
                                        <i class="fa fa-pencil"></i>
                                        <span>Chi tiết</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="c1-empty-cell" style="text-align: center; padding: 32px 16px; color: var(--c1-text-muted);">
                                    <i class="fa fa-calendar-times-o fa-2x mb-2 d-block" style="opacity: 0.5;"></i>
                                    Không tìm thấy lịch hẹn nào theo tiêu chí đã chọn.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px;">
                {{ $appointments->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
</div>
