@extends('admin.layouts.app')

@section('title', 'Đăng nhập Quản trị')
@section('without-admin-chrome', '1')

@section('admin-content')
<div class="c1-auth-container">
    <!-- Left Hero Showcase -->
    <div class="c1-auth-hero">
        <div class="c1-auth-hero-top">
            <div class="c1-auth-hero-badge">
                <i class="fa fa-shield-alt"></i> Quản trị Showroom
            </div>
            <h1 class="c1-auth-hero-title">Vận hành showroom thông minh &amp; toàn diện</h1>
            <p class="c1-auth-hero-desc">
                Nền tảng quản trị tập trung: danh mục xe, kiểm soát kho hàng, theo dõi khách hàng tiềm năng và tối ưu hóa quy trình bán hàng trên một workspace chuyên nghiệp.
            </p>

            <div class="c1-auth-features">
                <div class="c1-auth-feature-item">
                    <div class="c1-auth-feature-icon">
                        <i class="fa fa-car"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #f8fafc;">Quản lý Kho &amp; Danh mục xe</div>
                        <div style="font-size: 12px; color: #94a3b8;">Makes, Models, Trims &amp; kiểm soát chi tiết xe trong kho</div>
                    </div>
                </div>

                <div class="c1-auth-feature-item">
                    <div class="c1-auth-feature-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #f8fafc;">Khách hàng &amp; Lịch hẹn</div>
                        <div style="font-size: 12px; color: #94a3b8;">Theo dõi Leads &amp; sắp xếp lịch hẹn lái thử thông minh</div>
                    </div>
                </div>

                <div class="c1-auth-feature-item">
                    <div class="c1-auth-feature-icon">
                        <i class="fa fa-chart-line"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #f8fafc;">Giao dịch &amp; Báo cáo Realtime</div>
                        <div style="font-size: 12px; color: #94a3b8;">Theo dõi tiến độ hợp đồng &amp; phân tích doanh số trực quan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="c1-auth-hero-footer">
            <a href="{{ route('home') }}">
                <i class="fa fa-arrow-left"></i> Xem Website
            </a>
            <span style="color: rgba(255, 255, 255, 0.2);">|</span>
            <a href="{{ route('login') }}">
                <i class="fa fa-user-circle"></i> Đăng nhập Khách hàng
            </a>
        </div>
    </div>

    <!-- Right Form Side -->
    <div class="c1-auth-form-side">
        <div class="c1-auth-brand">
            <div class="c1-auth-logo">
                <i class="fa fa-car" style="color: var(--c1-primary);"></i> BOXCARS
            </div>
            <span class="c1-auth-portal-tag">Admin Portal</span>
        </div>

        <h2 class="c1-auth-title">Đăng nhập quản trị</h2>
        <p class="c1-auth-subtitle">Nhập thông tin quản trị viên hoặc nhân viên để tiếp tục</p>

        @if ($errors->any())
            <div class="c1-alert c1-alert-danger" role="alert" style="margin-bottom: 20px;">
                <i class="fa fa-exclamation-circle" style="font-size: 16px; margin-top: 2px;"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('admin.login.attempt') }}" method="POST">
            @csrf

            <div class="c1-auth-field">
                <label class="c1-auth-label" for="admin-identifier">
                    Tài khoản / Email / Số điện thoại
                </label>
                <div class="c1-auth-input-wrap">
                    <i class="fa fa-envelope c1-auth-input-icon"></i>
                    <input
                        id="admin-identifier"
                        type="text"
                        name="identifier"
                        class="c1-auth-input"
                        value="{{ old('identifier', 'admin@showroom.test') }}"
                        placeholder="admin@showroom.test hoặc username"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="c1-auth-field">
                <label class="c1-auth-label" for="admin-password">
                    Mật khẩu
                </label>
                <div class="c1-auth-input-wrap">
                    <i class="fa fa-lock c1-auth-input-icon"></i>
                    <input
                        id="admin-password"
                        type="password"
                        name="password"
                        class="c1-auth-input"
                        placeholder="Nhập mật khẩu"
                        autocomplete="current-password"
                        value="123456"
                        required
                    >
                    <button type="button" class="c1-auth-password-toggle" id="togglePasswordBtn" title="Hiện/ẩn mật khẩu">
                        <i class="fa fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="c1-auth-checkbox-wrap">
                <label class="c1-auth-checkbox-label">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                        style="width: 16px; height: 16px; border-radius: 4px; accent-color: var(--c1-primary); cursor: pointer;"
                    >
                    <span>Ghi nhớ đăng nhập</span>
                </label>
            </div>

            <button type="submit" class="c1-auth-submit-btn">
                <i class="fa fa-sign-in-alt"></i> Đăng nhập vào hệ thống
            </button>
        </form>

        <!-- Demo Accounts Quick Fill -->
        <div class="c1-auth-demo-card">
            <div class="c1-auth-demo-title">
                <i class="fa fa-bolt" style="color: #f59e0b; margin-right: 4px;"></i> Tài khoản mẫu (Bấm để điền nhanh)
            </div>
            <div class="c1-auth-demo-buttons">
                <button type="button" class="c1-auth-demo-pill" onclick="quickFill('admin@showroom.test', '123456')">
                    <strong>Admin:</strong> admin@showroom.test
                </button>
                <button type="button" class="c1-auth-demo-pill" onclick="quickFill('staff@showroom.test', '123456')">
                    <strong>Staff:</strong> staff@showroom.test
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function quickFill(identifier, password) {
        const idInput = document.getElementById('admin-identifier');
        const passInput = document.getElementById('admin-password');
        if (idInput && passInput) {
            idInput.value = identifier;
            passInput.value = password;
            idInput.focus();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passInput = document.getElementById('admin-password');
        const passIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passInput && passIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passInput.type === 'password';
                passInput.type = isPassword ? 'text' : 'password';
                passIcon.className = isPassword ? 'fa fa-eye-slash' : 'fa fa-eye';
            });
        }
    });
</script>
@endpush
