@php($editIcon = asset('boxcar/images/icons/edit.svg'))
@php($deleteIcon = asset('boxcar/images/icons/remove.svg'))
@php($fallbackLogo = asset('boxcar/images/resource/brandf.png'))
@php($uploadIcon = asset('boxcar/images/resource/uplode.svg'))
@php($createPreview = $this->createLogoPreviewUrl ?: $fallbackLogo)
@php($createUploadName = is_object($logoUpload) && method_exists($logoUpload, 'getClientOriginalName') ? $logoUpload->getClientOriginalName() : null)
@php($editingMake = $editingId ? $makes->getCollection()->firstWhere('id', $editingId) : null)
@php($editPreview = $this->editLogoPreviewUrl ?: ($editingMake?->logo_url ?: $fallbackLogo))
@php($editUploadName = is_object($editLogoUpload) && method_exists($editLogoUpload, 'getClientOriginalName') ? $editLogoUpload->getClientOriginalName() : null)

<div class="catalog-module">
    <div class="form-box catalog-form-box">
        <div class="catalog-box-head">
            <div>
                <h4>Tao make moi</h4>
                <p>Dung form template de them thuong hieu va logo ma khong can roi workspace.</p>
            </div>
        </div>

        <form wire:submit="create" class="catalog-make-form catalog-make-profile-form">
            <div class="gallery-sec catalog-make-gallery-section">
                <div class="right-box-three catalog-make-gallery-block">
                    <h6 class="title">Gallery</h6>

                    <div class="gallery-box">
                        <div class="inner-box catalog-upload-with-preview">
                            <div class="image-box catalog-upload-preview">
                                <img src="{{ $createPreview }}" alt="Logo preview">
                            </div>

                            <label class="uplode-box catalog-upload-trigger catalog-upload-trigger-wide">
                                <input type="file" class="catalog-upload-input" wire:model="logoUpload"
                                    accept=".png,.jpg,.jpeg,.svg,.webp">
                                <div class="content-box">
                                    <img src="{{ $uploadIcon }}" alt="Upload">
                                    <span>{{ $logoUpload ? 'Doi logo' : 'Upload' }}</span>
                                </div>
                            </label>
                        </div>

                        <div class="text catalog-make-gallery-text">
                            Max file size 2MB. Dinh dang ho tro: SVG, PNG, JPG, WebP.
                            @if ($createUploadName)
                                <br>Dang chon: {{ $createUploadName }}
                            @endif
                        </div>

                        @if ($logoUpload)
                            <button type="button" class="catalog-upload-clear" wire:click="removeCreateLogo">Bo chon
                                file</button>
                        @endif
                    </div>

                    <span class="catalog-head-note" wire:loading wire:target="logoUpload">Dang tai logo...</span>
                    @error('logoUpload')
                        <small class="catalog-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-sec catalog-make-gallery-form">
                    <div class="row">
                        <div class="form-column col-lg-4 col-md-6">
                            <div class="form_boxes">
                                <label>Ten hang xe</label>
                                <div class="drop-menu catalog-native-control">
                                    <input type="text" wire:model.blur="createForm.name" placeholder="Toyota">
                                </div>
                                @error('createForm.name')
                                    <small class="catalog-field-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-4 col-md-6">
                            <div class="form_boxes">
                                <label>Slug</label>
                                <div class="drop-menu catalog-native-control">
                                    <input type="text" wire:model.blur="createForm.slug" placeholder="toyota">
                                </div>
                                @error('createForm.slug')
                                    <small class="catalog-field-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-4 col-md-12"
                            style="display:flex; flex-direction: column; justify-content: flex-end;">
                            <button type="submit" class="theme-btn catalog-make-action-btn" wire:loading.attr="disabled"
                                wire:target="create,logoUpload">
                                Tao make
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="my-listing-table wrap-listing">
        <div class="title-listing">
            <div>
                <h4 class="catalog-section-title">Make directory</h4>
                <p class="catalog-section-text">UI table cua template duoc dung lai de quan ly danh muc thuong hieu ro
                    rang hon.</p>
            </div>

            <div class="catalog-toolbar">
                <div class="box-ip-search catalog-search-box">
                    <span class="icon">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.29301 0.287598C2.9872 0.287598 0.294312 2.98048 0.294312 6.28631C0.294312 9.59211 2.9872 12.2902 6.29301 12.2902C7.70502 12.2902 9.00364 11.7954 10.03 10.9738L12.5287 13.4712C12.6548 13.5921 12.8232 13.6588 12.9979 13.657C13.1725 13.6552 13.3395 13.5851 13.4631 13.4617C13.5867 13.3382 13.6571 13.1713 13.6591 12.9967C13.6611 12.822 13.5947 12.6535 13.474 12.5272L10.9753 10.0285C11.7976 9.00061 12.293 7.69995 12.293 6.28631C12.293 2.98048 9.59882 0.287598 6.29301 0.287598ZM6.29301 1.62095C8.87824 1.62095 10.9584 3.70108 10.9584 6.28631C10.9584 8.87153 8.87824 10.9569 6.29301 10.9569C3.70778 10.9569 1.62764 8.87153 1.62764 6.28631C1.62764 3.70108 3.70778 1.62095 6.29301 1.62095Z"
                                fill="#050B20" />
                        </svg>
                    </span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Tim theo ten hoac slug">
                </div>

                <div class="text-box v1 catalog-toolbar-boxes">
                    <div class="form_boxes v3 catalog-control-box">
                        <small>Sort by</small>
                        <div class="drop-menu">
                            <select wire:model.live="sort">
                                <option value="updated_desc"> moi nhat</option>
                                <option value="updated_asc">cu nhat</option>
                                <option value="name_asc">Ten A-Z</option>
                                <option value="name_desc">Ten Z-A</option>
                                <option value="models_desc">Nhieu models nhat</option>
                                <option value="models_asc">It models nhat</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cart-table">
            <table>
                <thead>
                    <tr>
                        <th>Make</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Models</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($makes as $make)
                        <tr wire:key="make-row-{{ $make->id }}">
                            <td>
                                <div class="shop-cart-product">
                                    <div class="shop-product-cart-img catalog-listing-thumb">
                                        <img src="{{ $make->logo_url ?: $fallbackLogo }}"
                                            alt="{{ $make->name }} logo">
                                    </div>
                                </div>
                            </td>
                            <td><span>{{ $make->name }}</span></td>
                            <td><span>{{ $make->slug }}</span></td>
                            <td><span>{{ number_format($make->models_count) }}</span></td>
                            <td><span>{{ optional($make->updated_at)->format('d/m/Y H:i') ?: '--' }}</span></td>
                            <td>
                                <button type="button" class="remove-cart-item"
                                    wire:click="startEdit({{ $make->id }})" title="Sua make">
                                    <img src="{{ $editIcon }}" alt="Edit">
                                </button>
                                <button type="button" class="remove-cart-item"
                                    wire:click="delete({{ $make->id }})"
                                    onclick="return confirm('Xac nhan xoa make nay?')" title="Xoa make">
                                    <img src="{{ $deleteIcon }}" alt="Delete">
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="catalog-empty-state">Chua co make nao phu hop bo loc hien tai.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="catalog-table-footer">
            <div class="catalog-table-summary">
                @if ($makes->total() > 0)
                    Hien thi {{ $makes->firstItem() }}-{{ $makes->lastItem() }} /
                    {{ number_format($makes->total()) }} make
                @else
                    Khong co du lieu
                @endif
            </div>

            <div class="catalog-table-footer-actions">
                <div class="form_boxes v3 catalog-per-page catalog-control-box">
                    <small>Rows</small>
                    <div class="drop-menu">
                        <select wire:model.live="perPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>

                <div class="catalog-pagination">
                    {{ $makes->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="catalogMakeEditModal" tabindex="-1" aria-labelledby="catalogMakeEditModalLabel"
        aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable catalog-edit-modal-dialog">
            <div class="modal-content catalog-edit-modal-content">
                <div class="modal-header catalog-edit-modal-header">
                    <div>
                        <h5 class="modal-title catalog-edit-modal-title" id="catalogMakeEditModalLabel">Sua make</h5>
                        <p class="catalog-edit-modal-text">Cap nhat ten, slug va logo trong mot modal gon hon, de doc va de thao tac.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body catalog-edit-modal-body">
                    @if ($editingMake)
                        <form wire:submit="update" class="catalog-make-form catalog-make-profile-form">
                            <div class="gallery-sec catalog-make-gallery-section">
                                <div class="right-box-three catalog-make-gallery-block">
                                    <h6 class="title">Gallery</h6>

                                    <div class="gallery-box">
                                        <div class="inner-box catalog-upload-with-preview">
                                            <div class="image-box catalog-upload-preview">
                                                <img src="{{ $editPreview }}" alt="Edit logo preview">
                                            </div>

                                            <label class="uplode-box catalog-upload-trigger catalog-upload-trigger-wide">
                                                <input type="file" class="catalog-upload-input" wire:model="editLogoUpload"
                                                    accept=".png,.jpg,.jpeg,.svg,.webp">
                                                <div class="content-box">
                                                    <img src="{{ $uploadIcon }}" alt="Upload">
                                                    <span>{{ $editLogoUpload ? 'Doi logo' : 'Upload' }}</span>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="text catalog-make-gallery-text">
                                            Max file size 2MB. Dinh dang ho tro: SVG, PNG, JPG, WebP.
                                            @if ($editUploadName)
                                                <br>Dang chon: {{ $editUploadName }}
                                            @endif
                                        </div>

                                        @if ($editLogoUpload)
                                            <button type="button" class="catalog-upload-clear" wire:click="removeEditLogo">Bo
                                                chon file</button>
                                        @endif
                                    </div>

                                    <span class="catalog-head-note" wire:loading wire:target="editLogoUpload">Dang tai
                                        logo...</span>
                                    @error('editLogoUpload')
                                        <small class="catalog-field-error">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-sec catalog-make-gallery-form">
                                    <div class="row">
                                        <div class="form-column col-lg-4 col-md-6">
                                            <div class="form_boxes">
                                                <label>Ten hang xe</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <input type="text" wire:model.blur="editForm.name" placeholder="Toyota">
                                                </div>
                                                @error('editForm.name')
                                                    <small class="catalog-field-error">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-column col-lg-4 col-md-6">
                                            <div class="form_boxes">
                                                <label>Slug</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <input type="text" wire:model.blur="editForm.slug" placeholder="toyota">
                                                </div>
                                                @error('editForm.slug')
                                                    <small class="catalog-field-error">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-column col-lg-4 col-md-12"
                                            style="display:flex; flex-direction: column; justify-content: flex-end;">
                                            <button type="submit" class="theme-btn catalog-make-action-btn"
                                                wire:loading.attr="disabled" wire:target="update,editLogoUpload">
                                                Luu thay doi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="catalog-empty-state">Khong tim thay make can sua.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
