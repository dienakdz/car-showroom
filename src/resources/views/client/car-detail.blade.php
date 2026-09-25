@extends('client.layouts.page')

@section('title', $car->make_name . ' ' . $car->model_name . ' ' . $car->trim_name . ' - ' . $car->year)

@section('content')
@php
    $imageMedia = $media->values();

    if ($imageMedia->isEmpty()) {
        $imageMedia = collect([(object) ['url' => $car->image_url]]);
    }

    $firstImageUrl = $imageMedia->first()->url ?? $car->image_url;

    $description = trim((string) ($car->trim_description ?? ''));
    $descriptionLead = $description !== ''
        ? \Illuminate\Support\Str::limit($description, 260, '...')
        : 'Chiếc ' . $car->make_name . ' ' . $car->model_name . ' ' . $car->trim_name . ' ' . $car->year . ' được tuyển chọn khắt khe qua quy trình kiểm định 160 bước kỹ thuật chuẩn quốc tế. Xe sở hữu ngoại hình sang trọng, nội thất tiện nghi cao cấp cùng khối động cơ vận hành bền bỉ và tiết kiệm.';
    $descriptionTail = $description !== '' && \Illuminate\Support\Str::length($description) > 260
        ? \Illuminate\Support\Str::substr($description, 260)
        : 'Xe đầy đủ hồ sơ pháp lý, sẵn sàng sang tên bấm biển trong ngày. Showroom hỗ trợ trả góp qua ngân hàng tối đa 80% giá trị xe với lãi suất ưu đãi đặc quyền, thủ tục duyệt nhanh chóng. Quý khách vui lòng liên hệ trực tiếp để nhận báo giá lăn bánh tốt nhất và đăng ký trải nghiệm lái thử.';

    $attributeColumns = $attributes->isNotEmpty()
        ? $attributes->chunk((int) ceil($attributes->count() / 2))
        : collect();

    $showroomName = $navShowroom->name ?? 'Minh Dien Auto Showroom';
    $showroomAddress = $navShowroom->address ?? 'TP. Hồ Chí Minh';
    $showroomPhone = $navShowroom->phone ?? '0900 000 000';
    $showroomEmail = $navShowroom->email ?? null;

    $phoneDigits = preg_replace('/\D+/', '', (string) $showroomPhone);
    $zaloUrl = $phoneDigits !== '' ? 'https://zalo.me/' . $phoneDigits : '#booking-consultation-section';

    $mapsQuery = rawurlencode($showroomAddress);
    $mapsDirectionsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $mapsQuery;
    $mapsEmbedUrl = 'https://maps.google.com/maps?width=100%25&height=600&hl=vi&q=' . $mapsQuery . '&t=&z=14&ie=UTF8&iwloc=B&output=embed';

    $shareUrl = route('car.show', $car->stock_code);
    $shareFacebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);

    $reviewAverage = $reviewSummary && $reviewSummary->avg_rating !== null
        ? number_format((float) $reviewSummary->avg_rating, 1)
        : '5.0';

    $defaultName = old('name', auth()->user()->name ?? '');
    $defaultPhone = old('phone', auth()->user()->phone ?? '');
    $defaultEmail = old('email', auth()->user()->email ?? '');

    $rawPrice = (float) ($car->price ?? 0);
    $estimatedMonthly = $rawPrice > 0 ? number_format(($rawPrice * 0.7 * 0.012), 0, ',', '.') : '15.000.000';

    $displayTitle = str_starts_with(strtolower((string) $car->trim_name), strtolower((string) $car->model_name))
        ? $car->make_name . ' ' . $car->trim_name
        : $car->make_name . ' ' . $car->model_name . ' ' . $car->trim_name;
@endphp

<section class="car-detail-page-wrap">
    <div class="boxcar-container">
        <!-- 1. Header & Breadcrumb -->
        <div class="car-detail-header-intro">
            <ul class="car-detail-breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><span>/</span></li>
                <li><a href="{{ route('inventory.index') }}">Kho xe</a></li>
                <li><span>/</span></li>
                <li><a href="{{ route('inventory.index', ['make' => $car->make_slug]) }}">{{ $car->make_name }}</a></li>
                <li><span>/</span></li>
                <li class="active">{{ $displayTitle }}</li>
            </ul>

            <div class="car-detail-header-main">
                <div class="car-detail-title-col">
                    <h1 class="car-detail-main-title">{{ $displayTitle }}</h1>

                    <div class="car-detail-meta-bar">
                        @if ($car->condition === 'new')
                            <span class="car-badge-item car-badge-condition-new">
                                <i class="fa-solid fa-circle-check"></i> {{ $car->condition_label }}
                            </span>
                        @elseif ($car->condition === 'cpo')
                            <span class="car-badge-item car-badge-condition-cpo">
                                <i class="fa-solid fa-shield-halved"></i> {{ $car->condition_label }}
                            </span>
                        @else
                            <span class="car-badge-item car-badge-condition-used">
                                <i class="fa-solid fa-car"></i> {{ $car->condition_label }}
                            </span>
                        @endif

                        <span class="car-badge-item car-badge-stock">Mã kho: {{ $car->stock_code }}</span>
                        <span class="car-badge-item car-badge-vin">VIN: {{ $car->vin ?? 'Đang cập nhật' }}</span>
                    </div>

                    <ul class="car-quick-specs-list">
                        <li>
                            <img src="{{ asset('boxcar/images/resource/spec1-1.svg') }}" alt="Năm sản xuất">
                            <span>Năm {{ $car->year }}</span>
                        </li>
                        <li>
                            <img src="{{ asset('boxcar/images/resource/spec1-2.svg') }}" alt="Số Odo">
                            <span>{{ $car->mileage ? number_format((float) $car->mileage, 0, ',', '.') . ' km' : 'Odo siêu lướt' }}</span>
                        </li>
                        <li>
                            <img src="{{ asset('boxcar/images/resource/spec1-3.svg') }}" alt="Hộp số">
                            <span>{{ $car->transmission_label }}</span>
                        </li>
                        <li>
                            <img src="{{ asset('boxcar/images/resource/spec1-4.svg') }}" alt="Nhiên liệu">
                            <span>{{ $car->fuel_label }}</span>
                        </li>
                    </ul>
                </div>

                <div class="car-detail-actions">
                    <a href="{{ $shareFacebookUrl }}" target="_blank" rel="noopener" class="car-action-btn" title="Chia sẻ Facebook">
                        <img src="{{ asset('boxcar/images/resource/share.svg') }}" alt="Share">
                        <span>Chia sẻ</span>
                    </a>
                    <button type="button" class="car-action-btn" onclick="copyCurrentCarLink(this)" title="Sao chép liên kết">
                        <i class="fa-regular fa-copy"></i>
                        <span>Copy Link</span>
                    </button>
                    <a href="{{ route('inventory.index', ['make' => $car->make_slug]) }}" class="car-action-btn" title="Xem thêm xe cùng hãng">
                        <img src="{{ asset('boxcar/images/resource/share1-1.svg') }}" alt="Kho xe">
                        <span>Cùng hãng</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Hero Section: Gallery (Left) + Pricing & Action Hub (Right) -->
        <div class="car-detail-hero-grid">
            <!-- Gallery Section -->
            <div class="car-detail-card p-0 overflow-hidden mb-0">
                <div class="car-gallery-box">
                    <div class="car-gallery-featured" id="main-preview-container">
                        <div class="car-gallery-badge-overlay">
                            <span class="car-badge-item car-badge-condition-{{ $car->condition === 'new' ? 'new' : ($car->condition === 'cpo' ? 'cpo' : 'used') }} bg-white">
                                {{ $car->condition_label }}
                            </span>
                        </div>

                        <a href="{{ $firstImageUrl }}" data-fancybox="car-gallery-lightbox" id="main-preview-link" title="Xem ảnh lớn">
                            <img src="{{ $firstImageUrl }}" id="main-preview-img" alt="{{ $car->make_name }} {{ $car->model_name }} {{ $car->trim_name }}">
                        </a>

                        <div class="car-gallery-action-overlay">
                            <a href="#booking-consultation-section" class="car-gallery-btn">
                                <i class="fa-regular fa-calendar-check"></i> Đặt lịch lái thử
                            </a>
                            <a href="{{ $firstImageUrl }}" data-fancybox="car-gallery-lightbox" class="car-gallery-btn">
                                <i class="fa-regular fa-images"></i> Toàn bộ {{ $imageMedia->count() }} ảnh
                            </a>
                        </div>
                    </div>

                    @if ($imageMedia->count() > 1)
                        <div class="car-gallery-thumbs" id="car-gallery-thumbs">
                            @foreach ($imageMedia as $index => $img)
                                <div class="car-gallery-thumb-item {{ $index === 0 ? 'active' : '' }}" onclick="switchMainImage('{{ $img->url }}', this)">
                                    <img src="{{ $img->url }}" alt="{{ $car->stock_code }} - Ảnh {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transaction & Conversion Hub -->
            <div class="car-hero-transaction-box">
                <!-- Pricing & CTA Card -->
                <div class="car-price-card">
                    <div class="car-price-eyebrow">
                        <span class="car-price-label">Giá bán niêm yết</span>
                        @if ($car->status === 'available')
                            <span class="car-status-pill"><i class="fa-solid fa-circle-check"></i> Sẵn sàng giao</span>
                        @else
                            <span class="car-status-pill on-hold"><i class="fa-regular fa-clock"></i> Đang giữ chỗ</span>
                        @endif
                    </div>

                    <div class="car-main-price">{{ $car->formatted_price }}</div>
                    <div class="car-price-note">Đã bao gồm thuế VAT • Hỗ trợ hoàn tất hồ sơ đăng ký sang tên trọn gói.</div>

                    @if ($rawPrice > 0)
                        <div class="car-installment-hint">
                            <i class="fa-solid fa-calculator"></i>
                            <span>Trả trước từ ~{{ number_format($rawPrice * 0.2, 0, ',', '.') }} VNĐ (góp từ ~{{ $estimatedMonthly }} VNĐ/tháng)</span>
                        </div>
                    @endif

                    <div class="car-cta-group">
                        <a href="#booking-consultation-section" onclick="switchBookingType('drive')" class="btn-cta-primary">
                            <i class="fa-solid fa-steering-wheel"></i>
                            <span>Đặt Lịch Lái Thử & Xem Xe</span>
                        </a>
                        <a href="#booking-consultation-section" onclick="switchBookingType('quote')" class="btn-cta-secondary">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Nhận Báo Giá Lăn Bánh</span>
                        </a>
                    </div>

                    <div class="car-quick-contact-grid">
                        <a href="tel:{{ $phoneDigits }}" class="btn-quick-call">
                            <i class="fa-solid fa-phone-volume text-primary"></i>
                            <span>{{ $showroomPhone }}</span>
                        </a>
                        <a href="{{ $zaloUrl }}" target="_blank" rel="noopener" class="btn-quick-zalo">
                            <i class="fa-solid fa-comment-dots"></i>
                            <span>Chat Zalo 24/7</span>
                        </a>
                    </div>
                </div>

                <!-- Showroom Direct Info -->
                <div class="car-sidebar-showroom-box">
                    <div class="car-showroom-header">
                        <div class="car-showroom-avatar">
                            <i class="fa-solid fa-car-rear"></i>
                        </div>
                        <div>
                            <h6 class="car-showroom-name">{{ $showroomName }}</h6>
                            <span class="car-showroom-hours"><i class="fa-solid fa-circle-dot text-success me-1"></i> Mở cửa 08:00 - 20:00</span>
                        </div>
                    </div>

                    <div class="car-showroom-address">
                        <i class="fa-solid fa-location-dot me-1 text-muted"></i> {{ $showroomAddress }}
                    </div>

                    <a href="{{ $mapsDirectionsUrl }}" target="_blank" rel="noopener" class="car-showroom-map-link">
                        <i class="fa-solid fa-location-arrow"></i> Xem chỉ đường Google Maps <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 11px;"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. Car Overview Grid (Full-Width) -->
        <div class="car-detail-card">
            <div class="car-detail-card-title">
                <span><i class="fa-solid fa-list-check text-primary me-2"></i> Tổng Quan Thông Số Xe</span>
                <span class="title-accent">Mã xe: {{ $car->stock_code }}</span>
            </div>

            <div class="car-overview-grid">
                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-1.svg') }}" alt="Kiểu dáng">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Kiểu dáng</span>
                        <span class="car-overview-val">{{ $car->body_type_name ?? 'Đang cập nhật' }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-2.svg') }}" alt="Số km">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Số km đã đi</span>
                        <span class="car-overview-val">{{ $car->mileage ? number_format((float) $car->mileage, 0, ',', '.') . ' km' : 'Odo lướt' }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-3.svg') }}" alt="Nhiên liệu">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Nhiên liệu</span>
                        <span class="car-overview-val">{{ $car->fuel_label }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-4.svg') }}" alt="Năm sản xuất">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Năm sản xuất</span>
                        <span class="car-overview-val">{{ $car->year }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-5.svg') }}" alt="Hộp số">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Hộp số</span>
                        <span class="car-overview-val">{{ $car->transmission_label }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-6.svg') }}" alt="Hệ dẫn động">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Hệ dẫn động</span>
                        <span class="car-overview-val">{{ $car->drivetrain_name ?? 'Đang cập nhật' }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-7.svg') }}" alt="Tình trạng">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Tình trạng kiểm định</span>
                        <span class="car-overview-val">{{ $car->condition_label }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-8.svg') }}" alt="Màu ngoại thất">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Màu ngoại thất</span>
                        <span class="car-overview-val">{{ $car->exterior_color_name ?? 'Đang cập nhật' }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-9.svg') }}" alt="Màu nội thất">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Màu nội thất</span>
                        <span class="car-overview-val">{{ $car->interior_color_name ?? 'Đang cập nhật' }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-10.svg') }}" alt="Mã kho">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Mã kho lưu trữ</span>
                        <span class="car-overview-val">{{ $car->stock_code }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-11.svg') }}" alt="Phiên bản">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Phiên bản</span>
                        <span class="car-overview-val">{{ $car->trim_name }}</span>
                    </div>
                </div>

                <div class="car-overview-item">
                    <div class="car-overview-icon">
                        <img src="{{ asset('boxcar/images/resource/insep1-12.svg') }}" alt="Số VIN">
                    </div>
                    <div class="car-overview-text">
                        <span class="car-overview-label">Số khung VIN</span>
                        <span class="car-overview-val font-monospace">{{ $car->vin ?? 'Đang cập nhật' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Description & Features (Balanced 2-Column Row) -->
        <div class="car-desc-feature-grid">
            <!-- Description Card -->
            <div class="car-detail-card mb-0">
                <div class="car-detail-card-title">
                    <span><i class="fa-solid fa-align-left text-primary me-2"></i> Giới Thiệu & Mô Tả Chi Tiết</span>
                </div>

                <div style="font-size: 14.5px; color: #334155; line-height: 1.75; margin-bottom: 16px;">
                    <p style="margin-bottom: 12px; font-weight: 500;">{{ $descriptionLead }}</p>
                    <p style="margin: 0; color: #64748B;">{{ $descriptionTail }}</p>
                </div>

                <a href="{{ route('trim.show', $car->trim_slug) }}" class="car-trim-banner">
                    <div class="car-trim-banner-content">
                        <h5>Xem Hồ Sơ Chi Tiết Phiên Bản {{ $car->trim_name }}</h5>
                        <p>Khám phá toàn bộ thông số nền tảng, bài đánh giá chuyên sâu và so sánh các xe cùng phiên bản.</p>
                    </div>
                    <div class="car-trim-banner-btn">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>
            </div>

            <!-- Features & Amenities -->
            <div class="car-detail-card mb-0">
                <div class="car-detail-card-title">
                    <span><i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i> Trang Bị & Tiện Nghi Nổi Bật</span>
                </div>

                <div class="car-features-grid">
                    @forelse ($features as $groupName => $groupFeatures)
                        <div class="feature-group-box">
                            <h6 class="feature-group-title">
                                <i class="fa-solid fa-layer-group"></i> {{ $groupName }}
                            </h6>
                            <ul class="feature-item-list">
                                @foreach ($groupFeatures as $feature)
                                    <li><i class="fa-solid fa-circle-check"></i> <span>{{ $feature->name }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light border text-muted">
                                Thông tin trang bị chi tiết đang được chuyên viên cập nhật theo danh mục phụ kiện thực tế của xe.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 5. Technical Specifications Table (Full-Width) -->
        <div class="car-detail-card">
            <div class="car-detail-card-title">
                <span><i class="fa-solid fa-gauge-high text-primary me-2"></i> Bảng Thông Số Kỹ Thuật Chi Tiết</span>
            </div>

            <div class="car-specs-table-wrap">
                <table class="car-specs-table">
                    <tbody>
                        @forelse ($attributes as $attribute)
                            <tr>
                                <td class="spec-prop">{{ $attribute->label }}</td>
                                <td class="spec-val">{{ $attribute->display_value ?? 'Đang cập nhật' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">
                                    Hồ sơ thông số kỹ thuật tiêu chuẩn đang được cập nhật từ nhà sản xuất.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. Interactive Loan Financing Calculator Widget (Full-Width) -->
        <div class="car-detail-card car-calc-widget" id="car-financing-calculator">
            <div class="car-detail-card-title">
                <span><i class="fa-solid fa-calculator text-primary me-2"></i> Bảng Tính Dự Toán Vay Trả Góp</span>
                <span class="title-accent"><i class="fa-solid fa-bolt"></i> Tính toán thời gian thực</span>
            </div>

            <div class="car-calc-layout">
                <!-- Controls -->
                <div class="car-calc-controls">
                    <div class="calc-field-group">
                        <label for="calc-car-price">
                            Giá xe tham khảo
                            <span id="label-calc-price">{{ $car->formatted_price }}</span>
                        </label>
                        <div class="calc-input-wrap">
                            <input type="number" id="calc-car-price" value="{{ $rawPrice > 0 ? $rawPrice : 1500000000 }}" step="10000000" oninput="calculateLoanPayment()">
                            <span class="calc-input-suffix">VNĐ</span>
                        </div>
                    </div>

                    <div class="calc-field-group">
                        <label for="calc-down-payment">
                            Số tiền trả trước ban đầu
                            <span id="label-down-payment">20%</span>
                        </label>
                        <div class="calc-input-wrap">
                            <select id="calc-down-payment" onchange="calculateLoanPayment()">
                                <option value="10">10% giá trị xe</option>
                                <option value="15">15% giá trị xe</option>
                                <option value="20" selected>20% giá trị xe (Khuyên dùng)</option>
                                <option value="30">30% giá trị xe</option>
                                <option value="40">40% giá trị xe</option>
                                <option value="50">50% giá trị xe</option>
                                <option value="70">70% giá trị xe</option>
                            </select>
                        </div>
                    </div>

                    <div class="calc-field-group">
                        <label for="calc-loan-period">
                            Thời hạn vay mua xe
                            <span id="label-loan-period">60 tháng (5 năm)</span>
                        </label>
                        <div class="calc-input-wrap">
                            <select id="calc-loan-period" onchange="calculateLoanPayment()">
                                <option value="12">12 tháng (1 năm)</option>
                                <option value="24">24 tháng (2 năm)</option>
                                <option value="36">36 tháng (3 năm)</option>
                                <option value="48">48 tháng (4 năm)</option>
                                <option value="60" selected>60 tháng (5 năm - Tiêu chuẩn)</option>
                                <option value="72">72 tháng (6 năm)</option>
                                <option value="84">84 tháng (7 năm)</option>
                                <option value="96">96 tháng (8 năm)</option>
                            </select>
                        </div>
                    </div>

                    <div class="calc-field-group">
                        <label for="calc-interest-rate">
                            Lãi suất năm (%/năm)
                            <span id="label-interest-rate">8.0%/năm</span>
                        </label>
                        <div class="calc-input-wrap">
                            <input type="number" id="calc-interest-rate" value="8.0" step="0.1" min="5" max="18" oninput="calculateLoanPayment()">
                            <span class="calc-input-suffix">%/năm</span>
                        </div>
                    </div>
                </div>

                <!-- Live Calculation Results -->
                <div class="car-calc-result-card">
                    <div>
                        <div class="calc-result-header">
                            <span>Ước Tính Trả Góp Tháng Đầu</span>
                            <div class="calc-monthly-number" id="calc-monthly-result">-- VNĐ</div>
                            <div class="calc-monthly-unit">*Dư nợ giảm dần theo thời gian thực tế</div>
                        </div>

                        <ul class="calc-result-details">
                            <li>
                                <span>Tiền trả trước ban đầu:</span>
                                <strong id="calc-down-amount">-- VNĐ</strong>
                            </li>
                            <li>
                                <span>Tổng số tiền vay:</span>
                                <strong id="calc-loan-amount">-- VNĐ</strong>
                            </li>
                            <li>
                                <span>Tổng tiền lãi dự tính:</span>
                                <strong id="calc-total-interest">-- VNĐ</strong>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <a href="#booking-consultation-section" onclick="prefillBookingInquiry('tư vấn gói vay trả góp')" class="calc-cta-btn">
                            <i class="fa-solid fa-file-signature me-1"></i> Áp Dụng Phương Án Này Vào Báo Giá
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Unified Consultation & Test Drive Booking (Full-Width) -->
        <div class="car-detail-card" id="booking-consultation-section">
            <div class="car-detail-card-title">
                <span><i class="fa-solid fa-calendar-check text-primary me-2"></i> Đặt Lịch Lái Thử & Tư Vấn Báo Giá</span>
                <span class="title-accent"><i class="fa-solid fa-shield-halved text-success"></i> Cam kết bảo mật thông tin</span>
            </div>

            <!-- Tab Switcher -->
            <div class="car-booking-tabs">
                <button type="button" class="car-booking-tab-btn active" id="tab-btn-drive" onclick="switchBookingType('drive')">
                    <i class="fa-solid fa-steering-wheel me-1"></i> 1. Đặt Lịch Lái Thử & Trải Nghiệm Xe
                </button>
                <button type="button" class="car-booking-tab-btn" id="tab-btn-quote" onclick="switchBookingType('quote')">
                    <i class="fa-solid fa-file-invoice-dollar me-1"></i> 2. Nhận Bảng Báo Giá Lăn Bánh Chi Tiết
                </button>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-4">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Drive Appointment Form (Default) -->
            <form method="POST" action="{{ route('appointments.store') }}" id="form-drive-booking">
                @csrf
                <input type="hidden" name="source" value="unit_detail">
                <input type="hidden" name="car_unit_id" value="{{ $car->id }}">
                <input type="hidden" name="trim_id" value="{{ $car->trim_id }}">

                <div class="booking-form-grid">
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
                        <label>Thời gian mong muốn trải nghiệm <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" min="{{ now()->addHour()->format('Y-m-d\\TH:i') }}" required>
                    </div>

                    <div class="booking-input-group full-col">
                        <label>Nhu cầu hoặc ghi chú đặc biệt</label>
                        <textarea name="message" placeholder="Ví dụ: Tôi muốn lái thử xe vào chiều thứ 7 tuần này tại showroom, tư vấn thêm màu sắc nội thất...">{{ old('message') }}</textarea>
                    </div>

                    <div class="full-col text-end pt-2">
                        <button type="submit" class="booking-submit-btn w-100 w-md-auto">
                            <span>Xác Nhận Đặt Lịch Lái Thử Ngay</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quote Inquiry Form (Hidden by default) -->
            <form method="POST" action="{{ route('lead.store') }}" id="form-quote-inquiry" style="display: none;">
                @csrf
                <input type="hidden" name="source" value="unit_detail">
                <input type="hidden" name="car_unit_id" value="{{ $car->id }}">
                <input type="hidden" name="trim_id" value="{{ $car->trim_id }}">

                <div class="booking-form-grid">
                    <div class="booking-input-group">
                        <label>Họ và tên quý khách <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ $defaultName }}" placeholder="Ví dụ: Trần Thị Mai" required>
                    </div>

                    <div class="booking-input-group">
                        <label>Số điện thoại nhận báo giá <span class="text-danger">*</span></label>
                        <input type="text" name="phone" value="{{ $defaultPhone }}" placeholder="0901 234 567" required>
                    </div>

                    <div class="booking-input-group">
                        <label>Địa chỉ Email nhận bảng dự toán</label>
                        <input type="email" name="email" value="{{ $defaultEmail }}" placeholder="example@email.com">
                    </div>

                    <div class="booking-input-group">
                        <label>Tỉnh / Thành phố đăng ký biển số</label>
                        <input type="text" placeholder="Ví dụ: TP. Hồ Chí Minh, Hà Nội, Bình Dương...">
                    </div>

                    <div class="booking-input-group full-col">
                        <label>Nội dung cần chuyên viên hỗ trợ</label>
                        <textarea name="message" id="quote-message-textarea" placeholder="Ví dụ: Vui lòng gửi dự toán chi phí lăn bánh, mức trả góp tối ưu và chương trình khuyến mãi tháng này.">{{ old('message') }}</textarea>
                    </div>

                    <div class="full-col text-end pt-2">
                        <button type="submit" class="booking-submit-btn w-100 w-md-auto">
                            <span>Gửi Yêu Cầu Nhận Báo Giá Lăn Bánh</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 8. Showroom Location Map & Verified Customer Reviews (Balanced 2-Column Row) -->
        <div class="car-map-reviews-grid">
            <!-- Location Map -->
            <div class="car-detail-card mb-0">
                <div class="car-detail-card-title">
                    <span><i class="fa-solid fa-location-dot text-primary me-2"></i> Địa Điểm Trưng Bày & Trải Nghiệm Xe</span>
                    <a href="{{ $mapsDirectionsUrl }}" target="_blank" rel="noopener" class="title-accent">
                        <i class="fa-solid fa-diamond-turn-right"></i> Xem chỉ đường
                    </a>
                </div>

                <div style="font-size: 14px; color: #475569; margin-bottom: 18px;">
                    <strong class="text-dark font-semibold d-block mb-1">{{ $showroomName }}</strong>
                    <p class="mb-2"><i class="fa-solid fa-map-pin text-danger me-1"></i> {{ $showroomAddress }}</p>
                    <p class="mb-0 text-muted"><i class="fa-regular fa-clock me-1 text-success"></i> Giờ mở cửa: 08:00 - 20:00 (Hàng ngày)</p>
                </div>

                <div style="border-radius: 14px; overflow: hidden; height: 260px; border: 1px solid #E2E8F0;">
                    <iframe src="{{ $mapsEmbedUrl }}" width="100%" height="100%" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <!-- Customer Reviews Section -->
            <div class="car-detail-card mb-0">
                <div class="car-detail-card-title">
                    <span><i class="fa-solid fa-star text-warning me-2"></i> Đánh Giá Từ Khách Hàng Đã Trải Nghiệm</span>
                    <span class="title-accent">{{ $reviews->count() }} nhận xét xác thực</span>
                </div>

                <div class="car-review-rating-box" style="margin-bottom: 16px; padding: 14px;">
                    <div class="rating-score-badge" style="flex: 0 0 90px;">
                        <strong style="font-size: 34px;">{{ $reviewAverage }}</strong>
                        <span style="font-size: 11.5px;">Thang điểm 5.0</span>
                        <div class="rating-stars" style="font-size: 12px;">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>

                    <div style="flex: 1; border-left: 1px solid #E2E8F0; padding-left: 16px;">
                        <h6 style="font-size: 13.5px; font-weight: 700; color: #050B20; margin-bottom: 2px;">Độ hài lòng chung tuyệt đối</h6>
                        <p style="font-size: 12px; color: #64748B; margin: 0;">100% đánh giá xác thực từ khách hàng mua xe hoặc lái thử tại showroom.</p>
                    </div>
                </div>

                <div class="car-reviews-list" style="max-height: 250px; overflow-y: auto; padding-right: 6px;">
                    @forelse ($reviews as $review)
                        <div class="review-item-card mb-2 p-3">
                            <div class="review-author-row mb-1">
                                <div class="review-author-info">
                                    <div class="review-avatar" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($review->user_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="review-author-name" style="font-size: 13px;">
                                            {{ $review->user_name }}
                                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 10px;">
                                                <i class="fa-solid fa-check-circle"></i> Đã mua xe
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div style="color: #F59E0B; font-size: 11px;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o text-muted' }}"></i>
                                    @endfor
                                </div>
                            </div>

                            <div class="review-text-content" style="font-size: 13px;">
                                {{ $review->comment }}
                            </div>
                        </div>
                    @empty
                        <div class="p-3 rounded-xl border border-dashed text-center text-muted">
                            <i class="fa-regular fa-comment-dots fs-4 d-block mb-1 text-secondary"></i>
                            <span style="font-size: 12.5px;">Chưa có đánh giá công khai cho phiên bản này.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 9. Related Vehicles Section (Full-Width) -->
        <div class="car-related-section" style="margin-top: 36px;">
            <div class="car-related-header">
                <div>
                    <h3>Xe Cùng Phân Khúc Đang Có Sẵn Tại Kho</h3>
                    <p class="text-muted mb-0 mt-1" style="font-size: 14px;">Gợi ý các mẫu xe tuyển chọn tương đồng về tầm giá và đẳng cấp vận hành.</p>
                </div>
                <a href="{{ route('inventory.index', ['make' => $car->make_slug]) }}">
                    <span>Xem tất cả kho xe {{ $car->make_name }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="row car-slider-three" data-preview="4">
                @forelse ($relatedCars as $related)
                    @include('client.partials.related-car-card', ['car' => $related])
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border text-center py-4 text-muted">
                            Hiện kho xe chưa có xe cùng phân khúc. Quý khách vui lòng tham khảo các mẫu xe khác trong danh mục.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Gallery Image Switcher
    function switchMainImage(url, thumbElement) {
        const previewImg = document.getElementById('main-preview-img');
        const previewLink = document.getElementById('main-preview-link');
        if (previewImg && previewLink) {
            previewImg.src = url;
            previewLink.href = url;
        }

        const thumbs = document.querySelectorAll('.car-gallery-thumb-item');
        thumbs.forEach(t => t.classList.remove('active'));
        if (thumbElement) {
            thumbElement.classList.add('active');
        }
    }

    // Copy link helper
    function copyCurrentCarLink(btn) {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check text-success"></i> <span>Đã chép!</span>';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
            }, 2000);
        }).catch(() => {
            alert('Đã sao chép liên kết vào bộ nhớ tạm: ' + url);
        });
    }

    // Booking Type Switcher
    function switchBookingType(type) {
        const btnDrive = document.getElementById('tab-btn-drive');
        const btnQuote = document.getElementById('tab-btn-quote');
        const formDrive = document.getElementById('form-drive-booking');
        const formQuote = document.getElementById('form-quote-inquiry');

        if (type === 'drive') {
            btnDrive.classList.add('active');
            btnQuote.classList.remove('active');
            formDrive.style.display = 'block';
            formQuote.style.display = 'none';
        } else {
            btnQuote.classList.add('active');
            btnDrive.classList.remove('active');
            formQuote.style.display = 'block';
            formDrive.style.display = 'none';
        }
    }

    function prefillBookingInquiry(topic) {
        switchBookingType('quote');
        const textarea = document.getElementById('quote-message-textarea');
        if (textarea) {
            textarea.value = 'Tôi quan tâm đến gói ' + topic + ' cho chiếc {{ $car->make_name }} {{ $car->model_name }} (Mã xe: {{ $car->stock_code }}). Vui lòng gửi bảng dự toán chi tiết.';
        }
    }

    // Loan Financing Calculator Logic
    function calculateLoanPayment() {
        const priceInput = document.getElementById('calc-car-price');
        const downPercentSelect = document.getElementById('calc-down-payment');
        const periodSelect = document.getElementById('calc-loan-period');
        const interestInput = document.getElementById('calc-interest-rate');

        if (!priceInput || !downPercentSelect || !periodSelect || !interestInput) return;

        const carPrice = parseFloat(priceInput.value) || 0;
        const downPercent = parseFloat(downPercentSelect.value) || 20;
        const loanMonths = parseInt(periodSelect.value) || 60;
        const annualRate = parseFloat(interestInput.value) || 8.0;

        const downPaymentAmount = carPrice * (downPercent / 100);
        const loanAmount = carPrice - downPaymentAmount;

        // Monthly interest rate
        const monthlyRate = (annualRate / 100) / 12;

        let monthlyPayment = 0;
        let totalInterest = 0;

        if (loanAmount > 0 && monthlyRate > 0 && loanMonths > 0) {
            // Amortization formula: M = P * [r(1+r)^n] / [(1+r)^n - 1]
            const factor = Math.pow(1 + monthlyRate, loanMonths);
            monthlyPayment = loanAmount * (monthlyRate * factor) / (factor - 1);
            totalInterest = (monthlyPayment * loanMonths) - loanAmount;
        }

        // Update labels and outputs
        const labelDown = document.getElementById('label-down-payment');
        if (labelDown) {
            labelDown.textContent = downPercent + '% (' + formatVND(downPaymentAmount) + ')';
        }

        const labelPeriod = document.getElementById('label-loan-period');
        if (labelPeriod) {
            const years = (loanMonths / 12).toFixed(loanMonths % 12 === 0 ? 0 : 1);
            labelPeriod.textContent = loanMonths + ' tháng (' + years + ' năm)';
        }

        const labelRate = document.getElementById('label-interest-rate');
        if (labelRate) {
            labelRate.textContent = annualRate.toFixed(1) + '%/năm';
        }

        const monthlyResult = document.getElementById('calc-monthly-result');
        if (monthlyResult) {
            monthlyResult.textContent = formatVND(monthlyPayment) + '/tháng';
        }

        const downAmountEl = document.getElementById('calc-down-amount');
        if (downAmountEl) {
            downAmountEl.textContent = formatVND(downPaymentAmount);
        }

        const loanAmountEl = document.getElementById('calc-loan-amount');
        if (loanAmountEl) {
            loanAmountEl.textContent = formatVND(loanAmount);
        }

        const totalInterestEl = document.getElementById('calc-total-interest');
        if (totalInterestEl) {
            totalInterestEl.textContent = formatVND(totalInterest);
        }
    }

    function formatVND(num) {
        if (!num || isNaN(num) || num < 0) return '0 VNĐ';
        return Math.round(num).toLocaleString('vi-VN') + ' VNĐ';
    }

    // Run once on DOM loaded
    document.addEventListener('DOMContentLoaded', function () {
        calculateLoanPayment();
    });
</script>
@endpush
@endsection
