@php
    $isEditing = $trimId !== null;
    $selectedModel = $models->firstWhere('id', (int) ($form['model_id'] ?? 0));
    $previewName = trim(collect([
        $selectedModel?->make?->name,
        $selectedModel?->name,
        $form['name'] ?? null,
    ])->filter()->implode(' '));
    $previewYearFrom = trim((string) ($form['year_from'] ?? ''));
    $previewYearTo = trim((string) ($form['year_to'] ?? ''));
    $engineAttribute = $attributes->firstWhere('code', 'engine');
    $previewEngine = $engineAttribute === null
        ? ''
        : trim((string) ($form['attributes'][(string) $engineAttribute->id]['value_string'] ?? ''));
    $selectedFeatureCount = count((array) ($form['feature_ids'] ?? []));
@endphp

<div class="c1-form-workspace">
    <div class="c1-page-header mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}" wire:navigate class="text-muted small text-decoration-none">Danh mục xe</a>
                <span class="text-muted small">/</span>
                <span class="small fw-semibold text-primary">{{ $isEditing ? 'Chỉnh sửa' : 'Tạo mới' }}</span>
            </div>
            <h1 class="c1-page-title">{{ $isEditing ? 'Cập nhật phiên bản: ' . $trimRecord?->name : 'Tạo phiên bản xe (Trim)' }}</h1>
            <p class="c1-page-subtitle">Khai báo thông số kỹ thuật, giá niêm yết và danh sách trang bị cho phiên bản xe.</p>
        </div>

        <div class="c1-header-actions">
            <a href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}" wire:navigate class="c1-action-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Về danh mục xe
            </a>
        </div>
    </div>

    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'success' ? 'c1-alert-success' : 'c1-alert-danger' }} d-flex align-items-center justify-content-between gap-3">
            <span>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    <form wire:submit="save" id="trimForm">
        <div class="c1-form-layout">
            <div class="c1-form-main">
                <div class="c1-catalog-card">
                    <div class="c1-catalog-card-header">
                        <div>
                            <h4 class="c1-catalog-card-title">1. Thông tin cơ bản phiên bản</h4>
                            <p class="c1-catalog-card-desc">Chọn dòng xe trực thuộc, đặt tên phiên bản và định giá niêm yết (MSRP).</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Dòng xe (Model) <span class="text-danger">*</span></label>
                            <select class="c1-catalog-select w-100" wire:model.live="form.model_id" required>
                                <option value="">-- Chọn dòng xe --</option>
                                @foreach ($models as $model)
                                    <option value="{{ $model->id }}">{{ $model->make?->name }} / {{ $model->name }}</option>
                                @endforeach
                            </select>
                            @error('form.model_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Tên phiên bản (Trim) <span class="text-danger">*</span></label>
                            <input type="text" class="c1-catalog-search-input" wire:model.live.debounce.300ms="form.name" placeholder="Ví dụ: RS, 2.5Q, Wildtrak 4x4..." required>
                            @error('form.name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Đường dẫn slug <span class="text-danger">*</span></label>
                            <input type="text" class="c1-catalog-search-input" wire:model.blur="form.slug" placeholder="Ví dụ: rs, 2-5q...">
                            @error('form.slug') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Năm bắt đầu</label>
                            <input type="number" min="1900" max="{{ now()->addYear()->format('Y') }}" class="c1-catalog-search-input" wire:model.live.debounce.300ms="form.year_from" placeholder="2020">
                            @error('form.year_from') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Năm kết thúc</label>
                            <input type="number" min="1900" max="{{ now()->addYear()->format('Y') }}" class="c1-catalog-search-input" wire:model.live.debounce.300ms="form.year_to" placeholder="{{ now()->format('Y') }}">
                            @error('form.year_to') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">Giá niêm yết đề xuất (MSRP - VNĐ)</label>
                            <input type="number" min="0" class="c1-catalog-search-input" wire:model.live.debounce.300ms="form.msrp" placeholder="Ví dụ: 870000000">
                            @error('form.msrp') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">Mô tả & Giới thiệu phiên bản</label>
                            <textarea class="c1-catalog-search-input" style="height: auto; padding: 10px 14px;" rows="4" wire:model.blur="form.description" placeholder="Nhập tóm tắt về thiết kế, động cơ, định vị khách hàng của phiên bản này..."></textarea>
                            @error('form.description') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <div class="c1-catalog-card">
                    <div class="c1-catalog-card-header">
                        <div>
                            <h4 class="c1-catalog-card-title">2. Thông số kỹ thuật chi tiết (Specs)</h4>
                            <p class="c1-catalog-card-desc">Cấu hình các chỉ số động cơ, công suất, tiêu hao nhiên liệu theo danh mục kỹ thuật.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach ($attributes as $attribute)
                            <div class="col-md-6" wire:key="trim-attribute-{{ $attribute->id }}">
                                <label class="form-label text-muted small fw-bold">
                                    {{ $attribute->label }}
                                    @if ($attribute->unit)
                                        <span class="fw-normal text-primary">({{ $attribute->unit }})</span>
                                    @endif
                                </label>

                                @if ($attribute->type === 'string')
                                    <input
                                        type="text"
                                        class="c1-catalog-search-input"
                                        wire:model.live.debounce.300ms="form.attributes.{{ $attribute->id }}.value_string"
                                        placeholder="Ví dụ: {{ $attribute->code === 'engine' ? '1.5L VTEC Turbo' : ($attribute->code === 'seat_material' ? 'Da cao cấp' : $attribute->code) }}"
                                    >
                                @elseif ($attribute->type === 'number')
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="c1-catalog-search-input"
                                        wire:model.blur="form.attributes.{{ $attribute->id }}.value_number"
                                        placeholder="Nhập giá trị số ({{ $attribute->unit ?: 'Số' }})"
                                    >
                                @else
                                    <select class="c1-catalog-select w-100" wire:model.blur="form.attributes.{{ $attribute->id }}.value_boolean">
                                        <option value="">-- Chưa khai báo --</option>
                                        <option value="1">Có trang bị</option>
                                        <option value="0">Không có</option>
                                    </select>
                                @endif

                                @error('form.attributes.' . $attribute->id . '.value_string') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                @error('form.attributes.' . $attribute->id . '.value_number') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                @error('form.attributes.' . $attribute->id . '.value_boolean') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="c1-catalog-card">
                    <div class="c1-catalog-card-header">
                        <div>
                            <h4 class="c1-catalog-card-title">3. Trang bị & Tiện ích tiêu chuẩn (Features)</h4>
                            <p class="c1-catalog-card-desc">Bấm chọn nhanh các tính năng an toàn, tiện nghi và công nghệ có sẵn trên phiên bản này.</p>
                        </div>
                    </div>

                    <div class="c1-features-wrapper">
                        @foreach ($featureGroups as $featureGroup)
                            <div class="c1-features-group">
                                <div class="c1-features-group-title">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $featureGroup->name }}
                                </div>
                                <div class="c1-chips-grid">
                                    @foreach ($featureGroup->features as $feature)
                                        <label class="c1-chip-item" wire:key="trim-feature-{{ $feature->id }}">
                                            <input type="checkbox" wire:model.live="form.feature_ids" value="{{ $feature->id }}">
                                            <span class="c1-chip-label">
                                                <svg class="c1-chip-check" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                {{ $feature->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('form.feature_ids.*') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="c1-form-sidebar">
                <div class="c1-catalog-card" style="position: sticky; top: 90px; border-color: #cbd5e1;">
                    <div class="c1-catalog-card-header mb-3">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11.5px; letter-spacing: 0.5px;">Xem trước phiên bản</span>
                        <span class="c1-pill-badge c1-pill-blue">
                            {{ $selectedModel === null ? 'Đang cập nhật' : trim($selectedModel->make?->name . ' ' . $selectedModel->name) }}
                        </span>
                    </div>

                    <div class="text-center py-4 px-3 mb-3" style="background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; color: var(--c1-primary);">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 17h14M5 17a2 2 0 01-2-2V9a2 2 0 012-2h1.5l1.5-2h8l1.5 2H19a2 2 0 012 2v6a2 2 0 01-2 2M5 17a2 2 0 100 4 2 2 0 000-4zm14 0a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                        </div>
                        <div class="fw-bold" style="font-size: 16px; color: var(--c1-text-heading);">
                            {{ $previewName !== '' ? $previewName : 'Tên phiên bản xe' }}
                        </div>
                        <div class="text-muted small mt-1">
                            {{ $previewYearFrom !== '' || $previewYearTo !== '' ? 'Năm đời: ' . ($previewYearFrom ?: '...') . ' - ' . ($previewYearTo ?: 'Hiện tại') : 'Vòng đời năm sản xuất' }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-muted small">Giá niêm yết đề xuất (MSRP):</div>
                        <div class="c1-price-highlight" style="font-size: 22px; margin-top: 2px;">
                            {{ is_numeric($form['msrp'] ?? null) && (int) $form['msrp'] > 0 ? number_format((int) $form['msrp'], 0, ',', '.') . ' ₫' : 'Chưa có MSRP' }}
                        </div>
                    </div>

                    <div class="p-3 mb-4" style="background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-muted">Trang bị đã chọn:</span>
                            <span class="c1-pill-badge c1-pill-blue fw-bold">{{ $selectedFeatureCount }} tính năng</span>
                        </div>
                        <div class="small text-muted">Động cơ: {{ $previewEngine !== '' ? $previewEngine : 'Chưa khai báo' }}</div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="c1-btn c1-btn-primary c1-btn-block" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                {{ $isEditing ? 'Lưu thay đổi phiên bản' : 'Tạo phiên bản xe' }}
                            </span>
                            <span wire:loading wire:target="save">Đang lưu...</span>
                        </button>
                        <a href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}" wire:navigate class="c1-btn-cancel">Hủy & Quay lại danh mục</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
