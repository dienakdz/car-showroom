@php($editIcon = asset('boxcar/images/icons/edit.svg'))
@php($deleteIcon = asset('boxcar/images/icons/remove.svg'))
@php($fallbackLogo = asset('boxcar/images/resource/brandf.png'))

<div class="catalog-module">
    @if ($feedback !== [])
        <div class="catalog-feedback {{ ($feedback['type'] ?? 'success') === 'error' ? 'is-error' : 'is-success' }}">
            <div>
                <strong>{{ ($feedback['type'] ?? 'success') === 'error' ? 'Can xu ly' : 'Da cap nhat' }}</strong>
                <p>{{ $feedback['message'] ?? '' }}</p>
            </div>
            <button type="button" wire:click="dismissFeedback">Dong</button>
        </div>
    @endif

    <div class="form-box catalog-form-box">
        <div class="catalog-box-head">
            <div>
                <h4>Tao model moi</h4>
                <p>Ap dung layout form cua template de tao model nhanh ngay trong catalog.</p>
            </div>
        </div>

        <form class="row" wire:submit="create">
            <div class="form-column col-xl-4 col-md-6">
                <div class="form_boxes">
                    <label>Make</label>
                    <div class="drop-menu catalog-native-control">
                        <select wire:model.blur="createForm.make_id">
                            <option value="">Chon make</option>
                            @foreach ($makeOptions as $makeOption)
                                <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('createForm.make_id') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-column col-xl-4 col-md-6">
                <div class="form_boxes">
                    <label>Ten model</label>
                    <div class="drop-menu catalog-native-control">
                        <input type="text" wire:model.blur="createForm.name" placeholder="Corolla Cross">
                    </div>
                    @error('createForm.name') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-column col-xl-4 col-md-6">
                <div class="form_boxes">
                    <label>Slug</label>
                    <div class="drop-menu catalog-native-control">
                        <input type="text" wire:model.blur="createForm.slug" placeholder="corolla-cross">
                    </div>
                    @error('createForm.slug') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-submit catalog-form-submit">
                    <div class="catalog-form-submit-copy">
                        Bo loc make o ben duoi duoc giu dong bo de thao tac nhieu du lieu nhanh hon.
                    </div>
                    <button type="submit" class="theme-btn" wire:loading.attr="disabled" wire:target="create">
                        Tao model
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="my-listing-table wrap-listing">
        <div class="title-listing">
            <div>
                <h4 class="catalog-section-title">Model directory</h4>
                <p class="catalog-section-text">Dung my-listings table de xem make, model va trim count tren cung mot man hinh.</p>
            </div>

            <div class="catalog-toolbar">
                <div class="box-ip-search catalog-search-box">
                    <span class="icon">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.29301 0.287598C2.9872 0.287598 0.294312 2.98048 0.294312 6.28631C0.294312 9.59211 2.9872 12.2902 6.29301 12.2902C7.70502 12.2902 9.00364 11.7954 10.03 10.9738L12.5287 13.4712C12.6548 13.5921 12.8232 13.6588 12.9979 13.657C13.1725 13.6552 13.3395 13.5851 13.4631 13.4617C13.5867 13.3382 13.6571 13.1713 13.6591 12.9967C13.6611 12.822 13.5947 12.6535 13.474 12.5272L10.9753 10.0285C11.7976 9.00061 12.293 7.69995 12.293 6.28631C12.293 2.98048 9.59882 0.287598 6.29301 0.287598ZM6.29301 1.62095C8.87824 1.62095 10.9584 3.70108 10.9584 6.28631C10.9584 8.87153 8.87824 10.9569 6.29301 10.9569C3.70778 10.9569 1.62764 8.87153 1.62764 6.28631C1.62764 3.70108 3.70778 1.62095 6.29301 1.62095Z" fill="#050B20"/>
                        </svg>
                    </span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Tim model">
                </div>

                <div class="text-box v1 catalog-toolbar-boxes">
                    <div class="form_boxes v3 catalog-control-box">
                        <small>Make</small>
                        <div class="drop-menu catalog-native-control">
                            <select wire:model.live="makeFilter">
                                <option value="">Tat ca make</option>
                                @foreach ($makeOptions as $makeOption)
                                    <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form_boxes v3 catalog-control-box">
                        <small>Sort by</small>
                        <div class="drop-menu catalog-native-control">
                            <select wire:model.live="sort">
                                <option value="updated_desc">Updated moi nhat</option>
                                <option value="updated_asc">Updated cu nhat</option>
                                <option value="name_asc">Ten A-Z</option>
                                <option value="name_desc">Ten Z-A</option>
                                <option value="trims_desc">Nhieu trims nhat</option>
                                <option value="trims_asc">It trims nhat</option>
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
                    <th>Model</th>
                    <th>Make</th>
                    <th>Slug</th>
                    <th>Trims</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($models as $model)
                    <tr wire:key="model-row-{{ $model->id }}">
                        <td>
                            <div class="shop-cart-product">
                                <div class="shop-product-cart-img catalog-listing-thumb">
                                    <img src="{{ $model->make?->logo_url ?: $fallbackLogo }}" alt="{{ $model->name }}">
                                </div>
                                <div class="shop-product-cart-info">
                                    <h3>{{ $model->name }}</h3>
                                    <p>ID #{{ $model->id }}</p>
                                    <div class="price">
                                        <span>{{ $model->make?->name ?: 'Chua gan make' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><span>{{ $model->make?->name ?: '--' }}</span></td>
                        <td><span>{{ $model->slug }}</span></td>
                        <td><span>{{ number_format($model->trims_count) }}</span></td>
                        <td><span>{{ optional($model->updated_at)->format('d/m/Y H:i') ?: '--' }}</span></td>
                        <td>
                            <button type="button" class="remove-cart-item" wire:click="startEdit({{ $model->id }})" title="Sua model">
                                <img src="{{ $editIcon }}" alt="Edit">
                            </button>
                            <button type="button" class="remove-cart-item" wire:click="delete({{ $model->id }})" onclick="return confirm('Xac nhan xoa model nay?')" title="Xoa model">
                                <img src="{{ $deleteIcon }}" alt="Delete">
                            </button>
                        </td>
                    </tr>

                    @if ($editingId === $model->id)
                        <tr class="catalog-inline-row" wire:key="model-editor-{{ $model->id }}">
                            <td colspan="6">
                                <div class="form-box catalog-inline-form-box">
                                    <div class="catalog-box-head">
                                        <div>
                                            <h4>Sua model</h4>
                                            <p>Inline edit de cap nhat make, ten va slug ma khong can reload.</p>
                                        </div>
                                    </div>

                                    <form class="row" wire:submit="update">
                                        <div class="form-column col-xl-4 col-md-6">
                                            <div class="form_boxes">
                                                <label>Make</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <select wire:model.blur="editForm.make_id">
                                                        @foreach ($makeOptions as $makeOption)
                                                            <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('editForm.make_id') <small class="catalog-field-error">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="form-column col-xl-4 col-md-6">
                                            <div class="form_boxes">
                                                <label>Ten model</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <input type="text" wire:model.blur="editForm.name">
                                                </div>
                                                @error('editForm.name') <small class="catalog-field-error">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="form-column col-xl-4 col-md-6">
                                            <div class="form_boxes">
                                                <label>Slug</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <input type="text" wire:model.blur="editForm.slug">
                                                </div>
                                                @error('editForm.slug') <small class="catalog-field-error">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-submit catalog-form-submit">
                                                <div class="catalog-form-submit-copy">
                                                    Phu hop cho workflow admin khi phai cap nhat nhieu dong lien tiep.
                                                </div>
                                                <div class="catalog-submit-actions">
                                                    <button type="button" class="catalog-text-btn" wire:click="cancelEdit">Huy</button>
                                                    <button type="submit" class="theme-btn" wire:loading.attr="disabled" wire:target="update">
                                                        Luu thay doi
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="catalog-empty-state">Chua co model nao phu hop bo loc hien tai.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="catalog-table-footer">
            <div class="catalog-table-summary">
                @if ($models->total() > 0)
                    Hien thi {{ $models->firstItem() }}-{{ $models->lastItem() }} / {{ number_format($models->total()) }} model
                @else
                    Khong co du lieu
                @endif
            </div>

            <div class="catalog-table-footer-actions">
                <div class="form_boxes v3 catalog-per-page catalog-control-box">
                    <small>Rows</small>
                    <div class="drop-menu catalog-native-control">
                        <select wire:model.live="perPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>

                <div class="catalog-pagination">
                    {{ $models->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
