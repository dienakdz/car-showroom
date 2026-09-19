<header class="c1-topbar">
    <div class="c1-topbar-search">
        <i class="fa fa-search c1-search-icon" aria-hidden="true"></i>
        <input
            type="text"
            class="c1-search-input"
            placeholder="Tìm kiếm xe, số khung VIN, khách hàng..."
            aria-label="Tìm kiếm trong hệ thống"
        >
    </div>

    <div class="c1-topbar-right">
        <button
            type="button"
            class="c1-theme-toggle"
            id="adminThemeToggle"
            onclick="window.toggleAdminTheme && window.toggleAdminTheme(event)"
            title="Chuyển sang chế độ tối"
            aria-label="Chuyển đổi giao diện Sáng / Tối"
        >
            <span class="c1-theme-toggle-icon c1-icon-moon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </span>
            <span class="c1-theme-toggle-icon c1-icon-sun">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4.5" fill="currentColor"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
            </span>
        </button>

        <a href="{{ route('home') }}" target="_blank" rel="noreferrer" class="c1-btn-site" title="Mở website khách hàng">
            <i class="fa fa-external-link" aria-hidden="true"></i>
            <span>Xem website</span>
        </a>

        <div class="c1-notif-wrap" title="Thông báo hệ thống">
            <i class="fa fa-bell c1-notif-icon" aria-hidden="true"></i>
            <span class="c1-notif-badge"></span>
        </div>

        <div class="c1-user-wrap">
            <div class="c1-user-avatar">
                {{ strtoupper(substr($adminCurrentUser?->name ?? 'A', 0, 1)) }}
            </div>
            <div class="c1-user-details">
                <span class="c1-user-name">{{ $adminCurrentUser?->name ?? 'Admin User' }}</span>
                <span class="c1-user-role">{{ $adminRoleLabel ?? 'Quản trị viên' }}</span>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST" class="c1-logout-form">
                @csrf
                <button type="submit" class="c1-logout-btn" title="Đăng xuất khỏi hệ thống">
                    <i class="fa fa-sign-out" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
</header>
