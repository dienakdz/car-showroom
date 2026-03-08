@php($footerClasses = $footerClasses ?? 'boxcar-footer footer-style-one')

<footer class="{{ $footerClasses }}">
    <div class="footer-top">
        <div class="boxcar-container">
            <div class="right-box">
                <div class="top-left wow fadeInUp">
                    <h6 class="title">Nhận thông tin xe mới</h6>
                    <div class="text">Cập nhật giá, chương trình và xe mới về kho mỗi tuần.</div>
                </div>
                <div class="subscribe-form wow fadeInUp" data-wow-delay="100ms">
                    <form action="{{ route('contact') }}" method="GET">
                        <div class="form-group">
                            <input type="email" class="email" placeholder="Email của bạn" required>
                            <button type="submit" class="theme-btn btn-style-one hover-light"><span class="btn-title">Đăng ký</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="widgets-section">
        <div class="boxcar-container">
            <div class="row">
                <div class="footer-column-two col-lg-9 col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="footer-widget links-widget wow fadeInUp">
                                <h4 class="widget-title">Điều hướng</h4>
                                <div class="widget-content">
                                    <ul class="user-links style-two">
                                        <li><a href="{{ route('home') }}">Trang chủ</a></li>
                                        <li><a href="{{ route('inventory.new') }}">Xe mới</a></li>
                                        <li><a href="{{ route('inventory.used') }}">Xe cũ</a></li>
                                        <li><a href="{{ route('inventory.index') }}">Kho xe</a></li>
                                        <li><a href="{{ route('about') }}">Về chúng tôi</a></li>
                                        <li><a href="{{ route('contact') }}">Liên hệ</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="footer-widget links-widget wow fadeInUp" data-wow-delay="100ms">
                                <h4 class="widget-title">Dịch vụ</h4>
                                <div class="widget-content">
                                    <ul class="user-links style-two">
                                        <li><a href="{{ route('finance') }}">Tư vấn tài chính</a></li>
                                        <li><a href="{{ route('tradein') }}">Thu cũ đổi mới</a></li>
                                        <li><a href="{{ route('contact') }}">Đặt lịch xem xe</a></li>
                                        <li><a href="{{ route('contact') }}">Test drive</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="footer-widget links-widget wow fadeInUp" data-wow-delay="200ms">
                                <h4 class="widget-title">Loại xe phổ biến</h4>
                                <div class="widget-content">
                                    <ul class="user-links style-two">
                                        <li><a href="{{ route('inventory.index', ['body_type' => 'sedan']) }}">Sedan</a></li>
                                        <li><a href="{{ route('inventory.index', ['body_type' => 'suv']) }}">SUV</a></li>
                                        <li><a href="{{ route('inventory.index', ['body_type' => 'pickup']) }}">Pickup</a></li>
                                        <li><a href="{{ route('inventory.cpo') }}">CPO</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-column col-lg-3 col-md-12 col-sm-12">
                    <div class="footer-widget links-widget wow fadeInUp" data-wow-delay="300ms">
                        <h4 class="widget-title">Thông tin showroom</h4>
                        <div class="widget-content">
                            <ul class="user-links style-two">
                                <li>{{ $navShowroom->name ?? 'Car Showroom' }}</li>
                                <li>{{ $navShowroom->phone ?? '0900 000 000' }}</li>
                                <li>{{ $navShowroom->email ?? 'hello@showroom.test' }}</li>
                                <li>{{ $navShowroom->address ?? 'TP. Hồ Chí Minh' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="boxcar-container">
            <div class="inner-container">
                <div class="copyright-text">© {{ date('Y') }} Car Showroom. All rights reserved.</div>
                <ul class="footer-nav">
                    <li><a href="{{ route('about') }}">Giới thiệu</a></li>
                    <li><a href="{{ route('contact') }}">Liên hệ</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
