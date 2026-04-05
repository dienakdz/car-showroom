@php($activeCatalogTab = $catalogTab ?? request()->query('tab', 'makes'))
<div class="form-box admin-subnav-box">
    <ul class="nav nav-tabs admin-subnav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a
                href="{{ route('admin.catalog.index', ['tab' => 'makes']) }}"
                class="nav-link {{ $activeCatalogTab === 'makes' ? 'active' : '' }}"
            >
                Makes
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a
                href="{{ route('admin.catalog.index', ['tab' => 'models']) }}"
                class="nav-link {{ $activeCatalogTab === 'models' ? 'active' : '' }}"
            >
                Models
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a
                href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}"
                class="nav-link {{ $activeCatalogTab === 'trims' ? 'active' : '' }}"
            >
                Trims
            </a>
        </li>
    </ul>
</div>
