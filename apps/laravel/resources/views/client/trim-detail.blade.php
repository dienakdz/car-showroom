@extends('client.layouts.page')

@section('title', $displayTitle . ' - Hồ Sơ Phiên Bản & Giá Lăn Bánh')

@section('content')
@php
    $defaultName = old('name', auth()->user()->name ?? '');
    $defaultPhone = old('phone', auth()->user()->phone ?? '');
    $defaultEmail = old('email', auth()->user()->email ?? '');
    $reviewCount = $reviews->count();
    $reviewAverage = $reviewCount > 0 ? number_format((float) $reviews->avg('rating'), 1) : '5.0';
    $latestReviewDate = $reviewCount > 0
        ? \Carbon\Carbon::parse($reviews->first()->created_at)->format('d/m/Y')
        : null;

    $reviewDistribution = collect(range(5, 1))->map(function (int $rating) use ($reviews, $reviewCount): array {
        $count = $reviews->where('rating', $rating)->count();

        return [
            'rating' => $rating,
            'count' => $count,
            'percent' => $reviewCount > 0 ? (int) round(($count / $reviewCount) * 100) : ($rating === 5 ? 100 : 0),
        ];
    });

    $descriptionText = trim((string) ($trim->description ?? ''));
    if ($descriptionText === '') {
        $descriptionText = 'Phiên bản ' . $displayTitle . ' (' . $yearRange . ') là sự kết hợp chuẩn mực giữa ngôn ngữ thiết kế sang trọng, khả năng vận hành bền bỉ và trang bị công nghệ tiện nghi vượt trội trong phân khúc.';
    }

    $showroomName = $navShowroom->name ?? 'Minh Dien Auto Showroom';
    $showroomPhone = $navShowroom->phone ?? '0900 000 002';
    $phoneDigits = preg_replace('/\D+/', '', (string) $showroomPhone);
    $zaloUrl = $phoneDigits !== '' ? 'https://zalo.me/' . $phoneDigits : '#inquiry-consult';

    // Helper closure to match icons for attributes
    $getAttrIcon = function (string $label, string $code): string {
        $text = mb_strtolower($label . ' ' . $code);
        if (str_contains($text, 'động cơ') || str_contains($text, 'engine') || str_contains($text, 'dung tích')) return 'fa-car-side';
        if (str_contains($text, 'công suất') || str_contains($text, 'power') || str_contains($text, 'mã lực')) return 'fa-bolt';
        if (str_contains($text, 'xoắn') || str_contains($text, 'torque')) return 'fa-arrows-spin';
        if (str_contains($text, 'hộp số') || str_contains($text, 'transmission')) return 'fa-gear';
        if (str_contains($text, 'tiêu thụ') || str_contains($text, 'nhiên liệu') || str_contains($text, 'fuel')) return 'fa-gas-pump';
        if (str_contains($text, 'dẫn động') || str_contains($text, 'drive')) return 'fa-arrows-split-up-and-left';
        if (str_contains($text, 'chỗ') || str_contains($text, 'ghế') || str_contains($text, 'seat')) return 'fa-users';
        if (str_contains($text, 'tăng tốc') || str_contains($text, 'acceleration') || str_contains($text, '0-100')) return 'fa-stopwatch';
        if (str_contains($text, 'cửa sổ') || str_contains($text, 'sunroof')) return 'fa-sun';
        if (str_contains($text, 'mâm') || str_contains($text, 'lốp') || str_contains($text, 'wheel') || str_contains($text, 'la-zăng')) return 'fa-circle-notch';
        if (str_contains($text, 'khí thải') || str_contains($text, 'emission') || str_contains($text, 'chuẩn')) return 'fa-shield-halved';
        return 'fa-sliders';
    };
@endphp

<section class="car-detail-page-wrap">
    <div class="boxcar-container">
        <!-- 1. Breadcrumb -->
        <div class="car-detail-header-intro" style="margin-bottom: 24px;">
            <ul class="car-detail-breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><span>/</span></li>
                <li><a href="{{ route('inventory.index') }}">Kho xe</a></li>
                <li><span>/</span></li>
                <li><a href="{{ route('inventory.index', ['make' => $trim->make_slug]) }}">{{ $trim->make_name }}</a></li>
                <li><span>/</span></li>
                <li><a href="{{ route('inventory.index', ['make' => $trim->make_slug, 'model' => $trim->model_slug]) }}">{{ $trim->model_name }}</a></li>
                <li><span>/</span></li>
                <li class="active">{{ $trim->name }}</li>
            </ul>
        </div>

        <!-- 2. Hero Dossier Showcase -->
        <div class="trim-hero-card">
            <div class="trim-hero-info">
                <div class="trim-badge-bar">
                    <span class="badge-pill badge-brand">
                        <i class="fa-solid fa-car me-1"></i> {{ $trim->make_name }} • {{ $trim->model_name }}
                    </span>
                    <span class="badge-pill badge-year">
                        <i class="fa-regular fa-calendar-days me-1"></i> Đời xe: {{ $yearRange }}
                    </span>
                    @if ($availableCarsCount > 0)
                        <span class="badge-pill badge-avail">
                            <i class="fa-solid fa-circle-check me-1"></i> Có sẵn {{ $availableCarsCount }} xe tại showroom
                        </span>
                    @else
                        <span class="badge-pill badge-year">
                            <i class="fa-regular fa-clock me-1"></i> Nhận đặt xe theo yêu cầu
                        </span>
                    @endif
                </div>

                <h1 class="trim-hero-title">{{ $displayTitle }}</h1>

                <div class="trim-msrp-box">
                    <div>
                        <span class="trim-msrp-label">Giá niêm yết (MSRP tham khảo)</span>
                        <div class="trim-msrp-value">{{ $formattedMsrp }}</div>
                    </div>
                    @if ($rawMsrp > 0)
                        <span class="trim-msrp-note">• Trả trước từ ~{{ number_format($rawMsrp * 0.2, 0, ',', '.') }} VNĐ (góp từ ~{{ $estimatedMonthly }} VNĐ/tháng)</span>
                    @endif
                </div>

                <p class="trim-hero-desc">{{ $descriptionText }}</p>

                <div class="trim-hero-cta">
                    @if ($availableCarsCount > 0)
                        <a href="#available-inventory" class="btn-primary-cta">
                            <i class="fa-solid fa-car-side"></i> Khám Phá {{ $availableCarsCount }} Xe Đang Sẵn Có
                        </a>
                    @else
                        <a href="{{ route('inventory.index', ['make' => $trim->make_slug]) }}" class="btn-primary-cta">
                            <i class="fa-solid fa-warehouse"></i> Xem Kho Xe {{ $trim->make_name }}
                        </a>
                    @endif
                    <a href="#inquiry-consult" class="btn-secondary-cta">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Nhận Báo Giá Lăn Bánh
                    </a>
                </div>
            </div>

            <div class="trim-hero-visual">
                <img src="{{ $heroImageUrl }}" alt="{{ $displayTitle }}">
            </div>
        </div>

        <!-- 3. Core Specifications Grid (Full-Width) -->
        <div class="car-detail-card">
            <div class="car-detail-card-title">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-list-check text-primary"></i>
                    <span>Thông Số Kỹ Thuật Nền Tảng Của Phiên Bản</span>
                </div>
                <span class="title-accent">Hồ sơ thông số tiêu chuẩn từ nhà sản xuất</span>
            </div>

            <div class="specs-grid-4col">
                @forelse ($attributes as $attr)
                    <div class="spec-box">
                        <div class="spec-icon">
                            <i class="fa-solid {{ $getAttrIcon($attr->label, $attr->code) }}"></i>
                        </div>
                        <div class="spec-data">
                            <span>{{ $attr->label }}</span>
                            <strong>{{ $attr->display_value ?? 'Đang cập nhật' }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-3 text-center text-muted">
                        <i class="fa-solid fa-circle-info me-1"></i> Thông số kỹ thuật đang được cập nhật chi tiết từ hãng sản xuất.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. Grouped Features & Equipment (Full-Width) -->
        <div class="car-detail-card">
            <div class="car-detail-card-title">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-primary"></i>
                    <span>Trang Bị Tiện Nghi & Công Nghệ Nổi Bật</span>
                </div>
                <span class="title-accent">Trang bị tiêu chuẩn theo phiên bản</span>
            </div>

            <div class="features-grid-4col">
                @forelse ($features as $groupName => $groupFeatures)
                    <div class="feature-category">
                        <h4 class="feature-cat-title">
                            <i class="fa-solid fa-layer-group"></i> {{ $groupName }}
                        </h4>
                        <ul class="feature-list">
                            @foreach ($groupFeatures as $feature)
                                <li>
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>{{ $feature->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="col-12 py-3 text-center text-muted">
                        <i class="fa-solid fa-circle-info me-1"></i> Danh mục trang bị chi tiết đang được chuyên viên cập nhật.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 5. Available Cars for this Trim -->
        <div class="car-detail-card" id="available-inventory">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="fs-5 fw-bold text-dark mb-1">
                        Xe Đang Sẵn Có Tại Showroom Cho Phiên Bản Này ({{ $availableCarsCount }} xe)
                    </h3>
                    <p class="text-muted mb-0" style="font-size: 13.5px;">
                        Các xe sẵn sàng bàn giao ngay trong ngày, hồ sơ pháp lý hoàn chỉnh và đã kiểm định 160 bước kỹ thuật.
                    </p>
                </div>
                <a href="{{ route('inventory.index', ['make' => $trim->make_slug]) }}" class="fw-bold text-primary text-decoration-none" style="font-size: 13.5px;">
                    Xem thêm các xe {{ $trim->make_name }} khác <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            @if ($availableCars->isNotEmpty())
                <div class="row">
                    @foreach ($availableCars as $car)
                        @include('client.partials.car-card', ['car' => $car])
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 px-3 bg-light rounded-4 border border-dashed">
                    <i class="fa-solid fa-warehouse fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">Hiện Chưa Có Xe Sẵn Tại Kho Cho Phiên Bản Này</h5>
                    <p class="text-muted mx-auto mb-4" style="max-width: 580px; font-size: 14px;">
                        Showroom liên tục cập nhật các lô xe mới. Quý khách có thể gửi yêu cầu đặt xe hoặc tham khảo các mẫu xe tương đương cùng hãng {{ $trim->make_name }}.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('inventory.index', ['make' => $trim->make_slug]) }}" class="btn-primary-cta">
                            <i class="fa-solid fa-car"></i> Xem Tất Cả Xe {{ $trim->make_name }}
                        </a>
                        <a href="#inquiry-consult" class="btn-secondary-cta">
                            <i class="fa-solid fa-envelope"></i> Đăng Ký Nhận Thông Báo Có Xe
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- 6. Consultation & Lead Form (Balanced 2 Columns) -->
        <div class="inquiry-grid" id="inquiry-consult">
            <div class="inquiry-info">
                <div>
                    <h3>Liên Hệ Nhận Tư Vấn & Báo Giá Lăn Bánh</h3>
                    <p>
                        Quý khách đang quan tâm đến phiên bản <strong>{{ $displayTitle }}</strong>? Hãy để lại thông tin, đội ngũ chuyên viên của {{ $showroomName }} sẽ gửi dự toán chi phí chi tiết và tư vấn chương trình ưu đãi đặc quyền trong 15 phút.
                    </p>

                    <ul class="trust-points-list">
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Hỗ trợ gói vay ngân hàng đến 80% với lãi suất ưu đãi đặc quyền.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Hỗ trợ thu cũ đổi mới - trợ giá lên tới 30 triệu đồng.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Bàn giao xe tận nhà và hỗ trợ hoàn tất bấm biển số trọn gói trong 24h.</span>
                        </li>
                    </ul>
                </div>

                <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #F1F5F9; font-size: 13.5px; color: #64748B;">
                    Hotline tư vấn 24/7: <a href="tel:{{ $phoneDigits }}" style="color: #050B20; font-weight: 700; text-decoration: none;">{{ $showroomPhone }}</a> • <a href="{{ $zaloUrl }}" target="_blank" rel="noopener" style="color: #059669; font-weight: 700; text-decoration: none;"><i class="fa-solid fa-comment-dots"></i> Chat Zalo Ngay</a>
                </div>
            </div>

            <div>
                @if (session('success'))
                    <div class="alert alert-success mb-3 rounded-3">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger mb-3 rounded-3">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> <strong>Đã có lỗi xảy ra:</strong>
                        <ul class="mb-0 mt-1 ps-3" style="font-size: 13px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('lead.store') }}">
                    @csrf
                    <input type="hidden" name="source" value="trim_page">
                    <input type="hidden" name="trim_id" value="{{ $trim->id }}">

                    <div class="form-grid-2col">
                        <div class="booking-input-group">
                            <label>Họ và tên quý khách <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ $defaultName }}" placeholder="Ví dụ: Nguyễn Văn An" required>
                        </div>

                        <div class="booking-input-group">
                            <label>Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ $defaultPhone }}" placeholder="0901 234 567" required>
                        </div>

                        <div class="booking-input-group">
                            <label>Hòm thư điện tử (Email)</label>
                            <input type="email" name="email" value="{{ $defaultEmail }}" placeholder="example@email.com">
                        </div>

                        <div class="booking-input-group">
                            <label>Phiên bản xe quan tâm</label>
                            <input type="text" value="{{ $displayTitle }}" readonly style="background: #F1F5F9; font-weight: 700; color: #050B20;">
                        </div>

                        <div class="booking-input-group full-col">
                            <label>Nội dung cần hỗ trợ tư vấn</label>
                            <textarea name="message" placeholder="Ví dụ: Tôi muốn nhận dự toán chi phí lăn bánh, ưu đãi tháng này và đăng ký lái thử xe...">{{ old('message') }}</textarea>
                        </div>

                        <div class="full-col pt-1">
                            <button type="submit" class="form-submit-btn">
                                <span>Gửi Yêu Cầu Nhận Báo Giá & Tư Vấn</span>
                                <i class="fa-solid fa-paper-plane ms-2"></i>
                            </button>
                            <p class="text-center text-muted mt-2 mb-0" style="font-size: 12px;">
                                <i class="fa-solid fa-shield-halved text-success me-1"></i> Thông tin của quý khách được bảo mật tuyệt đối 100%.
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- 7. Customer Reviews Section -->
        <div class="car-detail-card" id="customer-reviews">
            <div class="car-detail-card-title">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-star text-warning"></i>
                    <span>Đánh Giá Khách Hàng Về Phiên Bản Xe</span>
                </div>
                <span class="title-accent">100% đánh giá xác thực từ người mua</span>
            </div>

            <div class="reviews-layout">
                <!-- Summary Score Card -->
                <div class="reviews-summary-box">
                    <div>
                        <span style="font-size: 12px; text-transform: uppercase; font-weight: 700; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;">
                            Điểm hài lòng trung bình
                        </span>
                        <div style="font-size: 48px; font-weight: 850; line-height: 1; margin: 10px 0;">
                            {{ $reviewAverage }}<span style="font-size: 18px; font-weight: 500; opacity: 0.7;"> / 5.0</span>
                        </div>
                        <div style="color: #F59E0B; font-size: 15px; margin-bottom: 12px;">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-solid {{ $i <= round((float) $reviewAverage) ? 'fa-star' : 'fa-star-o text-white-50' }}"></i>
                            @endfor
                        </div>
                        <p style="font-size: 13px; color: rgba(255,255,255,0.8); line-height: 1.6; margin: 0;">
                            @if ($reviewCount > 0)
                                Tổng hợp từ {{ $reviewCount }} đánh giá thực tế của khách hàng đã mua xe thuộc phiên bản này.
                            @else
                                Tổng hợp đánh giá chất lượng phiên bản từ các khách hàng sở hữu xe tại hệ thống showroom.
                            @endif
                        </p>
                    </div>

                    <div class="rating-bars-list">
                        @foreach ($reviewDistribution as $distribution)
                            <div class="rating-bar-row">
                                <span style="width: 42px;">{{ $distribution['rating'] }} sao</span>
                                <div class="rating-bar-track">
                                    <div class="rating-bar-fill" style="width: {{ $distribution['percent'] }}%;"></div>
                                </div>
                                <span style="width: 32px; text-align: right;">{{ $distribution['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reviews List & Form Container -->
                <div>
                    @if ($errors->has('review'))
                        <div class="alert alert-warning mb-3">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first('review') }}
                        </div>
                    @endif

                    @forelse ($reviews as $review)
                        <div class="review-card-item">
                            <div class="review-header">
                                <div class="review-author">
                                    <div class="review-avatar">
                                        {{ strtoupper(substr($review->user_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="review-name">
                                            {{ $review->user_name }}
                                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 11px;">
                                                <i class="fa-solid fa-check-circle"></i> Đã mua xe
                                            </span>
                                        </div>
                                        <div class="review-date">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="review-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o text-muted' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="review-text">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <div class="p-4 rounded-4 border border-dashed text-center text-muted mb-3 bg-light">
                            <i class="fa-regular fa-comment-dots fs-3 d-block mb-2 text-secondary"></i>
                            <span style="font-size: 13.5px;">Chưa có đánh giá công khai cho phiên bản này. Quý khách hãy liên hệ showroom để trải nghiệm xe thực tế!</span>
                        </div>
                    @endforelse

                    <!-- Review Actions / Eligibility Box -->
                    <div style="background: #F8FAFC; border: 1.5px dashed #CBD5E1; border-radius: 14px; padding: 20px 24px; margin-top: 18px;">
                        @guest
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <strong style="font-size: 14px; color: #050B20; display: block; margin-bottom: 2px;">
                                        Bạn đã sở hữu phiên bản xe này?
                                    </strong>
                                    <p style="font-size: 12.5px; color: #64748B; margin: 0;">
                                        Đăng nhập bằng tài khoản mua xe để chia sẻ trải nghiệm thực tế của bạn với cộng đồng.
                                    </p>
                                </div>
                                <a href="{{ route('login') }}" class="btn-secondary-cta py-2 px-3 fs-6">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Đăng Nhập Đánh Giá
                                </a>
                            </div>
                        @else
                            @if ($canSubmitReview)
                                <form method="POST" action="{{ route('trim.reviews.store', ['trimSlug' => $trim->slug]) }}">
                                    @csrf
                                    <h5 class="fw-bold fs-6 text-dark mb-3">Gửi Đánh Giá Của Bạn Về Phiên Bản Này</h5>
                                    <div class="mb-3">
                                        <label class="form-label fs-7 fw-semibold text-dark">Mức độ hài lòng <span class="text-danger">*</span></label>
                                        <select class="form-select @error('rating') is-invalid @enderror" name="rating" required style="border-radius: 10px;">
                                            <option value="">Chọn số sao đánh giá</option>
                                            <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5/5 sao - Rất hài lòng)</option>
                                            <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (4/5 sao - Hài lòng)</option>
                                            <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>⭐⭐⭐ (3/5 sao - Bình thường)</option>
                                            <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>⭐⭐ (2/5 sao - Chưa hài lòng)</option>
                                            <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>⭐ (1/5 sao - Rất tệ)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fs-7 fw-semibold text-dark">Nhận xét chi tiết <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('comment') is-invalid @enderror" name="comment" rows="3" placeholder="Chia sẻ cảm nhận về khả năng vận hành, độ cách âm, tiện nghi..." required style="border-radius: 10px;">{{ old('comment') }}</textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn-primary-cta py-2 px-4">
                                            <span>Gửi Đánh Giá Xác Thực</span>
                                            <i class="fa-solid fa-paper-plane ms-1"></i>
                                        </button>
                                    </div>
                                </form>
                            @elseif ($userReview)
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 14px;">Bạn Đã Gửi Đánh Giá Cho Phiên Bản Này</strong>
                                        <span class="text-muted" style="font-size: 12.5px;">Trạng thái: 
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ match ($userReview->status ?? null) { 'approved' => 'Đã duyệt', 'hidden' => 'Đã ẩn', default => 'Đang chờ duyệt' } }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="text-warning">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid {{ $i <= $userReview->rating ? 'fa-star' : 'fa-star-o text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mt-2 p-2 bg-white rounded border" style="font-size: 13px; color: #475569;">
                                    "{{ $userReview->comment }}"
                                </div>
                            @elseif (! $userHasPurchasedTrim)
                                <div>
                                    <strong style="font-size: 14px; color: #050B20; display: block; margin-bottom: 2px;">
                                        Chính Sách Đánh Giá Xác Thực
                                    </strong>
                                    <p style="font-size: 12.5px; color: #64748B; margin: 0;">
                                        Để đảm bảo tính khách quan 100%, chỉ các tài khoản đã mua xe thuộc phiên bản này mới có quyền gửi đánh giá thực tế.
                                    </p>
                                </div>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
