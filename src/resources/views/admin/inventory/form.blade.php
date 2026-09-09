@extends('admin.layouts.app')

@section('title', $carUnit->exists ? 'Cập nhật thông tin xe' : 'Thêm xe mới vào kho')

@section('page-actions')
    <a href="{{ route('admin.inventory.index') }}" class="c1-btn c1-btn-secondary">
        <i class="fa fa-arrow-left"></i> Quay lại kho xe
    </a>
    <button type="button" onclick="submitWithStatus('available')" class="c1-btn c1-btn-primary">
        <i class="fa fa-check"></i> {{ $carUnit->exists ? 'Cập nhật xe' : 'Lưu & Đăng bán' }}
    </button>
@endsection

@section('admin-content')
    @php
        $mediaRows = old('media', $carUnit->exists
            ? $carUnit->media->map(fn ($media) => [
                'id' => $media->id,
                'type' => $media->type,
                'path_or_url' => $media->path_or_url,
                'caption' => $media->caption,
                'sort_order' => $media->sort_order,
                'is_cover' => $media->is_cover,
            ])->all()
            : []);
        $coverMedia = collect($mediaRows)->firstWhere('is_cover', true) ?? ($mediaRows[0] ?? null);
    @endphp

    @if ($errors->any())
        <div class="c1-card mb-4" style="background: #fef2f2; border-color: #fecaca; padding: 16px 20px;">
            <div style="font-weight: 700; color: #991b1b; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-exclamation-circle"></i> Vui lòng kiểm tra lại các thông tin sau:
            </div>
            <ul style="margin: 0; padding-left: 20px; color: #b91c1c; font-size: 13.5px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="inventory-form" action="{{ $carUnit->exists ? route('admin.inventory.update', $carUnit) : route('admin.inventory.store') }}" method="POST">
        @csrf
        @if ($carUnit->exists)
            @method('PATCH')
        @endif

        <input type="hidden" name="currency" value="VND">

        <div class="c1-form-layout">
            {{-- Cột trái (68%): Các khối thông tin nhập liệu --}}
            <div class="c1-form-main">
                {{-- Thẻ 1: Thông tin định danh & Phiên bản xe --}}
                <div class="c1-form-card">
                    <div class="c1-form-card-title">
                        <span><i class="fa fa-id-card text-primary me-2"></i>1. Thông tin định danh & Phiên bản xe</span>
                        <span class="c1-pill c1-pill-blue" style="font-size: 11px;">Bắt buộc</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-trim-id">Chọn phiên bản Trim <span class="c1-label-req">*</span></label>
                                <select name="trim_id" id="input-trim-id" class="c1-form-select" required>
                                    <option value="">-- Chọn Hãng / Dòng xe / Phiên bản --</option>
                                    @foreach ($trims as $trim)
                                        <option value="{{ $trim->id }}" 
                                            data-name="{{ $trim->model?->make?->name }} {{ $trim->model?->name }} {{ $trim->name }}"
                                            data-year="{{ $trim->year_from ?? $trim->year_to ?? date('Y') }}"
                                            @selected((string) old('trim_id', $carUnit->trim_id) === (string) $trim->id)>
                                            {{ $trim->model?->make?->name }} / {{ $trim->model?->name }} / {{ $trim->name }} ({{ $trim->year_from ?? '...' }} - {{ $trim->year_to ?? 'nay' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="c1-form-group">
                                <label class="c1-label">Tình trạng xe <span class="c1-label-req">*</span></label>
                                <div class="c1-segmented" id="condition-segmented">
                                    <button type="button" class="c1-segment-btn {{ old('condition', $carUnit->condition ?: 'new') === 'new' ? 'active' : '' }}" onclick="selectCondition('new')">
                                        <i class="fa fa-certificate me-1 text-primary"></i> Xe mới 100%
                                    </button>
                                    <button type="button" class="c1-segment-btn {{ old('condition', $carUnit->condition) === 'used' ? 'active' : '' }}" onclick="selectCondition('used')">
                                        <i class="fa fa-history me-1 text-muted"></i> Xe đã qua sử dụng
                                    </button>
                                    <button type="button" class="c1-segment-btn {{ old('condition', $carUnit->condition) === 'cpo' ? 'active' : '' }}" onclick="selectCondition('cpo')">
                                        <i class="fa fa-shield-alt me-1 text-success"></i> Chính hãng CPO
                                    </button>
                                </div>
                                <input type="hidden" name="condition" id="input-condition" value="{{ old('condition', $carUnit->condition ?: 'new') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-stock-code">Mã quản lý kho (Stock code) <span class="c1-label-req">*</span></label>
                                <input type="text" name="stock_code" id="input-stock-code" class="c1-input" value="{{ old('stock_code', $carUnit->stock_code ?: 'STK-' . date('Y') . '-' . rand(100, 999)) }}" required placeholder="VD: NEW-CIVIC-002">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-vin">Số khung (Số VIN)</label>
                                <input type="text" name="vin" id="input-vin" class="c1-input" value="{{ old('vin', $carUnit->vin) }}" placeholder="VD: JHMFC1001SL000002" maxlength="255">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-year">Năm sản xuất <span class="c1-label-req">*</span></label>
                                <input type="number" min="1900" max="{{ now()->addYear()->format('Y') }}" name="year" id="input-year" class="c1-input" value="{{ old('year', $carUnit->year ?: now()->format('Y')) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-mileage">Số Kilomet đã đi (ODO)</label>
                                <input type="number" min="0" name="mileage" id="input-mileage" class="c1-input" value="{{ old('mileage', $carUnit->mileage ?: 0) }}" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thẻ 2: Thông số kỹ thuật & Màu sắc --}}
                <div class="c1-form-card">
                    <div class="c1-form-card-title">
                        <span><i class="fa fa-cogs text-primary me-2"></i>2. Thông số kỹ thuật & Màu sắc</span>
                        <span style="font-size: 12px; color: #64748b; font-weight: normal;">Hỗ trợ hiển thị trên website và bộ lọc</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-body-type">Kiểu dáng (Body type)</label>
                                <select name="body_type_id" id="input-body-type" class="c1-form-select">
                                    <option value="">-- Chọn kiểu dáng --</option>
                                    @foreach ($bodyTypes as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('body_type_id', $carUnit->body_type_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-fuel-type">Nhiên liệu (Fuel type)</label>
                                <select name="fuel_type_id" id="input-fuel-type" class="c1-form-select">
                                    <option value="">-- Chọn nhiên liệu --</option>
                                    @foreach ($fuelTypes as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('fuel_type_id', $carUnit->fuel_type_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-transmission">Hộp số (Transmission)</label>
                                <select name="transmission_id" id="input-transmission" class="c1-form-select">
                                    <option value="">-- Chọn hộp số --</option>
                                    @foreach ($transmissions as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('transmission_id', $carUnit->transmission_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-drivetrain">Hệ dẫn động (Drivetrain)</label>
                                <select name="drivetrain_id" id="input-drivetrain" class="c1-form-select">
                                    <option value="">-- Chọn dẫn động --</option>
                                    @foreach ($drivetrains as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('drivetrain_id', $carUnit->drivetrain_id) === (string) $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-exterior-color">Màu ngoại thất</label>
                                <select name="exterior_color_id" id="input-exterior-color" class="c1-form-select">
                                    <option value="">-- Chọn màu ngoại thất --</option>
                                    @foreach ($exteriorColors ?? $colors as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('exterior_color_id', $carUnit->exterior_color_id) === (string) $item->id)>
                                            {{ $item->name }} {{ $item->hex ? '(' . $item->hex . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-interior-color">Màu nội thất</label>
                                <select name="interior_color_id" id="input-interior-color" class="c1-form-select">
                                    <option value="">-- Chọn màu nội thất --</option>
                                    @foreach ($interiorColors ?? $colors as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('interior_color_id', $carUnit->interior_color_id) === (string) $item->id)>
                                            {{ $item->name }} {{ $item->hex ? '(' . $item->hex . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thẻ 3: Định giá & Trạng thái kho --}}
                <div class="c1-form-card">
                    <div class="c1-form-card-title">
                        <span><i class="fa fa-tag text-primary me-2"></i>3. Định giá & Trạng thái kho</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-price">Giá niêm yết bán (VND) <span class="c1-label-req">*</span></label>
                                <div class="position-relative">
                                    <input type="number" min="0" step="1000000" name="price" id="input-price" class="c1-input" value="{{ old('price', $carUnit->price) }}" placeholder="VD: 870000000" style="padding-right: 60px;">
                                    <span style="position: absolute; right: 14px; top: 11px; font-size: 13px; font-weight: 700; color: #64748b;">VND</span>
                                </div>
                                <div id="price-formatted-hint" style="font-size: 12px; color: #2563eb; font-weight: 600; margin-top: 3px;">
                                    {{ $carUnit->price ? number_format($carUnit->price, 0, ',', '.') . ' VNĐ' : 'Chưa nhập giá' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-status">Trạng thái kho ban đầu <span class="c1-label-req">*</span></label>
                                <select name="status" id="input-status" class="c1-form-select" required>
                                    <option value="available" @selected(old('status', $carUnit->status ?: 'available') === 'available')>Sẵn sàng bán (Available)</option>
                                    <option value="draft" @selected(old('status', $carUnit->status) === 'draft')>Lưu kho / Bản nháp (Draft)</option>
                                    <option value="archived" @selected(old('status', $carUnit->status) === 'archived')>Lưu trữ (Archived)</option>
                                    @if ($carUnit->status === 'on_hold')
                                        <option value="on_hold" selected>Đang giữ cọc (On Hold)</option>
                                    @endif
                                    @if ($carUnit->status === 'sold')
                                        <option value="sold" selected>Đã bàn giao (Sold)</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="c1-form-group">
                                <label class="c1-label" for="input-notes">Ghi chú nội bộ</label>
                                <textarea name="notes_internal" id="input-notes" class="c1-form-textarea" placeholder="Ghi chú nội bộ cho tư vấn viên showroom (tình trạng giấy tờ, nguồn gốc xe, lưu ý đăng kiểm, bảo dưỡng)...">{{ old('notes_internal', $carUnit->notes_internal) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thẻ 4: Hình ảnh xe & Thư viện Media (Upload trực tiếp & Kéo thả, không input URL) --}}
                <div class="c1-form-card">
                    <div class="c1-form-card-title">
                        <span><i class="fa fa-images text-primary me-2"></i>4. Hình ảnh xe & Thư viện Media</span>
                        <button type="button" class="c1-btn c1-btn-primary" style="font-size: 12.5px; padding: 6px 14px;" onclick="triggerFileInput()">
                            <i class="fa fa-upload me-1"></i> Tải ảnh lên
                        </button>
                    </div>

                    {{-- Khung Dropzone Kéo Thả & Bấm Tải Ảnh --}}
                    <div class="c1-dropzone" id="dropzone-box" onclick="triggerFileInput()">
                        <input type="file" id="media-file-input" multiple accept="image/jpeg,image/png,image/webp" style="display: none;">
                        <i class="fa fa-cloud-upload-alt c1-dropzone-icon"></i>
                        <div class="c1-dropzone-text">Kéo & thả ảnh xe vào đây, hoặc bấm để chọn tệp từ máy tính</div>
                        <div class="c1-dropzone-sub">Hỗ trợ định dạng JPG, PNG, WEBP tối đa 10MB mỗi ảnh • Có thể chọn nhiều ảnh cùng lúc</div>
                        <div id="upload-status" class="mt-2 text-primary" style="display: none; font-size: 13px; font-weight: 600;">
                            <span class="c1-upload-spinner me-2"></span> Đang tải ảnh lên hệ thống...
                        </div>
                    </div>

                    {{-- Danh sách ảnh đã tải lên dạng Gallery Cards --}}
                    <div class="c1-media-gallery" id="media-gallery-container">
                        @if (empty($mediaRows))
                            <div class="c1-media-empty-state" id="empty-media-msg">
                                <i class="fa fa-image me-1"></i> Chưa có ảnh nào được tải lên. Bấm vào khung trên hoặc kéo thả ảnh vào để thêm ảnh xe.
                            </div>
                        @else
                            @foreach ($mediaRows as $index => $row)
                                <div class="c1-media-card media-item-card" data-index="{{ $index }}">
                                    <input type="hidden" name="media[{{ $index }}][id]" value="{{ $row['id'] ?? '' }}">
                                    <input type="hidden" name="media[{{ $index }}][type]" value="{{ $row['type'] ?? 'image' }}">
                                    <input type="hidden" name="media[{{ $index }}][sort_order]" value="{{ $row['sort_order'] ?? $index }}">
                                    <input type="hidden" name="media[{{ $index }}][path_or_url]" class="media-path-input" value="{{ $row['path_or_url'] ?? '' }}">
                                    <input type="hidden" name="media[{{ $index }}][is_cover]" class="media-cover-input" value="{{ !empty($row['is_cover']) ? '1' : '0' }}">

                                    <div class="c1-media-thumb-wrap">
                                        <img src="{{ asset($row['path_or_url']) }}" 
                                             alt="Ảnh xe" 
                                             class="c1-media-thumb"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="c1-media-thumb-fallback" style="display: none;">
                                            <i class="fa fa-car"></i>
                                        </div>
                                    </div>

                                    @if (!empty($row['is_cover']))
                                        <span class="c1-media-cover-tag">Ảnh bìa</span>
                                    @endif

                                    <button type="button" class="c1-media-remove" onclick="removeMediaCard(this)" title="Xóa ảnh này">
                                        <i class="fa fa-times"></i>
                                    </button>

                                    <div class="c1-media-card-body">
                                        <input type="text" name="media[{{ $index }}][caption]" class="c1-media-caption-input" value="{{ $row['caption'] ?? '' }}" placeholder="Mô tả góc chụp...">
                                        <label class="c1-media-cover-select mt-1">
                                            <input type="radio" name="cover_selection" value="{{ $index }}" {{ !empty($row['is_cover']) ? 'checked' : '' }} onchange="setAsCover({{ $index }})">
                                            <span>Đặt làm ảnh bìa</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cột phải (32%): Thẻ Xem trước trực tiếp (Live Preview) & Nút hành động --}}
            <div class="c1-form-sidebar">
                <div class="c1-preview-card">
                    <div class="c1-preview-card-header">
                        <i class="fa fa-eye text-primary"></i> Xem trước hiển thị (Live Preview)
                    </div>

                    <div class="c1-preview-box">
                        <div class="c1-preview-image-wrap">
                            <img id="preview-cover-img" class="c1-preview-image" src="{{ $coverMedia ? asset($coverMedia['path_or_url']) : '' }}" 
                                 style="{{ empty($coverMedia['path_or_url']) ? 'display: none;' : '' }}"
                                 onerror="this.style.display='none'; document.getElementById('preview-empty-icon').style.display='block';">
                            <i id="preview-empty-icon" class="fa fa-car c1-preview-empty-icon" style="{{ !empty($coverMedia['path_or_url']) ? 'display: none;' : '' }}"></i>
                            <span id="preview-condition-badge" class="c1-pill c1-pill-green c1-preview-badge-pos">
                                {{ $carUnit->condition === 'used' ? 'Xe lướt' : ($carUnit->condition === 'cpo' ? 'Chính hãng CPO' : 'Mới 100%') }}
                            </span>
                        </div>

                        <div class="c1-preview-content">
                            <div class="c1-preview-name" id="preview-car-name">
                                {{ $carUnit->trim ? ($carUnit->trim->model?->make?->name . ' ' . $carUnit->trim->model?->name . ' ' . $carUnit->trim->name) : 'Honda Civic Civic RS (2025)' }}
                            </div>
                            <div class="c1-preview-vin-code" id="preview-vin-code">
                                VIN: {{ $carUnit->vin ?: 'JHMFC1001SL000002' }}
                            </div>
                            <div class="c1-preview-price-tag" id="preview-price-tag">
                                {{ $carUnit->price ? number_format($carUnit->price, 0, ',', '.') . ' VND' : '870.000.000 VND' }}
                            </div>
                            <div class="c1-preview-specs-bar">
                                <span id="preview-body-type"><i class="fa fa-car me-1"></i>{{ $carUnit->bodyType?->name ?: 'Sedan' }}</span> • 
                                <span id="preview-fuel-type"><i class="fa fa-gas-pump me-1"></i>{{ $carUnit->fuelType?->name ?: 'Xăng Turbo' }}</span> • 
                                <span id="preview-transmission"><i class="fa fa-cog me-1"></i>{{ $carUnit->transmission?->name ?: 'Tự động CVT' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="c1-btn-stack">
                        <button type="button" class="c1-btn c1-btn-primary c1-btn-block" onclick="submitWithStatus('available')">
                            <i class="fa fa-check-circle me-1"></i> {{ $carUnit->exists ? 'Lưu thông tin xe' : 'Lưu & Đăng bán ngay' }}
                        </button>
                        <button type="button" class="c1-btn c1-btn-secondary c1-btn-block" onclick="submitWithStatus('draft')">
                            <i class="fa fa-save me-1"></i> Lưu bản nháp (Draft)
                        </button>
                        <a href="{{ route('admin.inventory.index') }}" class="c1-btn-cancel">
                            Hủy bỏ và quay lại
                        </a>
                    </div>
                </div>

                {{-- Khối quản lý trạng thái bổ sung khi đang Chỉnh sửa xe có sẵn --}}
                @if ($carUnit->exists)
                    <div class="c1-form-card">
                        <div class="c1-form-card-title">
                            <span><i class="fa fa-info-circle text-primary me-2"></i>Trạng thái niêm yết</span>
                        </div>
                        <div style="font-size: 13px; color: #475569; display: flex; flex-direction: column; gap: 8px;">
                            <div><strong>Ngày đăng:</strong> {{ optional($carUnit->published_at)->format('d/m/Y H:i') ?? 'Chưa xuất bản' }}</div>
                            <div><strong>Giữ cọc đến:</strong> {{ optional($carUnit->hold_until)->format('d/m/Y H:i') ?? 'Không giữ' }}</div>
                            <div><strong>Ngày bán:</strong> {{ optional($carUnit->sold_at)->format('d/m/Y H:i') ?? 'Chưa bán' }}</div>
                        </div>

                        <div class="mt-3 pt-3" style="border-top: 1px dashed #e2e8f0; display: flex; flex-direction: column; gap: 8px;">
                            @if ($carUnit->status !== 'available' && $carUnit->status !== 'sold')
                                <form action="{{ route('admin.inventory.publish', $carUnit) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="c1-btn c1-btn-primary w-100" style="font-size: 12.5px; height: 36px;">Xuất bản bán xe</button>
                                </form>
                            @endif

                            @if ($carUnit->status !== 'archived')
                                <form action="{{ route('admin.inventory.archive', $carUnit) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="c1-btn c1-btn-secondary w-100" style="font-size: 12.5px; height: 36px;">Lưu trữ xe (Archive)</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function triggerFileInput() {
        const fileInput = document.getElementById('media-file-input');
        if (fileInput) {
            fileInput.click();
        }
    }

    function selectCondition(val) {
        document.getElementById('input-condition').value = val;
        const btns = document.querySelectorAll('#condition-segmented .c1-segment-btn');
        btns.forEach(btn => btn.classList.remove('active'));

        const badge = document.getElementById('preview-condition-badge');
        if (val === 'new') {
            btns[0].classList.add('active');
            badge.className = 'c1-pill c1-pill-green c1-preview-badge-pos';
            badge.innerText = 'Mới 100%';
        } else if (val === 'used') {
            btns[1].classList.add('active');
            badge.className = 'c1-pill c1-pill-orange c1-preview-badge-pos';
            badge.innerText = 'Xe đã qua sử dụng';
        } else {
            btns[2].classList.add('active');
            badge.className = 'c1-pill c1-pill-purple c1-preview-badge-pos';
            badge.innerText = 'Chính hãng CPO';
        }
    }

    function submitWithStatus(status) {
        const statusSelect = document.getElementById('input-status');
        if (statusSelect) {
            statusSelect.value = status;
        }
        document.getElementById('inventory-form').submit();
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function setAsCover(targetIndex) {
        document.querySelectorAll('.media-item-card').forEach((card, idx) => {
            const coverInput = card.querySelector('.media-cover-input');
            const existingTag = card.querySelector('.c1-media-cover-tag');
            if (existingTag) existingTag.remove();

            if (idx === targetIndex) {
                coverInput.value = '1';
                const tag = document.createElement('span');
                tag.className = 'c1-media-cover-tag';
                tag.innerText = 'Ảnh bìa';
                card.appendChild(tag);

                // Update live preview image
                const imgThumb = card.querySelector('.c1-media-thumb');
                if (imgThumb && imgThumb.src) {
                    const previewCover = document.getElementById('preview-cover-img');
                    const emptyIcon = document.getElementById('preview-empty-icon');
                    previewCover.src = imgThumb.src;
                    previewCover.style.display = 'block';
                    emptyIcon.style.display = 'none';
                }
            } else {
                coverInput.value = '0';
            }
        });
    }

    function removeMediaCard(btn) {
        const card = btn.closest('.media-item-card');
        if (card) {
            card.remove();
            const remaining = document.querySelectorAll('.media-item-card');
            
            if (remaining.length === 0) {
                const gallery = document.getElementById('media-gallery-container');
                const emptyMsg = document.createElement('div');
                emptyMsg.className = 'c1-media-empty-state';
                emptyMsg.id = 'empty-media-msg';
                emptyMsg.innerHTML = '<i class="fa fa-image me-1"></i> Chưa có ảnh nào được tải lên. Bấm vào khung trên hoặc kéo thả ảnh vào để thêm ảnh xe.';
                gallery.appendChild(emptyMsg);

                // Reset preview image
                const previewCover = document.getElementById('preview-cover-img');
                const emptyIcon = document.getElementById('preview-empty-icon');
                previewCover.style.display = 'none';
                previewCover.src = '';
                emptyIcon.style.display = 'block';
            } else {
                // Re-index remaining cards
                remaining.forEach((item, index) => {
                    item.setAttribute('data-index', index);
                    const radio = item.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.value = index;
                        radio.setAttribute('onchange', `setAsCover(${index})`);
                    }
                });
                // Ensure there is a cover image
                const hasCover = Array.from(remaining).some(item => item.querySelector('.media-cover-input')?.value === '1');
                if (!hasCover) {
                    setAsCover(0);
                }
            }
        }
    }

    function addMediaCard(url, caption = '') {
        const emptyMsg = document.getElementById('empty-media-msg');
        if (emptyMsg) {
            emptyMsg.remove();
        }

        const gallery = document.getElementById('media-gallery-container');
        const index = gallery.querySelectorAll('.media-item-card').length;
        const isFirst = index === 0;

        const div = document.createElement('div');
        div.className = 'c1-media-card media-item-card';
        div.setAttribute('data-index', index);
        div.innerHTML = `
            <input type="hidden" name="media[${index}][id]" value="">
            <input type="hidden" name="media[${index}][type]" value="image">
            <input type="hidden" name="media[${index}][sort_order]" value="${index}">
            <input type="hidden" name="media[${index}][path_or_url]" class="media-path-input" value="${url}">
            <input type="hidden" name="media[${index}][is_cover]" class="media-cover-input" value="${isFirst ? '1' : '0'}">

            <div class="c1-media-thumb-wrap">
                <img src="${url}" alt="Ảnh xe" class="c1-media-thumb" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="c1-media-thumb-fallback" style="display: none;">
                    <i class="fa fa-car"></i>
                </div>
            </div>
            ${isFirst ? '<span class="c1-media-cover-tag">Ảnh bìa</span>' : ''}

            <button type="button" class="c1-media-remove" onclick="removeMediaCard(this)" title="Xóa ảnh này">
                <i class="fa fa-times"></i>
            </button>

            <div class="c1-media-card-body">
                <input type="text" name="media[${index}][caption]" class="c1-media-caption-input" value="${caption}" placeholder="Mô tả góc chụp...">
                <label class="c1-media-cover-select mt-1">
                    <input type="radio" name="cover_selection" value="${index}" ${isFirst ? 'checked' : ''} onchange="setAsCover(${index})">
                    <span>Đặt làm ảnh bìa</span>
                </label>
            </div>
        `;
        gallery.appendChild(div);

        if (isFirst) {
            setAsCover(0);
        }
    }

    async function handleFilesUpload(files) {
        if (!files || files.length === 0) return;

        const uploadStatus = document.getElementById('upload-status');
        if (uploadStatus) uploadStatus.style.display = 'block';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || document.querySelector('input[name="_token"]')?.value;

        for (const file of files) {
            if (!file.type.startsWith('image/')) continue;

            const formData = new FormData();
            formData.append('file', file);
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }

            try {
                const response = await fetch('{{ route('admin.inventory.media.upload') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success && data.path_or_url) {
                    addMediaCard(data.path_or_url, file.name.replace(/\.[^/.]+$/, ""));
                } else {
                    alert('Lỗi tải ảnh: ' + (data.message || 'Không thể tải tệp lên'));
                }
            } catch (err) {
                console.error('Upload failed:', err);
                alert('Có lỗi xảy ra khi tải ảnh lên máy chủ.');
            }
        }

        if (uploadStatus) uploadStatus.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Dropzone Drag & Drop Handlers
        const dropzone = document.getElementById('dropzone-box');
        const fileInput = document.getElementById('media-file-input');

        if (dropzone && fileInput) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('dragover');
                }, false);
            });

            dropzone.addEventListener('drop', function (e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFilesUpload(files);
            }, false);

            fileInput.addEventListener('change', function () {
                handleFilesUpload(this.files);
                this.value = ''; // Reset for consecutive uploads of same file
            });
        }

        // Dynamic Live Preview Listeners
        const trimSelect = document.getElementById('input-trim-id');
        const vinInput = document.getElementById('input-vin');
        const priceInput = document.getElementById('input-price');
        const priceHint = document.getElementById('price-formatted-hint');
        const bodySelect = document.getElementById('input-body-type');
        const fuelSelect = document.getElementById('input-fuel-type');
        const transSelect = document.getElementById('input-transmission');

        const previewName = document.getElementById('preview-car-name');
        const previewVin = document.getElementById('preview-vin-code');
        const previewPrice = document.getElementById('preview-price-tag');
        const previewBody = document.getElementById('preview-body-type');
        const previewFuel = document.getElementById('preview-fuel-type');
        const previewTrans = document.getElementById('preview-transmission');

        if (trimSelect) {
            trimSelect.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.value) {
                    previewName.innerText = opt.getAttribute('data-name') || opt.text;
                }
            });
        }

        if (vinInput) {
            vinInput.addEventListener('input', function () {
                previewVin.innerText = this.value ? `VIN: ${this.value}` : 'VIN: JHMFC1001SL000002';
            });
        }

        if (priceInput) {
            priceInput.addEventListener('input', function () {
                const val = parseInt(this.value, 10);
                if (!isNaN(val) && val > 0) {
                    const formatted = formatNumber(val) + ' VND';
                    previewPrice.innerText = formatted;
                    if (priceHint) priceHint.innerText = formatted;
                } else {
                    previewPrice.innerText = 'Chưa định giá';
                    if (priceHint) priceHint.innerText = 'Chưa định giá';
                }
            });
        }

        if (bodySelect) {
            bodySelect.addEventListener('change', function () {
                const text = this.options[this.selectedIndex]?.text;
                if (text && this.value) {
                    previewBody.innerHTML = `<i class="fa fa-car me-1"></i>${text}`;
                }
            });
        }

        if (fuelSelect) {
            fuelSelect.addEventListener('change', function () {
                const text = this.options[this.selectedIndex]?.text;
                if (text && this.value) {
                    previewFuel.innerHTML = `<i class="fa fa-gas-pump me-1"></i>${text}`;
                }
            });
        }

        if (transSelect) {
            transSelect.addEventListener('change', function () {
                const text = this.options[this.selectedIndex]?.text;
                if (text && this.value) {
                    previewTrans.innerHTML = `<i class="fa fa-cog me-1"></i>${text}`;
                }
            });
        }
    });
</script>
@endpush
