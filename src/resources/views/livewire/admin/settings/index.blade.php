<div class="c1-settings-workspace">
    <div class="c1-page-header mb-4">
        <div>
            <h1 class="c1-page-title">Settings / Cài đặt</h1>
            <p class="c1-page-subtitle">Quản lý thông tin showroom, định cấu hình thương hiệu và các chính sách vận hành.</p>
        </div>
    </div>

    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <div class="d-flex align-items-center gap-2">
                @if (($feedback['type'] ?? 'success') === 'error')
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                @else
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                @endif
                <span>{{ $feedback['message'] }}</span>
            </div>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    <form wire:submit="save" id="settingsForm">
        <div class="c1-settings-layout">
            <!-- Left Main Column (65%) -->
            <div class="c1-settings-main">
                <!-- Card 1: Thông tin Showroom -->
                <div class="c1-catalog-card">
                    <div class="c1-catalog-card-header mb-3">
                        <h3 class="c1-catalog-card-title">Thông tin Showroom</h3>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="c1-field-label">
                                Showroom Name <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model.live.debounce.300ms="form.showroom_name"
                                placeholder="Nhập tên showroom"
                                required
                            >
                            @error('form.showroom_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="c1-field-label">
                                Phone <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model.live.debounce.300ms="form.showroom_phone"
                                placeholder="Số điện thoại"
                                required
                            >
                            @error('form.showroom_phone') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="c1-field-label">Email</label>
                            <input
                                type="email"
                                class="c1-field-input"
                                wire:model.blur="form.showroom_email"
                                placeholder="email@example.com"
                            >
                            @error('form.showroom_email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="c1-field-label">Address</label>
                        <input
                            type="text"
                            class="c1-field-input"
                            wire:model.blur="form.showroom_address"
                            placeholder="Địa chỉ trụ sở showroom"
                        >
                        @error('form.showroom_address') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label class="c1-field-label">Description</label>
                        <textarea
                            rows="4"
                            class="c1-field-textarea"
                            wire:model.blur="form.showroom_description"
                            placeholder="Mô tả giới thiệu ngắn về showroom..."
                        ></textarea>
                        @error('form.showroom_description') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Card 2: Cấu hình Thương hiệu & Tiền tệ -->
                <div class="c1-catalog-card">
                    <div class="c1-catalog-card-header mb-3">
                        <h3 class="c1-catalog-card-title">Cấu hình Thương hiệu &amp; Tiền tệ</h3>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="c1-field-label">
                                Brand Name <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model.live.debounce.300ms="form.brand_name"
                                placeholder="Tên thương hiệu"
                                required
                            >
                            @error('form.brand_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="c1-field-label">
                                Default Currency <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input
                                    type="text"
                                    maxlength="3"
                                    class="c1-field-input text-uppercase fw-bold"
                                    wire:model.live.debounce.300ms="form.default_currency"
                                    placeholder="VND"
                                    required
                                >
                            </div>
                            @error('form.default_currency') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="c1-field-label">Sales Hotline</label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model.blur="form.sales_hotline"
                                placeholder="Hotline bán hàng"
                            >
                            @error('form.sales_hotline') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 3: Chính sách Vận hành & Thông báo -->
                <div class="c1-catalog-card">
                    <div class="c1-catalog-card-header mb-3">
                        <h3 class="c1-catalog-card-title">Chính sách Vận hành &amp; Thông báo</h3>
                    </div>

                    <div class="c1-toggle-grid">
                        <div class="c1-toggle-item-inline">
                            <span class="c1-toggle-label">Hiển thị xe đang giữ chỗ (On-Hold)</span>
                            <label class="c1-toggle-switch">
                                <input type="checkbox" wire:model.live="form.show_on_hold_public">
                                <span class="c1-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="c1-toggle-item-inline">
                            <span class="c1-toggle-label">Nhận email thông báo Lead mới</span>
                            <label class="c1-toggle-switch">
                                <input type="checkbox" wire:model.live="form.email_lead_notifications">
                                <span class="c1-toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column (35% - Sticky) -->
            <div class="c1-settings-sidebar">
                <div class="c1-snapshot-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="m-0 fw-bold" style="font-size: 16px; color: var(--c1-text-heading);">Showroom Snapshot</h4>
                    </div>

                    <div class="mb-3">
                        <span class="c1-snapshot-status-pill">
                            <span class="c1-snapshot-status-dot"></span>
                            Đang hoạt động
                        </span>
                    </div>

                    <div class="mb-4">
                        <div class="text-muted small mb-1">Showroom quick info</div>
                        <div class="fw-bold" style="font-size: 15px; color: var(--c1-text-heading);">
                            {{ $form['showroom_name'] ?: ($form['brand_name'] ?: 'Minh Dien Auto Showroom') }}
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="text-muted small">Currency:</span>
                            <span class="c1-pill-badge c1-pill-blue fw-bold">
                                {{ strtoupper((string) ($form['default_currency'] ?: 'VND')) }}
                            </span>
                        </div>
                        @if (!empty($form['sales_hotline']) || !empty($form['showroom_phone']))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <span class="text-muted small">Hotline:</span>
                                <span class="small fw-semibold text-dark">
                                    {{ $form['sales_hotline'] ?: $form['showroom_phone'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="c1-btn c1-btn-primary c1-btn-block"
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        <span wire:loading.remove wire:target="save">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: inline-block; vertical-align: -2px; margin-right: 4px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Lưu cài đặt
                        </span>
                        <span wire:loading wire:target="save" style="display: none;">
                            <svg class="fa-spin" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: inline-block; vertical-align: -2px; margin-right: 4px;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg>
                            Đang lưu...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
