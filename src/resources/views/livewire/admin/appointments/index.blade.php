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
                <a href="{{ route('admin.appointments.create') }}" wire:navigate.hover class="c1-btn c1-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Đặt lịch hẹn mới</span>
                </a>
            </div>
        </div>

        {{-- 4 Appointments KPI Cards with Reactive Filtering & Toggle-off --}}
        <div class="c1-kpi-grid">
            <div
                wire:click="resetFilters"
                class="c1-kpi-card {{ $status === '' && $dateFilter === '' ? 'active' : '' }}"
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
                        <span class="c1-kpi-unit">Toàn hệ thống</span>
                    </div>
                </div>
            </div>

            <div
                wire:click="filterByDate('today')"
                class="c1-kpi-card {{ $dateFilter === 'today' ? 'active' : '' }}"
                style="cursor: pointer; user-select: none;"
                title="Bấm để lọc lịch hẹn trong ngày hôm nay (Bấm lại để hủy)"
            >
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Lịch hôm nay</span>
                    <span class="c1-badge c1-badge-blue">Hôm nay</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $appointmentCounts['today'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Cần đón tiếp</span>
                    </div>
                </div>
            </div>

            <div
                wire:click="filterByStatus('pending')"
                class="c1-kpi-card {{ $status === 'pending' ? 'active' : '' }}"
                style="cursor: pointer; user-select: none;"
                title="Bấm để lọc lịch chờ xác nhận (Bấm lại để hủy)"
            >
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Chờ xác nhận</span>
                    <span class="c1-badge" style="background:#fef3c7; color:#92400e;">Chờ duyệt</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $appointmentCounts['pending'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Cần liên hệ</span>
                    </div>
                </div>
            </div>

            <div
                wire:click="filterByStatus('done')"
                class="c1-kpi-card {{ $status === 'done' ? 'active' : '' }}"
                style="cursor: pointer; user-select: none;"
                title="Bấm để lọc lịch đã hoàn tất (Bấm lại để hủy)"
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

                @if ($search !== '' || $status !== '' || $dateFilter !== '' || $handledBy > 0)
                    <button type="button" wire:click="resetFilters" class="c1-btn c1-btn-secondary" style="height: 38px;" title="Xóa toàn bộ bộ lọc">
                        <i class="fa fa-times me-1"></i>
                        <span>Đặt lại</span>
                    </button>
                @endif

                <span class="text-muted small ms-auto" wire:loading wire:target="search,status,dateFilter,handledBy,filterByStatus,filterByDate,resetFilters">
                    <i class="fa fa-spinner fa-spin me-1"></i>Đang tải dữ liệu...
                </span>
            </div>

            <div class="c1-table-wrap" style="margin-top: 16px;">
                <table class="c1-table">
                    <thead>
                        <tr>
                            <th style="width: 170px;">Khung giờ</th>
                            <th>Khách hàng</th>
                            <th>Mẫu xe lái thử</th>
                            <th>Chuyên viên đón tiếp</th>
                            <th style="width: 160px;">Trạng thái</th>
                            <th class="text-right" style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $appointment)
                            @php
                                $contextTrim = $appointment->carUnit?->trim ?? $appointment->trim;
                                $contextName = trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' '));
                                $contextMedia = $appointment->carUnit?->primaryMedia ?? $contextTrim?->carUnits?->first()?->primaryMedia;
                                $rawPath = $contextMedia?->path_or_url;
                                $thumbUrl = filled($rawPath)
                                    ? ((str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) ? $rawPath : asset(ltrim($rawPath, '/')))
                                    : null;
                                $timeLabel = optional($appointment->scheduled_at)->format('H:i • d/m/Y') ?? 'Chưa hẹn giờ';
                                $customerName = $appointment->user?->name ?: ($appointment->lead?->name ?: 'Khách hàng đặt lịch');
                                $customerContact = $appointment->user?->phone ?: ($appointment->lead?->phone ?: $appointment->user?->email);
                                $isToday = $appointment->scheduled_at?->isToday();
                                $isTomorrow = $appointment->scheduled_at?->isTomorrow();
                            @endphp
                            <tr wire:key="appointment-row-{{ $appointment->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="c1-user-avatar" style="width: 32px; height: 32px; font-size: 12px; background: #f1f5f9; color: var(--c1-text-body); border: 1px solid var(--c1-border-card); flex-shrink: 0;">
                                            <i class="fa fa-calendar-check" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <div class="c1-cell-primary" style="font-size: 12.5px; font-weight: 600;">{{ $timeLabel }}</div>
                                            @if ($isToday)
                                                <span class="c1-badge c1-badge-blue" style="font-size: 10px; margin-top: 2px;">Hôm nay</span>
                                            @elseif ($isTomorrow)
                                                <span class="c1-badge" style="font-size: 10px; background: #fef3c7; color: #92400e; margin-top: 2px;">Ngày mai</span>
                                            @else
                                                <div class="c1-cell-sub" style="font-size: 11px;">{{ $appointment->scheduled_at?->diffForHumans() }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="c1-cell-primary" style="font-weight: 600;">{{ $customerName }}</div>
                                        @if ($customerContact)
                                            <div class="c1-cell-sub">{{ $customerContact }}</div>
                                        @endif
                                        @if ($appointment->lead_id)
                                            <a href="{{ route('admin.leads.show', $appointment->lead_id) }}" wire:navigate class="c1-badge" style="font-size: 11px; background: #e0f2fe; color: #0284c7; text-decoration: none; margin-top: 3px; display: inline-flex; align-items: center;" title="Xem hồ sơ Lead CRM">
                                                <i class="fa fa-user-circle me-1"></i>#Lead-{{ $appointment->lead_id }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        @if ($thumbUrl)
                                            <img src="{{ $thumbUrl }}" alt="Thumb" style="width: 46px; height: 30px; object-fit: cover; border-radius: 4px; flex-shrink: 0; border: 1px solid #e2e8f0;">
                                        @else
                                            <span style="width: 46px; height: 30px; border-radius: 4px; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; color: #94a3b8; flex-shrink: 0; border: 1px solid #e2e8f0;">
                                                <i class="fa fa-car"></i>
                                            </span>
                                        @endif
                                        <div style="min-width: 0;">
                                            <div class="c1-cell-primary" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 240px;" title="{{ $contextName ?: 'Chưa chọn xe cụ thể' }}">
                                                {{ $contextName ?: 'Chưa chọn xe cụ thể' }}
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px; flex-wrap: wrap;">
                                                @if ($appointment->carUnit)
                                                    <span class="c1-vehicle-tag" style="font-size: 10px; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;">
                                                        #{{ $appointment->carUnit->stock_code }}
                                                    </span>
                                                    @if ($appointment->carUnit->price)
                                                        <span style="font-size: 11px; font-weight: 600; color: #16a34a;">
                                                            {{ number_format((float) $appointment->carUnit->price, 0, ',', '.') }} đ
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($appointment->handledBy)
                                        <span class="c1-cell-primary" style="font-weight: 500;">
                                            <i class="fa fa-user-circle-o me-1 text-muted"></i>{{ $appointment->handledBy->name }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 12.5px;">Chưa gán</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        @if ($appointment->status === 'pending')
                                            <span class="c1-pill c1-pill-amber" style="width: fit-content;"><i class="fa fa-clock-o me-1"></i>Chờ xác nhận</span>
                                        @elseif ($appointment->status === 'confirmed')
                                            <span class="c1-pill c1-pill-blue" style="width: fit-content;"><i class="fa fa-check me-1"></i>Đã xác nhận</span>
                                        @elseif ($appointment->status === 'done')
                                            <span class="c1-pill c1-pill-green" style="width: fit-content;"><i class="fa fa-check-circle me-1"></i>Đã hoàn tất</span>
                                        @else
                                            <span class="c1-pill c1-pill-gray" style="width: fit-content;"><i class="fa fa-times-circle me-1"></i>Đã hủy</span>
                                        @endif

                                        <select
                                            wire:change="updateStatus({{ $appointment->id }}, $event.target.value)"
                                            wire:loading.attr="disabled"
                                            class="c1-select"
                                            style="height: 26px; font-size: 11.5px; padding: 2px 6px; border-radius: 4px; border: 1px solid var(--c1-border-card); background: #ffffff; color: var(--c1-text-body);"
                                            aria-label="Cập nhật trạng thái #{{ $appointment->id }}"
                                        >
                                            @foreach ($statusOptions as $stKey => $stLbl)
                                                <option value="{{ $stKey }}" @selected($appointment->status === $stKey)>{{ $stLbl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.appointments.edit', $appointment) }}" wire:navigate.hover class="c1-action-btn c1-action-btn-primary" title="Xem và chỉnh sửa chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        <span>Chi tiết</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="c1-empty-cell" style="text-align: center; padding: 40px 16px; color: var(--c1-text-muted);">
                                    <i class="fa fa-calendar-times-o fa-2x mb-2 d-block" style="opacity: 0.4;"></i>
                                    Không tìm thấy lịch hẹn nào theo tiêu chí đã chọn.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $appointments->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
</div>

