<div class="admin-page-header">
    <div class="admin-page-title-block">
        <span class="admin-page-kicker">Showroom control</span>
        <h3 class="title">{{ $adminPageTitle ?? 'Admin' }}</h3>
        <div class="text">{{ $adminPageDescription ?? 'Quan ly cac module van hanh showroom theo tung nghiep vu.' }}</div>
    </div>

    @hasSection('page-actions')
        <div class="admin-page-actions">
            @yield('page-actions')
        </div>
    @endif
</div>
