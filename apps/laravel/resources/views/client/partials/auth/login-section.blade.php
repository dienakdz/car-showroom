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

            // Resend activation modal triggers
            const resendModalEl = document.getElementById('resendActivationModal');
            if (resendModalEl) {
                document.querySelectorAll('.js-resend-trigger').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const loginIdentifier = document.getElementById('login-identifier');
                        const resendEmailInput = document.getElementById('resend-email');
                        if (loginIdentifier && resendEmailInput && !resendEmailInput.value.trim() && loginIdentifier.value.includes('@')) {
                            resendEmailInput.value = loginIdentifier.value.trim();
                        }
                        const modal = bootstrap.Modal.getOrCreateInstance(resendModalEl);
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
                    <img src="{{ asset('boxcar/images/logo2.svg') }}" alt="MD-CARS Showroom" title="MD-CARS Showroom">
                </a>
                <h2 class="auth-title">Chào mừng bạn trở lại</h2>
                <p class="auth-desc">Đăng nhập tài khoản để trải nghiệm đầy đủ tiện ích</p>
            </div>

            @if (session('auth_notice'))
                @php($notice = session('auth_notice'))
                <div class="auth-notice-alert auth-notice-{{ $notice['type'] ?? 'info' }}" style="margin-bottom: 24px; padding: 14px 18px; border-radius: 8px; font-size: 14px; line-height: 1.5; display: flex; gap: 12px; align-items: flex-start; {{ ($notice['type'] ?? '') === 'success' ? 'background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;' : (($notice['type'] ?? '') === 'danger' ? 'background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;' : 'background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;') }}">
                    <i class="fa {{ ($notice['type'] ?? '') === 'success' ? 'fa-circle-check text-success' : (($notice['type'] ?? '') === 'danger' ? 'fa-circle-xmark text-danger' : 'fa-circle-info text-primary') }}" style="font-size: 18px; margin-top: 2px;"></i>
                    <div style="flex: 1;">
                        @if (! empty($notice['title']))
                            <strong style="display: block; font-weight: 600; margin-bottom: 4px;">{{ $notice['title'] }}</strong>
                        @endif
                        <span>{{ $notice['message'] }}</span>
                    </div>
                </div>
            @endif

            @if (session('unverified_email'))
                <div class="resend-activation-box" style="margin-bottom: 20px; padding: 12px 16px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <span style="font-size: 13px; color: #92400e;">
                        <i class="fa fa-envelope-open-text me-1"></i> Chưa nhận được email kích hoạt?
                    </span>
                    <form method="POST" action="{{ route('verification.resend') }}" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                        <button type="submit" class="btn btn-sm" style="font-size: 12px; font-weight: 600; padding: 5px 14px; border-radius: 6px; background: #f59e0b; color: #ffffff; border: none;">
                            Gửi lại email ngay
                        </button>
                    </form>
                </div>
            @endif

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
                            <div style="display: flex; gap: 14px; align-items: center;">
                                <button type="button" class="link-forgot js-resend-trigger" title="Yêu cầu gửi lại email kích hoạt tài khoản">Kích hoạt tài khoản?</button>
                                <button type="button" class="link-forgot js-forgot-trigger">Quên mật khẩu?</button>
                            </div>
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
                            <label for="register-email">Địa chỉ Email <span class="text-danger">*</span></label>
                            <div class="field-wrapper">
                                <i class="fa fa-envelope field-icon"></i>
                                <input id="register-email" class="@error('email') is-invalid @enderror" type="email" name="email" value="{{ $activeTab === 'register' ? old('email') : '' }}" placeholder="name@email.com" autocomplete="email" autocapitalize="none" spellcheck="false" required>
                            </div>
                            <span class="field-hint">Dùng để nhận liên kết kích hoạt tài khoản và xác nhận giao dịch.</span>
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

@once
    @push('modals')
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
                            Để bảo vệ an toàn thông tin tài khoản và dữ liệu giao dịch xe, MD-CARS cung cấp hai phương thức hỗ trợ cấp lại mật khẩu xác thực trực tiếp:
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

        {{-- Modal Gửi lại email kích hoạt tài khoản --}}
        <div class="modal fade auth-support-modal" id="resendActivationModal" tabindex="-1" aria-labelledby="resendActivationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resendActivationModalLabel">
                            <i class="fa fa-envelope-circle-check"></i> Kích hoạt Tài khoản
                        </h5>
                        <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Đóng">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <div class="modal-body">
                            <p style="color: #475467; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                                Quý khách vui lòng nhập địa chỉ email đã dùng để đăng ký tài khoản. Hệ thống MD-CARS sẽ gửi lại một liên kết kích hoạt mới (có hiệu lực trong 24 giờ).
                            </p>

                            <div class="form-group-field" style="margin-bottom: 8px;">
                                <label for="resend-email">
                                    Địa chỉ Email của bạn <span class="text-danger">*</span>
                                </label>
                                <div class="field-wrapper">
                                    <i class="fa fa-envelope field-icon"></i>
                                    <input id="resend-email" type="email" name="email" value="{{ session('unverified_email', old('email')) }}" placeholder="name@email.com" autocomplete="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 15px 24px; display: flex; justify-content: flex-end; gap: 10px;">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="font-size: 14px; font-weight: 500;">Hủy</button>
                            <button type="submit" class="btn btn-primary" style="background-color: #405ff2; border-color: #405ff2; font-size: 14px; font-weight: 600; padding: 8px 20px; border-radius: 8px;">
                                <i class="fa fa-paper-plane me-1"></i> Gửi lại email kích hoạt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endpush
@endonce
