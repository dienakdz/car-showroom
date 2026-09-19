<div class="c1-dash-wrapper">
    <div class="c1-page-header">
        <div>
            <h1 class="c1-page-title">Cài đặt hệ thống &amp; Showroom</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                Quản lý thông tin showroom, nhận diện thương hiệu và các chính sách vận hành.
            </div>
        </div>
    </div>

    <form wire:submit="save" id="settingsForm">
        <div class="c1-settings-layout">
            <!-- Cột chính (Nội dung biểu mẫu cài đặt) -->
            <div class="c1-settings-main">
                <!-- Card 1: Thông tin Showroom & Trụ sở -->
                <div class="c1-panel" style="padding: 22px 24px;">
                    <div style="padding-bottom: 14px; margin-bottom: 18px; border-bottom: 1px solid var(--c1-border-card);">
                        <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--c1-text-heading);">
                            1. Thông tin Showroom &amp; Địa chỉ
                        </h3>
                        <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--c1-text-muted);">
                            Thông tin pháp nhân và thông tin liên hệ chính của showroom được hiển thị trên website.
                        </p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="c1-field-label">
                                Tên Showroom / Đại lý <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model="form.showroom_name"
                                placeholder="Nhập tên showroom hoặc tên đại lý"
                                required
                            >
                            @error('form.showroom_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="c1-field-label">
                                Số điện thoại bàn / Hotline đại lý <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model="form.showroom_phone"
                                placeholder="Ví dụ: 028.3888.9999 hoặc 0900000002"
                                required
                            >
                            @error('form.showroom_phone') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="c1-field-label">Email liên hệ showroom</label>
                            <input
                                type="email"
                                class="c1-field-input"
                                wire:model="form.showroom_email"
                                placeholder="contact@showroom.test"
                            >
                            @error('form.showroom_email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="c1-field-label">Địa chỉ trụ sở showroom</label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model="form.showroom_address"
                                placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành"
                            >
                            @error('form.showroom_address') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="c1-field-label">Giới thiệu ngắn về showroom</label>
                        <textarea
                            rows="3"
                            class="c1-field-textarea"
                            wire:model="form.showroom_description"
                            placeholder="Mô tả tóm tắt thế mạnh, cam kết chất lượng xe của showroom..."
                        ></textarea>
                        @error('form.showroom_description') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Card 2: Nhận diện Thương hiệu & Tiền tệ -->
                <div class="c1-panel" style="padding: 22px 24px;">
                    <div style="padding-bottom: 14px; margin-bottom: 18px; border-bottom: 1px solid var(--c1-border-card);">
                        <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--c1-text-heading);">
                            2. Nhận diện Thương hiệu &amp; Tiền tệ
                        </h3>
                        <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--c1-text-muted);">
                            Cấu hình tên thương hiệu tiêu đề, đơn vị tiền tệ giao dịch và hotline tư vấn bán hàng.
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="c1-field-label">
                                Tên thương hiệu hiển thị <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model="form.brand_name"
                                placeholder="Ví dụ: Minh Dien Auto Showroom"
                                required
                            >
                            @error('form.brand_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="c1-field-label">
                                Đơn vị tiền tệ <span class="text-danger">*</span>
                            </label>
                            <select wire:model="form.default_currency" class="c1-field-input c1-select" style="cursor: pointer;" required>
                                @foreach ($currencyOptions as $code => $label)
                                    <option value="{{ $code }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('form.default_currency') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="c1-field-label">Hotline bán hàng (Tư vấn 24/7)</label>
                            <input
                                type="text"
                                class="c1-field-input"
                                wire:model="form.sales_hotline"
                                placeholder="Ví dụ: 0900000002"
                            >
                            @error('form.sales_hotline') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 3: Chính sách Vận hành & Thông báo -->
                <div class="c1-panel" style="padding: 22px 24px;">
                    <div style="padding-bottom: 14px; margin-bottom: 18px; border-bottom: 1px solid var(--c1-border-card);">
                        <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--c1-text-heading);">
                            3. Chính sách Vận hành &amp; Thông báo
                        </h3>
                        <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--c1-text-muted);">
                            Bật tắt các tính năng hiển thị và thông báo tự động cho hệ thống showroom.
                        </p>
                    </div>

                    <div class="c1-toggle-list">
                        <div class="c1-toggle-row">
                            <div class="c1-toggle-info">
                                <div class="c1-toggle-title">
                                    <i class="fa fa-clock-o text-primary" aria-hidden="true"></i>
                                    <span>Hiển thị xe đang giữ chỗ (On-Hold)</span>
                                </div>
                                <p class="c1-toggle-desc">
                                    Khi kích hoạt, các xe kho đang trong trạng thái cọc hoặc giữ chỗ vẫn hiển thị công khai trên website kèm nhãn "Đang giữ chỗ" để khách hàng theo dõi.
                                </p>
                            </div>
                            <label class="c1-toggle-switch" title="Gạt để bật/tắt">
                                <input type="checkbox" wire:model="form.show_on_hold_public">
                                <span class="c1-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="c1-toggle-row">
                            <div class="c1-toggle-info">
                                <div class="c1-toggle-title">
                                    <i class="fa fa-envelope-o text-primary" aria-hidden="true"></i>
                                    <span>Nhận email thông báo khi có Lead mới</span>
                                </div>
                                <p class="c1-toggle-desc">
                                    Tự động gửi email thông báo tới hòm thư quản trị viên showroom ngay khi có khách hàng mới đăng ký lái thử, yêu cầu tư vấn hoặc để lại số điện thoại.
                                </p>
                            </div>
                            <label class="c1-toggle-switch" title="Gạt để bật/tắt">
                                <input type="checkbox" wire:model="form.email_lead_notifications">
                                <span class="c1-toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Nút Lưu cài đặt duy nhất ở chân form -->
                <div class="d-flex align-items-center justify-content-end gap-3 pt-2">
                    <button
                        type="submit"
                        class="c1-btn c1-btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        style="height: 42px; padding: 0 28px; font-size: 14px; font-weight: 600;"
                    >
                        <i class="fa fa-save" wire:loading.remove wire:target="save"></i>
                        <i class="fa fa-spinner fa-spin" wire:loading wire:target="save" style="display: none;"></i>
                        <span wire:loading.remove wire:target="save">Lưu cài đặt</span>
                        <span wire:loading wire:target="save" style="display: none;">Đang lưu...</span>
                    </button>
                </div>
            </div>

            <!-- Cột phụ (Showroom Snapshot & Trạng thái hệ thống) -->
            <div class="c1-settings-sidebar">
                <div class="c1-snapshot-card" style="box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05); border: 1px solid var(--c1-border-card); border-radius: var(--c1-radius-card); padding: 22px;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="m-0 fw-bold" style="font-size: 15px; color: var(--c1-text-heading);">
                            Tổng quan Showroom
                        </h4>
                    </div>

                    <div class="mb-3">
                        <span class="c1-snapshot-status-pill">
                            <span class="c1-snapshot-status-dot"></span>
                            Hệ thống hoạt động bình thường
                        </span>
                    </div>

                    <div class="mb-4" style="font-size: 13px; display: flex; flex-direction: column; gap: 10px; border-top: 1px dashed var(--c1-border-card); padding-top: 14px;">
                        <div>
                            <div class="text-muted small" style="margin-bottom: 2px;">Tên đại lý:</div>
                            <div class="fw-bold" style="color: var(--c1-text-heading); font-size: 14px;">
                                {{ $form['showroom_name'] ?: ($form['brand_name'] ?: 'Minh Dien Auto Showroom') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-muted small" style="margin-bottom: 2px;">Tiền tệ niêm yết:</div>
                            <span class="c1-pill c1-pill-blue" style="font-weight: 700;">
                                {{ $form['default_currency'] ?: 'VND' }}
                            </span>
                        </div>

                        @if (!empty($form['sales_hotline']) || !empty($form['showroom_phone']))
                            <div>
                                <div class="text-muted small" style="margin-bottom: 2px;">Hotline tư vấn:</div>
                                <div class="fw-semibold" style="color: var(--c1-text-heading);">
                                    <i class="fa fa-phone text-primary me-1" aria-hidden="true"></i>
                                    {{ $form['sales_hotline'] ?: $form['showroom_phone'] }}
                                </div>
                            </div>
                        @endif

                        @if (!empty($form['showroom_address']))
                            <div>
                                <div class="text-muted small" style="margin-bottom: 2px;">Trụ sở:</div>
                                <div class="text-muted small" style="line-height: 1.4;">
                                    <i class="fa fa-map-marker text-danger me-1" aria-hidden="true"></i>
                                    {{ $form['showroom_address'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
