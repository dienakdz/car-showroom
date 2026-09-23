@extends('client.layouts.page')

@section('title', 'Về chúng tôi')

@section('content')
@php
    $showroomName = $showroom->name ?? 'BoxCar Showroom';
    $showroomAddress = $showroom->address ?? 'TP. Hồ Chí Minh';
    $showroomPhone = $showroom->phone ?? '0900 000 000';
    $showroomEmail = $showroom->email ?? 'contact@showroom.test';
@endphp

<section class="about-page-v2 layout-radius">
    <div class="boxcar-container">
        <!-- 1. Header & Brand Story (Matches Mockup 2) -->
        <div class="about-header-intro wow fadeInUp">
            <ul class="about-breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><span>/</span></li>
                <li><span>Về chúng tôi</span></li>
            </ul>
            <h1 class="about-main-title">VỀ CHÚNG TÔI - ĐẲNG CẤP & UY TÍN KHẲNG ĐỊNH GIÁ TRỊ</h1>
            <p class="about-main-desc">
                {{ $showroomName }} tự hào là điểm đến tin cậy của hàng ngàn khách hàng trên toàn quốc, mang đến những mẫu xe tuyển chọn khắt khe, chất lượng đỉnh cao cùng dịch vụ tận tâm và minh bạch tuyệt đối.
            </p>
        </div>

        <!-- 2. Showroom Multi-Vehicle Gallery (Matches Mockup 2) -->
        <div class="about-gallery-grid wow fadeInUp" data-wow-delay="100ms">
            <!-- Column 1: Featured Showroom Car -->
            <div class="about-gallery-col">
                <div class="about-gallery-img-wrap h-full">
                    <img src="{{ asset('boxcar/images/resource/about-inner1-2.jpg') }}" alt="Showroom xe sang">
                </div>
            </div>
            <!-- Column 2: Luxury Vehicle Center -->
            <div class="about-gallery-col">
                <div class="about-gallery-img-wrap h-full">
                    <img src="{{ asset('boxcar/images/resource/about-inner1-3.jpg') }}" alt="Xe cao cấp tại showroom">
                </div>
            </div>
            <!-- Column 3: Stacked Vehicles -->
            <div class="about-gallery-col">
                <div class="about-gallery-img-wrap h-half">
                    <img src="{{ asset('boxcar/images/resource/about-inner1-4.jpg') }}" alt="Trưng bày xe hiện đại">
                </div>
                <div class="about-gallery-img-wrap h-half">
                    <img src="{{ asset('boxcar/images/resource/about-inner1-5.jpg') }}" alt="Dịch vụ đón tiếp chu đáo">
                </div>
            </div>
            <!-- Column 4: Stacked Vehicles -->
            <div class="about-gallery-col">
                <div class="about-gallery-img-wrap h-half">
                    <img src="{{ asset('boxcar/images/resource/about-inner1-1.jpg') }}" alt="Khu tiếp đón VIP">
                </div>
                <div class="about-gallery-img-wrap h-half">
                    <img src="{{ asset('boxcar/images/resource/pricing1-1.jpg') }}" alt="Quy trình thẩm định xe">
                </div>
            </div>
        </div>

        <!-- 3. Section: "Vì sao chọn chúng tôi?" (Matches Mockup 2) -->
        <div class="about-values-section">
            <div class="about-section-heading wow fadeInUp">
                <h2>Vì sao chọn chúng tôi?</h2>
            </div>
            <div class="about-values-grid">
                <!-- Card 1: Kiem dinh 160 diem -->
                <div class="about-value-card wow fadeInUp">
                    <div class="about-value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            <path d="M11 8v6"></path>
                            <path d="M8 11h6"></path>
                        </svg>
                    </div>
                    <h3 class="about-value-title">Kiểm định 160 điểm khắt khe</h3>
                    <p class="about-value-text">Kiểm định 160 điểm nghiêm ngặt, cam kết chuẩn chỉ về pháp lý và nguồn gốc xe.</p>
                </div>

                <!-- Card 2: Gia ban minh bach -->
                <div class="about-value-card wow fadeInUp" data-wow-delay="100ms">
                    <div class="about-value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                    </div>
                    <h3 class="about-value-title">Giá bán minh bạch</h3>
                    <p class="about-value-text">Giá bán minh bạch, không phụ phí ẩn, hỗ trợ trọn gói thủ tục sang tên đổi chủ.</p>
                </div>

                <!-- Card 3: Tai chinh linh hoat -->
                <div class="about-value-card wow fadeInUp" data-wow-delay="200ms">
                    <div class="about-value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <line x1="6" y1="8" x2="6" y2="8"></line>
                            <line x1="10" y1="8" x2="10" y2="8"></line>
                            <line x1="14" y1="8" x2="14" y2="8"></line>
                            <line x1="18" y1="8" x2="18" y2="8"></line>
                            <line x1="6" y1="12" x2="18" y2="12"></line>
                            <line x1="6" y1="16" x2="18" y2="16"></line>
                        </svg>
                    </div>
                    <h3 class="about-value-title">Tài chính linh hoạt</h3>
                    <p class="about-value-text">Tài chính linh hoạt, hỗ trợ vay trả góp tới 80% với lãi suất ưu đãi nhanh gọn.</p>
                </div>

                <!-- Card 4: Dong hanh tron doi -->
                <div class="about-value-card wow fadeInUp" data-wow-delay="300ms">
                    <div class="about-value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                        </svg>
                    </div>
                    <h3 class="about-value-title">Đồng hành trọn đời</h3>
                    <p class="about-value-text">Đồng hành trọn đời, bảo hành chính hãng và hỗ trợ cứu hộ kỹ thuật 24/7.</p>
                </div>
            </div>

            <!-- 4. Dark Navy Stats Bar (Matches Mockup 2) -->
            <div class="about-stats-bar wow fadeInUp" data-wow-delay="200ms">
                <div class="about-stats-grid">
                    <div class="about-stat-item">
                        <div class="about-stat-number widget-counter">
                            <span class="count-text" data-speed="2500" data-stop="{{ $stats['cars_for_sale'] }}">0</span>+
                        </div>
                        <p class="about-stat-label">Xe sẵn có</p>
                    </div>
                    <div class="about-stat-item">
                        <div class="about-stat-number widget-counter">
                            <span class="count-text" data-speed="2500" data-stop="{{ $stats['years_in_business'] ?? 10 }}">0</span>+
                        </div>
                        <p class="about-stat-label">Năm kinh nghiệm</p>
                    </div>
                    <div class="about-stat-item">
                        <div class="about-stat-number widget-counter">
                            <span class="count-text" data-speed="2500" data-stop="{{ $stats['satisfied_customers'] ?? 5000 }}">0</span>+
                        </div>
                        <p class="about-stat-label">Khách hàng tin cậy</p>
                    </div>
                    <div class="about-stat-item">
                        <div class="about-stat-number">
                            4.9/5
                        </div>
                        <p class="about-stat-label">Đánh giá hài lòng</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Pricing / Trade-in Section (Matches Home Page) -->
        <div class="boxcar-pricing-section pb-0 pt-0" style="margin-bottom: 70px;">
            <div class="large-container">
                <div class="row g-0">
                    <div class="image-column col-lg-6 col-md-12 col-sm-12">
                        <div class="inner-column">
                            <div class="image-box">
                                <figure class="image"><a href="{{ route('tradein') }}"><img src="{{ asset('boxcar/images/resource/pricing1-1.jpg') }}" alt="Thu cũ đổi mới"></a></figure>
                                <a href="https://www.youtube.com/watch?v=AC1cREPIw_o&amp;autoplay=1&amp;rel=0&amp;controls=0&amp;showinfo=0" class="play-now" data-fancybox="gallery" data-caption=""><i class="fa fa-play" aria-hidden="true"></i><span class="ripple"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="content-column col-lg-6 col-md-12 col-sm-12">
                        <div class="inner-column">
                            <div class="boxcar-title wow fadeInUp">
                                <h2>Định giá xe công bằng, bán xe cho chúng tôi ngay hôm nay</h2>
                                <div class="text">Quy trình thu cũ đổi mới minh bạch, thẩm định nhanh chóng và hỗ trợ khách hàng nâng cấp lên dòng xe mơ ước thuận tiện nhất tại showroom.</div>
                            </div>
                            <ul class="list-style-one wow fadeInUp" data-wow-delay="100ms">
                                <li><i class="fa-solid fa-check"></i>Định giá chính xác theo tình trạng thực tế và giá trị thị trường</li>
                                <li><i class="fa-solid fa-check"></i>Hỗ trợ thủ tục sang tên, giải chấp ngân hàng và hồ sơ vay nhanh gọn</li>
                                <li><i class="fa-solid fa-check"></i>Đổi trực tiếp sang mọi mẫu xe mới hoặc xe lướt có sẵn tại showroom</li>
                            </ul>
                            <a href="{{ route('tradein') }}" class="read-more wow fadeInUp" data-wow-delay="200ms">
                                Định giá xe ngay
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <g clip-path="url(#clip0_about_pricing)">
                                        <path d="M13.6106 0H5.05509C4.84013 0 4.66619 0.173943 4.66619 0.388901C4.66619 0.603859 4.84013 0.777802 5.05509 0.777802H12.6719L0.113453 13.3362C-0.0384687 13.4881 -0.0384687 13.7342 0.113453 13.8861C0.189396 13.962 0.288927 14 0.388422 14C0.487917 14 0.587411 13.962 0.663391 13.8861L13.2218 1.3277V8.94447C13.2218 9.15943 13.3957 9.33337 13.6107 9.33337C13.8256 9.33337 13.9996 9.15943 13.9996 8.94447V0.388901C13.9995 0.173943 13.8256 0 13.6106 0Z" fill="white"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_about_pricing">
                                            <rect width="14" height="14" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Team Section -->
        <div class="boxcar-team-section-two pt-0" style="margin-bottom: 70px;">
            <div class="boxcar-title text-center wow fadeInUp">
                <h2>Đội ngũ chuyên gia ô tô của chúng tôi</h2>
                <div class="text">Những con người nhiệt huyết, am hiểu sâu sắc về từng dòng xe, luôn sẵn sàng đồng hành cùng bạn.</div>
            </div>
            <div class="row">
                <div class="team-block-two col-lg-3 col-md-6 col-sm-6">
                    <div class="inner-box wow fadeInUp">
                        <div class="image-box">
                            <figure class="image"><img src="{{ asset('boxcar/images/resource/team2-1.jpg') }}" alt="Trần Đức Anh"></figure>
                            <div class="contact-info">
                                <span><a href="mailto:{{ $showroomEmail }}">{{ $showroomEmail }}</a></span>
                                <small><a href="tel:{{ $showroomPhone }}">{{ $showroomPhone }}</a></small>
                            </div>
                        </div>
                        <div class="content-box">
                            <h4 class="title"><a href="{{ route('contact') }}">Trần Đức Anh</a></h4>
                            <span>Giám Đốc Kinh Doanh</span>
                        </div>
                    </div>
                </div>
                <div class="team-block-two col-lg-3 col-md-6 col-sm-6">
                    <div class="inner-box wow fadeInUp" data-wow-delay="100ms">
                        <div class="image-box">
                            <figure class="image"><img src="{{ asset('boxcar/images/resource/team2-2.jpg') }}" alt="Nguyễn Thị Mai"></figure>
                            <div class="contact-info">
                                <span><a href="mailto:{{ $showroomEmail }}">{{ $showroomEmail }}</a></span>
                                <small><a href="tel:{{ $showroomPhone }}">{{ $showroomPhone }}</a></small>
                            </div>
                        </div>
                        <div class="content-box">
                            <h4 class="title"><a href="{{ route('contact') }}">Nguyễn Thị Mai</a></h4>
                            <span>Trưởng Phòng CSKH</span>
                        </div>
                    </div>
                </div>
                <div class="team-block-two col-lg-3 col-md-6 col-sm-6">
                    <div class="inner-box wow fadeInUp" data-wow-delay="200ms">
                        <div class="image-box">
                            <figure class="image"><img src="{{ asset('boxcar/images/resource/team2-3.jpg') }}" alt="Lê Hoàng Quân"></figure>
                            <div class="contact-info">
                                <span><a href="mailto:{{ $showroomEmail }}">{{ $showroomEmail }}</a></span>
                                <small><a href="tel:{{ $showroomPhone }}">{{ $showroomPhone }}</a></small>
                            </div>
                        </div>
                        <div class="content-box">
                            <h4 class="title"><a href="{{ route('contact') }}">Lê Hoàng Quân</a></h4>
                            <span>Kỹ Sư Trưởng Giám Định</span>
                        </div>
                    </div>
                </div>
                <div class="team-block-two col-lg-3 col-md-6 col-sm-6">
                    <div class="inner-box wow fadeInUp" data-wow-delay="300ms">
                        <div class="image-box">
                            <figure class="image"><img src="{{ asset('boxcar/images/resource/team2-4.jpg') }}" alt="Phạm Thu Trang"></figure>
                            <div class="contact-info">
                                <span><a href="mailto:{{ $showroomEmail }}">{{ $showroomEmail }}</a></span>
                                <small><a href="tel:{{ $showroomPhone }}">{{ $showroomPhone }}</a></small>
                            </div>
                        </div>
                        <div class="content-box">
                            <h4 class="title"><a href="{{ route('contact') }}">Phạm Thu Trang</a></h4>
                            <span>Chuyên Viên Tư Vấn Tài Chính</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. FAQs Section -->
        <div class="faqs-section pt-0" style="margin-bottom: 70px;">
            <div class="inner-container" style="max-width: 900px; margin: 0 auto;">
                <div class="faq-column wow fadeInUp">
                    <div class="inner-column">
                        <div class="boxcar-title text-center">
                            <h2 class="title">Câu hỏi thường gặp</h2>
                            <div class="text">Giải đáp các thắc mắc phổ biến nhất khi tìm hiểu và mua xe tại showroom</div>
                        </div>
                        <ul class="widget-accordion wow fadeInUp">
                            <li class="accordion block active-block">
                                <div class="acc-btn active">Xe tại showroom có được bảo hành và kiểm định chất lượng không?<div class="icon fa fa-plus"></div></div>
                                <div class="acc-content current">
                                    <div class="content">
                                        <div class="text">100% xe tại showroom đều trải qua quy trình kiểm định 160 bước nghiêm ngặt về khung gầm, máy móc, hộp số và lịch sử vận hành. Chúng tôi cam kết bảo hành động cơ và hộp số từ 12 đến 24 tháng hoặc 20.000 km, cùng chính sách cam kết bằng văn bản: xe không đâm đụng, không ngập nước, hồ sơ pháp lý minh bạch hoàn toàn.</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                <div class="acc-btn">Tôi có thể lái thử xe trước khi quyết định mua không?<div class="icon fa fa-plus"></div></div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Hoàn toàn có thể. Chúng tôi luôn khuyến khích khách hàng trực tiếp trải nghiệm cảm giác lái và kiểm tra chi tiết các trang bị trước khi ra quyết định. Quý khách chỉ cần liên hệ hotline hoặc gửi yêu cầu hẹn trước, chuyên viên sẽ chuẩn bị xe chu đáo và đồng hành cùng quý khách lái thử trải nghiệm.</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                <div class="acc-btn">Thủ tục mua xe trả góp qua ngân hàng cần chuẩn bị những gì?<div class="icon fa fa-plus"></div></div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Thủ tục trả góp tại showroom rất đơn giản và nhanh gọn. Quý khách chỉ cần chuẩn bị CCCD gắn chip và giấy tờ chứng minh thu nhập cơ bản. Đội ngũ chuyên viên tài chính của chúng tôi sẽ liên hệ đối tác ngân hàng uy tín, hỗ trợ duyệt gói vay lên tới 80% giá trị xe với lãi suất ưu đãi chỉ trong vòng 24 giờ.</div>
                                    </div>
                                </div>
                            </li>
                            <li class="accordion block">
                                <div class="acc-btn">Showroom có hỗ trợ thu mua xe cũ và dịch vụ thu cũ đổi mới không?<div class="icon fa fa-plus"></div></div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">Có, chúng tôi cung cấp dịch vụ Trade-in (Thu cũ đổi mới) chuyên nghiệp. Kỹ thuật viên của showroom sẽ thẩm định thực tế chiếc xe của bạn theo giá trị thị trường tốt nhất và hỗ trợ thủ tục bù trừ trực tiếp để bạn nâng cấp sang dòng xe mới một cách tiện lợi, nhanh chóng nhất trong ngày.</div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Call to Action Banner (Matches Mockup 1 & 2 CTA) -->
        <div class="boxcar-cta-about wow fadeInUp">
            <div class="inner-box" style="background: #050B20; border-radius: 20px; padding: 50px 36px; text-align: center; color: #fff; box-shadow: 0 16px 40px rgba(5, 11, 32, 0.15);">
                <h2 style="color: #fff; font-size: clamp(22px, 2.6vw, 30px); font-weight: 800; margin-bottom: 24px; text-transform: uppercase; letter-spacing: -0.01em;">BẠN ĐÃ SẴN SÀNG CHO HÀNH TRÌNH MỚI CÙNG BOXCAR?</h2>
                <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('inventory.index') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 26px; border-radius: 12px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.25); color: #fff; font-weight: 700; text-decoration: none; font-size: 14px; transition: 0.3s;">KHÁM PHÁ BỘ SƯU TẬP XE</a>
                    <a href="{{ route('contact') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 26px; border-radius: 12px; background: #405FF2; color: #fff; font-weight: 700; text-decoration: none; font-size: 14px; transition: 0.3s; box-shadow: 0 4px 14px rgba(64, 95, 242, 0.4);">LIÊN HỆ ĐẶT LỊCH HẸN</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
