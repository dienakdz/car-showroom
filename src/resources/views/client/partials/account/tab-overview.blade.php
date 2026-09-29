@php
    $nextAppt = $accountSummary['nextAppointment'] ?? null;
    $kpis = [
        [
            'title' => 'Lịch hẹn sắp tới',
            'value' => $accountSummary['upcomingAppointmentsCount'] ?? 0,
            'icon' => 'fa-solid fa-calendar-check',
            'tone' => 'primary',
            'target_tab' => 'account-appointments-pane',
        ],
        [
            'title' => 'Yêu cầu tư vấn',
            'value' => $accountSummary['leadCount'] ?? 0,
            'icon' => 'fa-solid fa-file-invoice-dollar',
            'tone' => 'purple',
            'target_tab' => 'account-leads-pane',
        ],
        [
            'title' => 'Xe đã sở hữu',
            'value' => $accountSummary['purchaseCount'] ?? 0,
            'icon' => 'fa-solid fa-car-side',
            'tone' => 'emerald',
            'target_tab' => 'account-purchases-pane',
        ],
        [
            'title' => 'Thông báo mới',
            'value' => $accountSummary['unreadNotificationCount'] ?? 0,
            'icon' => 'fa-solid fa-bell',
            'tone' => 'amber',
            'target_tab' => 'account-notifications-pane',
        ],
    ];
@endphp

<div class="account-overview-section">
    <!-- Welcome Greeting Header -->
    <div class="account-section-banner">
        <div class="banner-content">
            <span class="banner-kicker"><i class="fa-solid fa-sparkles me-1 text-warning"></i> Trung tâm khách hàng</span>
            <h2 class="banner-title">Xin chào, {{ auth()->user()->name }} 👋</h2>
            <p class="banner-desc">
                Chào mừng bạn quay trở lại. Dưới đây là tóm tắt nhanh về lịch hẹn trải nghiệm xe, tiến độ yêu cầu tư vấn và các thông tin dịch vụ của bạn tại Showroom.
            </p>
        </div>
        <div class="banner-actions">
            <a href="{{ route('inventory.index') }}" class="theme-btn btn-sm">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Khám phá kho xe
            </a>
            <a href="{{ route('contact') }}" class="btn-outline-custom btn-sm">
                <i class="fa-solid fa-paper-plane me-1"></i> Gửi yêu cầu mới
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Grid -->
    <div class="account-kpi-grid">
        @foreach ($kpis as $kpi)
            <div class="account-kpi-card tone-{{ $kpi['tone'] }}" role="button" data-switch-tab="{{ $kpi['target_tab'] }}">
                <div class="kpi-info">
                    <span class="kpi-title">{{ $kpi['title'] }}</span>
                    <h3 class="kpi-value">{{ $kpi['value'] }}</h3>
                </div>
                <div class="kpi-icon-wrap">
                    <i class="{{ $kpi['icon'] }}"></i>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Featured Next Appointment (Boarding Pass Style) -->
    <div class="account-card mt-4">
        <div class="account-card-header">
            <div>
                <h3 class="account-card-title">
                    <i class="fa-regular fa-calendar-star me-2 text-primary"></i> Lịch hẹn trải nghiệm tiếp theo
                </h3>
                <p class="account-card-subtitle">Lịch hẹn lái thử hoặc xem xe gần nhất của bạn tại showroom</p>
            </div>
            @if ($nextAppt)
                <button type="button" class="action-link-btn" data-switch-tab="account-appointments-pane">
                    Xem tất cả lịch hẹn <i class="fa-solid fa-arrow-right ms-1"></i>
                </button>
            @endif
        </div>

        @if ($nextAppt)
            <div class="account-spotlight-card">
                <div class="spotlight-thumb">
                    <img src="{{ $nextAppt->image_url }}" alt="{{ $nextAppt->context_label }}">
                    <span class="badge-status status-{{ $nextAppt->status_tone }}">
                        {{ $nextAppt->status_label }}
                    </span>
                </div>
                <div class="spotlight-body">
                    <h4 class="spotlight-car-name">
                        <a href="{{ $nextAppt->context_url }}">{{ $nextAppt->context_label }}</a>
                    </h4>
                    
                    <div class="spotlight-meta-grid">
                        <div class="meta-item">
                            <span class="meta-label"><i class="fa-regular fa-clock me-1 text-primary"></i> Thời gian hẹn</span>
                            <strong class="meta-val">{{ $nextAppt->scheduled_at_label }}</strong>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label"><i class="fa-solid fa-location-dot me-1 text-danger"></i> Địa điểm đón tiếp</span>
                            <strong class="meta-val">{{ $showroom->name ?? 'MD-CARS Showroom' }}</strong>
                        </div>
                    </div>

                    @if (filled($nextAppt->note))
                        <div class="spotlight-note">
                            <i class="fa-solid fa-quote-left text-muted me-1"></i>
                            <span>{{ $nextAppt->note }}</span>
                        </div>
                    @endif

                    <div class="spotlight-actions">
                        <a href="{{ $nextAppt->context_url }}" class="theme-btn btn-sm">
                            Xem chi tiết xe <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                        <button type="button" class="btn-outline-custom btn-sm" data-switch-tab="account-appointments-pane">
                            Quản lý lịch hẹn
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="account-empty-state py-4">
                <div class="empty-icon-wrap">
                    <i class="fa-solid fa-calendar-plus text-primary"></i>
                </div>
                <h4 class="empty-title">Bạn chưa có lịch hẹn nào sắp tới</h4>
                <p class="empty-desc">
                    Hãy trải nghiệm cảm giác lái thực tế các dòng xe đỉnh cao. Chúng tôi sẵn sàng đón tiếp và chuẩn bị xe chu đáo cho bạn.
                </p>
                <a href="{{ route('inventory.index') }}" class="theme-btn btn-sm mt-3">
                    <i class="fa-solid fa-car-side me-1"></i> Đặt lịch lái thử xe ngay
                </a>
            </div>
        @endif
    </div>

    <!-- 2 Quick Action Shortcuts Grid -->
    <div class="account-actions-grid mt-4">
        <div class="action-promo-card">
            <div class="promo-icon">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div class="promo-content">
                <h4>Gara xe cá nhân</h4>
                <p>Theo dõi xe đã mua, xem hồ sơ kỹ thuật và gửi đánh giá trải nghiệm thực tế sau khi nhận bàn giao.</p>
                <button type="button" class="action-link-btn" data-switch-tab="account-purchases-pane">
                    Vào Gara của tôi <i class="fa-solid fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <div class="action-promo-card">
            <div class="promo-icon v2">
                <i class="fa-solid fa-arrows-rotate"></i>
            </div>
            <div class="promo-content">
                <h4>Thẩm định thu cũ đổi mới</h4>
                <p>Định giá xe ô tô cũ nhanh chóng, hỗ trợ bù trừ giá ưu đãi khi lên đời xe sang tại MD-CARS.</p>
                <a href="{{ route('tradein') }}" class="action-link-btn">
                    Định giá xe ngay <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
