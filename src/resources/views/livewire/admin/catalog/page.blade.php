<div class="catalog-workspace">
    <div class="list-title catalog-list-title">
        <div>
            <h3 class="title">Catalog Workspace</h3>
            <div class="text">Dung lai pattern cua template dashboard de quan ly makes, models va trims ro rang hon.</div>
        </div>

        <a href="{{ route('admin.catalog.trims.create') }}" class="theme-btn small">
            Tao trim chi tiet
        </a>
    </div>

    <div class="form-box catalog-module-box">
        <ul class="nav nav-tabs catalog-module-tabs" role="tablist" aria-label="Catalog sections">
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link {{ $tab === 'makes' ? 'active' : '' }}"
                    wire:click="switchTab('makes')"
                    aria-selected="{{ $tab === 'makes' ? 'true' : 'false' }}"
                >
                    Makes
                    <span>{{ number_format($summary['makes']) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link {{ $tab === 'models' ? 'active' : '' }}"
                    wire:click="switchTab('models')"
                    aria-selected="{{ $tab === 'models' ? 'true' : 'false' }}"
                >
                    Models
                    <span>{{ number_format($summary['models']) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link {{ $tab === 'trims' ? 'active' : '' }}"
                    wire:click="switchTab('trims')"
                    aria-selected="{{ $tab === 'trims' ? 'true' : 'false' }}"
                >
                    Trims
                    <span>{{ number_format($summary['trims']) }}</span>
                </button>
            </li>
        </ul>
    </div>

    @if ($tab === 'makes')
        <livewire:admin.catalog.makes.manager />
    @endif

    @if ($tab === 'models')
        <livewire:admin.catalog.models.manager />
    @endif

    @if ($tab === 'trims')
        <livewire:admin.catalog.trims.manager />
    @endif
</div>
