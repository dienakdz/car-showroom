<div class="admin-page-header">
    <div class="admin-page-title-block">
        <span class="admin-page-kicker">Phân hệ quản trị showroom</span>
        <h2 class="admin-page-title">{{ $adminPageTitle ?? 'Bảng điều khiển' }}</h2>
        <div class="admin-page-description">{{ $adminPageDescription ?? 'Quản lý các module vận hành showroom theo từng nghiệp vụ chuyên môn.' }}</div>
    </div>

    @hasSection('page-actions')
        <div class="admin-page-actions">
            @yield('page-actions')
        </div>
    @endif
</div>
