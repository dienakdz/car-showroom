<div class="account-card">
    <div class="account-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="account-card-title">
                <i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i> Yêu cầu tư vấn của tôi
            </h3>
            <p class="account-card-subtitle">
                Danh sách các yêu cầu báo giá dự toán, tư vấn tài chính trả góp hoặc định giá xe cũ đã gửi tới showroom.
            </p>
        </div>

        <a href="{{ route('contact') }}" class="btn-outline-custom btn-sm">
            <i class="fa-solid fa-paper-plane me-1"></i> Gửi yêu cầu mới
        </a>
    </div>

    @if ($accountLeads->isEmpty())
        <div class="account-empty-state py-5">
            <div class="empty-icon-wrap">
                <i class="fa-regular fa-comments text-muted"></i>
            </div>
            <h4 class="empty-title">Bạn chưa có yêu cầu tư vấn nào</h4>
            <p class="empty-desc">
                Khi cần báo giá lăn bánh, tư vấn gói tài chính hoặc thẩm định xe cũ đổi mới, hãy để lại thông tin để chuyên viên hỗ trợ nhé.
            </p>
            <a href="{{ route('contact') }}" class="theme-btn btn-sm mt-3">
                <i class="fa-solid fa-envelope-open-text me-1"></i> Liên hệ tư vấn ngay
            </a>
        </div>
    @else
        <div class="account-item-list">
            @foreach ($accountLeads as $lead)
                <div class="account-item-card no-thumb">
                    <div class="item-card-body">
                        <div class="item-card-top">
                            <div class="item-title-group">
                                <h4 class="item-title">
                                    <a href="{{ $lead->context_url }}">{{ $lead->context_label }}</a>
                                </h4>
                                <div class="item-meta-row">
                                    <span class="badge-neutral">
                                        <i class="fa-solid fa-tag me-1 text-primary"></i> {{ $lead->source_label }}
                                    </span>
                                    <span class="badge-status status-{{ $lead->status_tone }}">
                                        {{ $lead->status_label }}
                                    </span>
                                    <span class="badge-neutral">
                                        <i class="fa-regular fa-clock me-1 text-muted"></i> {{ $lead->created_at_label }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ $lead->context_url }}" class="action-link-btn">
                                Xem thông tin dòng xe <i class="fa-solid fa-angle-right ms-1"></i>
                            </a>
                        </div>

                        <div class="item-card-desc">
                            <i class="fa-solid fa-quote-left text-muted me-1"></i>
                            <span>{{ $lead->message !== '' ? $lead->message : 'Không có ghi chú bổ sung cho yêu cầu này.' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
