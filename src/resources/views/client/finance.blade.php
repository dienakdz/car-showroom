@extends('client.layouts.page')

@section('title', 'Dự Toán Tài Chính & Gói Vay Trả Góp Ưu Đãi')

@section('content')
@php
    $showroomName = $showroom->name ?? 'BoxCar Showroom';
    $showroomPhone = $showroom->phone ?? '0900 000 000';
    $cleanPhone = preg_replace('/\D+/', '', (string) $showroomPhone);

    // Find default selected car (first car from inventory)
    $firstCar = $availableCars->first();
    $defaultPrice = $firstCar ? (float) $firstCar->price : 1200000000;
@endphp

<section class="client-page-wrap">
    <div class="boxcar-container">
        <!-- 1. Header & Breadcrumb -->
        <div class="client-header-intro">
            <ul class="client-breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><span>/</span></li>
                <li><span>Dự toán tài chính</span></li>
            </ul>
            <h1 class="client-main-title">DỰ TOÁN TÀI CHÍNH & GÓI VAY TRẢ GÓP ƯU ĐÃI</h1>
            <p class="client-main-desc">
                Sở hữu chiếc xe mơ ước dễ dàng với hạn mức vay đến 85% giá trị xe, lãi suất ưu đãi từ các ngân hàng đối tác hàng đầu cùng thủ tục phê duyệt siêu tốc trong 24 giờ.
            </p>
        </div>

        <!-- 2. Interactive Loan Calculator (BoxCar loan-calculator style) -->
        <div class="finance-calc-wrap">
            <!-- Left: Controls -->
            <div class="boxcar-white-card">
                <div class="client-card-heading-box">
                    <h3>Công Cụ Tính Toán Khoản Vay Trực Quan</h3>
                    <p>Điều chỉnh giá xe, tỷ lệ trả trước và thời hạn vay để xem ngay số tiền cần trả hàng tháng.</p>
                </div>

                <!-- 1. Chọn xe hoặc nhập giá xe -->
                <div class="client-form-group">
                    <label for="calc-car-select">Chọn mẫu xe từ kho xe Showroom</label>
                    <select id="calc-car-select" class="form-control">
                        @if ($availableCars->isNotEmpty())
                            @foreach ($availableCars as $car)
                                <option value="{{ $car->id }}" data-price="{{ (float) $car->price }}" data-label="{{ $car->label }}">
                                    {{ $car->label }} — {{ number_format($car->price) }} {{ $car->currency ?? 'VNĐ' }}
                                </option>
                            @endforeach
                        @else
                            <option value="" data-price="1200000000" data-label="Mẫu xe mẫu (1.200.000.000 VNĐ)">Mẫu xe mẫu (1.200.000.000 VNĐ)</option>
                        @endif
                    </select>
                </div>

                <!-- 2. Giá trị xe -->
                <div class="calc-slider-box">
                    <div class="calc-slider-head">
                        <label for="calc-price-slider">Giá trị xe dự kiến (VNĐ)</label>
                        <span class="calc-slider-val" id="calc-car-price-display">{{ number_format($defaultPrice) }} ₫</span>
                    </div>
                    <input type="range" class="calc-range-slider" id="calc-price-slider" min="300000000" max="10000000000" step="50000000" value="{{ $defaultPrice }}">
                </div>

                <!-- 3. Tỷ lệ trả trước -->
                <div class="calc-slider-box">
                    <div class="calc-slider-head">
                        <label>Tỷ lệ trả trước (Vốn tự có)</label>
                        <span class="calc-slider-val" id="calc-downpayment-display">20% (<span id="calc-downpayment-amount">0 ₫</span>)</span>
                    </div>
                    <input type="range" class="calc-range-slider" id="calc-downpayment-slider" min="15" max="80" step="5" value="20">
                    <div class="calc-chips-group">
                        <button type="button" class="calc-chip-btn is-active" data-val="20">Trả trước 20%</button>
                        <button type="button" class="calc-chip-btn" data-val="30">Trả trước 30%</button>
                        <button type="button" class="calc-chip-btn" data-val="50">Trả trước 50%</button>
                        <button type="button" class="calc-chip-btn" data-val="70">Trả trước 70%</button>
                    </div>
                </div>

                <!-- 4. Thời hạn vay -->
                <div class="calc-slider-box">
                    <div class="calc-slider-head">
                        <label>Thời hạn vay</label>
                        <span class="calc-slider-val" id="calc-tenure-display">60 tháng (5 năm)</span>
                    </div>
                    <input type="range" class="calc-range-slider" id="calc-tenure-slider" min="12" max="84" step="12" value="60">
                    <div class="calc-chips-group">
                        <button type="button" class="calc-chip-btn" data-val="24">24 tháng</button>
                        <button type="button" class="calc-chip-btn" data-val="36">36 tháng</button>
                        <button type="button" class="calc-chip-btn" data-val="48">48 tháng</button>
                        <button type="button" class="calc-chip-btn is-active" data-val="60">60 tháng</button>
                        <button type="button" class="calc-chip-btn" data-val="84">84 tháng</button>
                    </div>
                </div>

                <!-- 5. Lãi suất ước tính -->
                <div class="calc-slider-box mb-0">
                    <div class="calc-slider-head">
                        <label>Lãi suất vay ưu đãi (%/năm)</label>
                        <span class="calc-slider-val" id="calc-rate-display">7.5% / năm</span>
                    </div>
                    <input type="range" class="calc-range-slider" id="calc-rate-slider" min="5" max="14" step="0.1" value="7.5">
                    <small class="calc-slider-note">Lãi suất cố định trung bình từ các ngân hàng đối tác trong 12 - 24 tháng đầu.</small>
                </div>
            </div>

            <!-- Right: Real-time Financial Breakdown Card -->
            <div class="calc-result-box">
                <span class="calc-result-label">Ước tính trả góp hàng tháng</span>
                <div class="calc-result-monthly" id="res-monthly-payment">0 ₫ / tháng</div>
                <div class="calc-result-sub">*Tính theo phương thức dư nợ giảm dần, số tiền trả sẽ giảm theo từng tháng.</div>

                <div class="calc-breakdown-list">
                    <div class="calc-breakdown-item">
                        <span class="label">Giá trị xe</span>
                        <span class="val" id="res-car-price">0 ₫</span>
                    </div>
                    <div class="calc-breakdown-item">
                        <span class="label">Số tiền trả trước ban đầu</span>
                        <span class="val" id="res-downpayment">0 ₫</span>
                    </div>
                    <div class="calc-breakdown-item">
                        <span class="label">Số tiền vay ngân hàng</span>
                        <span class="val" id="res-loan-amount">0 ₫</span>
                    </div>
                    <div class="calc-breakdown-item">
                        <span class="label">Thời hạn gói vay</span>
                        <span class="val" id="res-tenure">60 tháng</span>
                    </div>
                    <div class="calc-breakdown-item">
                        <span class="label">Lãi suất áp dụng</span>
                        <span class="val" id="res-interest-rate">7.5% / năm</span>
                    </div>
                </div>

                <a href="#finance-lead-form" class="client-submit-btn js-apply-calc-to-form">
                    <span>NHẬN BẢNG TÍNH CHI TIẾT & TƯ VẤN VAY</span>
                    <i class="fa-solid fa-arrow-down ms-2"></i>
                </a>

                <div class="calc-bank-partners">
                    <div class="calc-bank-title">Ngân hàng đối tác chiến lược</div>
                    <div class="calc-bank-logos">
                        <span class="calc-bank-badge">Vietcombank</span>
                        <span class="calc-bank-badge">Techcombank</span>
                        <span class="calc-bank-badge">MB Bank</span>
                        <span class="calc-bank-badge">VPBank</span>
                        <span class="calc-bank-badge">TPBank</span>
                        <span class="calc-bank-badge">Shinhan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Form Đăng Ký Tư Vấn Gói Vay Tối Ưu -->
        <div id="finance-lead-form" class="boxcar-white-card wow fadeInUp client-section-spacer">
            <div class="client-card-heading-box">
                <h3>Đăng Ký Nhận Hồ Sơ Gói Vay & Bảng Tính Ngân Hàng</h3>
                <p>Chuyên viên tín dụng ngân hàng đối tác sẽ liên hệ gửi bảng sao kê chi tiết từng tháng và hỗ trợ làm hồ sơ vay nhanh nhất.</p>
            </div>

            @if (session('success'))
                <div class="client-alert-banner is-success wow fadeInUp">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="client-alert-banner is-error wow fadeInUp">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('lead.store') }}" method="POST">
                @csrf
                <input type="hidden" name="source" value="finance">

                <div class="row">
                    <div class="col-md-4">
                        <div class="client-form-group">
                            <label for="lead-car-id">Xe cần làm hồ sơ trả góp <span class="text-danger">*</span></label>
                            <select id="lead-car-id" name="car_unit_id" class="form-control" required>
                                @foreach ($availableCars as $car)
                                    <option value="{{ $car->id }}" {{ old('car_unit_id') == $car->id ? 'selected' : '' }}>
                                        {{ $car->label }} - {{ number_format($car->price) }} {{ $car->currency ?? 'VNĐ' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="client-form-group">
                            <label for="finance-name">Họ và tên quý khách <span class="text-danger">*</span></label>
                            <input type="text" id="finance-name" name="name" class="form-control" placeholder="Ví dụ: Trần Minh Hoàng" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="client-form-group">
                            <label for="finance-phone">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" id="finance-phone" name="phone" class="form-control" placeholder="Ví dụ: 0987 654 321" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="client-form-group">
                            <label for="finance-email">Email (Nhận bảng tính file PDF)</label>
                            <input type="email" id="finance-email" name="email" class="form-control" placeholder="email@domain.com" value="{{ old('email', auth()->user()->email ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="client-form-group">
                            <label for="finance-bank-pref">Ngân hàng quý khách ưu tiên</label>
                            <select id="finance-bank-pref" class="form-control">
                                <option value="any">Tư vấn gói lãi suất thấp nhất thị trường</option>
                                <option value="vietcombank">Vietcombank</option>
                                <option value="techcombank">Techcombank</option>
                                <option value="mb">MB Bank</option>
                                <option value="vpbank">VPBank</option>
                                <option value="shinhan">Shinhan Bank</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="client-form-group">
                    <label for="finance-message">Ghi chú nhu cầu vay cụ thể</label>
                    <textarea id="finance-message" name="message" class="form-control" rows="4" placeholder="Ví dụ: Tôi muốn vay 70% giá trị xe trong 5 năm, cần tư vấn thủ tục chứng minh thu nhập...">{{ old('message') }}</textarea>
                </div>

                <div class="finance-submit-wrap">
                    <button type="submit" class="finance-submit-btn">
                        <span>GỬI YÊU CẦU DỰ TOÁN & TƯ VẤN TÍN DỤNG</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <div class="finance-security-note">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Cam kết bảo mật thông tin tuyệt đối. Bảng tính chi tiết sẽ được gửi trong 15 phút.</span>
                    </div>
                </div>
            </form>
        </div>

        <!-- 4. Quy trình 4 Bước Duyệt Vay Trả Góp -->
        <div class="client-section-spacer">
            <div class="client-section-heading wow fadeInUp">
                <h2>Quy Trình 4 Bước Mua Xe Trả Góp Đơn Giản</h2>
                <div class="text">Đơn giản hóa thủ tục ngân hàng, hỗ trợ duyệt hồ sơ nhanh gọn trong 24 giờ</div>
            </div>

            <div class="finance-process-grid wow fadeInUp" data-wow-delay="100ms">
                <!-- Step 1 -->
                <div class="finance-step-card">
                    <div class="finance-step-num">1</div>
                    <h4 class="finance-step-title">Chọn Xe & Phương Án Vay</h4>
                    <p class="finance-step-text">Quý khách chọn chiếc xe ưng ý, chuyên viên tài chính tư vấn gói vay có tỷ lệ trả trước và kỳ hạn phù hợp nhất.</p>
                </div>

                <!-- Step 2 -->
                <div class="finance-step-card">
                    <div class="finance-step-num">2</div>
                    <h4 class="finance-step-title">Thẩm Định Hồ Sơ Online</h4>
                    <p class="finance-step-text">Quý khách chỉ cần cung cấp CCCD gắn chip và thông tin thu nhập. Ngân hàng tiến hành phê duyệt trong vòng 24 giờ.</p>
                </div>

                <!-- Step 3 -->
                <div class="finance-step-card">
                    <div class="finance-step-num">3</div>
                    <h4 class="finance-step-title">Ký Hợp Đồng Tín Dụng</h4>
                    <p class="finance-step-text">Sau khi có thông báo cho vay (bảo lãnh thanh toán), quý khách ký hợp đồng tín dụng và thanh toán phần tiền đối ứng.</p>
                </div>

                <!-- Step 4 -->
                <div class="finance-step-card">
                    <div class="finance-step-num">4</div>
                    <h4 class="finance-step-title">Giải Ngân & Bàn Giao Xe</h4>
                    <p class="finance-step-text">Ngân hàng giải ngân vào tài khoản showroom, quý khách nhận xe ngay trong ngày cùng toàn bộ giấy tờ bàn giao hợp lệ.</p>
                </div>
            </div>
        </div>

        <!-- 5. 4 Đặc Quyền Vay Trả Góp Tại BoxCar -->
        <div class="client-section-spacer">
            <div class="client-section-heading wow fadeInUp">
                <h2>Vì Sao Nên Chọn Gói Tài Chính Tại BoxCar?</h2>
                <div class="text">Liên kết trực tiếp với các định chế tài chính uy tín nhằm mang lại lợi ích cao nhất cho khách hàng</div>
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="boxcar-white-card text-center wow fadeInUp h-100">
                        <div class="contact-quick-icon client-card-icon-center">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                        <h4 class="finance-feature-title">Lãi Suất Cố Định Ưu Đãi</h4>
                        <p class="finance-feature-text">Áp dụng mức lãi suất cạnh tranh nhất từ 6.99%/năm, biên độ lãi suất sau ưu đãi rõ ràng, minh bạch.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="boxcar-white-card text-center wow fadeInUp h-100" data-wow-delay="100ms">
                        <div class="contact-quick-icon client-card-icon-center">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                        <h4 class="finance-feature-title">Hạn Mức Vay Đến 85%</h4>
                        <p class="finance-feature-text">Chỉ cần trả trước từ 15 - 20% giá trị xe, hỗ trợ thời hạn vay tối đa lên đến 7 - 8 năm (84 - 96 tháng).</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="boxcar-white-card text-center wow fadeInUp h-100" data-wow-delay="200ms">
                        <div class="contact-quick-icon client-card-icon-center">
                            <i class="fa-solid fa-stopwatch"></i>
                        </div>
                        <h4 class="finance-feature-title">Phê Duyệt Trong 24 Giờ</h4>
                        <p class="finance-feature-text">Quy trình thẩm định hồ sơ tinh gọn, không rườm rà, chấp nhận hồ sơ kinh doanh tự do hoặc không chứng minh bảng lương.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="boxcar-white-card text-center wow fadeInUp h-100" data-wow-delay="300ms">
                        <div class="contact-quick-icon client-card-icon-center">
                            <i class="fa-solid fa-handshake-angle"></i>
                        </div>
                        <h4 class="finance-feature-title">Miễn Phí Thẩm Định Hồ Sơ</h4>
                        <p class="finance-feature-text">Quý khách không phải trả thêm bất kỳ chi phí thẩm định hồ sơ hay phụ phí ẩn nào ngoài quy định ngân hàng.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. FAQs Section -->
        <div class="faqs-section pt-0 client-section-spacer-lg">
            <div class="inner-container client-inner-max-900">
                <div class="client-section-heading wow fadeInUp">
                    <h2>Câu Hỏi Thường Gặp Về Vay Mua Xe</h2>
                    <div class="text">Giải đáp các câu hỏi quan trọng nhất của khách hàng khi mua xe ô tô trả góp</div>
                </div>
                <div class="about-faq-accordion wow fadeInUp">
                    <div class="about-faq-item is-active">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Hồ sơ vay mua xe trả góp bao gồm những giấy tờ gì?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body">
                            <p>Đối với khách hàng cá nhân: Chỉ cần Căn cước công dân gắn chip, Giấy xác nhận tình trạng hôn nhân (nếu có), và giấy tờ chứng minh nguồn thu nhập (Hợp đồng lao động, sao kê tài khoản nhận lương hoặc nguồn thu từ cửa hàng, cho thuê tài sản...). Đối với doanh nghiệp: Giấy phép ĐKKD, Báo cáo tài chính và sao kê tài khoản công ty.</p>
                        </div>
                    </div>
                    <div class="about-faq-item">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Tôi làm nghề tự do, không có bảng lương công ty có vay được không?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body">
                            <p>Hoàn toàn được. Showroom có liên kết với các gói tín dụng linh hoạt của các ngân hàng thương mại, hỗ trợ đánh giá dòng tiền qua số dư tài khoản, hoạt động kinh doanh thực tế hoặc tài sản hiện có mà không ép buộc phải có hợp đồng lao động công ty.</p>
                        </div>
                    </div>
                    <div class="about-faq-item">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Tôi có thể tất toán khoản vay trước thời hạn được không?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body">
                            <p>Quý khách hoàn toàn có thể trả hết nợ gốc trước hạn bất kỳ lúc nào. Phí trả nợ trước hạn theo quy định của ngân hàng rất thấp (khoảng 0.5% - 1.5% số tiền trả trước trong những năm đầu và thường được miễn phí từ năm thứ 4 trở đi).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Call to Action Banner -->
        <div class="boxcar-cta-about wow fadeInUp">
            <div class="client-cta-box-dark">
                <h2>CẦN TƯ VẤN PHƯƠNG ÁN TÀI CHÍNH TỐI ƯU NHẤT CHO BẠN?</h2>
                <div class="client-cta-btns-row">
                    <a href="{{ route('inventory.index') }}" class="client-cta-btn-outline">TÌM MẪU XE PHÙ HỢP</a>
                    <a href="tel:{{ $cleanPhone }}" class="client-cta-btn-primary">GỌI CHUYÊN VIÊN TÀI CHÍNH</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Toggle FAQ accordion
        $('.js-faq-item-toggle').on('click', function () {
            const item = $(this).closest('.about-faq-item');
            const body = item.find('.about-faq-body');
            const isActive = item.hasClass('is-active');

            if (isActive) {
                body.slideUp(250, function () {
                    item.removeClass('is-active');
                });
            } else {
                $('.about-faq-item.is-active').find('.about-faq-body').slideUp(250, function () {
                    $(this).closest('.about-faq-item').removeClass('is-active');
                });
                item.addClass('is-active');
                body.slideDown(250);
            }
        });

        // Interactive Loan Calculator Engine
        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' ₫';
        }

        const priceSlider = $('#calc-price-slider');
        const downpaymentSlider = $('#calc-downpayment-slider');
        const tenureSlider = $('#calc-tenure-slider');
        const rateSlider = $('#calc-rate-slider');
        const carSelect = $('#calc-car-select');
        const leadCarSelect = $('#lead-car-id');

        function recalculate() {
            const carPrice = parseFloat(priceSlider.val()) || 1200000000;
            const downpaymentPercent = parseFloat(downpaymentSlider.val()) || 20;
            const tenureMonths = parseInt(tenureSlider.val(), 10) || 60;
            const annualRate = parseFloat(rateSlider.val()) || 7.5;

            // Calculations
            const downpaymentAmount = carPrice * (downpaymentPercent / 100);
            const loanAmount = carPrice - downpaymentAmount;

            // Monthly interest rate
            const monthlyRate = (annualRate / 100) / 12;

            // Amortization formula (PMT)
            let monthlyPayment = 0;
            if (loanAmount > 0) {
                if (monthlyRate > 0) {
                    monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, tenureMonths)) / (Math.pow(1 + monthlyRate, tenureMonths) - 1);
                } else {
                    monthlyPayment = loanAmount / tenureMonths;
                }
            }

            // Update displays
            $('#calc-car-price-display').text(formatVND(carPrice));
            $('#calc-downpayment-display').html(downpaymentPercent + '% (<span id="calc-downpayment-amount">' + formatVND(downpaymentAmount) + '</span>)');
            $('#calc-tenure-display').text(tenureMonths + ' tháng (' + (tenureMonths / 12).toFixed(tenureMonths % 12 === 0 ? 0 : 1) + ' năm)');
            $('#calc-rate-display').text(annualRate.toFixed(1) + '% / năm');

            // Result Card
            $('#res-monthly-payment').text(formatVND(monthlyPayment) + ' / tháng');
            $('#res-car-price').text(formatVND(carPrice));
            $('#res-downpayment').text(formatVND(downpaymentAmount) + ' (' + downpaymentPercent + '%)');
            $('#res-loan-amount').text(formatVND(loanAmount));
            $('#res-tenure').text(tenureMonths + ' tháng');
            $('#res-interest-rate').text(annualRate.toFixed(1) + '% / năm');
        }

        // Event listeners
        priceSlider.on('input change', recalculate);
        downpaymentSlider.on('input change', function () {
            const val = $(this).val();
            $('.calc-chips-group button[data-val]').removeClass('is-active');
            $('.calc-chips-group button[data-val="' + val + '"]').addClass('is-active');
            recalculate();
        });
        tenureSlider.on('input change', function () {
            const val = $(this).val();
            $('.calc-chips-group button[data-val]').removeClass('is-active');
            $('.calc-chips-group button[data-val="' + val + '"]').addClass('is-active');
            recalculate();
        });
        rateSlider.on('input change', recalculate);

        // Chip buttons
        $(document).on('click', '.calc-chip-btn', function () {
            const val = $(this).data('val');
            const parent = $(this).closest('.calc-slider-box');
            parent.find('.calc-chip-btn').removeClass('is-active');
            $(this).addClass('is-active');

            if (parent.find('#calc-downpayment-slider').length) {
                downpaymentSlider.val(val);
            } else if (parent.find('#calc-tenure-slider').length) {
                tenureSlider.val(val);
            }
            recalculate();
        });

        // Car select dropdown changes price
        carSelect.on('change', function () {
            const selectedOpt = $(this).find('option:selected');
            const price = parseFloat(selectedOpt.data('price'));
            const carId = selectedOpt.val();

            if (!isNaN(price) && price > 0) {
                priceSlider.val(price);
                recalculate();
            }

            if (carId) {
                leadCarSelect.val(carId);
            }
        });

        // Compile real-time loan parameters into a clear, formatted summary
        function getLoanDetailsSummary() {
            const selectedCarText = carSelect.find('option:selected').data('label') || 'Mẫu xe đang xem';
            const carPrice = formatVND(parseFloat(priceSlider.val()) || 1200000000);
            const downpaymentPercent = downpaymentSlider.val() || 20;
            const downpaymentAmount = $('#calc-downpayment-amount').text();
            const loanAmount = $('#res-loan-amount').text();
            const tenureMonths = tenureSlider.val() || 60;
            const annualRate = rateSlider.val() || 7.5;
            const monthlyPayment = $('#res-monthly-payment').text();

            return [
                `[DỰ TOÁN GÓI VAY TRẢ GÓP TỰ TÍNH]`,
                `- Mẫu xe quan tâm: ${selectedCarText}`,
                `- Giá xe dự tính: ${carPrice}`,
                `- Tỷ lệ trả trước: ${downpaymentPercent}% (${downpaymentAmount})`,
                `- Hạn mức vay ngân hàng: ${loanAmount}`,
                `- Thời hạn vay: ${tenureMonths} tháng (${(tenureMonths / 12).toFixed(tenureMonths % 12 === 0 ? 0 : 1)} năm)`,
                `- Lãi suất ước tính: ${annualRate}%/năm`,
                `- Ước tính thanh toán: ${monthlyPayment}`
            ].join("\n");
        }

        // Quick quote button: synchronize calculation into message and scroll down to form
        $('.js-apply-calc-to-form').on('click', function (e) {
            e.preventDefault();
            const selectedCarId = carSelect.val();
            if (selectedCarId) {
                leadCarSelect.val(selectedCarId);
            }

            const currentMsg = $('#finance-message').val().trim();
            const summary = getLoanDetailsSummary();

            if (!currentMsg || currentMsg.startsWith('[DỰ TOÁN GÓI VAY TRẢ GÓP TỰ TÍNH]')) {
                $('#finance-message').val(summary);
            }

            $('html, body').animate({
                scrollTop: $('#finance-lead-form').offset().top - 80
            }, 500);
        });

        // Ensure loan calculation is attached when form is submitted
        $('#finance-lead-form form').on('submit', function () {
            const currentMsg = $('#finance-message').val().trim();
            const summary = getLoanDetailsSummary();

            if (!currentMsg) {
                $('#finance-message').val(summary);
            } else if (!currentMsg.includes('[DỰ TOÁN GÓI VAY TRẢ GÓP TỰ TÍNH]')) {
                $('#finance-message').val(summary + "\n\n[GHI CHÚ THÊM CỦA KHÁCH HÀNG]\n" + currentMsg);
            }
        });

        // Initialize calculation
        recalculate();
    });
</script>
@endpush
