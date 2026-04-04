@php
    $adminUser = $adminCurrentUser;
    $adminSidebarModules = [
        [
            'kind' => 'link',
            'label' => 'Dashboard',
            'icon' => 'fa fa-th-large',
            'route' => 'admin.dashboard',
            'patterns' => ['admin.dashboard'],
        ],
        [
            'kind' => 'submenu',
            'label' => 'Catalog',
            'icon' => 'fa fa-folder-open',
            'children' => [
                ['permission' => 'catalog.manage', 'route' => 'admin.catalog.makes.index', 'label' => 'Makes', 'patterns' => ['admin.catalog.makes.*']],
                ['permission' => 'catalog.manage', 'route' => 'admin.catalog.models.index', 'label' => 'Models', 'patterns' => ['admin.catalog.models.*']],
                ['permission' => 'catalog.manage', 'route' => 'admin.catalog.trims.index', 'label' => 'Trims', 'patterns' => ['admin.catalog.trims.*']],
            ],
        ],
        [
            'kind' => 'link',
            'label' => 'Inventory',
            'icon' => 'fa fa-car',
            'route' => 'admin.inventory.index',
            'patterns' => ['admin.inventory.*'],
            'permission' => 'inventory.manage',
        ],
        [
            'kind' => 'link',
            'label' => 'Sales',
            'icon' => 'fa fa-line-chart',
            'route' => 'admin.sales.index',
            'patterns' => ['admin.sales.*'],
            'permission' => 'sales.manage',
        ],
        [
            'kind' => 'submenu',
            'label' => 'CRM',
            'icon' => 'fa fa-users',
            'children' => [
                ['permission' => 'leads.manage', 'route' => 'admin.leads.index', 'label' => 'Leads', 'patterns' => ['admin.leads.*']],
                ['permission' => 'appointments.manage', 'route' => 'admin.appointments.index', 'label' => 'Appointments', 'patterns' => ['admin.appointments.*']],
                ['permission' => 'reviews.approve', 'route' => 'admin.reviews.index', 'label' => 'Reviews', 'patterns' => ['admin.reviews.*']],
            ],
        ],
        [
            'kind' => 'link',
            'label' => 'Settings',
            'icon' => 'fa fa-cog',
            'route' => 'admin.settings.index',
            'patterns' => ['admin.settings.*'],
            'permission' => 'settings.manage',
        ],
    ];

    $resolvedSidebarModules = collect($adminSidebarModules)->map(function (array $module) use ($adminUser): array {
        if (($module['kind'] ?? 'link') === 'submenu') {
            $children = collect($module['children'])
                ->map(function (array $child) use ($adminUser): array {
                    $child['can_access'] = empty($child['permission']) || $adminUser?->hasPermission($child['permission']);
                    $child['is_active'] = request()->routeIs(...$child['patterns']);

                    return $child;
                })
                ->values()
                ->all();

            $module['children'] = $children;
            $module['is_active'] = collect($children)->contains(fn (array $child): bool => $child['is_active']);

            return $module;
        }

        $module['can_access'] = empty($module['permission']) || $adminUser?->hasPermission($module['permission']);
        $module['is_active'] = request()->routeIs(...$module['patterns']);

        return $module;
    });
@endphp

<aside class="admin-sidebar" aria-label="Admin navigation">
    <nav class="admin-sidebar-nav">
        <ul class="admin-sidebar-root">
            @foreach ($resolvedSidebarModules as $module)
                @if (($module['kind'] ?? 'link') === 'submenu')
                    <li class="admin-sidebar-root-item">
                        <details class="admin-sidebar-submenu {{ $module['is_active'] ? 'is-active' : '' }}" open>
                            <summary class="admin-sidebar-link admin-sidebar-link-parent {{ $module['is_active'] ? 'is-active' : '' }}">
                                <span class="admin-sidebar-icon">
                                    <i class="{{ $module['icon'] }}" aria-hidden="true"></i>
                                </span>
                                <span class="admin-sidebar-copy">
                                    <strong>{{ $module['label'] }}</strong>
                                </span>
                                <span class="admin-sidebar-chevron">
                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                                </span>
                            </summary>

                            <ul class="admin-sidebar-submenu-list">
                                @foreach ($module['children'] as $child)
                                    <li>
                                        @if ($child['can_access'])
                                            <a
                                                href="{{ route($child['route']) }}"
                                                class="admin-sidebar-sublink {{ $child['is_active'] ? 'is-active' : '' }}"
                                                @if ($child['is_active']) aria-current="page" @endif
                                            >
                                                <span>{{ $child['label'] }}</span>
                                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                                            </a>
                                        @else
                                            <span class="admin-sidebar-sublink is-disabled" aria-disabled="true">
                                                <span>{{ $child['label'] }}</span>
                                                <i class="fa fa-lock" aria-hidden="true"></i>
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    </li>
                @else
                    <li class="admin-sidebar-root-item">
                        @if ($module['can_access'])
                            <a
                                href="{{ route($module['route']) }}"
                                class="admin-sidebar-link {{ $module['is_active'] ? 'is-active' : '' }}"
                                @if ($module['is_active']) aria-current="page" @endif
                            >
                                <span class="admin-sidebar-icon">
                                    <i class="{{ $module['icon'] }}" aria-hidden="true"></i>
                                </span>
                                <span class="admin-sidebar-copy">
                                    <strong>{{ $module['label'] }}</strong>
                                </span>
                                <span class="admin-sidebar-chevron">
                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                </span>
                            </a>
                        @else
                            <span class="admin-sidebar-link is-disabled" aria-disabled="true">
                                <span class="admin-sidebar-icon">
                                    <i class="{{ $module['icon'] }}" aria-hidden="true"></i>
                                </span>
                                <span class="admin-sidebar-copy">
                                    <strong>{{ $module['label'] }}</strong>
                                </span>
                                <span class="admin-sidebar-chevron">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </span>
                            </span>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
</aside>
