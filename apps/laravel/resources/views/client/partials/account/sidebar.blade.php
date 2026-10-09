@php
    $accountUser = auth()->user();
    $unreadCount = $accountSummary['unreadNotificationCount'] ?? 0;
    $upcomingAppointments = $accountSummary['upcomingAppointmentsCount'] ?? 0;
    $activeAccountTab = $activeAccountTab ?? 'account-overview';
    $showroomPhone = $showroom->phone ?? '0900 000 000';
    $cleanPhone = preg_replace('/\D+/', '', (string) $showroomPhone);
@endphp

<aside class="account-sidebar">
    <!-- VIP Profile Summary Card -->
    <div class="account-profile-card">
        <div class="account-profile-avatar-wrap">
            <div class="account-profile-avatar">
                {{ strtoupper(mb_substr($accountUser->name, 0, 1)) }}
            </div>
            <span class="account-avatar-status" title="Tài khoản đang hoạt động">
                <i class="fa-solid fa-check"></i>
            </span>
        </div>

        <h3 class="account-profile-name">{{ $accountUser->name }}</h3>
        
        <div class="account-vip-badge">
            <i class="fa-solid fa-crown"></i>
            <span>Khách hàng thân thiết</span>
        </div>

        <div class="account-profile-meta">
            <span><i class="fa-regular fa-clock me-1"></i> Tham gia: {{ $accountSummary['memberSinceLabel'] }}</span>
        </div>

        <!-- Profile Completion Tracker -->
        <div class="account-completion-wrap">
            <div class="account-completion-header">
                <span class="label">Hoàn thiện hồ sơ</span>
                <span class="percentage">{{ $accountSummary['profileCompletion'] }}%</span>
            </div>
            <div class="account-progress-track">
                <div class="account-progress-bar" style="width: {{ $accountSummary['profileCompletion'] }}%;"></div>
            </div>
            @if ($accountSummary['profileCompletion'] < 100)
                <small class="account-completion-hint">
                    <i class="fa-solid fa-circle-info me-1"></i> Bổ sung email & SĐT để nhận CSKH ưu tiên
                </small>
            @endif
        </div>
    </div>

    <!-- Navigation Menu List -->
    <div class="account-nav-card">
        <ul class="account-nav-list" id="account-tablist" role="tablist">
            <li role="presentation">
                <button class="account-nav-btn {{ $activeAccountTab === 'account-overview' ? 'active' : '' }}" 
                        id="account-overview-tab" 
                        type="button" 
                        role="tab" 
                        data-account-tab="account-overview-pane" 
                        aria-controls="account-overview-pane" 
                        aria-selected="{{ $activeAccountTab === 'account-overview' ? 'true' : 'false' }}">
                    <span class="btn-content">
                        <i class="fa-solid fa-chart-pie nav-icon"></i>
                        <span class="nav-text">Tổng quan tài khoản</span>
                    </span>
                    <i class="fa-solid fa-angle-right nav-arrow"></i>
                </button>
            </li>

            <li role="presentation">
                <button class="account-nav-btn {{ $activeAccountTab === 'account-profile' ? 'active' : '' }}" 
                        id="account-profile-tab" 
                        type="button" 
                        role="tab" 
                        data-account-tab="account-profile-pane" 
                        aria-controls="account-profile-pane" 
                        aria-selected="{{ $activeAccountTab === 'account-profile' ? 'true' : 'false' }}">
                    <span class="btn-content">
                        <i class="fa-solid fa-user-shield nav-icon"></i>
                        <span class="nav-text">Thông tin & Bảo mật</span>
                    </span>
                    <i class="fa-solid fa-angle-right nav-arrow"></i>
                </button>
            </li>

            <li role="presentation">
                <button class="account-nav-btn {{ $activeAccountTab === 'account-notifications' ? 'active' : '' }}" 
                        id="account-notifications-tab" 
                        type="button" 
                        role="tab" 
                        data-account-tab="account-notifications-pane" 
                        aria-controls="account-notifications-pane" 
                        aria-selected="{{ $activeAccountTab === 'account-notifications' ? 'true' : 'false' }}">
                    <span class="btn-content">
                        <i class="fa-solid fa-bell nav-icon"></i>
                        <span class="nav-text">Hộp thư thông báo</span>
                    </span>
                    @if ($unreadCount > 0)
                        <span class="account-pill-badge badge-unread">{{ $unreadCount }}</span>
                    @else
                        <i class="fa-solid fa-angle-right nav-arrow"></i>
                    @endif
                </button>
            </li>

            <li role="presentation">
                <button class="account-nav-btn {{ $activeAccountTab === 'account-appointments' ? 'active' : '' }}" 
                        id="account-appointments-tab" 
                        type="button" 
                        role="tab" 
                        data-account-tab="account-appointments-pane" 
                        aria-controls="account-appointments-pane" 
                        aria-selected="{{ $activeAccountTab === 'account-appointments' ? 'true' : 'false' }}">
                    <span class="btn-content">
                        <i class="fa-solid fa-calendar-check nav-icon"></i>
                        <span class="nav-text">Lịch hẹn lái thử</span>
                    </span>
                    @if ($upcomingAppointments > 0)
                        <span class="account-pill-badge badge-info">{{ $upcomingAppointments }}</span>
                    @else
                        <i class="fa-solid fa-angle-right nav-arrow"></i>
                    @endif
                </button>
            </li>

            <li role="presentation">
                <button class="account-nav-btn {{ $activeAccountTab === 'account-leads' ? 'active' : '' }}" 
                        id="account-leads-tab" 
                        type="button" 
                        role="tab" 
                        data-account-tab="account-leads-pane" 
                        aria-controls="account-leads-pane" 
                        aria-selected="{{ $activeAccountTab === 'account-leads' ? 'true' : 'false' }}">
                    <span class="btn-content">
                        <i class="fa-solid fa-file-invoice-dollar nav-icon"></i>
                        <span class="nav-text">Yêu cầu tư vấn</span>
                    </span>
                    <i class="fa-solid fa-angle-right nav-arrow"></i>
                </button>
            </li>

            <li role="presentation">
                <button class="account-nav-btn {{ $activeAccountTab === 'account-purchases' ? 'active' : '' }}" 
                        id="account-purchases-tab" 
                        type="button" 
                        role="tab" 
                        data-account-tab="account-purchases-pane" 
                        aria-controls="account-purchases-pane" 
                        aria-selected="{{ $activeAccountTab === 'account-purchases' ? 'true' : 'false' }}">
                    <span class="btn-content">
                        <i class="fa-solid fa-warehouse nav-icon"></i>
                        <span class="nav-text">Gara xe của tôi</span>
                    </span>
                    <i class="fa-solid fa-angle-right nav-arrow"></i>
                </button>
            </li>

            <li role="presentation">
                <button class="account-nav-btn {{ $activeAccountTab === 'account-reviews' ? 'active' : '' }}" 
                        id="account-reviews-tab" 
                        type="button" 
                        role="tab" 
                        data-account-tab="account-reviews-pane" 
                        aria-controls="account-reviews-pane" 
                        aria-selected="{{ $activeAccountTab === 'account-reviews' ? 'true' : 'false' }}">
                    <span class="btn-content">
                        <i class="fa-solid fa-star nav-icon"></i>
                        <span class="nav-text">Đánh giá của tôi</span>
                    </span>
                    <i class="fa-solid fa-angle-right nav-arrow"></i>
                </button>
            </li>
        </ul>

        <!-- Logout Action -->
        <div class="account-sidebar-logout">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="account-logout-btn">
                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Đăng xuất tài khoản
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Concierge Support Card -->
    <div class="account-support-card">
        <div class="support-icon">
            <i class="fa-solid fa-headset"></i>
        </div>
        <div class="support-content">
            <h4>Cố vấn dịch vụ 24/7</h4>
            <p>Cần hỗ trợ kỹ thuật, đặt cọc giữ xe hoặc giải đáp thủ tục?</p>
            <a href="tel:{{ $cleanPhone }}" class="support-phone-btn">
                <i class="fa-solid fa-phone me-1"></i> {{ $showroomPhone }}
            </a>
        </div>
    </div>
</aside>
