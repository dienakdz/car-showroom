@php
    $accountUser = auth()->user();
    $hasEmail = filled($accountUser->email);
    $hasPhone = filled($accountUser->phone);
@endphp

<div class="account-profile-section">
    <div class="row g-4">
        <!-- Card 1: Personal Info & Contact -->
        <div class="col-lg-6">
            <div class="account-card h-100">
                <div class="account-card-header">
                    <div>
                        <h3 class="account-card-title">
                            <i class="fa-solid fa-id-card me-2 text-primary"></i> Thông tin liên hệ
                        </h3>
                        <p class="account-card-subtitle">
                            Thông tin chính thức để showroom liên hệ tư vấn, đặt lịch lái thử và lập hồ sơ hợp đồng xe.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('account.profile.update') }}" class="account-form">
                    @csrf
                    <input type="hidden" name="form_mode" value="account_profile">

                    <div class="form_boxes mb-3">
                        <label class="form-label-custom">
                            Họ và tên <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               class="form-control-custom @error('name') is-invalid @enderror" 
                               value="{{ old('name', $accountUser->name) }}" 
                               placeholder="Ví dụ: Nguyễn Văn A" 
                               required>
                        <small class="form-hint-custom">
                            Tên hiển thị trên phiếu hẹn lái thử và hợp đồng mua bán xe.
                        </small>
                        @error('name')
                            <div class="form-error-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form_boxes mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label-custom mb-0">Địa chỉ Email</label>
                            @if ($hasEmail)
                                <span class="badge-status status-success"><i class="fa-solid fa-circle-check me-1"></i> Đã có email</span>
                            @else
                                <span class="badge-status status-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i> Chưa có</span>
                            @endif
                        </div>
                        <input type="email" 
                               name="email" 
                               class="form-control-custom @error('email') is-invalid @enderror" 
                               value="{{ old('email', $accountUser->email) }}" 
                               placeholder="name@domain.com">
                        <small class="form-hint-custom">
                            Email dùng để nhận thư xác nhận lịch hẹn, hợp đồng điện tử và báo giá chi tiết.
                        </small>
                        @error('email')
                            <div class="form-error-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form_boxes mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label-custom mb-0">Số điện thoại</label>
                            @if ($hasPhone)
                                <span class="badge-status status-success"><i class="fa-solid fa-circle-check me-1"></i> Đã có SĐT</span>
                            @else
                                <span class="badge-status status-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i> Chưa có</span>
                            @endif
                        </div>
                        <input type="tel" 
                               name="phone" 
                               class="form-control-custom @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $accountUser->phone) }}" 
                               placeholder="Ví dụ: 0901 234 567">
                        <small class="form-hint-custom">
                            Tài khoản cần ít nhất Email hoặc Số điện thoại để chuyên viên có thể liên hệ.
                        </small>
                        @error('phone')
                            <div class="form-error-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-submit-wrap">
                        <button type="submit" class="theme-btn w-100 justify-content-center">
                            Lưu thông tin cá nhân <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card 2: Security & Password Change -->
        <div class="col-lg-6">
            <div class="account-card h-100">
                <div class="account-card-header">
                    <div>
                        <h3 class="account-card-title">
                            <i class="fa-solid fa-shield-halved me-2 text-warning"></i> Bảo mật tài khoản
                        </h3>
                        <p class="account-card-subtitle">
                            Đổi mật khẩu định kỳ giúp bảo vệ các giao dịch và thông tin cá nhân của bạn.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('account.password.update') }}" class="account-form">
                    @csrf
                    <input type="hidden" name="form_mode" value="account_password">

                    <div class="form_boxes mb-3">
                        <label class="form-label-custom">
                            Mật khẩu hiện tại <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               name="current_password" 
                               class="form-control-custom @error('current_password') is-invalid @enderror" 
                               placeholder="Nhập mật khẩu đang sử dụng" 
                               required>
                        @error('current_password')
                            <div class="form-error-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form_boxes mb-3">
                        <label class="form-label-custom">
                            Mật khẩu mới <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               name="new_password" 
                               class="form-control-custom @error('new_password') is-invalid @enderror" 
                               placeholder="Mật khẩu mới (tối thiểu 6 ký tự)" 
                               required>
                        @error('new_password')
                            <div class="form-error-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form_boxes mb-3">
                        <label class="form-label-custom">
                            Xác nhận mật khẩu mới <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               name="new_password_confirmation" 
                               class="form-control-custom" 
                               placeholder="Nhập lại mật khẩu mới" 
                               required>
                    </div>

                    <!-- Security Tips Banner -->
                    <div class="account-tips-box mb-4">
                        <div class="tips-title"><i class="fa-solid fa-lock me-1 text-primary"></i> Gợi ý bảo mật:</div>
                        <ul class="tips-list">
                            <li>Mật khẩu tối thiểu 6 ký tự, nên kết hợp cả chữ và số.</li>
                            <li>Không dùng lại mật khẩu cũ hoặc mật khẩu dễ đoán.</li>
                        </ul>
                    </div>

                    <div class="form-submit-wrap">
                        <button type="submit" class="theme-btn w-100 justify-content-center">
                            Cập nhật mật khẩu mới <i class="fa-solid fa-key ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
