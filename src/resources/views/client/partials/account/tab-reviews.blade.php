<div class="account-card">
    <div class="account-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="account-card-title">
                <i class="fa-solid fa-star me-2 text-warning"></i> Đánh giá & Nhận xét của tôi
            </h3>
            <p class="account-card-subtitle">
                Theo dõi các đánh giá, cảm nhận vận hành thực tế đã gửi cho từng phiên bản xe bạn sở hữu.
            </p>
        </div>
    </div>

    @if ($accountReviews->isEmpty())
        <div class="account-empty-state py-5">
            <div class="empty-icon-wrap">
                <i class="fa-regular fa-star text-muted"></i>
            </div>
            <h4 class="empty-title">Bạn chưa có đánh giá nào</h4>
            <p class="empty-desc">
                Sau khi mua xe và nhận bàn giao tại showroom, bạn có thể để lại nhận xét công tâm để chia sẻ trải nghiệm thực tế với cộng đồng yêu xe.
            </p>
            <button type="button" class="theme-btn btn-sm mt-3" data-switch-tab="account-purchases-pane">
                <i class="fa-solid fa-warehouse me-1"></i> Xem Gara xe của tôi
            </button>
        </div>
    @else
        <div class="account-item-list">
            @foreach ($accountReviews as $review)
                <div class="account-item-card no-thumb">
                    <div class="item-card-body">
                        <div class="item-card-top">
                            <div class="item-title-group">
                                <h4 class="item-title">
                                    <a href="{{ $review->trim_url }}">{{ $review->trim_label }}</a>
                                </h4>
                                <div class="item-meta-row">
                                    <div class="account-rating-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa {{ $i <= $review->rating ? 'fa-star text-warning' : 'fa-star-o text-muted' }}"></i>
                                        @endfor
                                        <span class="rating-text ms-1">({{ $review->rating }}/5)</span>
                                    </div>

                                    <span class="badge-status status-{{ $review->status_tone }}">
                                        {{ $review->status_label }}
                                    </span>

                                    <span class="badge-neutral">
                                        <i class="fa-regular fa-clock me-1 text-muted"></i> {{ $review->created_at_label }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ $review->trim_url }}" class="action-link-btn">
                                Xem phiên bản <i class="fa-solid fa-angle-right ms-1"></i>
                            </a>
                        </div>

                        <div class="item-card-desc">
                            <i class="fa-solid fa-quote-left text-muted me-1"></i>
                            <span>{{ $review->comment }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
