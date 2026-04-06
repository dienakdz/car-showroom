@php($editIcon = asset('boxcar/images/icons/edit.svg'))
@php($deleteIcon = asset('boxcar/images/icons/remove.svg'))
@php($fallbackLogo = asset('boxcar/images/resource/list2-1.png'))

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
                <h4>Tao trim nhanh</h4>
                <p>Trim van co form chi tiet rieng, nhung metadata co ban nen duoc tao ngay trong workspace.</p>
            </div>
            <a href="{{ route('admin.catalog.trims.create') }}" class="catalog-text-link">Mo form day du</a>
        </div>

        <form class="row" wire:submit="create">
            <div class="form-column col-xl-4 col-md-6">
                <div class="form_boxes">
                    <label>Model</label>
                    <div class="drop-menu catalog-native-control">
                        <select wire:model.blur="createForm.model_id">
                            <option value="">Chon model</option>
                            @foreach ($modelOptions as $modelOption)
                                <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('createForm.model_id') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-column col-xl-4 col-md-6">
                <div class="form_boxes">
                    <label>Ten trim</label>
                    <div class="drop-menu catalog-native-control">
                        <input type="text" wire:model.blur="createForm.name" placeholder="RS">
                    </div>
                    @error('createForm.name') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-column col-xl-4 col-md-6">
                <div class="form_boxes">
                    <label>Slug</label>
                    <div class="drop-menu catalog-native-control">
                        <input type="text" wire:model.blur="createForm.slug" placeholder="rs">
                    </div>
                    @error('createForm.slug') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-column col-xl-3 col-md-6">
                <div class="form_boxes">
                    <label>Year from</label>
                    <div class="drop-menu catalog-native-control">
                        <input type="number" wire:model.blur="createForm.year_from" min="1900" max="{{ now()->addYear()->format('Y') }}">
                    </div>
                    @error('createForm.year_from') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-column col-xl-3 col-md-6">
                <div class="form_boxes">
                    <label>Year to</label>
                    <div class="drop-menu catalog-native-control">
                        <input type="number" wire:model.blur="createForm.year_to" min="1900" max="{{ now()->addYear()->format('Y') }}">
                    </div>
                    @error('createForm.year_to') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-column col-xl-3 col-md-6">
                <div class="form_boxes">
                    <label>MSRP</label>
                    <div class="drop-menu catalog-native-control">
                        <input type="number" wire:model.blur="createForm.msrp" min="0" placeholder="890000000">
                    </div>
                    @error('createForm.msrp') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-column col-12">
                <div class="form_boxes">
                    <label>Ghi chu ngan</label>
                    <div class="drop-menu catalog-native-control">
                        <textarea wire:model.blur="createForm.description" rows="4" placeholder="Mo ta ngan cho trim"></textarea>
                    </div>
                    @error('createForm.description') <small class="catalog-field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-submit catalog-form-submit">
                    <div class="catalog-form-submit-copy">
                        Feature groups va attributes van duoc xu ly tai form chi tiet de giu workspace gon.
                    </div>
                    <button type="submit" class="theme-btn" wire:loading.attr="disabled" wire:target="create">
                        Tao trim
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="my-listing-table wrap-listing">
        <div class="title-listing">
            <div>
                <h4 class="catalog-section-title">Trim directory</h4>
                <p class="catalog-section-text">Table nay duoc lam theo pattern my-listings va uu tien thao tac nhanh cho admin.</p>
            </div>

            <div class="catalog-toolbar">
                <div class="box-ip-search catalog-search-box">
                    <span class="icon">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.29301 0.287598C2.9872 0.287598 0.294312 2.98048 0.294312 6.28631C0.294312 9.59211 2.9872 12.2902 6.29301 12.2902C7.70502 12.2902 9.00364 11.7954 10.03 10.9738L12.5287 13.4712C12.6548 13.5921 12.8232 13.6588 12.9979 13.657C13.1725 13.6552 13.3395 13.5851 13.4631 13.4617C13.5867 13.3382 13.6571 13.1713 13.6591 12.9967C13.6611 12.822 13.5947 12.6535 13.474 12.5272L10.9753 10.0285C11.7976 9.00061 12.293 7.69995 12.293 6.28631C12.293 2.98048 9.59882 0.287598 6.29301 0.287598ZM6.29301 1.62095C8.87824 1.62095 10.9584 3.70108 10.9584 6.28631C10.9584 8.87153 8.87824 10.9569 6.29301 10.9569C3.70778 10.9569 1.62764 8.87153 1.62764 6.28631C1.62764 3.70108 3.70778 1.62095 6.29301 1.62095Z" fill="#050B20"/>
                        </svg>
                    </span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Tim trim, slug hoac mo ta">
                </div>

                <div class="text-box v1 catalog-toolbar-boxes">
                    <div class="form_boxes v3 catalog-control-box">
                        <small>Model</small>
                        <div class="drop-menu catalog-native-control">
                            <select wire:model.live="modelFilter">
                                <option value="">Tat ca model</option>
                                @foreach ($modelOptions as $modelOption)
                                    <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
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
                                <option value="year_desc">Year moi nhat</option>
                                <option value="year_asc">Year cu nhat</option>
                                <option value="msrp_desc">MSRP cao den thap</option>
                                <option value="msrp_asc">MSRP thap den cao</option>
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
                    <th>Trim</th>
                    <th>Model</th>
                    <th>Years</th>
                    <th>MSRP</th>
                    <th>Usage</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($trims as $trim)
                    <tr wire:key="trim-row-{{ $trim->id }}">
                        <td>
                            <div class="shop-cart-product">
                                <div class="shop-product-cart-img catalog-listing-thumb">
                                    <img src="{{ $trim->model?->make?->logo_url ?: $fallbackLogo }}" alt="{{ $trim->name }}">
                                </div>
                                <div class="shop-product-cart-info">
                                    <h3>{{ $trim->name }}</h3>
                                    <p>{{ $trim->slug }}</p>
                                    <div class="price">
                                        <span>{{ $trim->model?->make?->name }} / {{ $trim->model?->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><span>{{ $trim->model?->make?->name }} / {{ $trim->model?->name }}</span></td>
                        <td><span>{{ $trim->year_from ?: '...' }} - {{ $trim->year_to ?: 'Nay' }}</span></td>
                        <td><span>{{ $trim->msrp ? number_format((int) $trim->msrp, 0, ',', '.') . ' VND' : 'Chua co MSRP' }}</span></td>
                        <td>
                            <span>{{ $trim->car_units_count }} inventory</span>
                            <p class="catalog-cell-note">{{ $trim->reviews_count }} reviews</p>
                        </td>
                        <td>
                            <a href="{{ route('admin.catalog.trims.edit', $trim) }}" class="catalog-text-link">Chi tiet</a>
                            <button type="button" class="remove-cart-item" wire:click="startEdit({{ $trim->id }})" title="Sua trim">
                                <img src="{{ $editIcon }}" alt="Edit">
                            </button>
                            <button type="button" class="remove-cart-item" wire:click="delete({{ $trim->id }})" onclick="return confirm('Xac nhan xoa trim nay?')" title="Xoa trim">
                                <img src="{{ $deleteIcon }}" alt="Delete">
                            </button>
                        </td>
                    </tr>

                    @if ($editingId === $trim->id)
                        <tr class="catalog-inline-row" wire:key="trim-editor-{{ $trim->id }}">
                            <td colspan="6">
                                <div class="form-box catalog-inline-form-box">
                                    <div class="catalog-box-head">
                                        <div>
                                            <h4>Sua trim</h4>
                                            <p>Quick edit metadata co ban, sau do co the vao form chi tiet neu can mo rong.</p>
                                        </div>
                                    </div>

                                    <form class="row" wire:submit="update">
                                        <div class="form-column col-xl-4 col-md-6">
                                            <div class="form_boxes">
                                                <label>Model</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <select wire:model.blur="editForm.model_id">
                                                        @foreach ($modelOptions as $modelOption)
                                                            <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('editForm.model_id') <small class="catalog-field-error">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="form-column col-xl-4 col-md-6">
                                            <div class="form_boxes">
                                                <label>Ten trim</label>
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

                                        <div class="form-column col-xl-3 col-md-6">
                                            <div class="form_boxes">
                                                <label>Year from</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <input type="number" wire:model.blur="editForm.year_from" min="1900" max="{{ now()->addYear()->format('Y') }}">
                                                </div>
                                                @error('editForm.year_from') <small class="catalog-field-error">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="form-column col-xl-3 col-md-6">
                                            <div class="form_boxes">
                                                <label>Year to</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <input type="number" wire:model.blur="editForm.year_to" min="1900" max="{{ now()->addYear()->format('Y') }}">
                                                </div>
                                                @error('editForm.year_to') <small class="catalog-field-error">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="form-column col-xl-3 col-md-6">
                                            <div class="form_boxes">
                                                <label>MSRP</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <input type="number" wire:model.blur="editForm.msrp" min="0">
                                                </div>
                                                @error('editForm.msrp') <small class="catalog-field-error">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="form-column col-12">
                                            <div class="form_boxes">
                                                <label>Ghi chu ngan</label>
                                                <div class="drop-menu catalog-native-control">
                                                    <textarea wire:model.blur="editForm.description" rows="4"></textarea>
                                                </div>
                                                @error('editForm.description') <small class="catalog-field-error">{{ $message }}</small> @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-submit catalog-form-submit">
                                                <div class="catalog-form-submit-copy">
                                                    Neu can sua feature groups, attributes hoac media, chuyen sang form chi tiet.
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
                            <div class="catalog-empty-state">Chua co trim nao phu hop bo loc hien tai.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="catalog-table-footer">
            <div class="catalog-table-summary">
                @if ($trims->total() > 0)
                    Hien thi {{ $trims->firstItem() }}-{{ $trims->lastItem() }} / {{ number_format($trims->total()) }} trim
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
                    {{ $trims->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
