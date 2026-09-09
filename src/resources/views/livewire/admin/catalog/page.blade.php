<div class="c1-catalog-workspace">
    <div class="c1-page-header">
        <div>
            <h1 class="c1-page-title">Danh mục xe</h1>
            <p class="c1-page-subtitle">Quản lý Hãng xe (Makes), Dòng xe (Models) và Phiên bản xe (Trims) đồng bộ và trực quan.</p>
        </div>

        <div class="c1-header-actions">
            @if ($tab === 'makes')
                <button type="button" class="c1-btn c1-btn-primary" onclick="document.getElementById('c1-make-form-section')?.scrollIntoView({behavior: 'smooth'})">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
                    + Thêm hãng xe
                </button>
            @elseif ($tab === 'models')
                <button type="button" class="c1-btn c1-btn-primary" onclick="document.getElementById('c1-model-form-section')?.scrollIntoView({behavior: 'smooth'})">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
                    + Thêm dòng xe
                </button>
            @else
                <a href="{{ route('admin.catalog.trims.create') }}" class="c1-btn c1-btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
                    + Thêm phiên bản
                </a>
            @endif
        </div>
    </div>

    <div class="c1-catalog-tabs" role="tablist" aria-label="Catalog sections">
        <button
            type="button"
            class="c1-catalog-tab-btn {{ $tab === 'makes' ? 'active' : '' }}"
            wire:click="switchTab('makes')"
            aria-selected="{{ $tab === 'makes' ? 'true' : 'false' }}"
        >
            <span>Hãng xe (Makes)</span>
            <span class="c1-catalog-tab-badge">{{ number_format($summary['makes']) }}</span>
        </button>
        <button
            type="button"
            class="c1-catalog-tab-btn {{ $tab === 'models' ? 'active' : '' }}"
            wire:click="switchTab('models')"
            aria-selected="{{ $tab === 'models' ? 'true' : 'false' }}"
        >
            <span>Dòng xe (Models)</span>
            <span class="c1-catalog-tab-badge">{{ number_format($summary['models']) }}</span>
        </button>
        <button
            type="button"
            class="c1-catalog-tab-btn {{ $tab === 'trims' ? 'active' : '' }}"
            wire:click="switchTab('trims')"
            aria-selected="{{ $tab === 'trims' ? 'true' : 'false' }}"
        >
            <span>Phiên bản (Trims)</span>
            <span class="c1-catalog-tab-badge">{{ number_format($summary['trims']) }}</span>
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

<script>
    (() => {
        const initCatalogWorkspace = () => {
            if (window.catalogWorkspaceBound) {
                return;
            }

            window.catalogWorkspaceBound = true;

            const modalRegistry = {
                catalogMakeEditModal: 'syncEditModalClosed',
                catalogModelEditModal: 'syncEditModalClosed',
            };

            const blurActiveElement = () => {
                if (document.activeElement instanceof HTMLElement) {
                    document.activeElement.blur();
                }
            };

            const cleanupModalArtifacts = () => {
                if (document.querySelector('.modal.show')) {
                    return;
                }

                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');

                document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
                    backdrop.remove();
                });
            };

            const syncModalClosed = (modalElement, methodName) => {
                const componentRoot = modalElement.closest('[wire\\:id]');

                if (!componentRoot || typeof window.Livewire === 'undefined' || typeof Livewire.find !== 'function') {
                    return;
                }

                const component = Livewire.find(componentRoot.getAttribute('wire:id'));

                if (component) {
                    component.call(methodName);
                }
            };

            const ensureModalBound = (modalId) => {
                const modalElement = document.getElementById(modalId);
                const methodName = modalRegistry[modalId];

                if (!modalElement || !methodName || typeof bootstrap === 'undefined') {
                    return null;
                }

                if (modalElement.dataset.catalogModalBound !== 'true') {
                    modalElement.dataset.catalogModalBound = 'true';

                    modalElement.addEventListener('hide.bs.modal', () => {
                        blurActiveElement();
                    });

                    modalElement.addEventListener('hidden.bs.modal', () => {
                        const shouldSkipSync = modalElement.dataset.catalogModalSkipSync === 'true';

                        modalElement.dataset.catalogModalSkipSync = 'false';
                        cleanupModalArtifacts();

                        if (!shouldSkipSync) {
                            syncModalClosed(modalElement, methodName);
                        }
                    });
                }

                return {
                    element: modalElement,
                    instance: bootstrap.Modal.getOrCreateInstance(modalElement),
                };
            };

            const hideModal = (modal) => {
                if (!modal) {
                    cleanupModalArtifacts();
                    return;
                }

                if (!modal.element.classList.contains('show')) {
                    modal.element.dataset.catalogModalSkipSync = 'false';
                    cleanupModalArtifacts();
                    return;
                }

                blurActiveElement();
                modal.element.dataset.catalogModalSkipSync = 'true';
                modal.instance.hide();
            };

            const openModal = (modalId) => {
                cleanupModalArtifacts();

                const modal = ensureModalBound(modalId);

                if (!modal) {
                    return;
                }

                modal.instance.show();
            };

            const closeModal = (modalId) => {
                const modal = ensureModalBound(modalId);

                hideModal(modal);
            };

            const closeAllModals = () => {
                Object.keys(modalRegistry).forEach((modalId) => {
                    hideModal(ensureModalBound(modalId));
                });

                cleanupModalArtifacts();
            };

            Livewire.on('catalog-toast', (event) => {
                const payload = event || {};
                const type = payload.type || 'success';
                const message = payload.message || '';

                if (!message || typeof window.toastr === 'undefined' || typeof window.toastr[type] !== 'function') {
                    return;
                }

                window.toastr[type](message);
            });

            Livewire.on('catalog-make-edit-modal-opened', () => {
                openModal('catalogMakeEditModal');
            });

            Livewire.on('catalog-make-edit-modal-closed', () => {
                closeModal('catalogMakeEditModal');
            });

            Livewire.on('catalog-model-edit-modal-opened', () => {
                openModal('catalogModelEditModal');
            });

            Livewire.on('catalog-model-edit-modal-closed', () => {
                closeModal('catalogModelEditModal');
            });

            Livewire.on('catalog-close-all-modals', () => {
                closeAllModals();
            });
        };

        document.addEventListener('livewire:init', initCatalogWorkspace);

        if (window.Livewire) {
            initCatalogWorkspace();
        }
    })();
</script>
