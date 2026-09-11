<div>
    {{-- Feedback Message --}}
    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span><i class="fa {{ ($feedback['type'] ?? 'success') === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle' }} me-2"></i>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Error Banner --}}
    @if ($errors->any())
        <div class="c1-alert c1-alert-danger mb-4" style="padding: 14px 18px; border-radius: 8px;">
            <div style="font-weight: 600; margin-bottom: 6px;">
                <i class="fa fa-exclamation-triangle me-1"></i> Vui lòng kiểm tra lại các trường thông tin:
            </div>
            <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="c1-page-header mb-4" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 class="c1-page-title">{{ $carUnitId !== null ? 'Cập nhật xe [' . ($form['stock_code'] ?? '') . ']' : 'Thêm xe mới vào kho' }}</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                {{ $carUnitId !== null ? 'Chỉnh sửa thông số, hình ảnh và trạng thái kho xe.' : 'Khai báo thông tin định danh, thông số kỹ thuật, định giá và hình ảnh xe.' }}
            </div>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="{{ route('admin.inventory.index') }}" wire:navigate class="c1-btn c1-btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Về kho xe
            </a>
            <button
                type="button"
                wire:click="saveWithStatus('available')"
                wire:loading.attr="disabled"
                class="c1-btn c1-btn-primary"
            >
                <i class="fa fa-check me-1"></i> {{ $carUnitId !== null ? 'Cập nhật xe' : 'Lưu & Đăng bán' }}
            </button>
        </div>
    </div>

    {{-- 2-Column Form Layout --}}
    <div class="row g-4">
        {{-- Left Column (68%) --}}
        <div class="col-lg-8">
            {{-- Thẻ 1: Thông tin định danh & Phiên bản xe --}}
            <div class="c1-panel mb-4" style="padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
                        <i class="fa fa-id-card text-primary me-2"></i> 1. Thông tin định danh & Phiên bản xe
                    </h4>
                    <span class="c1-pill c1-pill-blue" style="font-size: 11px;">Bắt buộc</span>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">
                            Chọn phiên bản xe (Trim) <span class="text-danger">*</span>
                        </label>
                        <select wire:model.live="form.trim_id" class="form-control" style="border-radius: 8px; height: 42px;">
                            <option value="">-- Chọn Hãng / Dòng xe / Phiên bản --</option>
                            @foreach ($trims as $trim)
                                <option value="{{ $trim->id }}">
                                    {{ $trim->model?->make?->name }} / {{ $trim->model?->name }} / {{ $trim->name }} ({{ $trim->year_from ?? '...' }} - {{ $trim->year_to ?? 'nay' }})
                                </option>
                            @endforeach
                        </select>
                        @error('form.trim_id') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">
                            Tình trạng xe <span class="text-danger">*</span>
                        </label>
                        <div class="btn-group w-100" role="group">
                            <button
                                type="button"
                                wire:click="setCondition('new')"
                                class="btn {{ ($form['condition'] ?? 'new') === 'new' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}"
                                style="border-radius: 8px 0 0 8px; padding: 8px 16px; font-size: 13px;"
                            >
                                <i class="fa fa-certificate me-1"></i> Xe mới 100%
                            </button>
                            <button
                                type="button"
                                wire:click="setCondition('used')"
                                class="btn {{ ($form['condition'] ?? '') === 'used' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}"
                                style="padding: 8px 16px; font-size: 13px;"
                            >
                                <i class="fa fa-history me-1"></i> Đã qua sử dụng
                            </button>
                            <button
                                type="button"
                                wire:click="setCondition('cpo')"
                                class="btn {{ ($form['condition'] ?? '') === 'cpo' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}"
                                style="border-radius: 0 8px 8px 0; padding: 8px 16px; font-size: 13px;"
                            >
                                <i class="fa fa-shield me-1"></i> Chính hãng CPO
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">
                            Mã quản lý kho (Stock code) <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="form.stock_code"
                            class="form-control"
                            style="border-radius: 8px; height: 42px;"
                            placeholder="VD: STK-2026-001"
                        >
                        @error('form.stock_code') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">
                            Số khung VIN
                        </label>
                        <input
                            type="text"
                            wire:model="form.vin"
                            class="form-control"
                            style="border-radius: 8px; height: 42px;"
                            placeholder="VD: 1HGBH41JXMN109186"
                        >
                        @error('form.vin') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">
                            Năm sản xuất <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            min="1900"
                            max="{{ (int) date('Y') + 1 }}"
                            wire:model="form.year"
                            class="form-control"
                            style="border-radius: 8px; height: 42px;"
                        >
                        @error('form.year') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">
                            Số km đã đi (ODO) @if(($form['condition'] ?? '') !== 'new') <span class="text-danger">*</span> @endif
                        </label>
                        <input
                            type="number"
                            min="0"
                            wire:model="form.mileage"
                            class="form-control"
                            style="border-radius: 8px; height: 42px;"
                            placeholder="{{ ($form['condition'] ?? '') === 'new' ? 'Xe mới: 0 km' : 'VD: 15000' }}"
                        >
                        @error('form.mileage') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Thẻ 2: Thông số kỹ thuật & Vận hành --}}
            <div class="c1-panel mb-4" style="padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
                        <i class="fa fa-cogs text-primary me-2"></i> 2. Thông số kỹ thuật & Vận hành
                    </h4>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Kiểu dáng thân xe</label>
                        <select wire:model="form.body_type_id" class="form-control" style="border-radius: 8px; height: 42px;">
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($bodyTypes as $bt)
                                <option value="{{ $bt->id }}">{{ $bt->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Loại nhiên liệu</label>
                        <select wire:model="form.fuel_type_id" class="form-control" style="border-radius: 8px; height: 42px;">
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($fuelTypes as $ft)
                                <option value="{{ $ft->id }}">{{ $ft->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Hộp số</label>
                        <select wire:model="form.transmission_id" class="form-control" style="border-radius: 8px; height: 42px;">
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($transmissions as $tr)
                                <option value="{{ $tr->id }}">{{ $tr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Hệ dẫn động</label>
                        <select wire:model="form.drivetrain_id" class="form-control" style="border-radius: 8px; height: 42px;">
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($drivetrains as $dt)
                                <option value="{{ $dt->id }}">{{ $dt->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Màu ngoại thất</label>
                        <select wire:model="form.exterior_color_id" class="form-control" style="border-radius: 8px; height: 42px;">
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($exteriorColors as $ec)
                                <option value="{{ $ec->id }}">{{ $ec->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Màu nội thất</label>
                        <select wire:model="form.interior_color_id" class="form-control" style="border-radius: 8px; height: 42px;">
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($interiorColors as $ic)
                                <option value="{{ $ic->id }}">{{ $ic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Thẻ 3: Hình ảnh xe (Media Gallery) --}}
            <div class="c1-panel mb-4" style="padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <div>
                        <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
                            <i class="fa fa-picture-o text-primary me-2"></i> 4. Hình ảnh xe & Media Gallery ({{ count($media) }} mục)
                        </h4>
                        <span class="c1-cell-sub">Tải lên hình ảnh ngoại thất, nội thất và chi tiết xe.</span>
                    </div>
                </div>

                {{-- Upload Box --}}
                <div style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 24px; text-align: center; background: #f8fafc; margin-bottom: 20px;">
                    <i class="fa fa-cloud-upload fa-3x text-muted mb-2"></i>
                    <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">Kéo thả hoặc chọn ảnh từ máy tính</div>
                    <div class="text-muted" style="font-size: 12px; margin-bottom: 12px;">Hỗ trợ JPG, PNG, WEBP (tối đa 10MB mỗi ảnh)</div>
                    <label class="btn btn-primary" style="font-size: 13px; cursor: pointer; border-radius: 6px;">
                        <i class="fa fa-plus me-1"></i> Chọn tệp ảnh tải lên
                        <input type="file" wire:model="uploads" multiple accept="image/*" style="display: none;">
                    </label>

                    <div wire:loading wire:target="uploads" class="mt-2 text-primary" style="font-size: 13px;">
                        <i class="fa fa-spinner fa-spin me-1"></i> Đang tải ảnh lên...
                    </div>
                </div>

                {{-- Add Image by URL --}}
                <div style="display: flex; gap: 8px; margin-bottom: 20px;">
                    <input
                        type="url"
                        wire:model="newMediaUrl"
                        placeholder="Hoặc nhập đường dẫn ảnh trực tiếp (https://...)"
                        class="form-control"
                        style="height: 38px; border-radius: 6px; font-size: 13px;"
                    >
                    <button
                        type="button"
                        wire:click="addMediaUrl"
                        class="btn btn-outline-secondary"
                        style="height: 38px; font-size: 13px; white-space: nowrap; border-radius: 6px;"
                    >
                        <i class="fa fa-link me-1"></i> Thêm link
                    </button>
                </div>

                {{-- Gallery Grid --}}
                <div class="row g-3">
                    @forelse ($media as $index => $item)
                        <div class="col-sm-6 col-md-4" wire:key="media-item-{{ $index }}">
                            <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #fff; position: relative;">
                                <div style="height: 140px; background: #f1f5f9; position: relative;">
                                    <img
                                        src="{{ asset($item['path_or_url']) }}"
                                        alt="{{ $item['caption'] ?? 'Ảnh xe' }}"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                    >
                                    @if ($item['is_cover'] ?? false)
                                        <span class="badge bg-success" style="position: absolute; top: 8px; left: 8px; font-size: 11px; padding: 4px 8px;">
                                            <i class="fa fa-star me-1"></i> Ảnh bìa
                                        </span>
                                    @endif
                                </div>

                                <div style="padding: 10px; display: flex; justify-content: space-between; align-items: center; background: #fafafa; border-top: 1px solid #f1f5f9;">
                                    <div style="display: flex; gap: 4px;">
                                        @if (!($item['is_cover'] ?? false))
                                            <button
                                                type="button"
                                                wire:click="setCover({{ $index }})"
                                                class="btn btn-xs btn-outline-primary"
                                                title="Đặt làm ảnh bìa chính"
                                                style="font-size: 11px; padding: 2px 6px;"
                                            >
                                                Đặt bìa
                                            </button>
                                        @endif
                                        <button
                                            type="button"
                                            wire:click="moveMediaUp({{ $index }})"
                                            class="btn btn-xs btn-outline-secondary"
                                            title="Chuyển lên trước"
                                            style="font-size: 11px; padding: 2px 6px;"
                                            @disabled($index === 0)
                                        >
                                            <i class="fa fa-arrow-left"></i>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="moveMediaDown({{ $index }})"
                                            class="btn btn-xs btn-outline-secondary"
                                            title="Chuyển xuống sau"
                                            style="font-size: 11px; padding: 2px 6px;"
                                            @disabled($index === count($media) - 1)
                                        >
                                            <i class="fa fa-arrow-right"></i>
                                        </button>
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="removeMedia({{ $index }})"
                                        class="btn btn-xs btn-outline-danger"
                                        title="Xóa ảnh này"
                                        style="font-size: 11px; padding: 2px 6px;"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-4 text-muted" style="font-size: 13px;">
                                Chưa có hình ảnh nào được tải lên cho xe này.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Thẻ 4: Ghi chú nội bộ --}}
            <div class="c1-panel mb-4" style="padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 16px;">
                    <i class="fa fa-file-text text-primary me-2"></i> Ghi chú nội bộ Showroom
                </h4>
                <textarea
                    wire:model="form.notes_internal"
                    rows="3"
                    class="form-control"
                    style="border-radius: 8px; font-size: 13px;"
                    placeholder="Ghi chú về nguồn gốc xe, tình trạng bảo dưỡng, lịch sử cọc..."
                ></textarea>
            </div>
        </div>

        {{-- Right Column (32% Sidebar) --}}
        <div class="col-lg-4">
            {{-- Box 1: Xem trước hiển thị (Live Preview) --}}
            @php
                $selectedTrim = $trims->firstWhere('id', (int) ($form['trim_id'] ?? 0));
                $coverItem = collect($media)->firstWhere('is_cover', true) ?? ($media[0] ?? null);
            @endphp
            <div class="c1-panel mb-4" style="padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="c1-form-card-title mb-3" style="font-size: 15px; font-weight: 700; color: #0f172a;">
                    <span><i class="fa fa-eye text-primary me-2"></i>Xem trước hiển thị (Live Preview)</span>
                </div>

                <div style="border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #f8fafc;">
                    <div style="height: 150px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @if ($coverItem && !empty($coverItem['path_or_url']))
                            <img src="{{ asset($coverItem['path_or_url']) }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fa fa-car fa-3x text-muted" style="opacity: 0.4;"></i>
                        @endif
                    </div>
                    <div style="padding: 14px;">
                        <span class="badge bg-primary" style="font-size: 11px; margin-bottom: 6px;">
                            {{ ($form['condition'] ?? 'new') === 'new' ? 'Xe mới 100%' : (($form['condition'] ?? '') === 'cpo' ? 'Chính hãng CPO' : 'Đã qua sử dụng') }}
                        </span>
                        <div style="font-weight: 700; font-size: 15px; color: #1e293b; margin-bottom: 4px;">
                            {{ $selectedTrim ? $selectedTrim->model?->make?->name . ' ' . $selectedTrim->model?->name . ' ' . $selectedTrim->name : 'Chưa chọn phiên bản xe' }}
                        </div>
                        <div style="font-size: 12px; color: var(--c1-text-muted); margin-bottom: 8px;">
                            Năm {{ $form['year'] ?? date('Y') }} • Mã: {{ $form['stock_code'] ?? 'STK-...' }}
                        </div>
                        <div style="font-size: 16px; font-weight: 800; color: #0f172a;">
                            {{ !empty($form['price']) ? number_format((float) $form['price'], 0, ',', '.') . ' VNĐ' : 'Liên hệ' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Box 2: 3. Định giá & Thiết lập giá bán --}}
            <div class="c1-panel mb-4" style="padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h5 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 14px;">
                    <i class="fa fa-tag text-primary me-2"></i> 3. Định giá & Thiết lập giá bán
                </h5>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Giá niêm yết (VNĐ)</label>
                    <input
                        type="number"
                        min="0"
                        step="1000000"
                        wire:model="form.price"
                        placeholder="VD: 750000000"
                        class="form-control"
                        style="border-radius: 8px; height: 42px; font-weight: 600; font-size: 15px;"
                    >
                    @if (!empty($form['price']))
                        <div class="text-muted" style="font-size: 12px; margin-top: 4px;">
                            Bằng chữ: <strong>{{ number_format((float) $form['price'], 0, ',', '.') }} VNĐ</strong>
                        </div>
                    @endif
                    @error('form.price') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Box 3: Trạng thái xe --}}
            <div class="c1-panel mb-4" style="padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h5 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 14px;">
                    <i class="fa fa-flag text-primary me-2"></i> Trạng thái kho
                </h5>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Trạng thái hiện tại</label>
                    <select wire:model="form.status" class="form-control" style="border-radius: 8px; height: 42px;">
                        <option value="available">Sẵn sàng bán (Available)</option>
                        <option value="draft">Bản nháp (Draft)</option>
                        <option value="on_hold">Đang giữ cọc (On Hold)</option>
                        <option value="sold">Đã giao xe (Sold)</option>
                        <option value="archived">Lưu kho (Archived)</option>
                    </select>
                    @error('form.status') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                </div>

                <div style="font-size: 12px; color: var(--c1-text-muted); line-height: 1.5; background: #f8fafc; padding: 12px; border-radius: 6px;">
                    @if (($form['status'] ?? '') === 'available')
                        <span class="text-success font-weight-bold">• Sẵn sàng bán:</span> Xe sẽ hiển thị công khai trên website.
                    @elseif (($form['status'] ?? '') === 'draft')
                        <span class="text-secondary font-weight-bold">• Bản nháp:</span> Xe chỉ hiển thị trong nội bộ admin.
                    @elseif (($form['status'] ?? '') === 'on_hold')
                        <span class="text-warning font-weight-bold">• Đang giữ cọc:</span> Tạm thời khóa giao dịch cho khách đặt trước.
                    @elseif (($form['status'] ?? '') === 'sold')
                        <span class="text-danger font-weight-bold">• Đã giao xe:</span> Đã chốt bán và ký hợp đồng.
                    @else
                        <span class="text-muted font-weight-bold">• Lưu kho:</span> Xe ngừng đăng bán hoặc chuyển kho khác.
                    @endif
                </div>
            </div>

            {{-- Box 4: Thao tác chính --}}
            <div class="c1-panel" style="padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h5 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 14px;">
                    <i class="fa fa-save text-primary me-2"></i> Lưu dữ liệu
                </h5>

                <div class="d-grid gap-2">
                    <button
                        type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="c1-btn c1-btn-primary w-100"
                        style="height: 44px; font-weight: 600;"
                    >
                        <span wire:loading.remove><i class="fa fa-save me-1"></i> {{ $carUnitId !== null ? 'Cập nhật xe' : 'Lưu thông tin xe' }}</span>
                        <span wire:loading><i class="fa fa-spinner fa-spin me-1"></i> Đang lưu...</span>
                    </button>

                    <button
                        type="button"
                        wire:click="saveWithStatus('draft')"
                        wire:loading.attr="disabled"
                        class="c1-btn c1-btn-secondary w-100"
                        style="height: 40px;"
                    >
                        <i class="fa fa-file-o me-1"></i> Lưu bản nháp (Draft)
                    </button>

                    <a href="{{ route('admin.inventory.index') }}" wire:navigate class="c1-btn c1-btn-ghost w-100 text-center" style="height: 38px;">
                        Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
