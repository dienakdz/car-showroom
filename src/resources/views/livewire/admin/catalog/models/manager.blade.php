@php($editingModel = $editingId ? $models->getCollection()->firstWhere('id', $editingId) : null)

<div class="c1-catalog-workspace-tab">
    <!-- Quick Add Model Card -->
    <div class="c1-catalog-card" id="c1-model-form-section">
        <div class="c1-catalog-card-header">
            <div>
                <h4 class="c1-catalog-card-title">Tạo dòng xe mới</h4>
                <p class="c1-catalog-card-desc">Thêm dòng xe (Model) liên kết trực tiếp với hãng sản xuất.</p>
            </div>
        </div>

        <form wire:submit="create">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Hãng xe <span class="text-danger">*</span></label>
                    <select class="c1-catalog-select w-100" wire:model.blur="createForm.make_id">
                        <option value="">-- Chọn hãng xe --</option>
                        @foreach ($makeOptions as $makeOption)
                            <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                        @endforeach
                    </select>
                    @error('createForm.make_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Tên dòng xe <span class="text-danger">*</span></label>
                    <input type="text" class="c1-catalog-search-input" wire:model.blur="createForm.name" placeholder="Ví dụ: Corolla Cross, Camry, Civic...">
                    @error('createForm.name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Đường dẫn slug <span class="text-danger">*</span></label>
                    <input type="text" class="c1-catalog-search-input" wire:model.blur="createForm.slug" placeholder="Ví dụ: corolla-cross, camry...">
                    @error('createForm.slug') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="c1-btn c1-btn-primary" wire:loading.attr="disabled" wire:target="create">
                        <span wire:loading.remove wire:target="create">+ Thêm nhanh dòng xe</span>
                        <span wire:loading wire:target="create">Đang lưu...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Models Table Card -->
    <div class="c1-catalog-card">
        <div class="c1-catalog-toolbar">
            <!-- Search -->
            <div class="c1-catalog-search-wrap">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="search" class="c1-catalog-search-input" wire:model.live.debounce.300ms="search" placeholder="Tìm kiếm theo tên dòng xe hoặc slug...">
            </div>

            <!-- Filters -->
            <div class="c1-catalog-filter-group">
                <select class="c1-catalog-select" wire:model.live="makeFilter">
                    <option value="">Tất cả hãng xe</option>
                    @foreach ($makeOptions as $makeOption)
                        <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                    @endforeach
                </select>

                <select class="c1-catalog-select" wire:model.live="sort">
                    <option value="updated_desc">Mới cập nhật</option>
                    <option value="updated_asc">Cũ nhất</option>
                    <option value="name_asc">Tên dòng xe A - Z</option>
                    <option value="name_desc">Tên dòng xe Z - A</option>
                    <option value="trims_desc">Nhiều phiên bản nhất</option>
                    <option value="trims_asc">Ít phiên bản nhất</option>
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
                        <th>Hãng xe</th>
                        <th>Tên dòng xe</th>
                        <th>Mã slug</th>
                        <th>Số phiên bản</th>
                        <th>Cập nhật</th>
                        <th style="width: 140px; text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($models as $model)
                        <tr wire:key="model-row-{{ $model->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="c1-brand-logo-wrap" style="width: 32px; height: 32px; border-radius: 6px;">
                                        @if ($model->make?->logo_url)
                                            <img src="{{ $model->make->logo_url }}" alt="{{ $model->make->name }} logo">
                                        @else
                                            <span style="font-weight: 700; font-size: 13px; color: #2563eb;">{{ strtoupper(substr($model->make?->name ?: 'M', 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <span style="font-weight: 600; color: var(--c1-text-heading);">{{ $model->make?->name ?: '--' }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 14px; color: var(--c1-text-heading);">
                                    {{ $model->name }}
                                </div>
                            </td>
                            <td>
                                <span class="c1-slug-tag">{{ $model->slug }}</span>
                            </td>
                            <td>
                                <span class="c1-pill-badge c1-pill-blue">
                                    {{ number_format($model->trims_count) }} phiên bản
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ optional($model->updated_at)->format('d/m/Y H:i') ?: '--' }}</span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div class="c1-actions-cell">
                                    <button type="button" class="c1-action-btn c1-action-btn-edit" wire:click="startEdit({{ $model->id }})" title="Sửa dòng xe">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        <span>Sửa</span>
                                    </button>
                                    <button type="button" class="c1-action-btn c1-action-btn-icon c1-action-btn-danger" wire:click="delete({{ $model->id }})" onclick="return confirm('Xác nhận xóa dòng xe {{ $model->name }}?')" title="Xóa dòng xe">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Chưa có dòng xe nào phù hợp với điều kiện tìm kiếm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4 pt-3 border-top">
            <div class="text-muted small">
                @if ($models->total() > 0)
                    Hiển thị {{ $models->firstItem() }} - {{ $models->lastItem() }} trên tổng số {{ number_format($models->total()) }} dòng xe
                @else
                    0 dòng xe
                @endif
            </div>
            <div>
                {{ $models->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Model Modal -->
    <div class="modal fade" id="catalogModelEditModal" tabindex="-1" aria-labelledby="catalogModelEditModalLabel"
        aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered c1-modal-dialog">
            <div class="modal-content c1-modal-content">
                <div class="c1-modal-header">
                    <div>
                        <h5 class="c1-modal-title" id="catalogModelEditModalLabel">Chỉnh sửa dòng xe</h5>
                        <p class="c1-catalog-card-desc">Cập nhật hãng xe liên kết, tên dòng xe và slug URL.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="cancelEdit"></button>
                </div>

                <div class="c1-modal-body">
                    @if ($editingModel)
                        <form wire:submit="update" id="editModelForm">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Hãng xe <span class="text-danger">*</span></label>
                                <select class="c1-catalog-select w-100" wire:model.blur="editForm.make_id">
                                    @foreach ($makeOptions as $makeOption)
                                        <option value="{{ $makeOption->id }}">{{ $makeOption->name }}</option>
                                    @endforeach
                                </select>
                                @error('editForm.make_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Tên dòng xe <span class="text-danger">*</span></label>
                                <input type="text" class="c1-catalog-search-input" wire:model.blur="editForm.name" placeholder="Ví dụ: Corolla Cross">
                                @error('editForm.name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Đường dẫn slug <span class="text-danger">*</span></label>
                                <input type="text" class="c1-catalog-search-input" wire:model.blur="editForm.slug" placeholder="Ví dụ: corolla-cross">
                                @error('editForm.slug') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4 text-muted">Không tìm thấy thông tin dòng xe cần sửa.</div>
                    @endif
                </div>

                <div class="c1-modal-footer">
                    <button type="button" class="c1-action-btn" data-bs-dismiss="modal" wire:click="cancelEdit">Hủy bỏ</button>
                    @if ($editingModel)
                        <button type="submit" form="editModelForm" class="c1-btn c1-btn-primary" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">Lưu thay đổi</span>
                            <span wire:loading wire:target="update">Đang lưu...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
