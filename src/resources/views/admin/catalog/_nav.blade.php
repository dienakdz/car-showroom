<div class="form-box admin-subnav-box">
    <ul class="nav nav-tabs admin-subnav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a
                href="{{ route('admin.catalog.makes.index') }}"
                class="nav-link {{ ($catalogTab ?? '') === 'makes' ? 'active' : '' }}"
            >
                Makes
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a
                href="{{ route('admin.catalog.models.index') }}"
                class="nav-link {{ ($catalogTab ?? '') === 'models' ? 'active' : '' }}"
            >
                Models
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a
                href="{{ route('admin.catalog.trims.index') }}"
                class="nav-link {{ ($catalogTab ?? '') === 'trims' ? 'active' : '' }}"
            >
                Trims
            </a>
        </li>
    </ul>
</div>
