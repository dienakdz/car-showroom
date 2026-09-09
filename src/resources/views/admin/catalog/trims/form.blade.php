@extends('admin.layouts.app')

@section('title', $trimRecord->exists ? 'Cập nhật phiên bản: ' . $trimRecord->name : 'Tạo phiên bản xe (Trim)')

@section('admin-content')
    @php($attributeValueModels = $trimRecord->relationLoaded('attributeValues') ? $trimRecord->attributeValues->keyBy('attribute_id') : collect())
    @php($selectedFeatureIds = collect(old('feature_ids', $trimRecord->exists ? $trimRecord->features->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all())

    <div class="c1-form-workspace">
        <!-- Page Header & Breadcrumbs -->
        <div class="c1-page-header mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}" class="text-muted small text-decoration-none">Danh mục xe</a>
                    <span class="text-muted small">/</span>
                    <a href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}" class="text-muted small text-decoration-none">Phiên bản (Trims)</a>
                    <span class="text-muted small">/</span>
                    <span class="small fw-semibold text-primary">{{ $trimRecord->exists ? 'Chỉnh sửa' : 'Tạo mới' }}</span>
                </div>
                <h1 class="c1-page-title">{{ $trimRecord->exists ? 'Cập nhật phiên bản: ' . $trimRecord->name : 'Tạo phiên bản xe (Trim)' }}</h1>
                <p class="c1-page-subtitle">Khai báo thông số kỹ thuật, giá niêm yết và danh sách trang bị cho phiên bản xe.</p>
            </div>

            <div class="c1-header-actions">
                <a href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}" class="c1-action-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Về danh mục xe
                </a>
            </div>
        </div>

        <form action="{{ $trimRecord->exists ? route('admin.catalog.trims.update', $trimRecord) : route('admin.catalog.trims.store') }}" method="POST" id="trimForm">
            @csrf
            @if ($trimRecord->exists)
                @method('PATCH')
            @endif

            <div class="c1-form-layout">
                <!-- Left Main Column (68%) -->
                <div class="c1-form-main">
                    <!-- Card 1: Basic Information -->
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
                                <select name="model_id" id="trim_model_id" class="c1-catalog-select w-100" required>
                                    <option value="">-- Chọn dòng xe --</option>
                                    @foreach ($models as $model)
                                        <option value="{{ $model->id }}" data-make="{{ $model->make?->name }}" data-model="{{ $model->name }}" @selected((string) old('model_id', $trimRecord->model_id) === (string) $model->id)>
                                            {{ $model->make?->name }} / {{ $model->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('model_id') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Tên phiên bản (Trim) <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="trim_name" class="c1-catalog-search-input" value="{{ old('name', $trimRecord->name) }}" placeholder="Ví dụ: RS, 2.5Q, Wildtrak 4x4..." required>
                                @error('name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Đường dẫn slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="trim_slug" class="c1-catalog-search-input" value="{{ old('slug', $trimRecord->slug) }}" placeholder="Ví dụ: rs, 2-5q...">
                                @error('slug') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Năm bắt đầu</label>
                                <input type="number" min="1900" max="{{ now()->addYear()->format('Y') }}" name="year_from" id="trim_year_from" class="c1-catalog-search-input" value="{{ old('year_from', $trimRecord->year_from) }}" placeholder="2020">
                                @error('year_from') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Năm kết thúc</label>
                                <input type="number" min="1900" max="{{ now()->addYear()->format('Y') }}" name="year_to" id="trim_year_to" class="c1-catalog-search-input" value="{{ old('year_to', $trimRecord->year_to) }}" placeholder="{{ now()->format('Y') }}">
                                @error('year_to') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold">Giá niêm yết đề xuất (MSRP - VNĐ)</label>
                                <div class="position-relative">
                                    <input type="number" min="0" name="msrp" id="trim_msrp" class="c1-catalog-search-input" value="{{ old('msrp', $trimRecord->msrp) }}" placeholder="Ví dụ: 870000000">
                                </div>
                                @error('msrp') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold">Mô tả & Giới thiệu phiên bản</label>
                                <textarea name="description" id="trim_description" class="c1-catalog-search-input" style="height: auto; padding: 10px 14px;" rows="4" placeholder="Nhập tóm tắt về thiết kế, động cơ, định vị khách hàng của phiên bản này...">{{ old('description', $trimRecord->description) }}</textarea>
                                @error('description') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Specifications / Attributes -->
                    <div class="c1-catalog-card">
                        <div class="c1-catalog-card-header">
                            <div>
                                <h4 class="c1-catalog-card-title">2. Thông số kỹ thuật chi tiết (Specs)</h4>
                                <p class="c1-catalog-card-desc">Cấu hình các chỉ số động cơ, công suất, tiêu hao nhiên liệu theo danh mục kỹ thuật.</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach ($attributes as $attribute)
                                @php($attributeValue = $attributeValueModels->get($attribute->id))
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">
                                        {{ $attribute->label }}
                                        @if ($attribute->unit)
                                            <span class="fw-normal text-primary">({{ $attribute->unit }})</span>
                                        @endif
                                    </label>

                                    @if ($attribute->type === 'string')
                                        <input
                                            type="text"
                                            class="c1-catalog-search-input trim-attr-input"
                                            data-attr="{{ $attribute->code }}"
                                            name="attributes[{{ $attribute->id }}][value_string]"
                                            value="{{ old('attributes.' . $attribute->id . '.value_string', $attributeValue?->value_string) }}"
                                            placeholder="Ví dụ: {{ $attribute->code === 'engine' ? '1.5L VTEC Turbo' : ($attribute->code === 'seat_material' ? 'Da cao cấp' : $attribute->code) }}"
                                        >
                                    @elseif ($attribute->type === 'number')
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="c1-catalog-search-input trim-attr-input"
                                            data-attr="{{ $attribute->code }}"
                                            name="attributes[{{ $attribute->id }}][value_number]"
                                            value="{{ old('attributes.' . $attribute->id . '.value_number', $attributeValue?->value_number) }}"
                                            placeholder="Nhập giá trị số ({{ $attribute->unit ?: 'Số' }})"
                                        >
                                    @else
                                        <select name="attributes[{{ $attribute->id }}][value_boolean]" class="c1-catalog-select w-100">
                                            <option value="">-- Chưa khai báo --</option>
                                            <option value="1" @selected((string) old('attributes.' . $attribute->id . '.value_boolean', $attributeValue?->value_boolean === null ? '' : (int) $attributeValue->value_boolean) === '1')>Có trang bị</option>
                                            <option value="0" @selected((string) old('attributes.' . $attribute->id . '.value_boolean', $attributeValue?->value_boolean === null ? '' : (int) $attributeValue->value_boolean) === '0')>Không có</option>
                                        </select>
                                    @endif
                                    @error('attributes.' . $attribute->id . '.value_string') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    @error('attributes.' . $attribute->id . '.value_number') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                    @error('attributes.' . $attribute->id . '.value_boolean') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Card 3: Features & Equipment -->
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
                                            <label class="c1-chip-item">
                                                <input
                                                    type="checkbox"
                                                    class="feature-checkbox"
                                                    name="feature_ids[]"
                                                    value="{{ $feature->id }}"
                                                    data-feature-name="{{ $feature->name }}"
                                                    @checked(in_array($feature->id, $selectedFeatureIds, true))
                                                >
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
                    </div>
                </div>

                <!-- Right Sidebar Column (32% - Sticky Live Preview) -->
                <div class="c1-form-sidebar">
                    <div class="c1-catalog-card" style="position: sticky; top: 90px; border-color: #cbd5e1;">
                        <div class="c1-catalog-card-header mb-3">
                            <span class="text-uppercase fw-bold text-muted" style="font-size: 11.5px; letter-spacing: 0.5px;">Xem trước phiên bản</span>
                            <span class="c1-pill-badge c1-pill-blue" id="preview_model_badge">Đang cập nhật</span>
                        </div>

                        <!-- Preview Car Emblem Graphic -->
                        <div class="text-center py-4 px-3 mb-3" style="background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; color: var(--c1-primary);">
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M5 17h14M5 17a2 2 0 01-2-2V9a2 2 0 012-2h1.5l1.5-2h8l1.5 2H19a2 2 0 012 2v6a2 2 0 01-2 2M5 17a2 2 0 100 4 2 2 0 000-4zm14 0a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                            </div>
                            <div class="fw-bold" style="font-size: 16px; color: var(--c1-text-heading);" id="preview_full_name">
                                {{ $trimRecord->exists ? ($trimRecord->model?->make?->name . ' ' . $trimRecord->model?->name . ' ' . $trimRecord->name) : 'Tên phiên bản xe' }}
                            </div>
                            <div class="text-muted small mt-1" id="preview_years">
                                {{ $trimRecord->year_from ? ($trimRecord->year_from . ' - ' . ($trimRecord->year_to ?: 'Hiện tại')) : 'Vòng đời năm sản xuất' }}
                            </div>
                        </div>

                        <!-- Preview Details -->
                        <div class="mb-4">
                            <div class="text-muted small">Giá niêm yết đề xuất (MSRP):</div>
                            <div class="c1-price-highlight" style="font-size: 22px; margin-top: 2px;" id="preview_msrp">
                                {{ $trimRecord->msrp ? (number_format((int) $trimRecord->msrp, 0, ',', '.') . ' ₫') : 'Chưa có MSRP' }}
                            </div>
                        </div>

                        <!-- Selected Features Summary Badge -->
                        <div class="p-3 mb-4" style="background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small fw-semibold text-muted">Trang bị đã chọn:</span>
                                <span class="c1-pill-badge c1-pill-blue fw-bold" id="preview_feature_count">
                                    {{ count($selectedFeatureIds) }} tính năng
                                </span>
                            </div>
                            <div class="small text-muted" id="preview_engine_tag">
                                Động cơ: Chưa khai báo
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" form="trimForm" class="c1-btn c1-btn-primary c1-btn-block">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                {{ $trimRecord->exists ? 'Lưu thay đổi phiên bản' : 'Tạo phiên bản xe' }}
                            </button>
                            <a href="{{ route('admin.catalog.index', ['tab' => 'trims']) }}" class="c1-btn-cancel">
                                Hủy & Quay lại danh mục
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Live Preview Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modelSelect = document.getElementById('trim_model_id');
            const nameInput = document.getElementById('trim_name');
            const slugInput = document.getElementById('trim_slug');
            const yearFromInput = document.getElementById('trim_year_from');
            const yearToInput = document.getElementById('trim_year_to');
            const msrpInput = document.getElementById('trim_msrp');
            const engineInput = document.querySelector('.trim-attr-input[data-attr="engine"]');
            const featureCheckboxes = document.querySelectorAll('.feature-checkbox');

            const previewBadge = document.getElementById('preview_model_badge');
            const previewFullName = document.getElementById('preview_full_name');
            const previewYears = document.getElementById('preview_years');
            const previewMsrp = document.getElementById('preview_msrp');
            const previewFeatureCount = document.getElementById('preview_feature_count');
            const previewEngineTag = document.getElementById('preview_engine_tag');

            const slugify = (text) => {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            };

            const updatePreview = () => {
                const selectedOption = modelSelect?.options[modelSelect.selectedIndex];
                const makeName = selectedOption?.dataset.make || '';
                const modelName = selectedOption?.dataset.model || '';
                const trimName = nameInput?.value?.trim() || '';

                if (makeName && modelName) {
                    previewBadge.textContent = `${makeName} ${modelName}`;
                    previewFullName.textContent = trimName ? `${makeName} ${modelName} ${trimName}` : `${makeName} ${modelName}`;
                } else if (trimName) {
                    previewBadge.textContent = 'Phiên bản xe';
                    previewFullName.textContent = trimName;
                } else {
                    previewBadge.textContent = 'Đang cập nhật';
                    previewFullName.textContent = 'Tên phiên bản xe';
                }

                const yFrom = yearFromInput?.value?.trim();
                const yTo = yearToInput?.value?.trim();
                if (yFrom || yTo) {
                    previewYears.textContent = `Năm đời: ${yFrom || '...'} - ${yTo || 'Hiện tại'}`;
                } else {
                    previewYears.textContent = 'Vòng đời năm sản xuất';
                }

                const msrpVal = parseInt(msrpInput?.value || 0, 10);
                if (msrpVal > 0) {
                    previewMsrp.textContent = new Intl.NumberFormat('vi-VN').format(msrpVal) + ' ₫';
                } else {
                    previewMsrp.textContent = 'Chưa có MSRP';
                }

                if (engineInput && engineInput.value.trim()) {
                    previewEngineTag.textContent = `Động cơ: ${engineInput.value.trim()}`;
                } else {
                    previewEngineTag.textContent = 'Động cơ: Chưa khai báo';
                }

                let checkedCount = 0;
                featureCheckboxes.forEach(cb => {
                    if (cb.checked) checkedCount++;
                });
                previewFeatureCount.textContent = `${checkedCount} tính năng`;
            };

            nameInput?.addEventListener('input', () => {
                if (slugInput && !slugInput.dataset.manual) {
                    slugInput.value = slugify(nameInput.value);
                }
                updatePreview();
            });

            slugInput?.addEventListener('input', () => {
                slugInput.dataset.manual = 'true';
            });

            modelSelect?.addEventListener('change', updatePreview);
            yearFromInput?.addEventListener('input', updatePreview);
            yearToInput?.addEventListener('input', updatePreview);
            msrpInput?.addEventListener('input', updatePreview);
            engineInput?.addEventListener('input', updatePreview);

            featureCheckboxes.forEach(cb => {
                cb.addEventListener('change', updatePreview);
            });

            updatePreview();
        });
    </script>
@endsection
