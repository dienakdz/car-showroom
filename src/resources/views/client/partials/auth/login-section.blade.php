@php($activeTab = old('form_mode', request('tab', 'login')))

@once
    @push('styles')
    <style>
        .boxcar-auth-section {
            padding: 64px 0 96px;
            background: linear-gradient(180deg, #f4f6fa 0%, #ffffff 45%, #f1f4f9 100%);
            min-height: calc(100vh - 180px);
            display: flex;
            align-items: center;
        }

        .boxcar-auth-card {
            max-width: 500px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            padding: 44px 40px 38px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            position: relative;
        }

        /* --- Logo & Head --- */
        .boxcar-auth-head {
            text-align: center;
            margin-bottom: 24px;
        }

        .boxcar-auth-head .auth-brand-logo {
            display: inline-block;
            margin-bottom: 16px;
        }

        .boxcar-auth-head .auth-brand-logo img {
            height: 38px;
            width: auto;
        }

        .boxcar-auth-head .auth-title {
            font-size: 24px;
            font-weight: 700;
            color: #050b20;
            margin-bottom: 6px;
            letter-spacing: -0.01em;
        }

        .boxcar-auth-head .auth-desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
            margin: 0;
        }

        /* --- Underline Navigation Tabs --- */
        .boxcar-auth-nav {
            margin-bottom: 28px;
            border-bottom: 1.5px solid #e2e8f0;
        }

        .boxcar-auth-nav .nav-tabs {
            border: 0;
            display: flex;
            justify-content: center;
            gap: 48px;
            margin: 0;
        }

        .boxcar-auth-nav .nav-tabs .nav-link {
            position: relative;
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: 16px;
            font-weight: 600;
            padding: 0 6px 14px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .boxcar-auth-nav .nav-tabs .nav-link:hover {
            color: #050b20;
        }

        .boxcar-auth-nav .nav-tabs .nav-link.active {
            color: #050b20;
            font-weight: 700;
        }

        .boxcar-auth-nav .nav-tabs .nav-link.active::after {
            content: "";
            position: absolute;
            bottom: -1.5px;
            left: 0;
            right: 0;
            height: 2.5px;
            background-color: #405ff2;
            border-radius: 2px 2px 0 0;
        }

        /* --- Input Fields --- */
        .boxcar-auth-card .form-group-field {
            margin-bottom: 18px;
        }

        .boxcar-auth-card .form-group-field label {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .boxcar-auth-card .field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .boxcar-auth-card .field-wrapper .field-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
            z-index: 2;
        }

        .boxcar-auth-card .field-wrapper input {
            width: 100%;
            height: 52px;
            padding: 0 46px 0 44px;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            font-size: 14.5px;
            color: #0f172a;
            transition: all 0.2s ease;
            outline: none;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        .boxcar-auth-card .field-wrapper input:hover {
            border-color: #94a3b8;
            background: #ffffff;
        }

        .boxcar-auth-card .field-wrapper input:focus {
            border-color: #405ff2;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(64, 95, 242, 0.12);
        }

        .boxcar-auth-card .field-wrapper input.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .boxcar-auth-card .field-wrapper .btn-toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border: 0;
            background: transparent;
            color: #94a3b8;
            font-size: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: color 0.15s ease, background-color 0.15s ease;
            z-index: 2;
        }

        .boxcar-auth-card .field-wrapper .btn-toggle-password:hover {
            color: #405ff2;
            background: #eef2ff;
        }

        .boxcar-auth-card .error-text {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            color: #dc2626;
            margin-top: 6px;
            font-weight: 500;
            line-height: 1.4;
        }

        .boxcar-auth-card .field-hint {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
            line-height: 1.4;
        }

        /* --- Actions Row (Remember + Forgot) --- */
        .boxcar-auth-card .auth-actions-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .boxcar-auth-card .custom-check {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            cursor: pointer;
            user-select: none;
            font-size: 14px;
            color: #334155;
            font-weight: 500;
        }

        .boxcar-auth-card .custom-check input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            width: 18px;
            height: 18px;
            margin: 0;
        }

        .boxcar-auth-card .custom-check .checkmark {
            width: 19px;
            height: 19px;
            border: 1.5px solid #cbd5e1;
            border-radius: 5px;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .boxcar-auth-card .custom-check:hover .checkmark {
            border-color: #94a3b8;
        }

        .boxcar-auth-card .custom-check input:checked ~ .checkmark {
            border-color: #405ff2;
            background: #405ff2;
        }

        .boxcar-auth-card .custom-check .checkmark::after {
            content: "";
            display: none;
            width: 5px;
            height: 9px;
            border: solid #ffffff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            margin-bottom: 2px;
        }

        .boxcar-auth-card .custom-check input:checked ~ .checkmark::after {
            display: block;
        }

        .boxcar-auth-card .terms-row {
            margin-bottom: 20px;
        }

        .boxcar-auth-card .terms-row.has-error .checkmark {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        }

        .boxcar-auth-card .link-forgot {
            font-size: 13.5px;
            color: #405ff2;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            background: none;
            border: 0;
            padding: 0;
            transition: color 0.15s ease;
        }

        .boxcar-auth-card .link-forgot:hover {
            color: #2642cb;
            text-decoration: underline;
        }

        /* --- Buttons --- */
        .boxcar-auth-card .btn-primary-auth {
            width: 100%;
            height: 52px;
            border: 0;
            border-radius: 12px;
            background: #405ff2;
            color: #ffffff;
            font-size: 15.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(64, 95, 242, 0.25);
        }

        .boxcar-auth-card .btn-primary-auth:hover {
            background: #2b49e0;
            box-shadow: 0 8px 22px rgba(64, 95, 242, 0.38);
            transform: translateY(-1px);
        }

        .boxcar-auth-card .btn-primary-auth i {
            font-size: 14px;
            transition: transform 0.2s ease;
        }

        .boxcar-auth-card .btn-primary-auth:hover i {
            transform: translateX(3px);
        }

        /* --- Divider --- */
        .boxcar-auth-card .auth-divider {
            position: relative;
            text-align: center;
            margin: 22px 0 18px;
        }

        .boxcar-auth-card .auth-divider::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 1px;
            background: #e2e8f0;
        }

        .boxcar-auth-card .auth-divider span {
            position: relative;
            background: #ffffff;
            padding: 0 14px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
        }

        /* --- Secondary Support Button --- */
        .boxcar-auth-card .btn-secondary-auth {
            width: 100%;
            height: 48px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .boxcar-auth-card .btn-secondary-auth:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: #0f172a;
        }

        .boxcar-auth-card .auth-card-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13.5px;
            color: #64748b;
        }

        .boxcar-auth-card .auth-card-footer a {
            color: #405ff2;
            font-weight: 700;
            text-decoration: none;
            margin-left: 4px;
        }

        .boxcar-auth-card .auth-card-footer a:hover {
            text-decoration: underline;
        }

        /* --- Modal Support --- */
        .auth-support-modal .modal-content {
            border-radius: 20px;
            border: 0;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.2);
        }

        .auth-support-modal .modal-header {
            background: #050b20;
            color: #ffffff;
            border-bottom: 0;
            padding: 20px 24px;
        }

        .auth-support-modal .modal-header .modal-title {
            color: #ffffff;
            font-size: 17px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .auth-support-modal .btn-close-custom {
            background: rgba(255, 255, 255, 0.15);
            border: 0;
            color: #ffffff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .auth-support-modal .btn-close-custom:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .auth-support-modal .modal-body {
            padding: 24px;
        }

        .auth-support-modal .support-channel-card {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }

        .auth-support-modal .support-channel-card:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
        }

        .auth-support-modal .channel-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eef2ff;
            color: #405ff2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .auth-support-modal .channel-icon.hotline {
            background: #ecfdf5;
            color: #059669;
        }

        .auth-support-modal .channel-content strong {
            display: block;
            font-size: 14px;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .auth-support-modal .channel-content p {
            margin: 0 0 6px;
            font-size: 12.5px;
            color: #64748b;
            line-height: 1.5;
        }

        .auth-support-modal .channel-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #405ff2;
            text-decoration: none;
        }

        .auth-support-modal .channel-action.hotline {
            color: #059669;
        }

        /* --- Responsive --- */
        @media (max-width: 575.98px) {
            .boxcar-auth-section {
                padding: 32px 16px 64px;
            }

            .boxcar-auth-card {
                padding: 30px 22px 28px;
                border-radius: 20px;
            }

            .boxcar-auth-head .auth-title {
                font-size: 21px;
            }

            .boxcar-auth-nav .nav-tabs {
                gap: 28px;
            }

            .boxcar-auth-card .auth-actions-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
    @endpush

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

<section class="boxcar-auth-section layout-radius">
    <div class="boxcar-container">
        <div class="boxcar-auth-card">
            {{-- Header: Logo & Subtitle --}}
            <div class="boxcar-auth-head">
                <a href="{{ route('home') }}" class="auth-brand-logo">
                    <img src="{{ asset('boxcar/images/logo.svg') }}" alt="BoxCar" title="BoxCar Showroom">
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
