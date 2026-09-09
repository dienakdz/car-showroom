@extends('admin.layouts.app')

@section('title', 'Lịch hẹn xem xe & Lái thử | Admin')

@section('admin-content')
    <div class="c1-dash-wrapper">
        {{-- Page Header --}}
        <div class="c1-page-header">
            <h1 class="c1-page-title">Lịch hẹn xem xe & Lái thử</h1>
            <div class="c1-dash-quick-actions">
                <a href="{{ route('admin.appointments.create') }}" class="c1-btn c1-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Đặt lịch hẹn mới</span>
                </a>
            </div>
        </div>

        {{-- 4 Appointments Metric Cards --}}
        <div class="c1-kpi-grid">
            <a href="{{ route('admin.appointments.index') }}" class="c1-kpi-card" style="text-decoration: none;">
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
            </a>

            <a href="{{ route('admin.appointments.index') }}" class="c1-kpi-card" style="text-decoration: none;">
                <div class="c1-kpi-head">
                    <span class="c1-kpi-title">Lịch trong tuần</span>
                    <span class="c1-badge" style="background:#e0e7ff; color:#3730a3;">Tuần này</span>
                </div>
                <div class="c1-kpi-body">
                    <div class="c1-kpi-val-group">
                        <span class="c1-kpi-value">{{ $appointmentCounts['week'] ?? 0 }}</span>
                        <span class="c1-kpi-unit">Lịch hẹn</span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.appointments.index', ['status' => 'pending']) }}" class="c1-kpi-card" style="text-decoration: none;">
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
            </a>

            <a href="{{ route('admin.appointments.index', ['status' => 'done']) }}" class="c1-kpi-card" style="text-decoration: none;">
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
            </a>
        </div>

        {{-- Appointments Table Panel --}}
        <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
            <form method="GET" action="{{ route('admin.appointments.index') }}" class="c1-filter-toolbar">
                <select name="status" class="c1-select">
                    <option value="">Tất cả trạng thái</option>
                    @foreach (['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'done' => 'Đã hoàn tất', 'cancelled' => 'Đã hủy'] as $stKey => $stLbl)
                        <option value="{{ $stKey }}" @selected(($filters['status'] ?? '') === $stKey)>{{ $stLbl }}</option>
                    @endforeach
                </select>

                <select name="handled_by" class="c1-select">
                    <option value="">Tất cả chuyên viên đón tiếp</option>
                    @foreach ($staffUsers as $staff)
                        <option value="{{ $staff->id }}" @selected(($filters['handled_by'] ?? null) == $staff->id)>{{ $staff->name }}</option>
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
                            <th style="width: 170px;">Khung giờ</th>
                            <th>Khách hàng</th>
                            <th>Mẫu xe lái thử</th>
                            <th>Chuyên viên đón tiếp</th>
                            <th>Trạng thái</th>
                            <th class="text-right">Thao tác</th>
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
                            <tr>
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
                                    @if ($appointment->status === 'confirmed')
                                        <span class="c1-pill c1-pill-green">Đã xác nhận</span>
                                    @elseif ($appointment->status === 'pending')
                                        <span class="c1-pill c1-pill-amber">Chờ xác nhận</span>
                                    @elseif ($appointment->status === 'done')
                                        <span class="c1-pill c1-pill-blue">Đã hoàn tất</span>
                                    @else
                                        <span class="c1-pill c1-pill-red">Đã hủy</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.appointments.edit', $appointment) }}" class="c1-btn c1-btn-sm c1-btn-ghost" title="Xem chi tiết lịch hẹn">
                                        <i class="fa fa-pencil"></i>
                                        <span>Chi tiết</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="c1-empty-cell">Chưa có lịch hẹn nào được ghi nhận theo bộ lọc.</td>
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
@endsection

