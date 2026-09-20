@php($footerClasses = $footerClasses ?? 'boxcar-footer footer-style-one')



<footer class="{{ $footerClasses }}">
    {{-- Showroom Trust Bar (4 Cam kết Đại lý) --}}
    <div class="footer-trust-bar">
        <div class="boxcar-container">
            <div class="row g-3">
                <div class="col-lg-3 col-sm-6">
                    <div class="trust-item">
                        <div class="trust-icon">
                            <i class="fa-solid fa-shield-check"></i>
                        </div>
                        <div class="trust-info">
                            <div class="trust-title">Kiểm định 160 điểm</div>
                            <div class="trust-desc">Minh bạch lịch sử, cam kết không đâm đụng, thủy kích</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="trust-item">
                        <div class="trust-icon">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div class="trust-info">
                            <div class="trust-title">Hỗ trợ vay 85%</div>
                            <div class="trust-desc">Lãi suất ưu đãi từ 6.9%, duyệt hồ sơ trong 24h</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="trust-item">
                        <div class="trust-icon">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </div>
                        <div class="trust-info">
                            <div class="trust-title">Thu cũ đổi mới</div>
                            <div class="trust-desc">Định giá xe trong 15 phút, trợ giá thu mua đến 30 triệu</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="trust-item">
                        <div class="trust-icon">
                            <i class="fa-solid fa-truck-ramp-box"></i>
                        </div>
                        <div class="trust-info">
                            <div class="trust-title">Giao xe toàn quốc</div>
                            <div class="trust-desc">Bàn giao xe chu đáo, bảo hành tại nơi sử dụng</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Widgets Section (4 Cột thông tin chuẩn Showroom) --}}
    <div class="widgets-section">
        <div class="boxcar-container">
            <div class="row g-4">
                {{-- Cột 1: Thông tin Showroom & Thương hiệu --}}
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="footer-widget">
                        <div class="footer-logo mb-3">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('boxcar/images/logo.svg') }}" alt="BoxCar Showroom" style="max-height: 28px;">
                            </a>
                        </div>
                        <p class="small text-muted mb-3" style="line-height: 1.6;">
                            Hệ thống showroom mua bán ô tô chính hãng, xe lướt đã qua kiểm định uy tín hàng đầu. Cam kết chất lượng và dịch vụ tận tâm.
                        </p>
                        <div class="showroom-info-list">
                            <div class="showroom-info-item">
                                <i class="fa-solid fa-phone"></i>
                                <div>
                                    <span class="d-block small text-muted">Hotline tư vấn 24/7:</span>
                                    <a href="tel:{{ $navShowroom->phone ?? '1900 8888' }}"><strong>{{ $navShowroom->phone ?? '1900 8888' }}</strong></a>
                                </div>
                            </div>
                            <div class="showroom-info-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>{{ $navShowroom->address ?? '456 Lê Văn Lương, P. Tân Phong, Quận 7, TP.HCM' }}</span>
                            </div>
                            <div class="showroom-info-item">
                                <i class="fa-solid fa-envelope"></i>
                                <a href="mailto:{{ $navShowroom->email ?? 'contact@boxcar.vn' }}">{{ $navShowroom->email ?? 'contact@boxcar.vn' }}</a>
                            </div>
                        </div>
                        <div class="footer-socials mt-3">
                            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                            <a href="https://tiktok.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                            <a href="https://zalo.me" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="Zalo"><i class="fa-solid fa-comment-dots"></i></a>
                        </div>
                    </div>
                </div>

                {{-- Cột 2: Danh mục Xe & Kho xe --}}
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="footer-widget">
                        <h4 class="widget-title">Kho xe & Danh mục</h4>
                        <ul class="footer-links">
                            <li><a href="{{ route('inventory.index') }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Tất cả kho xe</a></li>
                            <li><a href="{{ route('inventory.new') }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Xe mới chính hãng</a></li>
                            <li><a href="{{ route('inventory.used') }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Xe đã qua sử dụng</a></li>
                            <li><a href="{{ route('inventory.cpo') }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Xe lướt CPO chứng nhận</a></li>
                            <li><a href="{{ route('inventory.index', ['body_type' => 'sedan']) }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Sedan sang trọng</a></li>
                            <li><a href="{{ route('inventory.index', ['body_type' => 'suv']) }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>SUV đa dụng</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Cột 3: Dịch vụ & Hỗ trợ khách hàng --}}
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="footer-widget">
                        <h4 class="widget-title">Dịch vụ & Hỗ trợ</h4>
                        <ul class="footer-links">
                            <li><a href="{{ route('finance') }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Tư vấn tài chính & Trả góp</a></li>
                            <li><a href="{{ route('tradein') }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Định giá xe & Thu cũ đổi mới</a></li>
                            <li><a href="{{ route('contact') }}#test-drive"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Đăng ký lái thử xe</a></li>
                            <li><a href="{{ route('about') }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Về chúng tôi & Cam kết</a></li>
                            <li><a href="{{ route('contact') }}"><i class="fa-solid fa-angle-right me-2 small text-primary"></i>Liên hệ & Tìm showroom</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Cột 4: Giờ làm việc & Đăng ký nhận ưu đãi --}}
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="footer-widget">
                        <h4 class="widget-title">Giờ mở cửa & Bản tin</h4>
                        <div class="hours-card">
                            <div class="hours-row">
                                <span>Thứ 2 - Thứ 7:</span>
                                <strong class="text-white">08:00 - 20:00</strong>
                            </div>
                            <div class="hours-row">
                                <span>Chủ nhật:</span>
                                <strong class="text-white">08:00 - 18:00</strong>
                            </div>
                        </div>

                        <div class="footer-newsletter-wrap">
                            <span class="d-block small text-muted mb-2">Đăng ký nhận bảng giá lăn bánh và xe mới về kho mỗi tuần:</span>
                            <form action="{{ route('contact') }}" method="GET" class="footer-newsletter-form">
                                <div class="input-group">
                                    <input type="email" name="subscribe_email" class="form-control" placeholder="Nhập email của bạn..." required>
                                    <button type="submit" class="btn btn-subscribe" title="Gửi email">
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Bottom --}}
    <div class="footer-bottom">
        <div class="boxcar-container">
            <div class="inner-container d-flex align-items-center justify-content-between">
                <div class="copyright-text">
                    © {{ date('Y') }} {{ $navShowroom->name ?? 'BoxCar Showroom' }}. Bản quyền thuộc về đại lý BoxCar Việt Nam. All rights reserved.
                </div>
                <div class="footer-legal-links">
                    <a href="{{ route('about') }}">Giới thiệu</a>
                    <a href="{{ route('contact') }}">Chính sách bảo mật</a>
                    <a href="{{ route('contact') }}">Điều khoản sử dụng</a>
                    <a href="{{ route('contact') }}">Liên hệ hỗ trợ</a>
                </div>
            </div>
        </div>
    </div>
</footer>
