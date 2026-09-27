@php
    $headerClasses = $headerClasses ?? 'boxcar-header header-style-v1 header-default';
    $showSearch = $showSearch ?? false;
    $currentUser = auth()->user();
    $isStaffOrAdmin = $currentUser !== null && $currentUser->hasAnyRole(['admin', 'staff']);
    $accountLabel = $isStaffOrAdmin ? 'Khu vực quản trị' : (auth()->check() ? 'Tài khoản' : 'Đăng nhập');
    $accountUrl = $isStaffOrAdmin ? route('admin.dashboard') : (auth()->check() ? route('account.show') : route('login'));
    $inventoryMenuActive = request()->routeIs('inventory.*');
@endphp


@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sticky header handler
            const headers = document.querySelectorAll('.js-site-header');
            headers.forEach((header) => {
                const bar = header.querySelector('.site-header-bar');
                if (!bar) return;

                let ticking = false;
                let baseHeight = 0;

                const measureBaseHeight = () => {
                    const wasSticky = header.classList.contains('is-sticky');
                    if (wasSticky) {
                        header.classList.remove('is-sticky');
                        header.style.height = '';
                    }
                    baseHeight = Math.ceil(header.getBoundingClientRect().height);
                    if (wasSticky) {
                        header.classList.add('is-sticky');
                    }
                };

                const syncStickyState = () => {
                    const shouldStick = window.scrollY > 40;
                    header.classList.toggle('is-sticky', shouldStick);
                    header.style.height = shouldStick && baseHeight > 0 ? `${baseHeight}px` : '';
                    ticking = false;
                };

                const requestSync = () => {
                    if (ticking) return;
                    ticking = true;
                    window.requestAnimationFrame(syncStickyState);
                };

                measureBaseHeight();
                syncStickyState();
                window.addEventListener('scroll', requestSync, { passive: true });
                window.addEventListener('resize', () => {
                    measureBaseHeight();
                    syncStickyState();
                });
            });

            // Search toggle popover
            const searchTrigger = document.querySelector('.js-search-trigger');
            const searchPopover = document.querySelector('.js-search-popover');
            if (searchTrigger && searchPopover) {
                searchTrigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    searchPopover.classList.toggle('show');
                    searchTrigger.classList.toggle('active');
                    if (searchPopover.classList.contains('show')) {
                        const input = searchPopover.querySelector('input[type="search"]');
                        if (input) input.focus();
                    }
                });
            }

            // User account hub toggle
            const userHub = document.querySelector('.js-user-hub');
            const userTrigger = document.querySelector('.js-user-trigger');
            const notificationHub = document.querySelector('.js-notification-hub');
            const notificationTrigger = document.querySelector('.js-notification-trigger');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (userHub && userTrigger) {
                userTrigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    userHub.classList.toggle('active');
                    if (notificationHub) notificationHub.classList.remove('active');
                });
            }

            // Client Notification Hub toggle & actions
            if (notificationHub && notificationTrigger) {
                notificationTrigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    notificationHub.classList.toggle('active');
                    if (userHub) userHub.classList.remove('active');
                });

                // Mark all as read
                const markAllBtn = notificationHub.querySelector('.js-mark-all-read');
                if (markAllBtn) {
                    markAllBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const url = this.dataset.url;
                        if (!url) return;

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        }).then(res => res.json()).then(data => {
                            if (data.success) {
                                notificationHub.querySelectorAll('.notification-item').forEach(item => {
                                    item.classList.remove('is-unread');
                                    const dot = item.querySelector('.unread-dot');
                                    if (dot) dot.remove();
                                });
                                const headerBadge = notificationHub.querySelector('.js-header-badge');
                                if (headerBadge) headerBadge.remove();
                                const triggerBadge = notificationHub.querySelector('.js-notification-badge');
                                if (triggerBadge) triggerBadge.remove();
                                markAllBtn.remove();
                            }
                        }).catch(() => {});
                    });
                }

                // Individual item click mark as read
                notificationHub.querySelectorAll('.js-notification-item.is-unread').forEach(item => {
                    item.addEventListener('click', function () {
                        const readUrl = this.dataset.readUrl;
                        if (readUrl && csrfToken) {
                            fetch(readUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                keepalive: true
                            }).catch(() => {});
                        }
                    });
                });
            }

            // Click outside to close open popovers
            document.addEventListener('click', function (e) {
                if (searchPopover && !searchPopover.contains(e.target) && !searchTrigger?.contains(e.target)) {
                    searchPopover.classList.remove('show');
                    searchTrigger?.classList.remove('active');
                }
                if (userHub && !userHub.contains(e.target)) {
                    userHub.classList.remove('active');
                }
                if (notificationHub && !notificationHub.contains(e.target)) {
                    notificationHub.classList.remove('active');
                }
            });
        });
    </script>
    @endpush
@endonce

<header class="{{ $headerClasses }} js-site-header">
    {{-- Main Site Header Bar --}}
    <div class="site-header-bar">
        <div class="header-inner">
            <div class="inner-container">
                <div class="c-box">
                    {{-- Logo --}}
                    <div class="logo-inner">
                        <div class="logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('boxcar/images/logo2.svg') }}" alt="BoxCar Showroom" title="BoxCar Showroom">
                            </a>
                        </div>
                    </div>

                    {{-- Main Navigation Menu (Centered & Spacious) --}}
                    <div class="nav-out-bar">
                        <nav class="nav main-menu">
                            <ul class="navigation" id="navbar">
                                <li class="{{ request()->routeIs('home') ? 'current' : '' }}">
                                    <a href="{{ route('home') }}">Trang chủ</a>
                                </li>
                                <li class="current-dropdown {{ $inventoryMenuActive ? 'current' : '' }}">
                                    <a href="{{ route('inventory.index') }}">Kho xe <i class="fa-solid fa-angle-down"></i></a>
                                    <ul class="dropdown">
                                        <li class="{{ request()->routeIs('inventory.index') && !request()->routeIs('inventory.new') && !request()->routeIs('inventory.used') && !request()->routeIs('inventory.cpo') ? 'current' : '' }}">
                                            <a href="{{ route('inventory.index') }}">Tất cả xe</a>
                                        </li>
                                        <li class="{{ request()->routeIs('inventory.new') ? 'current' : '' }}">
                                            <a href="{{ route('inventory.new') }}">Xe mới chính hãng</a>
                                        </li>
                                        <li class="{{ request()->routeIs('inventory.used') ? 'current' : '' }}">
                                            <a href="{{ route('inventory.used') }}">Xe đã qua sử dụng</a>
                                        </li>
                                        <li class="{{ request()->routeIs('inventory.cpo') ? 'current' : '' }}">
                                            <a href="{{ route('inventory.cpo') }}">Xe lướt CPO chứng nhận</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="current-dropdown {{ request()->routeIs('finance') || request()->routeIs('tradein') ? 'current' : '' }}">
                                    <span>Dịch vụ <i class="fa-solid fa-angle-down"></i></span>
                                    <ul class="dropdown">
                                        <li class="{{ request()->routeIs('finance') ? 'current' : '' }}">
                                            <a href="{{ route('finance') }}">Tư vấn tài chính & Trả góp</a>
                                        </li>
                                        <li class="{{ request()->routeIs('tradein') ? 'current' : '' }}">
                                            <a href="{{ route('tradein') }}">Thu cũ đổi mới (Trade-in)</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('contact') }}#test-drive">Đăng ký lái thử xe</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="{{ request()->routeIs('about') ? 'current' : '' }}">
                                    <a href="{{ route('about') }}">Về chúng tôi</a>
                                </li>
                                <li class="{{ request()->routeIs('contact') ? 'current' : '' }}">
                                    <a href="{{ route('contact') }}">Liên hệ</a>
                                </li>
                                <li class="d-lg-none {{ request()->routeIs('login') || request()->routeIs('account.show') ? 'current' : '' }}">
                                    <a href="{{ $accountUrl }}">{{ $accountLabel }}</a>
                                </li>
                                @if (auth()->check() && ! $isStaffOrAdmin)
                                    <li class="d-lg-none">
                                        <a href="{{ route('account.show', ['tab' => 'notifications']) }}">
                                            Thông báo @if (($clientUnreadNotificationsCount ?? 0) > 0) <span class="badge bg-danger rounded-pill ms-1">{{ $clientUnreadNotificationsCount }}</span> @endif
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>

                    {{-- Right Box Actions --}}
                    <div class="right-box">
                        {{-- Search Trigger & Popover --}}
                        <div class="header-search-wrap">
                            <button type="button" class="search-trigger-btn js-search-trigger" title="Tìm kiếm xe" aria-label="Tìm kiếm xe">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <div class="header-search-popover js-search-popover">
                                <form action="{{ route('inventory.index') }}" method="GET">
                                    <div class="search-input-group">
                                        <input type="search" name="q" placeholder="Tìm theo hãng, dòng xe, mã xe..." value="{{ request('q') }}">
                                        <button type="submit" class="submit-btn" title="Tìm kiếm">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Client Notification Hub --}}
                        @if (auth()->check() && ! $isStaffOrAdmin)
                            @php
                                $clientNotifications = $clientNotifications ?? collect();
                                $clientUnreadNotificationsCount = $clientUnreadNotificationsCount ?? 0;
                            @endphp
                            <div class="client-notification-hub js-notification-hub">
                                <button type="button" class="notification-trigger-btn js-notification-trigger" title="Thông báo của bạn" aria-label="Thông báo của bạn">
                                    <i class="fa-regular fa-bell"></i>
                                    @if ($clientUnreadNotificationsCount > 0)
                                        <span class="notification-badge js-notification-badge">{{ $clientUnreadNotificationsCount > 9 ? '9+' : $clientUnreadNotificationsCount }}</span>
                                    @endif
                                </button>
                                <div class="notification-dropdown-menu js-notification-popover">
                                    <div class="notification-dropdown-header">
                                        <div class="header-title">
                                            <span class="fw-bold">Thông báo</span>
                                            @if ($clientUnreadNotificationsCount > 0)
                                                <span class="badge bg-primary rounded-pill js-header-badge">{{ $clientUnreadNotificationsCount }} mới</span>
                                            @endif
                                        </div>
                                        @if ($clientUnreadNotificationsCount > 0)
                                            <button type="button" class="mark-all-read-btn js-mark-all-read" title="Đánh dấu tất cả đã đọc" data-url="{{ route('account.notifications.read-all') }}">
                                                <i class="fa-solid fa-check-double"></i> Đọc tất cả
                                            </button>
                                        @endif
                                    </div>
                                    <div class="notification-dropdown-body">
                                        @forelse ($clientNotifications as $notification)
                                            @php
                                                $data = $notification->data;
                                                $isUnread = $notification->read_at === null;
                                                $actionUrl = $data['action_url'] ?? route('account.show', ['tab' => 'notifications']);
                                                $iconClass = $data['icon'] ?? 'fa-solid fa-bell';
                                            @endphp
                                            <a href="{{ $actionUrl }}" class="notification-item {{ $isUnread ? 'is-unread' : '' }} js-notification-item" data-id="{{ $notification->id }}" data-read-url="{{ route('account.notifications.read', $notification->id) }}">
                                                <div class="item-icon">
                                                    <i class="{{ $iconClass }}"></i>
                                                </div>
                                                <div class="item-content">
                                                    <div class="item-title">{{ $data['title'] ?? 'Thông báo từ Showroom' }}</div>
                                                    <div class="item-message">{{ Str::limit($data['message'] ?? '', 75) }}</div>
                                                    <div class="item-time">{{ $notification->created_at->diffForHumans() }}</div>
                                                </div>
                                                @if ($isUnread)
                                                    <span class="unread-dot"></span>
                                                @endif
                                            </a>
                                        @empty
                                            <div class="notification-empty">
                                                <i class="fa-regular fa-bell-slash text-muted mb-2" style="font-size: 24px;"></i>
                                                <p class="mb-0 text-muted small">Bạn chưa có thông báo nào</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    <div class="notification-dropdown-footer">
                                        <a href="{{ route('account.show', ['tab' => 'notifications']) }}" class="view-all-link">
                                            Xem tất cả thông báo <i class="fa-solid fa-angle-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- User Account Hub --}}
                        @if (auth()->check())
                            <div class="user-account-hub js-user-hub">
                                <button type="button" class="user-account-trigger js-user-trigger">
                                    <span class="user-avatar-badge">
                                        {{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}
                                    </span>
                                    <span class="d-none d-md-inline">{{ Str::limit($currentUser->name, 12) }}</span>
                                    <i class="fa-solid fa-angle-down text-muted" style="font-size: 11px;"></i>
                                </button>
                                <div class="user-dropdown-menu">
                                    <div class="px-3 py-2 border-bottom mb-1">
                                        <div class="fw-bold text-dark">{{ $currentUser->name }}</div>
                                        <div class="text-muted small">{{ $currentUser->email }}</div>
                                    </div>
                                    @if ($isStaffOrAdmin)
                                        <a href="{{ route('admin.dashboard') }}" class="user-dropdown-item text-primary fw-semibold">
                                            <i class="fa-solid fa-shield-halved"></i>
                                            <span>Khu vực quản trị</span>
                                        </a>
                                    @else
                                        <a href="{{ route('account.show') }}" class="user-dropdown-item">
                                            <i class="fa-solid fa-user-gear"></i>
                                            <span>Thông tin tài khoản</span>
                                        </a>
                                        <a href="{{ route('account.show', ['tab' => 'notifications']) }}" class="user-dropdown-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa-regular fa-bell"></i>
                                                <span>Thông báo của tôi</span>
                                            </div>
                                            @if (($clientUnreadNotificationsCount ?? 0) > 0)
                                                <span class="badge bg-danger rounded-pill">{{ $clientUnreadNotificationsCount }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('account.show', ['tab' => 'appointments']) }}" class="user-dropdown-item">
                                            <i class="fa-solid fa-calendar-check"></i>
                                            <span>Lịch hẹn của tôi</span>
                                        </a>
                                    @endif
                                    <div class="user-dropdown-divider"></div>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="user-dropdown-item text-danger">
                                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                            <span>Đăng xuất</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="user-account-trigger">
                                <i class="fa-regular fa-circle-user text-primary"></i>
                                <span>Đăng nhập</span>
                            </a>
                        @endif

                        {{-- Primary CTA Button --}}
                        <a href="{{ route('contact') }}#test-drive" class="btn-test-drive-cta">
                            <i class="fa-solid fa-steering-wheel"></i>
                            <span>Đặt lịch lái thử</span>
                        </a>

                        {{-- Mobile Menu Hamburger --}}
                        <div class="mobile-navigation">
                            <a href="#nav-mobile" title="Mở menu">
                                <svg width="22" height="11" viewBox="0 0 22 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="22" height="2" fill="currentColor"/>
                                    <rect y="9" width="22" height="2" fill="currentColor"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="nav-mobile"></div>
    </div>
</header>
