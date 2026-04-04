@extends('admin.layouts.app')

@section('title', 'Dang nhap Admin')
@section('without-admin-chrome', '1')

@section('admin-content')
    <section class="admin-auth-card">
        <div class="admin-auth-hero">
            <span class="admin-overline">Admin dashboard</span>
            <h1>Quan ly showroom tren mot workspace rieng.</h1>
            <p>
                Dang nhap de quan ly catalog, kho xe, CRM lead, appointments, sales va review moderation.
            </p>
            <div class="admin-auth-links">
                <a href="{{ route('home') }}">Mo public site</a>
                <a href="{{ route('login') }}">Client auth</a>
            </div>
        </div>

        <div class="admin-auth-form-card">
            <div class="form-sec">
                <div class="text-box">
                    <h4>Dang nhap quan tri</h4>
                    <div class="text">Ho tro dang nhap bang email, so dien thoai hoac ten tai khoan.</div>
                </div>

                <form action="{{ route('admin.login.attempt') }}" method="POST" class="admin-form-stack">
                    @csrf

                    <div class="form_boxes">
                        <label class="labels">Email / So dien thoai / Username</label>
                        <input
                            type="text"
                            name="identifier"
                            value="{{ old('identifier') }}"
                            placeholder="admin@showroom.test"
                            required
                        >
                    </div>

                    <div class="form_boxes">
                        <label class="labels">Mat khau</label>
                        <input type="password" name="password" placeholder="Nhap mat khau" required>
                    </div>

                    <div class="admin-inline-checkbox">
                        <label>
                            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            Ghi nho dang nhap
                        </label>
                    </div>

                    <button type="submit" class="theme-btn btn-style-one">
                        <span class="btn-title">Dang nhap dashboard</span>
                    </button>
                </form>

                <div class="admin-seed-hint">
                    <strong>Seed mac dinh:</strong>
                    <span>`admin@showroom.test / 123456` hoac `staff@showroom.test / 123456`</span>
                </div>
            </div>
        </div>
    </section>
@endsection
