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
