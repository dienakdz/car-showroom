<div class="account-card">
    <div class="account-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="account-card-title">
                <i class="fa-solid fa-calendar-check me-2 text-primary"></i> Lịch hẹn của tôi
            </h3>
            <p class="account-card-subtitle">
                Theo dõi tiến trình tiếp nhận, chuẩn bị xe và đón tiếp của các buổi lái thử tại Showroom.
            </p>
        </div>

        <a href="{{ route('inventory.index') }}" class="btn-outline-custom btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Đăng ký lái thử xe khác
        </a>
    </div>

    @if ($accountAppointments->isEmpty())
        <div class="account-empty-state py-5">
            <div class="empty-icon-wrap">
                <i class="fa-solid fa-calendar-xmark text-muted"></i>
            </div>
            <h4 class="empty-title">Bạn chưa có lịch hẹn nào</h4>
            <p class="empty-desc">
                Chọn mẫu xe ưng ý trong kho xe của BoxCar và đặt lịch lái thử để trải nghiệm trực tiếp!
            </p>
            <a href="{{ route('inventory.index') }}" class="theme-btn btn-sm mt-3">
                <i class="fa-solid fa-car-side me-1"></i> Khám phá kho xe
            </a>
        </div>
    @else
        <div class="account-item-list">
            @foreach ($accountAppointments as $appointment)
                <div class="account-item-card">
                    <div class="item-card-thumb">
                        <img src="{{ $appointment->image_url }}" alt="{{ $appointment->context_label }}">
                    </div>

                    <div class="item-card-body">
                        <div class="item-card-top">
                            <div class="item-title-group">
                                <h4 class="item-title">
                                    <a href="{{ $appointment->context_url }}">{{ $appointment->context_label }}</a>
                                </h4>
                                <div class="item-meta-row">
                                    <span class="badge-status status-{{ $appointment->status_tone }}">
                                        {{ $appointment->status_label }}
                                    </span>
                                    <span class="badge-neutral">
                                        <i class="fa-regular fa-clock me-1 text-primary"></i> {{ $appointment->scheduled_at_label }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ $appointment->context_url }}" class="action-link-btn">
                                Xem chi tiết xe <i class="fa-solid fa-angle-right ms-1"></i>
                            </a>
                        </div>

                        <div class="item-card-desc">
                            <i class="fa-solid fa-message text-muted me-1"></i>
                            <span>{{ $appointment->note !== '' ? $appointment->note : 'Không có ghi chú thêm cho buổi hẹn này.' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
