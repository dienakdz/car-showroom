<div>
    @php
        $isOnHold = $carUnit->status === 'on_hold';
        $isSold = $carUnit->status === 'sold';
        $isWorkflowLocked = $isOnHold || $isSold;
    @endphp

    {{-- Page Header --}}
    <div class="c1-page-header mb-4" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 class="c1-page-title">{{ $carUnitId !== null ? 'Cập nhật xe [' . ($form['stock_code'] ?? '') . ']' : 'Thêm xe mới vào kho' }}</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                @if ($isSold)
                    Xe đã bán: thông tin Inventory được khóa và chỉ dùng để xem.
                @elseif ($isOnHold)
                    Xe đang giữ cọc: chỉ có thể cập nhật hình ảnh và ghi chú nội bộ.
                @else
                    {{ $carUnitId !== null ? 'Chỉnh sửa thông số, hình ảnh và trạng thái kho xe.' : 'Khai báo thông tin định danh, thông số kỹ thuật, định giá và hình ảnh xe.' }}
                @endif
            </div>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="{{ route('admin.inventory.index') }}" wire:navigate.hover class="c1-action-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                <span>Về kho xe</span>
            </a>
            @if ($isSold)
                <span class="c1-pill c1-pill-gray"><i class="fa fa-lock me-1"></i> Chỉ xem</span>
            @elseif ($isOnHold)
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="c1-btn c1-btn-primary">
                    <i class="fa fa-check me-1"></i> Lưu ảnh & ghi chú
                </button>
            @else
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="c1-btn c1-btn-primary">
                    <span wire:loading.remove><i class="fa fa-save me-1"></i> {{ $carUnitId !== null ? 'Lưu thay đổi' : 'Lưu thông tin xe' }}</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin me-1"></i> Đang lưu...</span>
                </button>
            @endif
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
                        <select wire:model.live="form.trim_id" class="form-control" style="border-radius: 8px; height: 42px;" @disabled($isWorkflowLocked)>
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
                                @disabled($isWorkflowLocked)
                            >
                                <i class="fa fa-certificate me-1"></i> Xe mới 100%
                            </button>
                            <button
                                type="button"
                                wire:click="setCondition('used')"
                                class="btn {{ ($form['condition'] ?? '') === 'used' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}"
                                style="padding: 8px 16px; font-size: 13px;"
                                @disabled($isWorkflowLocked)
                            >
                                <i class="fa fa-history me-1"></i> Đã qua sử dụng
                            </button>
                            <button
                                type="button"
                                wire:click="setCondition('cpo')"
                                class="btn {{ ($form['condition'] ?? '') === 'cpo' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}"
                                style="border-radius: 0 8px 8px 0; padding: 8px 16px; font-size: 13px;"
                                @disabled($isWorkflowLocked)
                            >
                                <i class="fa fa-shield me-1"></i> Chính hãng CPO
                            </button>
                        </div>
                        @error('form.condition') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
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
                            @disabled($isWorkflowLocked)
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
                            @disabled($isWorkflowLocked)
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
                            @disabled($isWorkflowLocked)
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
                            @disabled($isWorkflowLocked)
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
                        <select wire:model="form.body_type_id" class="form-control" style="border-radius: 8px; height: 42px;" @disabled($isWorkflowLocked)>
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($bodyTypes as $bt)
                                <option value="{{ $bt->id }}">{{ $bt->name }}</option>
                            @endforeach
                        </select>
                        @error('form.body_type_id') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Loại nhiên liệu</label>
                        <select wire:model="form.fuel_type_id" class="form-control" style="border-radius: 8px; height: 42px;" @disabled($isWorkflowLocked)>
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($fuelTypes as $ft)
                                <option value="{{ $ft->id }}">{{ $ft->name }}</option>
                            @endforeach
                        </select>
                        @error('form.fuel_type_id') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Hộp số</label>
                        <select wire:model="form.transmission_id" class="form-control" style="border-radius: 8px; height: 42px;" @disabled($isWorkflowLocked)>
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($transmissions as $tr)
                                <option value="{{ $tr->id }}">{{ $tr->name }}</option>
                            @endforeach
                        </select>
                        @error('form.transmission_id') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Hệ dẫn động</label>
                        <select wire:model="form.drivetrain_id" class="form-control" style="border-radius: 8px; height: 42px;" @disabled($isWorkflowLocked)>
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($drivetrains as $dt)
                                <option value="{{ $dt->id }}">{{ $dt->name }}</option>
                            @endforeach
                        </select>
                        @error('form.drivetrain_id') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Màu ngoại thất</label>
                        <select wire:model="form.exterior_color_id" class="form-control" style="border-radius: 8px; height: 42px;" @disabled($isWorkflowLocked)>
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($exteriorColors as $ec)
                                <option value="{{ $ec->id }}">{{ $ec->name }}</option>
                            @endforeach
                        </select>
                        @error('form.exterior_color_id') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; font-size: 13px;">Màu nội thất</label>
                        <select wire:model="form.interior_color_id" class="form-control" style="border-radius: 8px; height: 42px;" @disabled($isWorkflowLocked)>
                            <option value="">-- Chưa chọn --</option>
                            @foreach ($interiorColors as $ic)
                                <option value="{{ $ic->id }}">{{ $ic->name }}</option>
                            @endforeach
                        </select>
                        @error('form.interior_color_id') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Thẻ 3: Thư viện hình ảnh xe --}}
            <div class="c1-panel mb-4" style="padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <div>
                        <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
                            <i class="fa fa-picture-o text-primary me-2"></i> 4. Thư viện hình ảnh xe ({{ count($media) }} ảnh)
                        </h4>
                        <span class="c1-cell-sub">Ảnh được giữ tạm để xem trước và chỉ lưu chính thức khi bạn lưu xe.</span>
                    </div>
                </div>

                {{-- Upload Box --}}
                <label style="position: relative; display: block; border: 2px dashed #cbd5e1; border-radius: 10px; padding: 36px 24px; text-align: center; background: #f8fafc; margin-bottom: 20px; cursor: {{ $isSold ? 'not-allowed' : 'pointer' }}; overflow: hidden;">
                    <input
                        type="file"
                        wire:model="uploads"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: inherit; z-index: 2;"
                        @disabled($isSold)
                    >

                    <div style="pointer-events: none;">
                        <i class="fa fa-cloud-upload fa-3x text-muted mb-2"></i>
                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">Kéo thả hoặc chọn ảnh từ máy tính</div>
                        <div class="text-muted" style="font-size: 12px; margin-bottom: 12px;">Hỗ trợ JPG, PNG, WEBP (tối đa 10MB mỗi ảnh)</div>
                        <span class="btn btn-primary" style="font-size: 13px; border-radius: 6px;">
                            <i class="fa fa-plus me-1"></i> Chọn tệp ảnh tải lên
                        </span>
                    </div>

                    <div wire:loading wire:target="uploads" class="mt-2 text-primary" style="font-size: 13px;">
                        <i class="fa fa-spinner fa-spin me-1"></i> Đang tải ảnh lên...
                    </div>
                    @error('uploads.*') <div class="text-danger mt-2" style="font-size: 12px;">{{ $message }}</div> @enderror
                </label>

                {{-- Image Grid --}}
                <div class="row g-3">
                    @forelse ($media as $index => $item)
                        @php
                            $uploadKey = (string) ($item['upload_key'] ?? '');
                            $pendingUpload = $uploadKey !== '' ? ($pendingUploads[$uploadKey] ?? null) : null;
                            $mediaKey = $item['id'] ?? ($uploadKey !== '' ? $uploadKey : $index);
                            $previewUrl = $pendingUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                                ? $pendingUpload->temporaryUrl()
                                : asset($item['path_or_url']);
                        @endphp
                        <div class="col-sm-6 col-md-4" wire:key="media-item-{{ $mediaKey }}">
                            <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #fff; position: relative;">
                                <div style="height: 140px; background: #f1f5f9; position: relative;">
                                    <img
                                        src="{{ $previewUrl }}"
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
                                    <div class="c1-actions-cell" style="gap: 4px;">
                                        @if (!$isSold && !($item['is_cover'] ?? false))
                                            <button
                                                type="button"
                                                wire:click="setCover({{ $index }})"
                                                class="c1-action-btn c1-action-btn-primary"
                                                title="Đặt làm ảnh bìa chính"
                                                style="height: 28px; padding: 0 8px; font-size: 11.5px;"
                                            >
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                                <span>Đặt bìa</span>
                                            </button>
                                        @endif
                                        <button
                                            type="button"
                                            wire:click="moveMediaUp({{ $index }})"
                                            class="c1-action-btn c1-action-btn-icon"
                                            title="Chuyển lên trước"
                                            style="width: 28px; height: 28px;"
                                            @disabled($isSold || $index === 0)
                                        >
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="moveMediaDown({{ $index }})"
                                            class="c1-action-btn c1-action-btn-icon"
                                            title="Chuyển xuống sau"
                                            style="width: 28px; height: 28px;"
                                            @disabled($isSold || $index === count($media) - 1)
                                        >
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                        </button>
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="removeMedia({{ $index }})"
                                        class="c1-action-btn c1-action-btn-icon c1-action-btn-danger"
                                        title="Xóa ảnh này"
                                        style="width: 28px; height: 28px;"
                                        @disabled($isSold)
                                    >
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
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
                    @disabled($isSold)
                ></textarea>
                @error('form.notes_internal') <span class="text-danger" style="font-size: 12px;">{{ $message }}</span> @enderror
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
                <div class="c1-form-card-title mb-3" style="font-size: 15px; font-weight: 700; color: var(--c1-text-heading);">
                    <span><i class="fa fa-eye text-primary me-2"></i>Xem trước hiển thị (Live Preview)</span>
                </div>

                <div style="border: 1px solid var(--c1-border-card); border-radius: 10px; overflow: hidden; background: var(--c1-bg-page);">
                    <div style="height: 150px; background: var(--c1-border-card); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @if ($coverItem)
                            @php
                                $coverUploadKey = (string) ($coverItem['upload_key'] ?? '');
                                $coverPendingUpload = $coverUploadKey !== '' ? ($pendingUploads[$coverUploadKey] ?? null) : null;
                                $coverPreviewUrl = $coverPendingUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                                    ? $coverPendingUpload->temporaryUrl()
                                    : asset($coverItem['path_or_url']);
                            @endphp
                            <img src="{{ $coverPreviewUrl }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fa fa-car fa-3x text-muted" style="opacity: 0.4;"></i>
                        @endif
                    </div>
                    <div style="padding: 14px;">
                        <span class="badge bg-primary" style="font-size: 11px; margin-bottom: 6px;">
                            {{ ($form['condition'] ?? 'new') === 'new' ? 'Xe mới 100%' : (($form['condition'] ?? '') === 'cpo' ? 'Chính hãng CPO' : 'Đã qua sử dụng') }}
                        </span>
                        <div style="font-weight: 700; font-size: 15px; color: var(--c1-text-heading); margin-bottom: 4px;">
                            {{ $selectedTrim ? $selectedTrim->model?->make?->name . ' ' . $selectedTrim->model?->name . ' ' . $selectedTrim->name : 'Chưa chọn phiên bản xe' }}
                        </div>
                        <div style="font-size: 12px; color: var(--c1-text-muted); margin-bottom: 8px;">
                            Năm {{ $form['year'] ?? date('Y') }} • Mã: {{ $form['stock_code'] ?? 'STK-...' }}
                        </div>
                        <div style="font-size: 16px; font-weight: 800; color: var(--c1-primary);">
                            {{ !empty($form['price']) ? number_format((float) $form['price'], 0, ',', '.') . ' ' . ($form['currency'] ?? 'VND') : 'Liên hệ' }}
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
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Giá niêm yết ({{ $form['currency'] ?? 'VND' }})</label>
                    <input
                        type="number"
                        min="0"
                        step="1000000"
                        wire:model="form.price"
                        placeholder="VD: 750000000"
                        class="form-control"
                        style="border-radius: 8px; height: 42px; font-weight: 600; font-size: 15px;"
                        @disabled($isWorkflowLocked)
                    >
                    @if (!empty($form['price']))
                        <div class="text-muted" style="font-size: 12px; margin-top: 4px;">
                            Bằng chữ: <strong>{{ number_format((float) $form['price'], 0, ',', '.') }} {{ $form['currency'] ?? 'VND' }}</strong>
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
                    <select wire:model.live="form.status" class="form-control" style="border-radius: 8px; height: 42px;" @disabled($isWorkflowLocked)>
                        @if ($carUnit->status === 'on_hold')
                            <option value="on_hold">Đang giữ cọc (On Hold)</option>
                        @elseif ($carUnit->status === 'sold')
                            <option value="sold">Đã giao xe (Sold)</option>
                        @else
                            <option value="available">Sẵn sàng bán (Available)</option>
                            <option value="draft">Bản nháp (Draft)</option>
                            @if ($carUnitId !== null)
                                <option value="archived">Lưu kho (Archived)</option>
                            @endif
                        @endif
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
                    @if ($isSold)
                        <div class="text-muted text-center small py-2">
                            <i class="fa fa-lock me-1"></i> Xe đã chốt giao dịch bán. Thông tin chỉ dùng để xem.
                        </div>
                    @else
                        <button
                            type="button"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            class="c1-btn c1-btn-primary w-100"
                            style="height: 44px; font-weight: 600;"
                        >
                            <span wire:loading.remove>
                                <i class="fa fa-save me-1"></i>
                                {{ $isOnHold ? 'Lưu ảnh & ghi chú' : ($carUnitId !== null ? 'Lưu thay đổi' : 'Lưu thông tin xe') }}
                            </span>
                            <span wire:loading><i class="fa fa-spinner fa-spin me-1"></i> Đang lưu...</span>
                        </button>
                    @endif

                    <a href="{{ route('admin.inventory.index') }}" wire:navigate class="c1-action-btn w-100 text-center justify-content-center" style="height: 38px;">
                        Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
