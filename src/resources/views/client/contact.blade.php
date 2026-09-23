@extends('client.layouts.page')

@section('title', 'Liên Hệ Showroom & Đặt Lịch Trải Nghiệm Xe')

@section('content')
@php
    $showroomName = $showroom->name ?? 'BoxCar Showroom';
    $showroomAddress = $showroom->address ?? 'TP. Hồ Chí Minh';
    $showroomPhone = $showroom->phone ?? '0900 000 000';
    $cleanPhone = preg_replace('/\D+/', '', (string) $showroomPhone);
    $showroomEmail = $showroom->email ?? 'contact@showroom.test';
    $mapAddress = urlencode($showroomAddress);
@endphp

<section class="client-page-wrap">
    <div class="boxcar-container">
        <!-- 1. Header & Breadcrumb -->
        <div class="client-header-intro wow fadeInUp">
            <ul class="client-breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><span>/</span></li>
                <li><span>Liên hệ</span></li>
            </ul>
            <h1 class="client-main-title">LIÊN HỆ SHOWROOM & ĐẶT LỊCH TRẢI NGHIỆM XE</h1>
            <p class="client-main-desc">
                Đội ngũ chuyên viên tư vấn cao cấp sẵn sàng hỗ trợ 24/7. Trực tiếp tham quan không gian trưng bày, trải nghiệm lái thử các dòng xe hàng đầu hoặc nhận tư vấn chuyên sâu mọi lúc, mọi nơi.
            </p>
        </div>

        <!-- 2. Hàng 4 Thẻ Kênh Kết Nối Nhanh -->
        <div class="contact-quick-grid wow fadeInUp" data-wow-delay="100ms">
            <!-- Hotline -->
            <div class="contact-quick-card">
                <div class="contact-quick-icon">
                    <i class="fa-solid fa-phone-volume"></i>
                </div>
                <h3 class="contact-quick-title">Hotline 24/7</h3>
                <p class="contact-quick-text">Hỗ trợ tư vấn giải đáp kỹ thuật, đặt cọc giữ xe và cứu hộ khẩn cấp.</p>
                <a href="tel:{{ $cleanPhone }}" class="contact-quick-link">
                    {{ $showroomPhone }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Showroom -->
            <div class="contact-quick-card">
                <div class="contact-quick-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h3 class="contact-quick-title">Showroom Trực Tiếp</h3>
                <p class="contact-quick-text">{{ $showroomAddress }}</p>
                <a href="#showroom-map-section" class="contact-quick-link">
                    Xem chỉ đường <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Email -->
            <div class="contact-quick-card">
                <div class="contact-quick-icon">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
                <h3 class="contact-quick-title">Hòm Thư Điện Tử</h3>
                <p class="contact-quick-text">Tiếp nhận yêu cầu báo giá dự toán và tài liệu thông số kỹ thuật.</p>
                <a href="mailto:{{ $showroomEmail }}" class="contact-quick-link">
                    {{ $showroomEmail }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Test Drive -->
            <div class="contact-quick-card">
                <div class="contact-quick-icon">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <h3 class="contact-quick-title">Lái Thử Trải Nghiệm</h3>
                <p class="contact-quick-text">Đặt lịch lái thử xe tại showroom hoặc hỗ trợ giao xe tận nhà theo yêu cầu.</p>
                <a href="#contact-form-section" class="contact-quick-link">
                    Đặt lịch ngay <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 3. Khu vực Trọng tâm 2 Cột: Form Đặt Hẹn & Bản Đồ Showroom -->
        <div class="contact-main-grid wow fadeInUp" data-wow-delay="200ms" id="contact-form-section">
            <!-- Cột trái: Form Đặt Hẹn & Tư Vấn -->
            <div class="boxcar-white-card">
                <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #EEF1F6;">
                    <h3 style="font-size: 20px; font-weight: 700; color: #050B20; margin-bottom: 6px;">Đăng Ký Tư Vấn & Đặt Lịch Hẹn</h3>
                    <p style="font-size: 14px; color: #64748B; margin: 0;">Quý khách vui lòng để lại thông tin, chuyên viên tư vấn sẽ liên hệ xác nhận trong vòng 15 phút.</p>
                </div>

                @if (isset($errors) && $errors->any())
                    <div style="margin-bottom: 20px; padding: 14px 18px; border-radius: 12px; background: #FEF2F2; border: 1px solid #FCA5A5; color: #B91C1C; font-size: 14px;">
                        <ul style="margin: 0; padding-left: 18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('lead.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="source" value="contact">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="client-form-group">
                                <label for="contact-name">Họ và tên <span style="color: #EF4444;">*</span></label>
                                <input type="text" id="contact-name" name="name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="client-form-group">
                                <label for="contact-phone">Số điện thoại <span style="color: #EF4444;">*</span></label>
                                <input type="tel" id="contact-phone" name="phone" class="form-control" placeholder="Ví dụ: 0912 345 678" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="client-form-group">
                                <label for="contact-email">Địa chỉ Email</label>
                                <input type="email" id="contact-email" name="email" class="form-control" placeholder="email@domain.com" value="{{ old('email', auth()->user()->email ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="client-form-group">
                                <label for="contact-demand">Nhu cầu của quý khách</label>
                                <select id="contact-demand" class="form-control" name="demand_type">
                                    <option value="test_drive">Đăng ký lái thử trải nghiệm</option>
                                    <option value="showroom_visit">Đến xem xe tại showroom</option>
                                    <option value="pricing_quote">Nhận báo giá lăn bánh & ưu đãi</option>
                                    <option value="consultation">Tư vấn thông số xe & tài chính</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="client-form-group">
                        <label for="contact-car">Xe quý khách đang quan tâm (Không bắt buộc)</label>
                        <select id="contact-car" name="car_unit_id" class="form-control">
                            <option value="">-- Chọn xe đang có sẵn trong kho xe --</option>
                            @foreach ($availableCars as $car)
                                <option value="{{ $car->id }}" {{ old('car_unit_id') == $car->id ? 'selected' : '' }}>
                                    {{ $car->label }} - {{ number_format($car->price) }} {{ $car->currency ?? 'VNĐ' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="client-form-group">
                        <label for="contact-message">Ghi chú hoặc thời gian hẹn thuận tiện</label>
                        <textarea id="contact-message" name="message" class="form-control" placeholder="Quý khách có thể ghi chú khung giờ thuận tiện để nghe điện thoại hoặc yêu cầu đặc biệt khi lái thử...">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="client-submit-btn">
                        GỬI YÊU CẦU LIÊN HỆ & ĐẶT LỊCH HẸN
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- Cột phải: Bản Đồ & Trải Nghiệm Showroom VIP -->
            <div class="contact-map-card" id="showroom-map-section">
                <iframe
                    class="contact-map-iframe"
                    src="https://maps.google.com/maps?width=100%25&height=600&hl=vi&q={{ $mapAddress }}&t=&z=14&ie=UTF8&iwloc=B&output=embed"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>

                <div class="contact-showroom-details">
                    <h4>Trải Nghiệm Tại Showroom</h4>

                    <div class="contact-detail-row">
                        <i class="fa-solid fa-clock"></i>
                        <div>
                            <strong>Thời gian đón tiếp khách hàng</strong>
                            <span>08:00 - 20:00 (Mở cửa tất cả các ngày trong tuần, kể cả Thứ 7 & CN)</span>
                        </div>
                    </div>

                    <div class="contact-detail-row">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <strong>Địa chỉ vị trí showroom</strong>
                            <span>{{ $showroomAddress }}</span>
                        </div>
                    </div>

                    <div class="contact-detail-row">
                        <i class="fa-solid fa-square-parking"></i>
                        <div>
                            <strong>Tiện ích đỗ xe & đón tiếp</strong>
                            <span>Bãi đỗ ô tô rộng rãi miễn phí, bảo vệ túc trực hỗ trợ chu đáo.</span>
                        </div>
                    </div>

                    <div class="contact-vip-badge">
                        <i class="fa-solid fa-crown"></i>
                        <span>Phòng chờ VIP Lounge sang trọng, quầy cafe cao cấp & wifi tốc độ cao.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Cam kết dịch vụ khách hàng (3 Giá trị cốt lõi) -->
        <div style="margin-bottom: 50px;">
            <div class="client-section-heading wow fadeInUp">
                <h2>Cam Kết Dịch Vụ Khách Hàng</h2>
                <div class="text">Đem lại trải nghiệm mua xe minh bạch, chu đáo và đẳng cấp xứng tầm</div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="boxcar-white-card text-center wow fadeInUp" style="height: 100%;">
                        <div class="contact-quick-icon" style="margin: 0 auto 16px;">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h4 style="font-size: 17px; font-weight: 700; color: #050B20; margin-bottom: 8px;">Phản Hồi Siêu Tốc Trong 15 Phút</h4>
                        <p style="font-size: 14px; color: #5F6980; line-height: 1.6; margin: 0;">Mọi yêu cầu liên hệ hoặc đặt lịch lái thử đều được chuyên viên tiếp nhận và liên hệ xác nhận ngay trong 15 phút làm việc.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="boxcar-white-card text-center wow fadeInUp" data-wow-delay="100ms" style="height: 100%;">
                        <div class="contact-quick-icon" style="margin: 0 auto 16px;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h4 style="font-size: 17px; font-weight: 700; color: #050B20; margin-bottom: 8px;">Minh Bạch Thông Tin & Giá Bán</h4>
                        <p style="font-size: 14px; color: #5F6980; line-height: 1.6; margin: 0;">Báo giá chính xác, thông tin kiểm định 160 bước công khai rõ ràng, không phát sinh bất kỳ khoản phí ngoài dự kiến.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="boxcar-white-card text-center wow fadeInUp" data-wow-delay="200ms" style="height: 100%;">
                        <div class="contact-quick-icon" style="margin: 0 auto 16px;">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <h4 style="font-size: 17px; font-weight: 700; color: #050B20; margin-bottom: 8px;">Lái Thử Xe Tận Nơi Linh Hoạt</h4>
                        <p style="font-size: 14px; color: #5F6980; line-height: 1.6; margin: 0;">Nếu quý khách bận rộn không thể ghé showroom, chúng tôi sẵn sàng mang xe đến tận nhà hoặc cơ quan để quý khách trải nghiệm trực tiếp.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. FAQs Section -->
        <div class="faqs-section pt-0" style="margin-bottom: 60px;">
            <div class="inner-container" style="max-width: 900px; margin: 0 auto;">
                <div class="client-section-heading wow fadeInUp">
                    <h2>Câu Hỏi Thường Gặp Khi Liên Hệ</h2>
                    <div class="text">Giải đáp các băn khoăn phổ biến nhất trước khi quý khách đến trải nghiệm xe</div>
                </div>
                <div class="about-faq-accordion wow fadeInUp">
                    <div class="about-faq-item is-active">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Tôi có cần đặt hẹn trước khi đến tham quan showroom không?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body" style="display: block;">
                            <p>Quý khách có thể ghé thăm showroom bất kỳ lúc nào trong giờ làm việc (08:00 - 20:00). Tuy nhiên, chúng tôi khuyến khích quý khách đặt lịch trước để showroom chuẩn bị sẵn mẫu xe quý khách yêu thích trong trạng thái tốt nhất và bố trí chuyên viên tiếp đón riêng tư chu đáo.</p>
                        </div>
                    </div>
                    <div class="about-faq-item">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Đăng ký lái thử xe có phát sinh chi phí nào không?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body">
                            <p>Hoàn toàn miễn phí 100%. Quý khách chỉ cần chuẩn bị Giấy phép lái xe (GPLX) còn hiệu lực. Chuyên viên của BoxCar sẽ đồng hành, giới thiệu các tính năng an toàn và công nghệ trên xe trong suốt lộ trình lái thử.</p>
                        </div>
                    </div>
                    <div class="about-faq-item">
                        <div class="about-faq-header js-faq-item-toggle">
                            <h4 class="about-faq-title">Showroom có hỗ trợ vận chuyển và giao xe tận nhà trên toàn quốc không?</h4>
                            <span class="about-faq-icon"><i class="fa-solid fa-plus"></i></span>
                        </div>
                        <div class="about-faq-body">
                            <p>Có. Chúng tôi sở hữu đội xe cứu hộ và xe lồng chuyên dụng bàn giao xe tận nhà khách hàng trên toàn quốc, kèm lễ bàn giao hoa chúc mừng trang trọng và bàn giao đầy đủ hồ sơ pháp lý tận tay.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Call to Action Banner -->
        <div class="boxcar-cta-about wow fadeInUp">
            <div class="inner-box" style="background: #050B20; border-radius: 20px; padding: 48px 36px; text-align: center; color: #fff; box-shadow: 0 16px 40px rgba(5, 11, 32, 0.15);">
                <h2 style="color: #fff; font-size: clamp(22px, 2.6vw, 30px); font-weight: 800; margin-bottom: 20px; text-transform: uppercase; letter-spacing: -0.01em;">BẠN ĐÃ SẴN SÀNG CHO HÀNH TRÌNH MỚI CÙNG BOXCAR?</h2>
                <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('inventory.index') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 26px; border-radius: 12px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.25); color: #fff; font-weight: 700; text-decoration: none; font-size: 14px; transition: 0.3s;">KHÁM PHÁ BỘ SƯU TẬP XE</a>
                    <a href="tel:{{ $cleanPhone }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 26px; border-radius: 12px; background: #405FF2; color: #fff; font-weight: 700; text-decoration: none; font-size: 14px; transition: 0.3s; box-shadow: 0 4px 14px rgba(64, 95, 242, 0.4);">GỌI HOTLINE TƯ VẤN NGAY</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
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
    });
</script>
@endpush
