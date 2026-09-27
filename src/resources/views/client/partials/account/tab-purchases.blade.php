<div class="account-card">
    <div class="account-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="account-card-title">
                <i class="fa-solid fa-warehouse me-2 text-primary"></i> Gara xe của tôi
            </h3>
            <p class="account-card-subtitle">
                Danh sách các dòng xe đã được bàn giao chính thức và liên kết bảo hành điện tử với tài khoản của bạn.
            </p>
        </div>

        <a href="{{ route('inventory.index') }}" class="btn-outline-custom btn-sm">
            <i class="fa-solid fa-car-side me-1"></i> Xem thêm xe mới
        </a>
    </div>

    @if ($accountPurchases->isEmpty())
        <div class="account-empty-state py-5">
            <div class="empty-icon-wrap">
                <i class="fa-solid fa-warehouse text-muted"></i>
            </div>
            <h4 class="empty-title">Gara của bạn hiện đang trống</h4>
            <p class="empty-desc">
                Hiện chưa có hợp đồng mua xe nào được liên kết với tài khoản này. Sau khi hoàn tất thủ tục bàn giao xe tại showroom, thông tin xe và hồ sơ bảo hành sẽ được cập nhật tự động tại đây.
            </p>
            <a href="{{ route('inventory.index') }}" class="theme-btn btn-sm mt-3">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Khám phá bộ sưu tập xe
            </a>
        </div>
    @else
        <div class="account-garage-grid">
            @foreach ($accountPurchases as $purchase)
                <div class="account-garage-card">
                    <div class="garage-card-thumb">
                        <img src="{{ $purchase->image_url }}" alt="{{ $purchase->car_label }}">
                        <span class="badge-status status-{{ $purchase->review_status_tone }}">
                            {{ $purchase->review_status_label }}
                        </span>
                    </div>

                    <div class="garage-card-body">
                        <h4 class="garage-car-title">
                            <a href="{{ $purchase->trim_url }}">{{ $purchase->car_label }}</a>
                        </h4>
                        
                        <p class="garage-car-trim">{{ $purchase->trim_label }}</p>

                        <div class="garage-meta-list">
                            <div class="garage-meta-item">
                                <span class="meta-label">Ngày bàn giao:</span>
                                <strong class="meta-val">{{ $purchase->sold_at_label }}</strong>
                            </div>
                            <div class="garage-meta-item">
                                <span class="meta-label">Giá trị hợp đồng:</span>
                                <strong class="meta-val text-primary">{{ $purchase->sold_price_label }}</strong>
                            </div>
                        </div>

                        <div class="garage-card-actions">
                            @if ($purchase->can_review)
                                <a href="{{ $purchase->trim_url }}" class="theme-btn btn-sm w-100 justify-content-center">
                                    <i class="fa-solid fa-star me-1 text-warning"></i> Viết đánh giá trải nghiệm
                                </a>
                            @else
                                <a href="{{ $purchase->trim_url }}" class="btn-outline-custom btn-sm w-100 justify-content-center">
                                    Xem chi tiết phiên bản <i class="fa-solid fa-angle-right ms-1"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
