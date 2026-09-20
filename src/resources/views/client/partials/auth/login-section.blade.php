@php($activeTab = old('form_mode', request('tab', 'login')))

@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Password reveal/hide toggle
            const passwordToggles = document.querySelectorAll('.js-password-toggle');
            passwordToggles.forEach(function (button) {
                button.addEventListener('click', function () {
                    const wrapper = button.closest('.field-wrapper');
                    if (!wrapper) return;
                    const input = wrapper.querySelector('.js-password-input');
                    const icon = button.querySelector('i');
                    if (!input || !icon) return;

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                        button.setAttribute('aria-label', 'Ẩn mật khẩu');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                        button.setAttribute('aria-label', 'Hiện mật khẩu');
                    }
                });
            });

            // Quick tab switch links
            const registerTabBtn = document.getElementById('register-tab-btn');
            const loginTabBtn = document.getElementById('login-tab-btn');

            document.querySelectorAll('.js-switch-to-register').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (registerTabBtn) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(registerTabBtn);
                        tabInstance.show();
                    }
                });
            });

            document.querySelectorAll('.js-switch-to-login').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (loginTabBtn) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(loginTabBtn);
                        tabInstance.show();
                    }
                });
            });

            // Forgot password modal triggers
            const modalEl = document.getElementById('forgotPasswordModal');
            if (modalEl) {
                document.querySelectorAll('.js-forgot-trigger').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    });
                });
            }
        });
    </script>
    @endpush
@endonce

<section class="boxcar-auth-section">
    <div class="boxcar-container">
        <div class="boxcar-auth-card">
            {{-- Header: Logo & Subtitle --}}
            <div class="boxcar-auth-head">
                <a href="{{ route('home') }}" class="auth-brand-logo">
                    <img src="{{ asset('boxcar/images/logo2.svg') }}" alt="BoxCar" title="BoxCar Showroom">
                </a>
                <h2 class="auth-title">Chào mừng bạn trở lại</h2>
                <p class="auth-desc">Đăng nhập tài khoản để trải nghiệm đầy đủ tiện ích</p>
            </div>

            {{-- Underline Navigation Tabs --}}
            <nav class="boxcar-auth-nav">
                <div class="nav nav-tabs" id="authTab" role="tablist">
                    <button class="nav-link {{ $activeTab !== 'register' ? 'active' : '' }}" id="login-tab-btn" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="{{ $activeTab !== 'register' ? 'true' : 'false' }}">
                        Đăng nhập
                    </button>
                    <button class="nav-link {{ $activeTab === 'register' ? 'active' : '' }}" id="register-tab-btn" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="{{ $activeTab === 'register' ? 'true' : 'false' }}">
                        Đăng ký
                    </button>
                </div>
            </nav>

            {{-- Tab Content --}}
            <div class="tab-content" id="authTabContent">
                {{-- TAB 1: ĐĂNG NHẬP --}}
                <div class="tab-pane fade {{ $activeTab !== 'register' ? 'show active' : '' }}" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab-btn">
                    <form method="POST" action="{{ route('login.attempt') }}">
                        @csrf
                        <input type="hidden" name="form_mode" value="login">

                        <div class="form-group-field">
                            <label for="login-identifier">Email hoặc số điện thoại</label>
                            <div class="field-wrapper">
                                <i class="fa fa-user field-icon"></i>
                                <input id="login-identifier" class="@error('identifier') is-invalid @enderror" type="text" name="identifier" value="{{ $activeTab !== 'register' ? old('identifier') : '' }}" placeholder="email@example.com hoặc 0901234567" autocomplete="username" autocapitalize="none" spellcheck="false" required>
                            </div>
                            @error('identifier')
                                <span class="error-text"><i class="fa fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-field">
                            <label for="login-password">Mật khẩu</label>
                            <div class="field-wrapper">
                                <i class="fa fa-lock field-icon"></i>
                                <input id="login-password" class="js-password-input @error('password') is-invalid @enderror" type="password" name="password" placeholder="Nhập mật khẩu của bạn" autocomplete="current-password" required>
                                <button type="button" class="btn-toggle-password js-password-toggle" tabindex="-1" title="Hiện/Ẩn mật khẩu" aria-label="Hiện/Ẩn mật khẩu">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="error-text"><i class="fa fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="auth-actions-row">
                            <label class="custom-check">
                                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                <span>Ghi nhớ đăng nhập</span>
                            </label>
                            <button type="button" class="link-forgot js-forgot-trigger">Quên mật khẩu?</button>
                        </div>

                        <button type="submit" class="btn-primary-auth">
                            <span>Đăng nhập</span>
                            <i class="fa fa-angle-right"></i>
                        </button>

                        <div class="auth-divider">
                            <span>Hoặc</span>
                        </div>

                        <a href="{{ route('contact') }}" class="btn-secondary-auth">
                            <i class="fa fa-headset"></i>
                            <span>Liên hệ tư vấn viên</span>
                        </a>

                        <div class="auth-card-footer">
                            <span>Chưa có tài khoản?</span>
                            <a href="javascript:void(0)" class="js-switch-to-register">Đăng ký ngay</a>
                        </div>
                    </form>
                </div>

                {{-- TAB 2: ĐĂNG KÝ --}}
                <div class="tab-pane fade {{ $activeTab === 'register' ? 'show active' : '' }}" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab-btn">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden" name="form_mode" value="register">

                        <div class="form-group-field">
                            <label for="register-name">Họ và tên <span class="text-danger">*</span></label>
                            <div class="field-wrapper">
                                <i class="fa fa-id-card field-icon"></i>
                                <input id="register-name" class="@error('name') is-invalid @enderror" type="text" name="name" value="{{ $activeTab === 'register' ? old('name') : '' }}" placeholder="Ví dụ: Nguyễn Văn An" autocomplete="name" required>
                            </div>
                            @error('name')
                                <span class="error-text"><i class="fa fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-field">
                            <label for="register-email">Địa chỉ Email</label>
                            <div class="field-wrapper">
                                <i class="fa fa-envelope field-icon"></i>
                                <input id="register-email" class="@error('email') is-invalid @enderror" type="email" name="email" value="{{ $activeTab === 'register' ? old('email') : '' }}" placeholder="name@email.com" autocomplete="email" autocapitalize="none" spellcheck="false">
                            </div>
                            <span class="field-hint">Dùng để nhận xác nhận lịch hẹn lái thử và chứng từ.</span>
                            @error('email')
                                <span class="error-text"><i class="fa fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-field">
                            <label for="register-phone">Số điện thoại</label>
                            <div class="field-wrapper">
                                <i class="fa fa-phone field-icon"></i>
                                <input id="register-phone" class="@error('phone') is-invalid @enderror" type="text" name="phone" value="{{ $activeTab === 'register' ? old('phone') : '' }}" placeholder="Ví dụ: 0901234567" autocomplete="tel">
                            </div>
                            @error('phone')
                                <span class="error-text"><i class="fa fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-field">
                            <label for="register-password">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="field-wrapper">
                                <i class="fa fa-lock field-icon"></i>
                                <input id="register-password" class="js-password-input @error('password') is-invalid @enderror" type="password" name="password" placeholder="Tối thiểu 6 ký tự" autocomplete="new-password" required>
                                <button type="button" class="btn-toggle-password js-password-toggle" tabindex="-1" title="Hiện/Ẩn mật khẩu" aria-label="Hiện/Ẩn mật khẩu">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="error-text"><i class="fa fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="terms-row @error('accept_privacy') has-error @enderror">
                            <label class="custom-check">
                                <input type="checkbox" name="accept_privacy" value="1" {{ old('accept_privacy') ? 'checked' : '' }} required>
                                <span class="checkmark"></span>
                                <span>Tôi đồng ý với <a href="{{ route('about') }}" target="_blank" class="text-primary text-decoration-underline">Chính sách bảo mật</a> &amp; Điều khoản thành viên.</span>
                            </label>
                            @error('accept_privacy')
                                <span class="error-text"><i class="fa fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn-primary-auth">
                            <span>Tạo tài khoản</span>
                            <i class="fa fa-angle-right"></i>
                        </button>

                        <div class="auth-card-footer">
                            <span>Đã có tài khoản?</span>
                            <a href="javascript:void(0)" class="js-switch-to-login">Đăng nhập ngay</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Modal hỗ trợ Quên mật khẩu --}}
<div class="modal fade auth-support-modal" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">
                    <i class="fa fa-shield-halved"></i> Hỗ trợ Khôi phục Mật khẩu
                </h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Đóng">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p style="color: #475467; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                    Để bảo vệ an toàn thông tin tài khoản và dữ liệu giao dịch xe, BoxCar cung cấp hai phương thức hỗ trợ cấp lại mật khẩu xác thực trực tiếp:
                </p>

                <div class="support-channel-card">
                    <div class="channel-icon hotline">
                        <i class="fa fa-phone-volume"></i>
                    </div>
                    <div class="channel-content">
                        <strong>Tổng đài hỗ trợ trực tiếp (24/7)</strong>
                        <p>Xác thực nhanh qua số điện thoại đăng ký và nhận mã kích hoạt/mật khẩu tạm trong 2 phút.</p>
                        <a href="tel:19008888" class="channel-action hotline">
                            <i class="fa fa-phone"></i> Gọi ngay: 1900 8888 (Miễn phí)
                        </a>
                    </div>
                </div>

                <div class="support-channel-card">
                    <div class="channel-icon">
                        <i class="fa fa-envelope-open-text"></i>
                    </div>
                    <div class="channel-content">
                        <strong>Gửi yêu cầu qua bộ phận Chăm sóc Khách hàng</strong>
                        <p>Để lại thông tin tại trang liên hệ, chuyên viên hỗ trợ sẽ liên hệ xử lý trong vòng 15 phút.</p>
                        <a href="{{ route('contact') }}" class="channel-action">
                            <i class="fa fa-arrow-right"></i> Đi đến trang Liên hệ hỗ trợ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
