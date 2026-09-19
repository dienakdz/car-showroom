<div class="c1-dash-wrapper">
    {{-- Page Header --}}
    <div class="c1-page-header">
        <div>
            <h1 class="c1-page-title">Quản lý khách hàng</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                Danh sách tài khoản khách hàng đã đăng ký trong hệ thống showroom.
            </div>
        </div>
    </div>

    {{-- 4 Metric Cards --}}
    <div class="c1-kpi-grid">
        <button
            type="button"
            wire:click="setFilter('all')"
            class="c1-kpi-card {{ $filter === 'all' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer; border: none; background: #fff;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Tổng khách hàng</span>
                <span class="c1-badge" style="background: #f1f5f9; color: #475569;">Tài khoản</span>
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
            wire:click="setFilter('purchased')"
            class="c1-kpi-card {{ $filter === 'purchased' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer; border: none; background: #fff;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Khách đã mua xe</span>
                <span class="c1-badge" style="background: #eff6ff; color: #2563eb;">Đã chốt xe</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">{{ number_format($stats['purchased']) }}</span>
                    <span class="c1-kpi-unit">Khách</span>
                </div>
            </div>
        </button>

        <button
            type="button"
            wire:click="setFilter('appointment')"
            class="c1-kpi-card {{ $filter === 'appointment' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer; border: none; background: #fff;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Lịch hẹn đang chờ</span>
                <span class="c1-badge" style="background: #fef3c7; color: #b45309;">Sắp tới</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">{{ number_format($stats['pending_appointments']) }}</span>
                    <span class="c1-kpi-unit">Lịch hẹn</span>
                </div>
            </div>
        </button>

        <div class="c1-kpi-card">
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Khách mới tháng này</span>
                <span class="c1-badge" style="background: #dcfce7; color: #166534;">Tăng trưởng</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">+{{ number_format($stats['new_this_month']) }}</span>
                    <span class="c1-kpi-unit">Khách mới</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar & Table Panel --}}
    <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; margin-bottom: 20px;">
            <div style="position: relative; width: 340px; max-width: 100%;">
                <i class="fa fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--c1-text-light); font-size: 13px;"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    class="form-control"
                    placeholder="Tìm theo tên, email, số điện thoại..."
                    style="padding-left: 34px; font-size: 13.5px; height: 40px; border-radius: 8px; border: 1px solid var(--c1-border-card);"
                >
            </div>

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                <select
                    wire:model.live="filter"
                    class="form-select"
                    style="width: 190px; font-size: 13.5px; height: 40px; border-radius: 8px; border: 1px solid var(--c1-border-card);"
                >
                    <option value="all">Tất cả khách hàng</option>
                    <option value="purchased">Khách đã mua xe</option>
                    <option value="appointment">Khách có lịch hẹn</option>
                    <option value="active">Tài khoản hoạt động</option>
                    <option value="inactive">Tài khoản bị khóa</option>
                </select>

                @if ($search !== '' || $filter !== 'all')
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="c1-btn c1-btn-secondary"
                        style="height: 40px; padding: 0 14px; font-size: 13px;"
                        title="Xóa bộ lọc"
                    >
                        <i class="fa fa-refresh me-1"></i> Đặt lại
                    </button>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="c1-table">
                <thead>
                    <tr>
                        <th style="width: 270px;">Khách hàng</th>
                        <th>Thông tin liên hệ</th>
                        <th>Ngày đăng ký</th>
                        <th style="text-align: center;">Xe đã mua</th>
                        <th style="text-align: center;">Lịch hẹn</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: center; width: 160px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        @php
                            $isNew = $customer->created_at && $customer->created_at->diffInDays(now()) <= 30;
                            $hasPurchased = $customer->purchases_count > 0;
                            $initials = mb_strtoupper(mb_substr($customer->name, 0, 1));

                            $palettes = [
                                ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe'], // Blue
                                ['bg' => '#f5f3ff', 'color' => '#6d28d9', 'border' => '#ddd6fe'], // Indigo
                                ['bg' => '#ecfdf5', 'color' => '#047857', 'border' => '#a7f3d0'], // Emerald
                                ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a'], // Amber
                                ['bg' => '#fff1f2', 'color' => '#be123c', 'border' => '#fecdd3'], // Rose
                                ['bg' => '#ecfeff', 'color' => '#0e7490', 'border' => '#a5f3fc'], // Cyan
                            ];
                            $palette = $palettes[$customer->id % count($palettes)];
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 40px; height: 40px; border-radius: 50%; background: {{ $palette['bg'] }}; color: {{ $palette['color'] }}; border: 2px solid {{ $palette['border'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.04);"
                                    >
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <a
                                            href="{{ route('admin.customers.show', $customer) }}"
                                            wire:navigate.hover
                                            style="font-weight: 600; color: var(--c1-text-heading); text-decoration: none; font-size: 14px;"
                                            class="c1-cust-name-link"
                                        >
                                            {{ $customer->name }}
                                        </a>
                                        <div style="margin-top: 3px; display: flex; gap: 4px; align-items: center;">
                                            @if ($customer->purchases_count >= 2)
                                                <span class="c1-badge" style="background: #faf5ff; color: #7e22ce; border: 1px solid #d8b4fe; font-size: 10.5px; padding: 2px 7px; font-weight: 600; display: inline-flex; align-items: center; gap: 3px;">
                                                    <i class="fa fa-star" style="font-size: 9.5px; color: #a855f7;"></i> VIP
                                                </span>
                                            @elseif ($customer->purchases_count === 1)
                                                <span class="c1-badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 10.5px; padding: 2px 7px; font-weight: 600; display: inline-flex; align-items: center; gap: 3px;">
                                                    <i class="fa fa-check-circle" style="font-size: 9.5px; color: #2563eb;"></i> Đã mua xe
                                                </span>
                                            @elseif ($isNew)
                                                <span class="c1-badge" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 10.5px; padding: 2px 7px; font-weight: 600; display: inline-flex; align-items: center; gap: 3px;">
                                                    <i class="fa fa-bolt" style="font-size: 9.5px; color: #f59e0b;"></i> Khách mới
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 13.5px; color: var(--c1-text-body); display: flex; flex-direction: column; gap: 3px;">
                                    @if ($customer->email)
                                        <div>
                                            <i class="fa fa-envelope-o me-1" style="color: #3b82f6; width: 14px;"></i>
                                            <a href="mailto:{{ $customer->email }}" style="color: var(--c1-text-body); text-decoration: none;">{{ $customer->email }}</a>
                                        </div>
                                    @endif
                                    @if ($customer->phone)
                                        <div>
                                            <i class="fa fa-phone me-1" style="color: #10b981; width: 14px;"></i>
                                            <a href="tel:{{ $customer->phone }}" style="color: var(--c1-text-body); text-decoration: none; font-weight: 500;">{{ $customer->phone }}</a>
                                        </div>
                                    @endif
                                    @if (! $customer->email && ! $customer->phone)
                                        <span class="text-muted" style="font-style: italic;">Chưa cập nhật</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 13px; color: var(--c1-text-muted); font-weight: 500;">
                                    {{ $customer->created_at?->format('d/m/Y') ?? '—' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if ($customer->purchases_count > 0)
                                    <span class="c1-badge" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 600; padding: 3px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa fa-car" style="font-size: 11px;"></i> {{ $customer->purchases_count }} xe
                                    </span>
                                @else
                                    <span style="color: #cbd5e1; font-size: 14px;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if ($customer->appointments_count > 0)
                                    <span class="c1-badge" style="background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; font-weight: 600; padding: 3px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa fa-calendar-check-o" style="font-size: 11px;"></i> {{ $customer->appointments_count }} hẹn
                                    </span>
                                @else
                                    <span style="color: #cbd5e1; font-size: 14px;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if ($customer->is_active)
                                    <span class="c1-badge" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-weight: 600; padding: 4px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);"></span>
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="c1-badge" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; font-weight: 600; padding: 4px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25);"></span>
                                        Bị khóa
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                    <a
                                        href="{{ route('admin.customers.show', $customer) }}"
                                        wire:navigate.hover
                                        class="c1-cust-btn-view"
                                        title="Xem toàn bộ hồ sơ khách hàng 360°"
                                    >
                                        <i class="fa fa-id-card-o"></i>
                                        <span>Chi tiết</span>
                                    </a>

                                    @if ($customer->is_active)
                                        <button
                                            type="button"
                                            wire:click="toggleCustomerStatus({{ $customer->id }})"
                                            wire:confirm="Bạn có chắc muốn tạm khóa tài khoản của khách hàng {{ $customer->name }}?"
                                            class="c1-cust-btn-lock"
                                            title="Tạm khóa tài khoản này"
                                        >
                                            <i class="fa fa-lock"></i>
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="toggleCustomerStatus({{ $customer->id }})"
                                            wire:confirm="Bạn có chắc muốn kích hoạt lại tài khoản cho khách hàng {{ $customer->name }}?"
                                            class="c1-cust-btn-unlock"
                                            title="Kích hoạt lại tài khoản này"
                                        >
                                            <i class="fa fa-unlock"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px 20px;">
                                <div style="color: var(--c1-text-light); font-size: 36px; margin-bottom: 12px;">
                                    <i class="fa fa-users"></i>
                                </div>
                                <h4 style="color: var(--c1-text-heading); font-size: 16px; margin-bottom: 6px;">Không tìm thấy khách hàng nào</h4>
                                <p style="color: var(--c1-text-muted); font-size: 13.5px; margin: 0;">
                                    Thử thay đổi từ khóa tìm kiếm hoặc đặt lại bộ lọc.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($customers->hasPages())
            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--c1-border-card);">
                {{ $customers->links('admin.partials.pagination') }}
            </div>
        @endif
    </div>

    <style>
        .c1-cust-name-link:hover {
            color: #2563eb !important;
            text-decoration: underline !important;
        }
        .c1-cust-btn-view {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: 600;
            padding: 0 12px;
            height: 32px;
            border-radius: 6px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 12.5px;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .c1-cust-btn-view i {
            color: #2563eb;
            font-size: 13px;
            transition: transform 0.15s ease, color 0.15s ease;
        }
        .c1-cust-btn-view:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(37, 99, 235, 0.28);
            text-decoration: none;
        }
        .c1-cust-btn-view:hover i {
            color: #ffffff !important;
        }
        .c1-cust-btn-lock {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1px solid #fecdd3;
            background: #fff1f2;
            color: #e11d48;
            font-size: 12.5px;
            cursor: pointer;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .c1-cust-btn-lock:hover {
            background: #e11d48;
            border-color: #e11d48;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(225, 29, 72, 0.28);
        }
        .c1-cust-btn-unlock {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1px solid #a7f3d0;
            background: #ecfdf5;
            color: #059669;
            font-size: 12.5px;
            cursor: pointer;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .c1-cust-btn-unlock:hover {
            background: #059669;
            border-color: #059669;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(5, 150, 105, 0.28);
        }
    </style>
</div>
