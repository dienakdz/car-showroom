<div class="admin-catalog-workspace">
    <div class="admin-page-header">
        <div class="admin-page-title-block">
            <span class="admin-page-kicker">Showroom control</span>
            <h3 class="title">Catalog Workspace</h3>
            <div class="text">Quan ly makes, models va trims theo module tach biet, toi uu cho CRUD nhanh va de mo rong ve sau.</div>
        </div>

        <div class="admin-page-actions">
            <a href="{{ route('admin.catalog.trims.create') }}" class="admin-action-btn">Tao trim chi tiet</a>
        </div>
    </div>

    <section class="admin-catalog-summary-grid">
        <article class="admin-catalog-summary-card">
            <span class="admin-catalog-summary-label">Makes</span>
            <strong>{{ number_format($summary['makes']) }}</strong>
            <p>Danh muc thuong hieu va logo dang duoc dung trong catalog.</p>
        </article>
        <article class="admin-catalog-summary-card">
            <span class="admin-catalog-summary-label">Models</span>
            <strong>{{ number_format($summary['models']) }}</strong>
            <p>Dong xe theo tung hang de lam nen cho trims va inventory.</p>
        </article>
        <article class="admin-catalog-summary-card">
            <span class="admin-catalog-summary-label">Trims</span>
            <strong>{{ number_format($summary['trims']) }}</strong>
            <p>Phien ban co the duoc sua nhanh hoac mo sang form chi tiet.</p>
        </article>
        <article class="admin-catalog-summary-card is-highlight">
            <span class="admin-catalog-summary-label">Architecture</span>
            <strong>Modular</strong>
            <p>Page chi dieu huong va tong hop. Moi module tu handle state va CRUD rieng.</p>
        </article>
    </section>

    <div class="admin-catalog-tabs" role="tablist" aria-label="Catalog sections">
        <button
            type="button"
            class="admin-catalog-tab {{ $tab === 'makes' ? 'is-active' : '' }}"
            wire:click="switchTab('makes')"
            aria-pressed="{{ $tab === 'makes' ? 'true' : 'false' }}"
        >
            <span>Makes</span>
            <small>{{ number_format($summary['makes']) }}</small>
        </button>
        <button
            type="button"
            class="admin-catalog-tab {{ $tab === 'models' ? 'is-active' : '' }}"
            wire:click="switchTab('models')"
            aria-pressed="{{ $tab === 'models' ? 'true' : 'false' }}"
        >
            <span>Models</span>
            <small>{{ number_format($summary['models']) }}</small>
        </button>
        <button
            type="button"
            class="admin-catalog-tab {{ $tab === 'trims' ? 'is-active' : '' }}"
            wire:click="switchTab('trims')"
            aria-pressed="{{ $tab === 'trims' ? 'true' : 'false' }}"
        >
            <span>Trims</span>
            <small>{{ number_format($summary['trims']) }}</small>
        </button>
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
