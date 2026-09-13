@php($createUploadName = is_object($logoUpload) && method_exists($logoUpload, 'getClientOriginalName') ? $logoUpload->getClientOriginalName() : null)
@php($editingMake = $editingId ? $makes->getCollection()->firstWhere('id', $editingId) : null)
@php($editUploadName = is_object($editLogoUpload) && method_exists($editLogoUpload, 'getClientOriginalName') ? $editLogoUpload->getClientOriginalName() : null)

<div class="c1-catalog-workspace-tab">
    <!-- Quick Add Make Card -->
    <div class="c1-catalog-card" id="c1-make-form-section">
        <div class="c1-catalog-card-header">
            <div>
                <h4 class="c1-catalog-card-title">Tạo hãng xe mới</h4>
                <p class="c1-catalog-card-desc">Thêm thương hiệu và logo vào hệ thống quản lý danh mục xe.</p>
            </div>
        </div>

        <form wire:submit="create">
            <div class="c1-make-form-grid">
                <!-- Logo Upload Dropzone -->
                <div>
                    <label class="form-label text-muted small fw-bold mb-2">Logo thương hiệu</label>
                    <div class="c1-logo-dropzone">
                        <input type="file" wire:model="logoUpload" accept=".png,.jpg,.jpeg,.svg,.webp">
                        @if ($this->createLogoPreviewUrl)
                            <div class="c1-logo-preview-box">
                                <img src="{{ $this->createLogoPreviewUrl }}" alt="Logo preview" class="c1-logo-preview-img">
                                <span class="c1-logo-dropzone-text">{{ $createUploadName ?: 'Logo đã chọn' }}</span>
                                <button type="button" class="c1-action-btn c1-action-btn-danger mt-1" wire:click.stop="removeCreateLogo">
                                    Bỏ chọn tệp
                                </button>
                            </div>
                        @else
                            <svg class="c1-logo-dropzone-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <div class="c1-logo-dropzone-text">Kéo logo vào đây hoặc <span style="color: var(--c1-primary); font-weight: 600;">chọn tệp</span></div>
                            <div class="c1-logo-dropzone-hint">SVG, PNG trong suốt, JPG, WebP. Tối đa 2MB</div>
                        @endif
                    </div>
                    <div wire:loading wire:target="logoUpload" class="text-primary small mt-1">Đang tải logo...</div>
                    @error('logoUpload') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <!-- Make Name & Slug -->
                <div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Tên hãng xe <span class="text-danger">*</span></label>
                            <input type="text" class="c1-catalog-search-input" wire:model.blur="createForm.name" placeholder="Ví dụ: Toyota, Honda, Ford, Porsche...">
                            @error('createForm.name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Đường dẫn slug <span class="text-danger">*</span></label>
                            <input type="text" class="c1-catalog-search-input" wire:model.blur="createForm.slug" placeholder="Ví dụ: toyota, honda, ford...">
                            @error('createForm.slug') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="c1-btn c1-btn-primary" wire:loading.attr="disabled" wire:target="create,logoUpload">
                                <span wire:loading.remove wire:target="create">+ Thêm nhanh hãng xe</span>
                                <span wire:loading wire:target="create">Đang lưu...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Directory Card -->
    <div class="c1-catalog-card">
        <div class="c1-catalog-toolbar">
            <!-- Search -->
            <div class="c1-catalog-search-wrap">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="search" class="c1-catalog-search-input" wire:model.live.debounce.300ms="search" placeholder="Tìm theo tên hãng xe hoặc mã slug...">
            </div>

            <!-- Sort & Pagination Count -->
            <div class="c1-catalog-filter-group">
                <select class="c1-catalog-select" wire:model.live="sort">
                    <option value="updated_desc">Mới cập nhật</option>
                    <option value="updated_asc">Cũ nhất</option>
                    <option value="name_asc">Tên hãng A - Z</option>
                    <option value="name_desc">Tên hãng Z - A</option>
                    <option value="models_desc">Nhiều dòng xe nhất</option>
                    <option value="models_asc">Ít dòng xe nhất</option>
                </select>

                <select class="c1-catalog-select" wire:model.live="perPage">
                    <option value="10">10 dòng / trang</option>
                    <option value="25">25 dòng / trang</option>
                    <option value="50">50 dòng / trang</option>
                </select>
            </div>
        </div>

        <!-- Modern Data Table -->
        <div class="c1-table-responsive">
            <table class="c1-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Logo</th>
                        <th>Tên hãng xe</th>
                        <th>Mã slug</th>
                        <th>Số dòng xe</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật</th>
                        <th style="width: 140px; text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($makes as $make)
                        <tr wire:key="make-row-{{ $make->id }}">
                            <td>
                                <div class="c1-brand-logo-wrap">
                                    @if ($make->logo_url)
                                        <img src="{{ $make->logo_url }}" alt="{{ $make->name }} logo">
                                    @else
                                        <span style="font-weight: 700; font-size: 16px; color: #2563eb;">{{ strtoupper(substr($make->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 14px; color: var(--c1-text-heading);">
                                    {{ $make->name }}
                                </div>
                            </td>
                            <td>
                                <span class="c1-slug-tag">{{ $make->slug }}</span>
                            </td>
                            <td>
                                <span class="c1-pill-badge c1-pill-blue">
                                    {{ number_format($make->models_count) }} dòng xe
                                </span>
                            </td>
                            <td>
                                <span class="c1-pill-badge c1-pill-green">Đang kinh doanh</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ optional($make->updated_at)->format('d/m/Y H:i') ?: '--' }}</span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div class="c1-actions-cell">
                                    <button type="button" class="c1-action-btn c1-action-btn-edit" wire:click="startEdit({{ $make->id }})" title="Sửa hãng xe">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        <span>Sửa</span>
                                    </button>
                                    <button type="button" class="c1-action-btn c1-action-btn-icon c1-action-btn-danger" wire:click="delete({{ $make->id }})" onclick="return confirm('Xác nhận xóa hãng xe {{ $make->name }}?')" title="Xóa hãng xe">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Chưa có hãng xe nào phù hợp với điều kiện tìm kiếm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4 pt-3 border-top">
            <div class="text-muted small">
                @if ($makes->total() > 0)
                    Hiển thị {{ $makes->firstItem() }} - {{ $makes->lastItem() }} trên tổng số {{ number_format($makes->total()) }} hãng xe
                @else
                    0 hãng xe
                @endif
            </div>
            <div>
                {{ $makes->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Make Modal -->
    <div class="modal fade" id="catalogMakeEditModal" tabindex="-1" aria-labelledby="catalogMakeEditModalLabel"
        aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered c1-modal-dialog">
            <div class="modal-content c1-modal-content">
                <div class="c1-modal-header">
                    <div>
                        <h5 class="c1-modal-title" id="catalogMakeEditModalLabel">Chỉnh sửa hãng xe</h5>
                        <p class="c1-catalog-card-desc">Cập nhật thông tin nhận diện và logo thương hiệu.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="cancelEdit"></button>
                </div>

                <div class="c1-modal-body">
                    @if ($editingMake)
                        <form wire:submit="update" id="editMakeForm">
                            <!-- Logo Upload in Modal -->
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold mb-2">Logo thương hiệu</label>
                                <div class="c1-logo-dropzone" style="min-height: 120px;">
                                    <input type="file" wire:model="editLogoUpload" accept=".png,.jpg,.jpeg,.svg,.webp">
                                    @if ($this->editLogoPreviewUrl || $editingMake->logo_url)
                                        <div class="c1-logo-preview-box">
                                            <img src="{{ $this->editLogoPreviewUrl ?: $editingMake->logo_url }}" alt="Logo preview" class="c1-logo-preview-img">
                                            <span class="c1-logo-dropzone-text">{{ $editUploadName ?: 'Nhấn để đổi logo mới' }}</span>
                                            @if ($editLogoUpload)
                                                <button type="button" class="c1-action-btn c1-action-btn-danger mt-1" wire:click.stop="removeEditLogo">
                                                    Bỏ chọn tệp
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <svg class="c1-logo-dropzone-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <div class="c1-logo-dropzone-text">Chọn logo mới</div>
                                    @endif
                                </div>
                                <div wire:loading wire:target="editLogoUpload" class="text-primary small mt-1">Đang tải logo...</div>
                                @error('editLogoUpload') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Tên hãng xe <span class="text-danger">*</span></label>
                                <input type="text" class="c1-catalog-search-input" wire:model.blur="editForm.name">
                                @error('editForm.name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Đường dẫn slug <span class="text-danger">*</span></label>
                                <input type="text" class="c1-catalog-search-input" wire:model.blur="editForm.slug">
                                @error('editForm.slug') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4 text-muted">Không tìm thấy thông tin hãng xe cần sửa.</div>
                    @endif
                </div>

                <div class="c1-modal-footer">
                    <button type="button" class="c1-action-btn" data-bs-dismiss="modal" wire:click="cancelEdit">Hủy bỏ</button>
                    @if ($editingMake)
                        <button type="submit" form="editMakeForm" class="c1-btn c1-btn-primary" wire:loading.attr="disabled" wire:target="update,editLogoUpload">
                            <span wire:loading.remove wire:target="update">Lưu thay đổi</span>
                            <span wire:loading wire:target="update">Đang lưu...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
