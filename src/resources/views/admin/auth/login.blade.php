@extends('admin.layouts.app')

@section('title', 'Đăng nhập Quản trị')
@section('without-admin-chrome', '1')

@section('admin-content')
    <section class="admin-auth-card">
        <div class="admin-auth-hero">
            <span class="admin-overline">Hệ thống quản trị Showroom</span>
            <h1>Quản lý showroom trên một workspace chuyên nghiệp.</h1>
            <p>
                Đăng nhập để quản lý kho xe, danh mục xe, khách hàng tiềm năng, lịch hẹn xem xe và giao dịch bán hàng.
            </p>
            <div class="admin-auth-links">
                <a href="{{ route('home') }}"><i class="fa fa-globe mr-1"></i> Xem website</a>
                <a href="{{ route('login') }}"><i class="fa fa-user mr-1"></i> Đăng nhập khách hàng</a>
            </div>
        </div>

        <div class="admin-auth-form-card">
            <div class="form-sec">
                <div class="text-box">
                    <h4>Đăng nhập quản trị</h4>
                    <div class="text">Hỗ trợ đăng nhập bằng email, số điện thoại hoặc tên tài khoản.</div>
                </div>

                <form action="{{ route('admin.login.attempt') }}" method="POST" class="admin-form-stack">
                    @csrf

                    <div class="form_boxes">
                        <label class="labels">Email / Số điện thoại / Tên đăng nhập</label>
                        <input
                            type="text"
                            name="identifier"
                            value="{{ old('identifier') }}"
                            placeholder="admin@showroom.test"
                            required
                        >
                    </div>

                    <div class="form_boxes">
                        <label class="labels">Mật khẩu</label>
                        <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="admin-inline-checkbox">
                        <label>
                            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    <button type="submit" class="theme-btn btn-style-one">
                        <span class="btn-title">Đăng nhập vào hệ thống</span>
                    </button>
                </form>

                <div class="admin-seed-hint">
                    <strong>Tài khoản mặc định:</strong>
                    <span>`admin@showroom.test / 123456` hoặc `staff@showroom.test / 123456`</span>
                </div>
            </div>
        </div>
    </section>
@endsection
