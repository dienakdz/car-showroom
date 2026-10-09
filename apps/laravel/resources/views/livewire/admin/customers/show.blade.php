<div class="c1-dash-wrapper">
    {{-- Header & Breadcrumb --}}
    <div style="margin-bottom: 20px;">
        <a
            href="{{ route('admin.customers.index') }}"
            wire:navigate.hover
            style="color: var(--c1-text-muted); font-size: 13.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 8px;"
        >
            <i class="fa fa-arrow-left"></i> Quay lại danh sách khách hàng
        </a>

        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <h1 class="c1-page-title" style="margin: 0;">Hồ sơ khách hàng: {{ $customer->name }}</h1>
                    @if ($customer->is_active)
                        <span class="c1-badge" style="background: #dcfce7; color: #15803d; font-size: 12px; padding: 4px 8px;">
                            <i class="fa fa-check-circle me-1"></i> Tài khoản hoạt động
                        </span>
                    @else
                        <span class="c1-badge" style="background: #fee2e2; color: #b91c1c; font-size: 12px; padding: 4px 8px;">
                            <i class="fa fa-ban me-1"></i> Tài khoản bị khóa
                        </span>
                    @endif
                </div>
            </div>

            @php
                $palettes = [
                    ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe'],
                    ['bg' => '#f5f3ff', 'color' => '#6d28d9', 'border' => '#ddd6fe'],
                    ['bg' => '#ecfdf5', 'color' => '#047857', 'border' => '#a7f3d0'],
                    ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a'],
                    ['bg' => '#fff1f2', 'color' => '#be123c', 'border' => '#fecdd3'],
                    ['bg' => '#ecfeff', 'color' => '#0e7490', 'border' => '#a5f3fc'],
                ];
                $palette = $palettes[$customer->id % count($palettes)];
            @endphp

            <div>
                @if ($customer->is_active)
                    <button
                        type="button"
                        wire:click="toggleStatus"
                        wire:confirm="Bạn có chắc muốn tạm khóa tài khoản của khách hàng {{ $customer->name }}? Khách hàng sẽ không thể đăng nhập vào hệ thống khi bị khóa."
                        class="c1-btn"
                        style="height: 38px; font-size: 13.5px; font-weight: 600; background: #fff1f2; border: 1px solid #fecdd3; color: #e11d48; border-radius: 8px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);"
                    >
                        <i class="fa fa-lock"></i> Khóa tài khoản
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="toggleStatus"
                        wire:confirm="Bạn có chắc muốn kích hoạt lại tài khoản cho khách hàng {{ $customer->name }}?"
                        class="c1-btn"
                        style="height: 38px; font-size: 13.5px; font-weight: 600; background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; border-radius: 8px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);"
                    >
                        <i class="fa fa-unlock"></i> Mở khóa tài khoản
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Customer Profile Overview Card --}}
    <div class="c1-panel" style="padding: 24px; margin-bottom: 24px;">
        <div class="row align-items-center g-4">
            <div class="col-lg-6" style="border-right: 1px solid var(--c1-border-card);">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div
                        style="width: 72px; height: 72px; border-radius: 50%; background: {{ $palette['bg'] }}; color: {{ $palette['color'] }}; border: 3px solid {{ $palette['border'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 28px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);"
                    >
                        {{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 style="font-size: 20px; font-weight: 700; color: var(--c1-text-heading); margin: 0 0 6px;">
                            {{ $customer->name }}
                        </h2>
                        <div style="display: flex; flex-direction: column; gap: 4px; font-size: 13.5px; color: var(--c1-text-body);">
                            <div>
                                <i class="fa fa-envelope-o me-2 text-muted" style="width: 14px;"></i>
                                {{ $customer->email ?: 'Chưa cập nhật email' }}
                            </div>
                            <div>
                                <i class="fa fa-phone me-2 text-muted" style="width: 14px;"></i>
                                {{ $customer->phone ?: 'Chưa cập nhật số điện thoại' }}
                            </div>
                            <div style="color: var(--c1-text-muted); font-size: 12.5px; margin-top: 2px;">
                                <i class="fa fa-calendar-o me-2" style="width: 14px;"></i>
                                Ngày tham gia: {{ $customer->created_at?->format('d/m/Y H:i') ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; text-align: center;">
                    <div style="padding: 12px; background: var(--c1-bg-card-subtle, #f8fafc); border: 1px solid var(--c1-border-card); border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: 700; color: #2563eb;">
                            {{ $customer->purchases->count() }}
                        </div>
                        <div style="font-size: 12.5px; color: var(--c1-text-muted); margin-top: 2px;">
                            Xe đã mua
                        </div>
                    </div>

                    <div style="padding: 12px; background: var(--c1-bg-card-subtle, #f8fafc); border: 1px solid var(--c1-border-card); border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: 700; color: #4f46e5;">
                            {{ $customer->appointments->count() }}
                        </div>
                        <div style="font-size: 12.5px; color: var(--c1-text-muted); margin-top: 2px;">
                            Lịch hẹn lái thử
                        </div>
                    </div>

                    <div style="padding: 12px; background: var(--c1-bg-card-subtle, #f8fafc); border: 1px solid var(--c1-border-card); border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: 700; color: #0891b2;">
                            {{ $customer->leads->count() }}
                        </div>
                        <div style="font-size: 12.5px; color: var(--c1-text-muted); margin-top: 2px;">
                            Yêu cầu tư vấn
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="c1-catalog-tabs" style="margin-bottom: 20px;" role="tablist">
        <button
            type="button"
            wire:click="switchTab('purchases')"
            class="c1-catalog-tab-btn {{ $tab === 'purchases' ? 'active' : '' }}"
        >
            <i class="fa fa-car"></i>
            <span>Lịch sử mua xe</span>
            <span class="c1-catalog-tab-badge">{{ $customer->purchases->count() }}</span>
        </button>

        <button
            type="button"
            wire:click="switchTab('appointments')"
            class="c1-catalog-tab-btn {{ $tab === 'appointments' ? 'active' : '' }}"
        >
            <i class="fa fa-calendar-check-o"></i>
            <span>Lịch hẹn</span>
            <span class="c1-catalog-tab-badge">{{ $customer->appointments->count() }}</span>
        </button>

        <button
            type="button"
            wire:click="switchTab('leads')"
            class="c1-catalog-tab-btn {{ $tab === 'leads' ? 'active' : '' }}"
        >
            <i class="fa fa-users"></i>
            <span>Yêu cầu tư vấn (Leads)</span>
            <span class="c1-catalog-tab-badge">{{ $customer->leads->count() }}</span>
        </button>

        <button
            type="button"
            wire:click="switchTab('reviews')"
            class="c1-catalog-tab-btn {{ $tab === 'reviews' ? 'active' : '' }}"
        >
            <i class="fa fa-star"></i>
            <span>Đánh giá đã viết</span>
            <span class="c1-catalog-tab-badge">{{ $customer->trimReviews->count() }}</span>
        </button>

    </div>

    {{-- TAB 1: PURCHASES --}}
    @if ($tab === 'purchases')
        <div class="c1-panel" style="padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--c1-text-heading); margin: 0 0 16px;">
                Danh sách xe đã mua & Hợp đồng
            </h3>

            @if ($customer->purchases->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach ($customer->purchases as $sale)
                        @php
                            $car = $sale->carUnit;
                            $coverMedia = $car?->media?->firstWhere('is_cover', true) ?? $car?->media?->first();
                            $rawPath = $coverMedia?->path_or_url;
                            $thumbUrl = filled($rawPath)
                                ? ((str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) ? $rawPath : asset(ltrim($rawPath, '/')))
                                : null;
                        @endphp
                        <div
                            style="display: flex; flex-wrap: wrap; align-items: center; gap: 20px; padding: 18px; border: 1px solid var(--c1-border-card); border-radius: 8px; background: var(--c1-bg-card-subtle, #fafafa);"
                        >
                            @if ($thumbUrl)
                                <img
                                    src="{{ $thumbUrl }}"
                                    alt="{{ $car?->title ?? 'Xe' }}"
                                    style="width: 140px; height: 95px; object-fit: cover; border-radius: 6px; border: 1px solid var(--c1-border-card); flex-shrink: 0;"
                                >
                            @else
                                <div
                                    style="width: 140px; height: 95px; border-radius: 6px; border: 1px solid var(--c1-border-card); background: var(--c1-bg-hover, #f1f5f9); display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; flex-shrink: 0;"
                                >
                                    <i class="fa fa-car" style="font-size: 26px; margin-bottom: 4px; color: #cbd5e1;"></i>
                                    <span style="font-size: 11px; color: #94a3b8;">Chưa có ảnh</span>
                                </div>
                            @endif
                            <div style="flex: 1; min-width: 240px;">
                                <div style="font-size: 16px; font-weight: 700; color: var(--c1-text-heading); margin-bottom: 4px;">
                                    {{ $car?->title ?? ($car?->trim?->name ? $car->trim->model?->make?->name . ' ' . $car->trim->name : 'Xe đã giao dịch') }}
                                </div>
                                <div style="font-size: 13px; color: var(--c1-text-muted); margin-bottom: 6px;">
                                    <span>Mã kho: <strong>{{ $car?->stock_code ?? '—' }}</strong></span>
                                    @if ($car?->vin)
                                        <span class="ms-3">VIN: <strong>{{ $car->vin }}</strong></span>
                                    @endif
                                </div>
                                <div style="font-size: 13.5px; color: var(--c1-text-body);">
                                    Giá trị hợp đồng:
                                    <strong style="color: #2563eb; font-size: 15px;">
                                        {{ number_format($sale->sold_price, 0, ',', '.') }} đ
                                    </strong>
                                </div>
                            </div>

                            <div style="text-align: right; min-width: 180px;">
                                <div style="margin-bottom: 6px;">
                                    <span class="c1-badge" style="background: #dcfce7; color: #166534; font-weight: 600;">
                                        <i class="fa fa-check me-1"></i> Đã bàn giao xe
                                    </span>
                                </div>
                                <div style="font-size: 12.5px; color: var(--c1-text-muted);">
                                    Ngày chốt: {{ $sale->sold_at?->format('d/m/Y H:i') ?? '—' }}
                                </div>
                                <div style="font-size: 12px; color: var(--c1-text-light); margin-top: 2px;">
                                    Tạo bởi: {{ $sale->createdBy?->name ?? 'Showroom Staff' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 48px 20px; color: var(--c1-text-muted);">
                    <i class="fa fa-car" style="font-size: 36px; color: var(--c1-text-light); margin-bottom: 12px;"></i>
                    <p style="margin: 0;">Khách hàng này chưa có hợp đồng mua xe nào tại showroom.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB 2: APPOINTMENTS --}}
    @if ($tab === 'appointments')
        <div class="c1-panel" style="padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--c1-text-heading); margin: 0 0 16px;">
                Lịch sử cuộc hẹn xem xe & Lái thử
            </h3>

            @if ($customer->appointments->isNotEmpty())
                <div class="table-responsive">
                    <table class="c1-table">
                        <thead>
                            <tr>
                                <th>Thời gian hẹn</th>
                                <th>Dòng xe quan tâm</th>
                                <th>Loại lịch hẹn</th>
                                <th>Chuyên viên phụ trách</th>
                                <th>Trạng thái</th>
                                <th style="text-align: right;">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->appointments as $apt)
                                @php
                                    $car = $apt->carUnit;
                                    $carName = $car?->title
                                        ?? ($apt->trim ? $apt->trim->model?->make?->name . ' ' . $apt->trim->name : 'Xem xe tổng quát');
                                    $aptMedia = $car?->media?->firstWhere('is_cover', true) ?? $car?->media?->first();
                                    $aptRawPath = $aptMedia?->path_or_url;
                                    $aptThumb = filled($aptRawPath)
                                        ? ((str_starts_with($aptRawPath, 'http://') || str_starts_with($aptRawPath, 'https://')) ? $aptRawPath : asset(ltrim($aptRawPath, '/')))
                                        : null;
                                @endphp
                                <tr>
                                    <td>
                                        <strong style="color: var(--c1-text-heading);">
                                            {{ $apt->scheduled_at?->format('d/m/Y H:i') ?? '—' }}
                                        </strong>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            @if ($aptThumb)
                                                <img src="{{ $aptThumb }}" alt="{{ $carName }}" style="width: 48px; height: 34px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                            @else
                                                <span style="width: 48px; height: 34px; border-radius: 4px; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; color: #94a3b8; flex-shrink: 0; border: 1px solid #e2e8f0;">
                                                    <i class="fa fa-car"></i>
                                                </span>
                                            @endif
                                            <div>
                                                <div style="font-weight: 600; color: var(--c1-text-heading); font-size: 13.5px;">{{ $carName }}</div>
                                                @if ($car?->stock_code)
                                                    <div style="font-size: 11.5px; color: var(--c1-text-muted);">#{{ $car->stock_code }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="c1-badge" style="background: #f1f5f9; color: #475569;">
                                            {{ $apt->service_type ?? 'Lái thử & Xem xe' }}
                                        </span>
                                    </td>
                                    <td>{{ $apt->handledBy?->name ?? 'Chưa phân công' }}</td>
                                    <td>
                                        @if ($apt->status === 'completed')
                                            <span class="c1-badge" style="background: #dcfce7; color: #166534;">Đã hoàn thành</span>
                                        @elseif ($apt->status === 'confirmed')
                                            <span class="c1-badge" style="background: #eff6ff; color: #1d4ed8;">Đã xác nhận</span>
                                        @elseif ($apt->status === 'cancelled')
                                            <span class="c1-badge" style="background: #fee2e2; color: #991b1b;">Đã hủy</span>
                                        @else
                                            <span class="c1-badge" style="background: #fef3c7; color: #92400e;">Đang chờ</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <a
                                            href="{{ route('admin.appointments.edit', $apt) }}"
                                            wire:navigate.hover
                                            class="c1-btn c1-btn-secondary"
                                            style="height: 30px; padding: 0 10px; font-size: 12px;"
                                        >
                                            <i class="fa fa-external-link"></i> Mở
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 48px 20px; color: var(--c1-text-muted);">
                    <i class="fa fa-calendar-o" style="font-size: 36px; color: var(--c1-text-light); margin-bottom: 12px;"></i>
                    <p style="margin: 0;">Khách hàng chưa đặt lịch hẹn lái thử nào.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB 3: LEADS --}}
    @if ($tab === 'leads')
        <div class="c1-panel" style="padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--c1-text-heading); margin: 0 0 16px;">
                Lịch sử yêu cầu tư vấn (CRM Leads)
            </h3>

            @if ($customer->leads->isNotEmpty())
                <div class="table-responsive">
                    <table class="c1-table">
                        <thead>
                            <tr>
                                <th>Thời gian gửi</th>
                                <th>Nhu cầu / Nguồn</th>
                                <th>Dòng xe quan tâm</th>
                                <th>Ghi chú của khách</th>
                                <th>Chuyên viên phụ trách</th>
                                <th>Trạng thái</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->leads as $lead)
                                @php
                                    $car = $lead->carUnit;
                                    $carName = $car?->title
                                        ?? ($lead->trim ? $lead->trim->model?->make?->name . ' ' . $lead->trim->name : 'Tư vấn chung');
                                    $leadMedia = $car?->media?->firstWhere('is_cover', true) ?? $car?->media?->first();
                                    $leadRawPath = $leadMedia?->path_or_url;
                                    $leadThumb = filled($leadRawPath)
                                        ? ((str_starts_with($leadRawPath, 'http://') || str_starts_with($leadRawPath, 'https://')) ? $leadRawPath : asset(ltrim($leadRawPath, '/')))
                                        : null;
                                @endphp
                                <tr>
                                    <td>{{ $lead->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td>
                                        <span class="c1-badge" style="background: #e0e7ff; color: #3730a3;">
                                            {{ $lead->source }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            @if ($leadThumb)
                                                <img src="{{ $leadThumb }}" alt="{{ $carName }}" style="width: 48px; height: 34px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                            @else
                                                <span style="width: 48px; height: 34px; border-radius: 4px; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; color: #94a3b8; flex-shrink: 0; border: 1px solid #e2e8f0;">
                                                    <i class="fa fa-car"></i>
                                                </span>
                                            @endif
                                            <div>
                                                <div style="font-weight: 600; color: var(--c1-text-heading); font-size: 13.5px;">{{ $carName }}</div>
                                                @if ($car?->stock_code)
                                                    <div style="font-size: 11.5px; color: var(--c1-text-muted);">#{{ $car->stock_code }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $lead->note ?: '—' }}
                                    </td>
                                    <td>{{ $lead->assignedTo?->name ?? 'Chưa nhận' }}</td>
                                    <td>
                                        <span class="c1-badge" style="background: #f1f5f9; color: #334155;">
                                            {{ $lead->status }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a
                                            href="{{ route('admin.leads.show', $lead) }}"
                                            wire:navigate.hover
                                            class="c1-btn c1-btn-secondary"
                                            style="height: 30px; padding: 0 10px; font-size: 12px;"
                                        >
                                            <i class="fa fa-external-link"></i> Xử lý
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 48px 20px; color: var(--c1-text-muted);">
                    <i class="fa fa-users" style="font-size: 36px; color: var(--c1-text-light); margin-bottom: 12px;"></i>
                    <p style="margin: 0;">Khách hàng chưa gửi form yêu cầu tư vấn nào.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB 4: REVIEWS --}}
    @if ($tab === 'reviews')
        <div class="c1-panel" style="padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--c1-text-heading); margin: 0 0 16px;">
                Đánh giá phiên bản xe đã viết
            </h3>

            @if ($customer->trimReviews->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach ($customer->trimReviews as $review)
                        <div style="padding: 16px; border: 1px solid var(--c1-border-card); border-radius: 8px; background: #fff;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <div>
                                    <strong style="color: var(--c1-text-heading); font-size: 15px;">
                                        {{ $review->trim ? $review->trim->model?->make?->name . ' ' . $review->trim->name : 'Phiên bản xe' }}
                                    </strong>
                                    <span class="ms-2 text-warning" style="font-size: 13px;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i>
                                        @endfor
                                    </span>
                                </div>
                                <div>
                                    @if ($review->status === 'approved')
                                        <span class="c1-badge" style="background: #dcfce7; color: #166534;">Đã duyệt hiển thị</span>
                                    @else
                                        <span class="c1-badge" style="background: #fef3c7; color: #92400e;">Chờ duyệt</span>
                                    @endif
                                </div>
                            </div>
                            <div style="font-size: 13.5px; color: var(--c1-text-body); line-height: 1.5;">
                                {{ $review->content }}
                            </div>
                            <div style="font-size: 12px; color: var(--c1-text-muted); margin-top: 8px;">
                                Đăng ngày: {{ $review->created_at?->format('d/m/Y H:i') ?? '—' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 48px 20px; color: var(--c1-text-muted);">
                    <i class="fa fa-star-o" style="font-size: 36px; color: var(--c1-text-light); margin-bottom: 12px;"></i>
                    <p style="margin: 0;">Khách hàng chưa viết đánh giá phiên bản xe nào.</p>
                </div>
            @endif
        </div>
    @endif

</div>
