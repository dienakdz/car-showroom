@php
    $adminPermissions = $adminPermissionMap ?? [];
    $adminNavModules = [
        [
            'label' => 'Bảng điều khiển',
            'sublabel' => 'Dashboard',
            'icon' => 'fa fa-home',
            'route' => 'admin.dashboard',
            'patterns' => ['admin.dashboard'],
        ],
        [
            'label' => 'Kho xe',
            'sublabel' => 'Inventory',
            'icon' => 'fa fa-car',
            'route' => 'admin.inventory.index',
            'patterns' => ['admin.inventory.*'],
            'permission' => 'inventory.manage',
        ],
        [
            'label' => 'Danh mục xe',
            'sublabel' => 'Catalog',
            'icon' => 'fa fa-th-large',
            'route' => 'admin.catalog.index',
            'patterns' => ['admin.catalog.*'],
            'permission' => 'catalog.manage',
        ],
        [
            'label' => 'Bán hàng',
            'sublabel' => 'Sales',
            'icon' => 'fa fa-usd',
            'route' => 'admin.sales.index',
            'patterns' => ['admin.sales.*'],
            'permission' => 'sales.manage',
        ],
        [
            'label' => 'Khách hàng (CRM)',
            'sublabel' => 'CRM Leads',
            'icon' => 'fa fa-users',
            'route' => 'admin.leads.index',
            'patterns' => ['admin.leads.*'],
            'permission' => 'leads.manage',
        ],
        [
            'label' => 'Lịch hẹn',
            'sublabel' => 'Appointments',
            'icon' => 'fa fa-calendar-check',
            'route' => 'admin.appointments.index',
            'patterns' => ['admin.appointments.*'],
            'permission' => 'appointments.manage',
        ],
        [
            'label' => 'Cài đặt',
            'sublabel' => 'Settings',
            'icon' => 'fa fa-cog',
            'route' => 'admin.settings.index',
            'patterns' => ['admin.settings.*'],
            'permission' => 'settings.manage',
        ],
    ];
@endphp

<aside class="c1-sidebar" aria-label="Điều hướng quản trị">
    <div class="c1-sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" wire:navigate.hover class="c1-brand-link">
            <img src="{{ asset('boxcar/images/logo2.svg') }}" alt="BoxCar" class="c1-brand-logo">
        </a>
    </div>

    <nav class="c1-sidebar-nav">
        <ul class="c1-nav-list">
            @foreach ($adminNavModules as $module)
                @php
                    $canAccess = empty($module['permission']) || ($adminPermissions[$module['permission']] ?? false);
                    $isActive = request()->routeIs(...$module['patterns']);
                @endphp
                <li class="c1-nav-item">
                    @if ($canAccess)
                        <a
                            href="{{ route($module['route']) }}"
                            wire:navigate.hover
                            class="c1-nav-link {{ $isActive ? 'is-active' : '' }}"
                            @if ($isActive) aria-current="page" @endif
                        >
                            <i class="{{ $module['icon'] }} c1-nav-icon" aria-hidden="true"></i>
                            <span class="c1-nav-text">{{ $module['label'] }}</span>
                        </a>
                    @else
                        <span class="c1-nav-link is-disabled" title="Chưa được cấp quyền">
                            <i class="{{ $module['icon'] }} c1-nav-icon" aria-hidden="true"></i>
                            <span class="c1-nav-text">{{ $module['label'] }}</span>
                            <i class="fa fa-lock c1-nav-lock" aria-hidden="true"></i>
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
</aside>
