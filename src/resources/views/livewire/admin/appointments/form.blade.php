<div>
    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span><i class="fa {{ ($feedback['type'] ?? 'success') === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle' }} me-2"></i>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1" style="font-weight: 700;">{{ $appointment->exists ? 'Cập nhật lịch hẹn #' . $appointment->id : 'Tạo lịch hẹn mới' }}</h4>
            <div class="text-muted" style="font-size: 13px;">
                {{ $appointment->exists ? 'Chỉnh sửa thời gian, chuyên viên đón tiếp và trạng thái lịch hẹn.' : 'Thiết lập buổi lái thử hoặc tư vấn trực tiếp tại showroom.' }}
            </div>
        </div>
        <div>
            <a href="{{ route('admin.appointments.index') }}" wire:navigate class="c1-btn c1-btn-secondary">
                <i class="fa fa-arrow-left me-1"></i>
                <span>Về danh sách</span>
            </a>
        </div>
    </div>

    <form wire:submit="save">
        <div class="form-box admin-template-form-box admin-form-tabs-shell">
            <ul class="nav nav-tabs" id="appointment-form-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="appointment-booking-tab" data-bs-toggle="tab" data-bs-target="#appointment-booking" type="button" role="tab" aria-controls="appointment-booking" aria-selected="true">
                        <i class="fa fa-calendar me-1"></i> Thông tin buổi hẹn (Booking)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="appointment-context-tab" data-bs-toggle="tab" data-bs-target="#appointment-context" type="button" role="tab" aria-controls="appointment-context" aria-selected="false">
                        <i class="fa fa-car me-1"></i> Xe & Ngữ cảnh (Vehicle Context)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="appointment-form-tabs-content">
                {{-- TAB 1: BOOKING --}}
                <div class="tab-pane fade show active" id="appointment-booking" role="tabpanel" aria-labelledby="appointment-booking-tab">
                    <div class="row">
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label for="field-user-id">Khách hàng (Tài khoản User)</label>
                                <select id="field-user-id" wire:model.live="form.user_id" class="form-select @error('form.user_id') is-invalid @enderror">
                                    <option value="">-- Không liên kết tài khoản user --</option>
                                    @foreach ($customerUsers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}{{ $customer->phone ? ' • ' . $customer->phone : '' }}</option>
                                    @endforeach
                                </select>
                                @error('form.user_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label for="field-lead-id">Lead liên kết (Tự động điền thông tin nếu có)</label>
                                <select id="field-lead-id" wire:model.live="form.lead_id" class="form-select @error('form.lead_id') is-invalid @enderror">
                                    <option value="">-- Không liên kết Lead --</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->id }}">#{{ $lead->id }} - {{ $lead->name }} ({{ $lead->phone }})</option>
                                    @endforeach
                                </select>
                                @error('form.lead_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label for="field-handled-by">Chuyên viên phụ trách đón tiếp</label>
                                <select id="field-handled-by" wire:model="form.handled_by" class="form-select @error('form.handled_by') is-invalid @enderror">
                                    <option value="">Gán cho nhân viên hiện tại</option>
                                    @foreach ($staffUsers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.handled_by') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label for="field-status">Trạng thái lịch hẹn <span class="text-danger">*</span></label>
                                <select id="field-status" wire:model="form.status" class="form-select @error('form.status') is-invalid @enderror" required>
                                    @foreach ($statusOptions as $stKey => $stLbl)
                                        <option value="{{ $stKey }}">{{ $stLbl }}</option>
                                    @endforeach
                                </select>
                                @error('form.status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-4">
                            <div class="form_boxes">
                                <label for="field-scheduled-at">Thời gian hẹn <span class="text-danger">*</span></label>
                                <input id="field-scheduled-at" type="datetime-local" wire:model="form.scheduled_at" class="form-control @error('form.scheduled_at') is-invalid @enderror" required>
                                @error('form.scheduled_at') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-column col-lg-12">
                            <div class="form_boxes">
                                <label for="field-note">Ghi chú & Yêu cầu của khách</label>
                                <textarea id="field-note" wire:model="form.note" rows="4" class="form-control @error('form.note') is-invalid @enderror" placeholder="Ghi chú thêm về yêu cầu lái thử, lộ trình, điểm đặc biệt..."></textarea>
                                @error('form.note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: CONTEXT --}}
                <div class="tab-pane fade" id="appointment-context" role="tabpanel" aria-labelledby="appointment-context-tab">
                    <div class="row">
                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label for="field-car-unit-id">Xe cụ thể trong kho (Car Unit)</label>
                                <select id="field-car-unit-id" wire:model.live="form.car_unit_id" class="form-select @error('form.car_unit_id') is-invalid @enderror">
                                    <option value="">-- Lấy từ Lead hoặc chọn sau --</option>
                                    @foreach ($carUnits as $carUnit)
                                        <option value="{{ $carUnit->id }}">
                                            [{{ $carUnit->stock_code }}] {{ $carUnit->trim?->model?->make?->name }} {{ $carUnit->trim?->model?->name }} {{ $carUnit->trim?->name }} (VIN: {{ $carUnit->vin ?: 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.car_unit_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <small class="text-muted d-block mt-1">Khi chọn xe cụ thể, phiên bản xe sẽ tự động được gán theo xe.</small>
                            </div>
                        </div>

                        <div class="form-column col-lg-6">
                            <div class="form_boxes">
                                <label for="field-trim-id">Hoặc chọn Phiên bản xe quan tâm (Trim)</label>
                                <select id="field-trim-id" wire:model="form.trim_id" class="form-select @error('form.trim_id') is-invalid @enderror">
                                    <option value="">-- Lấy từ Car Unit hoặc Lead --</option>
                                    @foreach ($trims as $trim)
                                        <option value="{{ $trim->id }}">{{ $trim->model?->make?->name }} • {{ $trim->model?->name }} • {{ $trim->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.trim_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-submit admin-form-submit-end mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.appointments.index') }}" wire:navigate class="c1-btn c1-btn-secondary">
                    Hủy bỏ
                </a>
                <button type="submit" class="theme-btn btn-style-one" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $appointment->exists ? 'Cập nhật lịch hẹn' : 'Tạo lịch hẹn mới' }}</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin me-2"></i>Đang lưu thông tin...</span>
                    <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow" wire:loading.remove>
                </button>
            </div>
        </div>
    </form>

    @if ($appointment->exists)
        <div class="right-box-three admin-side-box mt-4" style="background: #ffffff; border: 1px solid var(--c1-border-card); border-radius: 12px; padding: 20px;">
            <h6 class="title mb-3" style="font-weight: 600; font-size: 15px;">Thông tin bổ trợ (Metadata)</h6>
            <div class="admin-meta-list" style="display: flex; flex-direction: column; gap: 8px; font-size: 13.5px;">
                <div><strong>Mã lịch hẹn:</strong> #{{ $appointment->id }}</div>
                <div><strong>Ngày khởi tạo:</strong> {{ optional($appointment->created_at)->format('d/m/Y H:i') }}</div>
                <div><strong>Cập nhật lần cuối:</strong> {{ optional($appointment->updated_at)->format('d/m/Y H:i') }}</div>
                <div><strong>Lead liên kết:</strong> {{ $appointment->lead ? '#' . $appointment->lead->id . ' - ' . $appointment->lead->name : 'Không có' }}</div>
                <div><strong>Chuyên viên phụ trách:</strong> {{ $appointment->handledBy?->name ?? 'Chưa chỉ định' }}</div>
            </div>
        </div>
    @endif
</div>
