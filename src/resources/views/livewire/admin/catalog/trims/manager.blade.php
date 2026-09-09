<div class="c1-catalog-workspace-tab">
    @if ($feedback !== [])
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <strong>{{ ($feedback['type'] ?? 'success') === 'error' ? 'Lưu ý:' : 'Thành công:' }}</strong>
                <span>{{ $feedback['message'] ?? '' }}</span>
            </div>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Close" style="font-size: 11px;"></button>
        </div>
    @endif

    <!-- Quick Add Trim Card -->
    <div class="c1-catalog-card" id="c1-trim-form-section">
        <div class="c1-catalog-card-header">
            <div>
                <h4 class="c1-catalog-card-title">Tạo phiên bản xe (Trim)</h4>
                <p class="c1-catalog-card-desc">Thêm phiên bản nhanh với các thông tin cơ bản hoặc mở form đầy đủ để cấu hình chi tiết trang bị.</p>
            </div>
            <a href="{{ route('admin.catalog.trims.create') }}" class="c1-action-btn" style="color: var(--c1-primary); font-weight: 600;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Mở form đầy đủ
            </a>
        </div>

        <form wire:submit="create">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Dòng xe (Model) <span class="text-danger">*</span></label>
                    <select class="c1-catalog-select w-100" wire:model.blur="createForm.model_id">
                        <option value="">-- Chọn dòng xe --</option>
                        @foreach ($modelOptions as $modelOption)
                            <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
                        @endforeach
                    </select>
                    @error('createForm.model_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Tên phiên bản <span class="text-danger">*</span></label>
                    <input type="text" class="c1-catalog-search-input" wire:model.blur="createForm.name" placeholder="Ví dụ: RS, 2.5Q, Wildtrak 4x4...">
                    @error('createForm.name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Mã slug <span class="text-danger">*</span></label>
                    <input type="text" class="c1-catalog-search-input" wire:model.blur="createForm.slug" placeholder="Ví dụ: rs, 2-5q, wildtrak-4x4...">
                    @error('createForm.slug') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Năm bắt đầu</label>
                    <input type="number" class="c1-catalog-search-input" wire:model.blur="createForm.year_from" min="1900" max="{{ now()->addYear()->format('Y') }}" placeholder="2020">
                    @error('createForm.year_from') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Năm kết thúc</label>
                    <input type="number" class="c1-catalog-search-input" wire:model.blur="createForm.year_to" min="1900" max="{{ now()->addYear()->format('Y') }}" placeholder="{{ now()->format('Y') }}">
                    @error('createForm.year_to') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold">Giá niêm yết (MSRP - VNĐ)</label>
                    <input type="number" class="c1-catalog-search-input" wire:model.blur="createForm.msrp" min="0" placeholder="Ví dụ: 870000000">
                    @error('createForm.msrp') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label text-muted small fw-bold">Ghi chú ngắn</label>
                    <textarea class="c1-catalog-search-input" style="height: auto; padding: 8px 12px;" wire:model.blur="createForm.description" rows="2" placeholder="Ghi chú nhanh về động cơ, nhiên liệu hoặc điểm nổi bật của phiên bản..."></textarea>
                    @error('createForm.description') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="c1-btn c1-btn-primary" wire:loading.attr="disabled" wire:target="create">
                        <span wire:loading.remove wire:target="create">+ Thêm nhanh phiên bản</span>
                        <span wire:loading wire:target="create">Đang lưu...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Trims Table Card -->
    <div class="c1-catalog-card">
        <div class="c1-catalog-toolbar">
            <!-- Search -->
            <div class="c1-catalog-search-wrap">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="search" class="c1-catalog-search-input" wire:model.live.debounce.300ms="search" placeholder="Tìm theo tên phiên bản, slug hoặc ghi chú...">
            </div>

            <!-- Filters -->
            <div class="c1-catalog-filter-group">
                <select class="c1-catalog-select" wire:model.live="modelFilter">
                    <option value="">Tất cả dòng xe</option>
                    @foreach ($modelOptions as $modelOption)
                        <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
                    @endforeach
                </select>

                <select class="c1-catalog-select" wire:model.live="sort">
                    <option value="updated_desc">Mới cập nhật</option>
                    <option value="updated_asc">Cũ nhất</option>
                    <option value="name_asc">Tên phiên bản A - Z</option>
                    <option value="name_desc">Tên phiên bản Z - A</option>
                    <option value="year_desc">Năm đời mới nhất</option>
                    <option value="year_asc">Năm đời cũ nhất</option>
                    <option value="msrp_desc">Giá MSRP cao đến thấp</option>
                    <option value="msrp_asc">Giá MSRP thấp đến cao</option>
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
                        <th>Phiên bản (Trim)</th>
                        <th>Dòng xe & Hãng</th>
                        <th>Năm đời</th>
                        <th>Giá niêm yết (MSRP)</th>
                        <th>Tồn kho</th>
                        <th style="width: 180px; text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trims as $trim)
                        <tr wire:key="trim-row-{{ $trim->id }}">
                            <td>
                                <div>
                                    <div style="font-weight: 600; font-size: 14px; color: var(--c1-text-heading);">
                                        {{ $trim->name }}
                                    </div>
                                    <span class="c1-slug-tag mt-1">{{ $trim->slug }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 500; color: var(--c1-text-heading);">
                                    {{ $trim->model?->make?->name }} {{ $trim->model?->name }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $trim->year_from ?: '...' }} - {{ $trim->year_to ?: 'Hiện tại' }}</span>
                            </td>
                            <td>
                                @if ($trim->msrp)
                                    <span class="c1-price-highlight">{{ number_format((int) $trim->msrp, 0, ',', '.') }} ₫</span>
                                @else
                                    <span class="text-muted small">Chưa có MSRP</span>
                                @endif
                            </td>
                            <td>
                                <span class="c1-pill-badge {{ $trim->car_units_count > 0 ? 'c1-pill-green' : 'c1-pill-slate' }}">
                                    {{ $trim->car_units_count }} xe có sẵn
                                </span>
                            </td>
                            <td>
                                <div class="c1-actions-cell" style="justify-content: flex-end;">
                                    <button type="button" class="c1-action-btn" wire:click="startEdit({{ $trim->id }})" title="Sửa nhanh">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Sửa
                                    </button>
                                    <a href="{{ route('admin.catalog.trims.edit', $trim) }}" class="c1-action-btn" title="Chi tiết đầy đủ">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Chi tiết
                                    </a>
                                    <button type="button" class="c1-action-btn c1-action-btn-danger" wire:click="delete({{ $trim->id }})" onclick="return confirm('Xác nhận xóa phiên bản {{ $trim->name }}?')" title="Xóa phiên bản">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        @if ($editingId === $trim->id)
                            <tr wire:key="trim-editor-{{ $trim->id }}" style="background-color: #f8fafc;">
                                <td colspan="6" style="padding: 20px;">
                                    <div class="c1-catalog-card mb-0" style="border: 1px solid #bfdbfe; background: #ffffff;">
                                        <div class="c1-catalog-card-header mb-3">
                                            <div>
                                                <h5 class="c1-catalog-card-title text-primary">Chỉnh sửa nhanh: {{ $trim->name }}</h5>
                                                <p class="c1-catalog-card-desc">Cập nhật thông tin phiên bản hoặc chuyển sang trang chi tiết để cấu hình trang bị đầy đủ.</p>
                                            </div>
                                        </div>

                                        <form wire:submit="update">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label text-muted small fw-bold">Dòng xe</label>
                                                    <select class="c1-catalog-select w-100" wire:model.blur="editForm.model_id">
                                                        @foreach ($modelOptions as $modelOption)
                                                            <option value="{{ $modelOption->id }}">{{ $modelOption->make?->name }} / {{ $modelOption->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('editForm.model_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label text-muted small fw-bold">Tên phiên bản</label>
                                                    <input type="text" class="c1-catalog-search-input" wire:model.blur="editForm.name">
                                                    @error('editForm.name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label text-muted small fw-bold">Mã slug</label>
                                                    <input type="text" class="c1-catalog-search-input" wire:model.blur="editForm.slug">
                                                    @error('editForm.slug') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label text-muted small fw-bold">Năm bắt đầu</label>
                                                    <input type="number" class="c1-catalog-search-input" wire:model.blur="editForm.year_from" min="1900" max="{{ now()->addYear()->format('Y') }}">
                                                    @error('editForm.year_from') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label text-muted small fw-bold">Năm kết thúc</label>
                                                    <input type="number" class="c1-catalog-search-input" wire:model.blur="editForm.year_to" min="1900" max="{{ now()->addYear()->format('Y') }}">
                                                    @error('editForm.year_to') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small fw-bold">Giá niêm yết (MSRP)</label>
                                                    <input type="number" class="c1-catalog-search-input" wire:model.blur="editForm.msrp" min="0">
                                                    @error('editForm.msrp') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label text-muted small fw-bold">Ghi chú</label>
                                                    <textarea class="c1-catalog-search-input" style="height: auto; padding: 8px 12px;" wire:model.blur="editForm.description" rows="2"></textarea>
                                                    @error('editForm.description') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                                </div>

                                                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                                    <button type="button" class="c1-action-btn" wire:click="cancelEdit">Hủy</button>
                                                    <button type="submit" class="c1-btn c1-btn-primary" wire:loading.attr="disabled" wire:target="update">
                                                        <span wire:loading.remove wire:target="update">Lưu thay đổi</span>
                                                        <span wire:loading wire:target="update">Đang lưu...</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Chưa có phiên bản nào phù hợp với điều kiện tìm kiếm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4 pt-3 border-top">
            <div class="text-muted small">
                @if ($trims->total() > 0)
                    Hiển thị {{ $trims->firstItem() }} - {{ $trims->lastItem() }} trên tổng số {{ number_format($trims->total()) }} phiên bản
                @else
                    0 phiên bản
                @endif
            </div>
            <div>
                {{ $trims->links() }}
            </div>
        </div>
    </div>
</div>
