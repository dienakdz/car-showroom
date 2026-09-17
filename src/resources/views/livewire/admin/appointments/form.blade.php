<div>
    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span><i class="fa {{ ($feedback['type'] ?? 'success') === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle' }} me-2"></i>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1" style="font-weight: 700; color: var(--c1-text-heading);">
                {{ $appointment->exists ? 'Cập nhật lịch hẹn #' . $appointment->id : 'Đặt lịch hẹn mới xem xe / Lái thử' }}
            </h4>
            <div class="text-muted" style="font-size: 13px;">
                {{ $appointment->exists ? 'Chỉnh sửa thời gian, chuyên viên đón tiếp và trạng thái lịch hẹn.' : 'Thiết lập buổi lái thử hoặc tư vấn trực tiếp tại showroom.' }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.appointments.index') }}" wire:navigate class="c1-btn c1-btn-secondary">
                <i class="fa fa-arrow-left me-1"></i>
                <span>Về danh sách</span>
            </a>
            @if ($appointment->exists && $appointment->lead_id)
                <a href="{{ route('admin.leads.show', $appointment->lead_id) }}" wire:navigate class="c1-btn c1-btn-secondary">
                    <i class="fa fa-user-circle me-1"></i>
                    <span>Hồ sơ Lead #{{ $appointment->lead_id }}</span>
                </a>
            @endif
        </div>
    </div>

    <form wire:submit="save">
        <div class="row g-4">
            {{-- CỘT TRÁI: THÔNG TIN BUỔI HẸN & KHÁCH HÀNG (7 COLUMNS) --}}
            <div class="col-lg-7">
                {{-- Panel 1: Khách hàng & Lead --}}
                <div class="c1-panel p-4 mb-4">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--c1-border-card); flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-user-circle text-primary" style="font-size: 18px;"></i>
                            <h6 style="font-weight: 600; margin: 0; color: var(--c1-text-heading);">Thông tin khách hàng & Tiếp đón</h6>
                        </div>

                        @if (! $appointment->exists)
                            <div class="c1-view-switcher" style="padding: 2px;">
                                <button
                                    type="button"
                                    wire:click="setCustomerMode('existing_lead')"
                                    class="c1-view-switcher-btn {{ $customerMode === 'existing_lead' ? 'active' : '' }}"
                                    style="border: none; font-size: 12px; padding: 4px 10px; cursor: pointer;"
                                >
                                    <i class="fa fa-list-ul me-1"></i>Đã có Lead CRM
                                </button>
                                <button
                                    type="button"
                                    wire:click="setCustomerMode('new_lead')"
                                    class="c1-view-switcher-btn {{ $customerMode === 'new_lead' ? 'active' : '' }}"
                                    style="border: none; font-size: 12px; padding: 4px 10px; cursor: pointer;"
                                >
                                    <i class="fa fa-user-plus me-1"></i>+ Khách hàng mới
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3">
                        @if ($customerMode === 'new_lead')
                            {{-- GIAO DIỆN TẠO NHANH LEAD CHO KHÁCH HÀNG MỚI --}}
                            <div class="col-12">
                                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; font-size: 12.5px; color: #1e40af; margin-bottom: 4px;">
                                    <i class="fa fa-info-circle me-1"></i>Hệ thống sẽ <strong>tự động tạo 1 hồ sơ Lead mới</strong> trong CRM và chuyển vào giai đoạn <em>"Đặt lịch hẹn"</em> để đội Sales tiếp tục chăm sóc.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="field-customer-name" class="form-label" style="font-size: 13px; font-weight: 600;">
                                    Họ và tên khách hàng <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="field-customer-name"
                                    type="text"
                                    wire:model="form.customer_name"
                                    class="c1-input w-100 @error('form.customer_name') is-invalid @enderror"
                                    placeholder="Ví dụ: Nguyễn Văn Tuấn"
                                    required
                                >
                                @error('form.customer_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="field-customer-phone" class="form-label" style="font-size: 13px; font-weight: 600;">
                                    Số điện thoại liên hệ <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="field-customer-phone"
                                    type="text"
                                    wire:model="form.customer_phone"
                                    class="c1-input w-100 @error('form.customer_phone') is-invalid @enderror"
                                    placeholder="Ví dụ: 0988777666"
                                    required
                                >
                                @error('form.customer_phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="field-customer-email" class="form-label" style="font-size: 13px; font-weight: 600;">
                                    Email khách hàng (Tùy chọn)
                                </label>
                                <input
                                    id="field-customer-email"
                                    type="email"
                                    wire:model="form.customer_email"
                                    class="c1-input w-100 @error('form.customer_email') is-invalid @enderror"
                                    placeholder="khachhang@example.com"
                                >
                                @error('form.customer_email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        @else
                            {{-- GIAO DIỆN CHỌN TỪ LEAD ĐÃ CÓ TRONG CRM --}}
                            <div class="col-md-12">
                                <label for="field-lead-id" class="form-label" style="font-size: 13px; font-weight: 600;">
                                    <i class="fa fa-handshake-o me-1 text-primary"></i>Khách hàng từ CRM Lead (Khuyên dùng)
                                    @if (! empty($form['lead_id']))
                                        <span class="badge bg-primary-subtle text-primary ms-1" style="font-size: 11px;">✓ Đang liên kết Lead #{{ $form['lead_id'] }}</span>
                                    @endif
                                </label>
                                <select id="field-lead-id" wire:model.live="form.lead_id" class="c1-select w-100 @error('form.lead_id') is-invalid @enderror">
                                    <option value="">-- Chọn khách hàng từ Lead CRM --</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->id }}">
                                            #{{ $lead->id }} • {{ $lead->name }} ({{ $lead->phone }}){{ $lead->user ? ' • [User: ' . $lead->user->name . ']' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.lead_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                @if ($activeLead)
                                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; margin-top: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                        <div>
                                            <div style="font-weight: 600; font-size: 13.5px; color: #166534;">
                                                <i class="fa fa-check-circle me-1"></i>{{ $activeLead->name }}
                                                <span style="font-weight: 400; color: #475569; font-size: 12.5px; margin-left: 8px;">{{ $activeLead->phone }}</span>
                                            </div>
                                            <div style="font-size: 11.5px; color: #15803d; margin-top: 2px;">
                                                ✓ Tự động đồng bộ xe quan tâm và chuyên viên tư vấn từ hồ sơ Lead.
                                            </div>
                                        </div>
                                        @if ($activeLead->user)
                                            <span class="c1-badge c1-badge-blue" style="font-size: 11px;" title="Tài khoản hệ thống">
                                                <i class="fa fa-user me-1"></i>Tài khoản User #{{ $activeLead->user->id }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label for="field-user-id" class="form-label" style="font-size: 13px; font-weight: 600;">
                                    Hoặc chọn Tài khoản User (Nếu khách đăng ký qua website)
                                </label>
                                <select id="field-user-id" wire:model.live="form.user_id" class="c1-select w-100 @error('form.user_id') is-invalid @enderror">
                                    <option value="">-- Chọn tài khoản người dùng đăng ký --</option>
                                    @foreach ($customerUsers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}{{ $customer->phone ? ' • ' . $customer->phone : '' }} ({{ $customer->email }})</option>
                                    @endforeach
                                </select>
                                @error('form.user_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <div class="text-muted" style="font-size: 11.5px; margin-top: 4px;">
                                    <i class="fa fa-info-circle me-1"></i>Nếu User đã có Lead trong CRM, hệ thống sẽ tự động nhận diện và liên kết ngay khi chọn.
                                </div>
                            </div>
                        @endif


                        <div class="col-md-6">
                            <label for="field-scheduled-at" class="form-label" style="font-size: 13px; font-weight: 600;">
                                Thời gian hẹn <span class="text-danger">*</span>
                            </label>
                            <input
                                id="field-scheduled-at"
                                type="datetime-local"
                                wire:model="form.scheduled_at"
                                class="c1-input w-100 @error('form.scheduled_at') is-invalid @enderror"
                                required
                            >
                            @error('form.scheduled_at') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="field-handled-by" class="form-label" style="font-size: 13px; font-weight: 600;">Chuyên viên đón tiếp</label>
                            <select id="field-handled-by" wire:model="form.handled_by" class="c1-select w-100 @error('form.handled_by') is-invalid @enderror">
                                <option value="">Chưa phân công (Gán cho người tạo)</option>
                                @foreach ($staffUsers as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                            @error('form.handled_by') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>


                {{-- Panel 2: Ghi chú & Yêu cầu --}}
                <div class="c1-panel p-4 mb-4">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                        <i class="fa fa-commenting-o text-primary" style="font-size: 17px;"></i>
                        <h6 style="font-weight: 600; margin: 0; color: var(--c1-text-heading);">Ghi chú buổi hẹn & Nhu cầu lái thử</h6>
                    </div>

                    <div>
                        <textarea
                            id="field-note"
                            wire:model="form.note"
                            rows="4"
                            class="c1-input w-100 @error('form.note') is-invalid @enderror"
                            style="height: auto; padding: 10px 12px;"
                            placeholder="Ghi chú thêm về yêu cầu lái thử, khung giờ đặc biệt, nhu cầu kiểm tra xe hoặc tư vấn tài chính..."
                        ></textarea>
                        @error('form.note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Nút Submit --}}
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.appointments.index') }}" wire:navigate class="c1-btn c1-btn-secondary">
                        Hủy bỏ
                    </a>
                    <button type="submit" class="c1-btn c1-btn-primary" wire:loading.attr="disabled" style="min-width: 170px; justify-content: center;">
                        <span wire:loading.remove>
                            <i class="fa fa-check me-1"></i>{{ $appointment->exists ? 'Lưu thay đổi' : 'Tạo lịch hẹn' }}
                        </span>
                        <span wire:loading>
                            <i class="fa fa-spinner fa-spin me-1"></i>Đang lưu...
                        </span>
                    </button>
                </div>
            </div>

            {{-- CỘT PHẢI: NGỮ CẢNH XE LÁI THỬ & TRẠNG THÁI (5 COLUMNS) --}}
            <div class="col-lg-5">
                {{-- Panel Ngữ cảnh xe --}}
                <div class="c1-panel p-4 mb-4">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--c1-border-card);">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-car text-primary" style="font-size: 18px;"></i>
                            <h6 style="font-weight: 600; margin: 0; color: var(--c1-text-heading);">Mẫu xe lái thử & Xem trực tiếp</h6>
                        </div>
                        @if ($activeCarUnit)
                            <span class="c1-pill c1-pill-green" style="font-size: 11px;">Xe sẵn sàng</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="field-car-unit-id" class="form-label" style="font-size: 13px; font-weight: 600;">Xe cụ thể trong kho (Car Unit)</label>
                        <select id="field-car-unit-id" wire:model.live="form.car_unit_id" class="c1-select w-100 @error('form.car_unit_id') is-invalid @enderror">
                            <option value="">-- Chọn xe cụ thể trong kho --</option>
                            @foreach ($carUnits as $carUnit)
                                <option value="{{ $carUnit->id }}">
                                    [{{ $carUnit->stock_code }}] {{ $carUnit->trim?->model?->make?->name }} {{ $carUnit->trim?->model?->name }} {{ $carUnit->trim?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('form.car_unit_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="field-trim-id" class="form-label" style="font-size: 13px; font-weight: 600;">Hoặc chọn Phiên bản xe (Trim)</label>
                        <select id="field-trim-id" wire:model="form.trim_id" class="c1-select w-100 @error('form.trim_id') is-invalid @enderror">
                            <option value="">-- Chọn phiên bản xe quan tâm --</option>
                            @foreach ($trims as $trim)
                                <option value="{{ $trim->id }}">{{ $trim->model?->make?->name }} • {{ $trim->model?->name }} • {{ $trim->name }}</option>
                            @endforeach
                        </select>
                        @error('form.trim_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Dynamic Live Preview Card --}}
                    @php
                        $previewMedia = $activeCarUnit?->primaryMedia ?? $activeTrim?->carUnits?->first()?->primaryMedia;
                        $rawPath = $previewMedia?->path_or_url;
                        $previewUrl = filled($rawPath)
                            ? ((str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) ? $rawPath : asset(ltrim($rawPath, '/')))
                            : null;
                        $previewTitle = trim(collect([$activeTrim?->model?->make?->name, $activeTrim?->model?->name, $activeTrim?->name])->filter()->implode(' '));
                    @endphp

                    @if ($activeCarUnit !== null || $activeTrim !== null)
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-top: 14px;">
                            <div style="position: relative; border-radius: 8px; overflow: hidden; background: #0f172a; aspect-ratio: 16/9; border: 1px solid #e2e8f0;">
                                @if ($previewUrl)
                                    <img src="{{ $previewUrl }}" alt="{{ $previewTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 24px;">
                                        <i class="fa fa-car mb-1"></i>
                                        <span style="font-size: 11px;">Chưa có ảnh xe</span>
                                    </div>
                                @endif
                                @if ($activeCarUnit)
                                    <div style="position: absolute; top: 10px; right: 10px;">
                                        <span class="c1-pill c1-pill-green" style="box-shadow: 0 2px 6px rgba(0,0,0,0.25); font-weight: 700;">
                                            #{{ $activeCarUnit->stock_code }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div style="margin-top: 12px;">
                                <div style="font-weight: 700; font-size: 15px; color: var(--c1-text-heading);">
                                    {{ $previewTitle ?: 'Mẫu xe tư vấn' }}
                                </div>
                                @if ($activeCarUnit)
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 6px; font-size: 13px; padding-top: 6px; border-top: 1px dashed #e2e8f0;">
                                        <span class="text-muted">Giá niêm yết:</span>
                                        <strong style="color: #2563eb; font-size: 14px;">
                                            {{ number_format((float) $activeCarUnit->selling_price, 0, ',', '.') }} đ
                                        </strong>
                                    </div>
                                    @if ($activeCarUnit->vin)
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 3px; font-size: 12px;">
                                            <span class="text-muted">Số khung (VIN):</span>
                                            <span class="font-monospace" style="color: #475569;">{{ $activeCarUnit->vin }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @else
                        <div style="padding: 24px 16px; border: 2px dashed #cbd5e1; border-radius: 8px; text-align: center; color: #94a3b8; margin-top: 14px; background: #f8fafc;">
                            <i class="fa fa-car fa-2x mb-2 d-block" style="opacity: 0.5;"></i>
                            <div style="font-size: 12.5px; font-weight: 500;">Chưa chọn mẫu xe cụ thể</div>
                            <div style="font-size: 11.5px; margin-top: 2px;">Vui lòng chọn xe trong kho hoặc phiên bản để xem ảnh và thông số.</div>
                        </div>
                    @endif
                </div>

                {{-- Panel Trạng thái & Thao tác nhanh --}}
                <div class="c1-panel p-4 mb-4">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                        <i class="fa fa-sliders text-primary" style="font-size: 17px;"></i>
                        <h6 style="font-weight: 600; margin: 0; color: var(--c1-text-heading);">Trạng thái & Thao tác nhanh</h6>
                    </div>

                    <div class="mb-3">
                        <label for="field-status" class="form-label" style="font-size: 13px; font-weight: 600;">
                            Trạng thái lịch hẹn <span class="text-danger">*</span>
                        </label>
                        <select id="field-status" wire:model="form.status" class="c1-select w-100 @error('form.status') is-invalid @enderror" required>
                            @foreach ($statusOptions as $stKey => $stLbl)
                                <option value="{{ $stKey }}">{{ $stLbl }}</option>
                            @endforeach
                        </select>
                        @error('form.status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Quick Action Buttons for Existing Appointment --}}
                    @if ($appointment->exists)
                        <div style="padding-top: 12px; border-top: 1px solid var(--c1-border-card);">
                            <div style="font-size: 12px; font-weight: 600; color: var(--c1-text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Chuyển trạng thái 1 chạm
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @if ($appointment->status !== 'confirmed')
                                    <button type="button" wire:click="quickChangeStatus('confirmed')" class="c1-btn c1-btn-sm" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; justify-content: center; width: 100%;">
                                        <i class="fa fa-check me-1"></i>Xác nhận lịch hẹn
                                    </button>
                                @endif
                                @if ($appointment->status !== 'done')
                                    <button type="button" wire:click="quickChangeStatus('done')" class="c1-btn c1-btn-sm" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; justify-content: center; width: 100%;">
                                        <i class="fa fa-check-circle me-1"></i>Đã hoàn tất lái thử
                                    </button>
                                @endif
                                @if ($appointment->status !== 'cancelled')
                                    <button type="button" wire:click="quickChangeStatus('cancelled')" class="c1-btn c1-btn-sm" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; justify-content: center; width: 100%;" onclick="return confirm('Bạn có chắc muốn hủy lịch hẹn này?')">
                                        <i class="fa fa-times-circle me-1"></i>Hủy lịch hẹn
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Panel Metadata --}}
                @if ($appointment->exists)
                    <div class="c1-panel p-4" style="background: #ffffff;">
                        <h6 class="mb-3" style="font-weight: 600; font-size: 14px; color: var(--c1-text-heading);">
                            Thông tin kiểm soát hệ thống
                        </h6>
                        <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--c1-text-body);">
                            <div style="display: flex; justify-content: space-between;">
                                <span class="text-muted">Mã lịch hẹn:</span>
                                <strong>#{{ $appointment->id }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span class="text-muted">Thời điểm tạo:</span>
                                <span>{{ optional($appointment->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span class="text-muted">Cập nhật lần cuối:</span>
                                <span>{{ optional($appointment->updated_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            @if ($appointment->lead)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                                    <span class="text-muted">Hồ sơ Lead:</span>
                                    <a href="{{ route('admin.leads.show', $appointment->lead) }}" wire:navigate class="c1-badge" style="background: #e0f2fe; color: #0284c7; text-decoration: none;">
                                        #{{ $appointment->lead->id }} • {{ $appointment->lead->name }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>
