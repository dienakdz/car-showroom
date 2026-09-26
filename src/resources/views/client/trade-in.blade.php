@extends('client.layouts.page')

@section('title', 'Thu Cũ Đổi Mới - Lên Đời Xe Sang Nhanh Chóng')

@section('content')
@php
    $showroomName = $showroom->name ?? 'BoxCar Showroom';
    $showroomPhone = $showroom->phone ?? '0900 000 000';
    $cleanPhone = preg_replace('/\D+/', '', (string) $showroomPhone);
@endphp

<section class="client-page-wrap">
    <div class="boxcar-container">
        <!-- 1. Header & Breadcrumb -->
        <div class="client-header-intro">
            <ul class="client-breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><span>/</span></li>
                <li><span>Thu cũ đổi mới</span></li>
            </ul>
            <h1 class="client-main-title">THU CŨ ĐỔI MỚI - LÊN ĐỜI XE SANG NHANH CHÓNG</h1>
            <p class="client-main-desc">
                Chương trình Trade-in độc quyền tại BoxCar: Định giá minh bạch sát giá thị trường, không ép giá, hỗ trợ trọn gói thủ tục pháp lý và rút ngắn thời gian lên đời xe chỉ trong 2 giờ.
            </p>
        </div>

        <!-- 2. Quy trình Thu Cũ Đổi Mới 4 Bước (Horizontal Stepper) -->
        <div class="tradein-stepper">
            <!-- Step 1 -->
            <div class="tradein-step-box">
                <div class="tradein-step-badge">1</div>
                <h4 class="tradein-step-heading">Gửi Thông Tin Xe Cũ</h4>
                <p class="tradein-step-desc">Cung cấp hãng xe, năm sản xuất, số ODO và tình trạng xe qua form online.</p>
            </div>

            <!-- Step 2 -->
            <div class="tradein-step-box">
                <div class="tradein-step-badge">2</div>
                <h4 class="tradein-step-heading">Thẩm Định 160 Bước</h4>
                <p class="tradein-step-desc">Kỹ thuật viên kiểm tra xe thực tế tại showroom hoặc hỗ trợ tận nhà miễn phí.</p>
            </div>

            <!-- Step 3 -->
            <div class="tradein-step-box">
                <div class="tradein-step-badge">3</div>
                <h4 class="tradein-step-heading">Thỏa Thuận Bù Trừ</h4>
                <p class="tradein-step-desc">Nhận mức giá thu mua cao nhất và chọn xe mới để tính toán khoản chênh lệch.</p>
            </div>

            <!-- Step 4 -->
            <div class="tradein-step-box">
                <div class="tradein-step-badge">4</div>
                <h4 class="tradein-step-heading">Lái Xe Mới Về Nhà</h4>
                <p class="tradein-step-desc">Hoàn tất hợp đồng mua bán, bàn giao xe cũ và lái chiếc xe mới về ngay trong ngày.</p>
            </div>
        </div>

        <!-- 3. Form Thẩm Định & Đổi Xe 2 Chiều (Dual Valuation Form) -->
        <div class="boxcar-white-card wow fadeInUp client-section-spacer" data-wow-delay="200ms">
            <div class="client-card-heading-box">
                <h3>Đăng Ký Định Giá Xe Cũ & Đổi Xe Mới</h3>
                <p>Điền thông tin xe hiện tại của quý khách và chọn mẫu xe muốn đổi sang để nhận báo giá bù trừ chính xác nhất.</p>
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

            <form action="{{ route('lead.store') }}" method="POST" id="tradein-form">
                @csrf
                <input type="hidden" name="source" value="trade_in">

                <div class="tradein-dual-grid">
                    <!-- Khối Trái: Thông tin xe đang sử dụng của quý khách -->
                    <div>
                        <div class="tradein-card-header">
                            <div class="tradein-card-header-icon">
                                <i class="fa-solid fa-car"></i>
                            </div>
                            <div>
                                <h3>1. Thông Tin Chiếc Xe Của Bạn</h3>
                                <p>Càng chi tiết, báo giá sơ bộ ban đầu càng sát thực tế</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="client-form-group">
                                    <label for="old-car-make">Hãng sản xuất <span class="text-danger">*</span></label>
                                    <select id="old-car-make" class="form-control" required>
                                        <option value="">-- Chọn thương hiệu xe --</option>
                                        @foreach ($popularMakes as $make)
                                            <option value="{{ $make }}">{{ $make }}</option>
                                        @endforeach
                                        <option value="Khác">Hãng xe khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="client-form-group">
                                    <label for="old-car-model">Dòng xe & Phiên bản <span class="text-danger">*</span></label>
                                    <input type="text" id="old-car-model" class="form-control" placeholder="Ví dụ: CX-5 2.0 Luxury" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="client-form-group">
                                    <label for="old-car-year">Năm sản xuất <span class="text-danger">*</span></label>
                                    <select id="old-car-year" class="form-control" required>
                                        @for ($year = (int) date('Y'); $year >= 2012; $year--)
                                            <option value="{{ $year }}" {{ $year == 2021 ? 'selected' : '' }}>Năm {{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="client-form-group">
                                    <label for="old-car-mileage">Số Km đã đi (ODO)</label>
                                    <input type="text" id="old-car-mileage" class="form-control" placeholder="Ví dụ: 35.000 km">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="client-form-group">
                                    <label for="old-car-transmission">Hộp số</label>
                                    <select id="old-car-transmission" class="form-control">
                                        <option value="Số tự động (AT)">Số tự động (AT)</option>
                                        <option value="Số sàn (MT)">Số sàn (MT)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="client-form-group">
                                    <label for="old-car-status">Tình trạng hồ sơ xe</label>
                                    <select id="old-car-status" class="form-control">
                                        <option value="Chính chủ, biển số tỉnh">Chính chủ, biển số tỉnh</option>
                                        <option value="Chính chủ, biển số TP.HCM / Hà Nội">Chính chủ, biển Hà Nội / TP.HCM</option>
                                        <option value="Xe đang vay thế chấp ngân hàng">Xe đang vay thế chấp ngân hàng</option>
                                        <option value="Xe đứng tên công ty">Xe đứng tên công ty (Xuất VAT)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="client-form-group">
                            <label for="old-car-note">Tình trạng bảo dưỡng & mô tả thêm</label>
                            <textarea id="old-car-note" class="form-control client-textarea-sm" placeholder="Ví dụ: Xe đi giữ gìn một chủ từ đầu, bảo dưỡng định kỳ đầy đủ tại hãng, sơn zin 95%..."></textarea>
                        </div>
                    </div>

                    <!-- Khối Phải: Chọn xe muốn đổi sang & Thông tin liên hệ -->
                    <div>
                        <div class="tradein-card-header">
                            <div class="tradein-card-header-icon">
                                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                            </div>
                            <div>
                                <h3>2. Chọn Xe Muốn Đổi & Đăng Ký</h3>
                                <p>Chọn chiếc xe quý khách muốn đổi sang từ kho xe của chúng tôi</p>
                            </div>
                        </div>

                        <div class="client-form-group">
                            <label for="tradein-target-car">Xe mục tiêu quý khách muốn đổi sang <span class="text-danger">*</span></label>
                            <select id="tradein-target-car" name="car_unit_id" class="form-control" required>
                                @foreach ($availableCars as $car)
                                    <option value="{{ $car->id }}" {{ old('car_unit_id') == $car->id ? 'selected' : '' }}>
                                        {{ $car->label }} — {{ number_format($car->price) }} {{ $car->currency ?? 'VNĐ' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="client-form-group">
                            <label for="tradein-payment-pref">Hình thức thanh toán khoản chênh lệch</label>
                            <select id="tradein-payment-pref" class="form-control">
                                <option value="cash">Thanh toán bù trừ một lần bằng tiền mặt / chuyển khoản</option>
                                <option value="finance">Vay trả góp phần tiền chênh lệch (Lãi suất ưu đãi)</option>
                                <option value="consult">Cần chuyên viên tư vấn phương án phù hợp nhất</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="client-form-group">
                                    <label for="tradein-name">Họ và tên quý khách <span class="text-danger">*</span></label>
                                    <input type="text" id="tradein-name" name="name" class="form-control" placeholder="Ví dụ: Nguyễn Văn B" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="client-form-group">
                                    <label for="tradein-phone">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                                    <input type="tel" id="tradein-phone" name="phone" class="form-control" placeholder="Ví dụ: 0918 888 999" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="client-form-group">
                            <label for="tradein-location">Khu vực hẹn thẩm định xe</label>
                            <input type="text" id="tradein-location" class="form-control" placeholder="Ví dụ: Quận 7, TP.HCM hoặc Đến Showroom">
                        </div>

                        <!-- Hidden compiled message that packs old car details for CRM -->
                        <input type="hidden" name="message" id="tradein-compiled-message" value="">
                    </div>
                </div>

                <!-- Balanced Submit Bar centered across full card width -->
                <div class="tradein-submit-bar">
                    <button type="submit" class="tradein-submit-btn">
                        <span>YÊU CẦU ĐỊNH GIÁ & NHẬN ƯU ĐÃI ĐỔI XE</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <div class="tradein-security-note">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Thông tin định giá được bảo mật tuyệt đối. Kỹ thuật viên sẽ liên hệ thẩm định trong vòng 30 phút.</span>
                    </div>
                </div>
            </form>
        </div>

        <!-- 4. Cam kết Trade-in Vượt Trội -->
        <div class="client-section-spacer">
            <div class="client-section-heading wow fadeInUp">
                <h2>Cam Kết Dịch Vụ Thu Cũ Đổi Mới Tại BoxCar</h2>
                <div class="text">Đồng hành cùng khách hàng nâng tầm đẳng cấp phương tiện một cách thuận lợi và an tâm nhất</div>
            </div>

            <div class="tradein-perks-grid wow fadeInUp" data-wow-delay="100ms">
                <!-- Perk 1 -->
                <div class="tradein-perk-card">
                    <div class="tradein-perk-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h4 class="tradein-perk-title">Định Giá Tốt Hơn Thị Trường</h4>
                    <p class="tradein-perk-text">Nhờ hệ sinh thái bán lẻ trực tiếp tới khách hàng cuối, chúng tôi cam kết thu mua xe cũ của quý khách với mức giá cao hơn thị trường từ 5 đến 15 triệu đồng.</p>
                </div>

                <!-- Perk 2 -->
                <div class="tradein-perk-card">
                    <div class="tradein-perk-icon">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <h4 class="tradein-perk-title">Hỗ Trợ Toàn Bộ Pháp Lý 100%</h4>
                    <p class="tradein-perk-text">Đội ngũ pháp lý chuyên nghiệp hỗ trợ toàn bộ thủ tục công chứng mua bán, rút gốc giấy tờ, và giải chấp ngân hàng đối với các xe đang có dư nợ thế chấp.</p>
                </div>

                <!-- Perk 3 -->
                <div class="tradein-perk-card">
                    <div class="tradein-perk-icon">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <h4 class="tradein-perk-title">Trợ Giá Đổi Xe Lên Tới 20 Triệu</h4>
                    <p class="tradein-perk-text">Tặng ngay voucher trợ giá đổi xe từ 10 - 20 triệu đồng, tặng kèm gói bảo dưỡng cao cấp 1 năm và phủ ceramic bóng đẹp khi quý khách đổi xe tại showroom.</p>
                </div>
            </div>
        </div>

        <!-- 5. Gợi Ý Kho Xe Đang Có Sẵn Để Lên Đời -->
        @if ($availableCars->isNotEmpty())
            <div class="client-section-spacer">
                <div class="client-section-heading wow fadeInUp">
                    <h2>Gợi Ý Xe Đổi Mới Đang Có Sẵn</h2>
                    <div class="text">Những mẫu xe tuyển chọn chất lượng cao nhất tại showroom đang chờ đón chủ nhân mới</div>
                </div>

                <div class="row">
                    @foreach ($availableCars->take(3) as $car)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="tradein-car-card wow fadeInUp" @if ($loop->index > 0) data-wow-delay="{{ $loop->index * 100 }}ms" @endif>
                                <div class="tradein-car-thumb">
                                    <img src="{{ $car->image_url }}" alt="{{ $car->label }}" loading="lazy">
                                    <span class="tradein-car-badge">
                                        <i class="fa-solid fa-check"></i> {{ $car->condition_label ?? 'Chính hãng' }}
                                    </span>
                                </div>
                                <div class="tradein-car-body">
                                    <div>
                                        <div class="tradein-car-stock">Mã xe: {{ $car->stock_code }}</div>
                                        <h4 class="tradein-car-title">{{ $car->label }}</h4>
                                        <div class="tradein-car-specs">
                                            <span><i class="fa-solid fa-calendar-days"></i> {{ $car->year ?? '2024' }}</span>
                                            <span><i class="fa-solid fa-gas-pump"></i> {{ $car->fuel_label ?? 'Xăng' }}</span>
                                            <span><i class="fa-solid fa-gear"></i> {{ $car->transmission_label ?? 'Tự động' }}</span>
                                        </div>
                                        <div class="tradein-car-price">{{ $car->formatted_price ?? number_format($car->price) . ' VNĐ' }}</div>
                                    </div>
                                    <a href="#tradein-form" class="tradein-pick-btn js-pick-target-car" data-car-id="{{ $car->id }}">
                                        <i class="fa-solid fa-arrow-right-arrow-left"></i> CHỌN ĐỔI SANG XE NÀY
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 6. FAQs Section -->
        <div class="faqs-section pt-0 client-section-spacer-lg">
            <div class="inner-container client-inner-max-900">
                <div class="client-section-heading wow fadeInUp">
                    <h2>Câu Hỏi Thường Gặp Về Thu Cũ Đổi Mới</h2>
                    <div class="text">Tất cả những điều quý khách cần biết khi thực hiện nâng cấp đổi xe tại BoxCar</div>
                </div>
                <div class="about-faq-accordion wow fadeInUp">
                    <div class="about-faq-item is-active">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Xe cũ của tôi đang vay ngân hàng thì có tham gia đổi xe được không?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body">
                            <p>Hoàn toàn được. Showroom sẽ hỗ trợ ứng tiền giải chấp khoản vay tại ngân hàng của quý khách để lấy đăng ký xe gốc ra, sau đó tiến hành thủ tục sang tên đổi chủ và cấn trừ vào giá trị chiếc xe mới mà quý khách muốn đổi sang một cách minh bạch.</p>
                        </div>
                    </div>
                    <div class="about-faq-item">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Tôi ở tỉnh xa, showroom có đến tận nơi để định giá xe không?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body">
                            <p>Chúng tôi có đội ngũ chuyên viên kỹ thuật thẩm định lưu động trên toàn quốc. Sau khi nhận thông tin sơ bộ và hình ảnh qua Zalo/Online, chúng tôi sẽ cử kỹ sư đến tận nhà quý khách để kiểm định thực tế và chốt giá mà quý khách không phải tốn thời gian di chuyển.</p>
                        </div>
                    </div>
                    <div class="about-faq-item">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Quy trình bàn giao xe cũ và nhận xe mới mất bao lâu?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body">
                            <p>Thông thường quy trình chỉ mất từ 2 đến 4 giờ làm việc trong ngày. Quý khách lái xe cũ đến showroom và hoàn toàn có thể lái ngay chiếc xe mới đã được đăng kiểm, rửa xe sạch bóng và đổ đầy bình xăng về nhà.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Call to Action Banner -->
        <div class="boxcar-cta-about wow fadeInUp">
            <div class="client-cta-box-dark">
                <h2>SẴN SÀNG LÊN ĐỜI CHIẾC XE SANG TIẾP THEO CỦA BẠN?</h2>
                <div class="client-cta-btns-row">
                    <a href="{{ route('inventory.index') }}" class="client-cta-btn-outline">XEM KHO XE CÓ SẴN</a>
                    <a href="tel:{{ $cleanPhone }}" class="client-cta-btn-primary">GỌI HOTLINE THẨM ĐỊNH XE</a>
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

        // Quick Pick Target Car button
        $('.js-pick-target-car').on('click', function (e) {
            e.preventDefault();
            const carId = $(this).data('car-id');
            $('#tradein-target-car').val(carId);

            $('html, body').animate({
                scrollTop: $('#tradein-form').offset().top - 80
            }, 500);
        });

        // Compile trade-in details into message on form submit
        $('#tradein-form').on('submit', function () {
            const make = $('#old-car-make').val() || 'Không rõ hãng';
            const model = $('#old-car-model').val() || 'Không rõ dòng xe';
            const year = $('#old-car-year').val() || '';
            const mileage = $('#old-car-mileage').val() || 'Chưa rõ số km';
            const transmission = $('#old-car-transmission').val() || '';
            const status = $('#old-car-status').val() || '';
            const location = $('#tradein-location').val() || 'Showroom';
            const paymentPref = $('#tradein-payment-pref').find('option:selected').text();
            const note = $('#old-car-note').val() || 'Không có ghi chú thêm';

            const compiledMessage = [
                `[YÊU CẦU THU CŨ ĐỔI MỚI - TRADE-IN]`,
                `- Xe cũ cần thẩm định: ${make} ${model} (Năm ${year}, Hộp số: ${transmission})`,
                `- Số Km đã đi (ODO): ${mileage}`,
                `- Hồ sơ pháp lý xe cũ: ${status}`,
                `- Địa điểm hẹn thẩm định: ${location}`,
                `- Hình thức bù trừ: ${paymentPref}`,
                `- Ghi chú tình trạng xe: ${note}`
            ].join("\n");

            $('#tradein-compiled-message').val(compiledMessage);
        });
    });
</script>
@endpush
