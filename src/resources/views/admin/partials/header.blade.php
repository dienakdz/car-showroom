@php
    $adminHeaderBrand = $adminBrandName ?? $adminShowroom?->name ?? 'Car Showroom';
@endphp

<header class="admin-topbar">
    <div class="admin-topbar-inner">
        <div class="admin-topbar-brand">
            <a href="{{ route('admin.dashboard') }}" class="admin-topbar-logo" aria-label="Admin dashboard">
                <img src="{{ asset('boxcar/images/logo.svg') }}" alt="{{ $adminHeaderBrand }}" title="{{ $adminHeaderBrand }}">
            </a>

            <div class="admin-topbar-brand-copy">
                <span class="admin-overline">Admin workspace</span>
                <strong>{{ $adminHeaderBrand }}</strong>
                <small>{{ $adminShowroom?->address ?: 'Dieu phoi catalog, inventory, CRM va giao dich showroom.' }}</small>
            </div>
        </div>

        <div class="admin-topbar-actions">
            <a href="{{ route('home') }}" target="_blank" rel="noreferrer" class="admin-public-link">
                Mo public site
            </a>

            <div class="admin-user-chip">
                <span class="admin-user-avatar">{{ strtoupper(substr($adminCurrentUser?->name ?? 'A', 0, 1)) }}</span>
                <div class="admin-user-meta">
                    <span class="admin-user-label">{{ $adminRoleLabel }}</span>
                    <strong>{{ $adminCurrentUser?->name ?? 'Admin User' }}</strong>
                </div>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="admin-logout-btn">Dang xuat</button>
            </form>
        </div>
    </div>
</header>
