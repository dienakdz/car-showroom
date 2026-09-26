@extends('client.layouts.app')

@section('title', auth()->check() ? 'Tài khoản khách hàng' : 'Đăng nhập & Đăng ký')

@section('header')
    @include('client.partials.layout.header', [
        'headerClasses' => 'boxcar-header header-style-v1 header-default',
        'showSearch' => true,
    ])
@endsection

@section('footer')
    @include('client.partials.layout.footer', [
        'footerClasses' => 'boxcar-footer footer-style-one v1 cus-st-1',
    ])
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabList = document.getElementById('account-tablist');
        if (!tabList) {
            return;
        }

        const buttons = Array.from(tabList.querySelectorAll('[data-account-tab]'));
        const panes = buttons
            .map((button) => document.getElementById(button.dataset.accountTab))
            .filter(Boolean);

        const setActiveTab = (paneId, syncUrl = true) => {
            buttons.forEach((button) => {
                const isActive = button.dataset.accountTab === paneId;
                button.classList.toggle('active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            panes.forEach((pane) => {
                const isActive = pane.id === paneId;
                pane.classList.toggle('is-active', isActive);
                pane.hidden = !isActive;
            });

            if (!syncUrl) {
                return;
            }

            const url = new URL(window.location.href);
            url.searchParams.set('tab', paneId.replace('-pane', ''));
            window.history.replaceState({}, '', url);
        };

        const initialButton = buttons.find((button) => button.classList.contains('active')) ?? buttons[0];
        if (!initialButton) {
            return;
        }

        setActiveTab(initialButton.dataset.accountTab, false);

        buttons.forEach((button) => {
            button.addEventListener('click', function () {
                setActiveTab(button.dataset.accountTab);
            });
        });
    });
</script>
@endpush

@section('content')
@php
    $activeTab = auth()->check()
        ? old('form_mode', 'account_profile')
        : old('form_mode', 'login');
    $activeAccountTab = request('tab', 'account-overview');

    if (in_array(old('form_mode'), ['account_profile', 'account_password'], true)) {
        $activeAccountTab = 'account-profile';
    }
@endphp

@auth
    @php
        $accountUser = auth()->user();
        $accountSummary = $accountSummary ?? [
            'profileCompletion' => 34,
            'leadCount' => 0,
            'upcomingAppointmentsCount' => 0,
            'purchaseCount' => 0,
            'reviewCount' => 0,
            'reviewableCount' => 0,
            'memberSinceLabel' => 'Mới tham gia',
            'nextAppointment' => null,
        ];
        $accountAppointments = $accountAppointments ?? collect();
        $accountLeads = $accountLeads ?? collect();
        $accountPurchases = $accountPurchases ?? collect();
        $accountReviews = $accountReviews ?? collect();
        $overviewCards = [
            ['value' => $accountSummary['leadCount'], 'label' => 'Yêu cầu đã gửi'],
            ['value' => $accountSummary['upcomingAppointmentsCount'], 'label' => 'Lịch hẹn sắp tới'],
            ['value' => $accountSummary['purchaseCount'], 'label' => 'Xe đã sở hữu'],
            ['value' => $accountSummary['reviewCount'], 'label' => 'Đánh giá đã gửi'],
        ];
    @endphp

    <section class="client-account-shell">
        <div class="boxcar-container">
            <div class="shell">
                <aside class="sidebar">
                    <div class="panel hero">
                        <div class="panel-inner">
                            <span class="kicker">Trung tâm khách hàng</span>
                            <div class="avatar">{{ strtoupper(substr($accountUser->name, 0, 1)) }}</div>
                            <h2>Quản lý tài khoản</h2>
                            <p>Theo dõi yêu cầu tư vấn, lịch hẹn lái thử, lịch sử mua xe và đánh giá trong một giao diện duy nhất.</p>

                            <div class="list-grid mt-3">
                                <div><span>Hồ sơ:</span> <strong>{{ $accountSummary['profileCompletion'] }}%</strong></div>
                                <div><span>Thành viên từ:</span> <strong>{{ $accountSummary['memberSinceLabel'] }}</strong></div>
                                <div><span>Chờ đánh giá:</span> <strong>{{ $accountSummary['reviewableCount'] }}</strong></div>
                            </div>

                            <div class="meta mt-3">
                                <a href="{{ route('inventory.index') }}" class="chip neutral">Xem kho xe</a>
                                <a href="{{ route('contact') }}" class="chip neutral">Gửi yêu cầu mới</a>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-inner">
                            <ul class="nav nav-pills flex-column nav-list" id="account-tablist" role="tablist">
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-overview' ? 'active' : '' }}" id="account-overview-tab" type="button" role="tab" data-account-tab="account-overview-pane" aria-controls="account-overview-pane" aria-selected="{{ $activeAccountTab === 'account-overview' ? 'true' : 'false' }}">
                                        Tổng quan <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-profile' ? 'active' : '' }}" id="account-profile-tab" type="button" role="tab" data-account-tab="account-profile-pane" aria-controls="account-profile-pane" aria-selected="{{ $activeAccountTab === 'account-profile' ? 'true' : 'false' }}">
                                        Thông tin cá nhân <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-appointments' ? 'active' : '' }}" id="account-appointments-tab" type="button" role="tab" data-account-tab="account-appointments-pane" aria-controls="account-appointments-pane" aria-selected="{{ $activeAccountTab === 'account-appointments' ? 'true' : 'false' }}">
                                        Lịch hẹn của tôi <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-leads' ? 'active' : '' }}" id="account-leads-tab" type="button" role="tab" data-account-tab="account-leads-pane" aria-controls="account-leads-pane" aria-selected="{{ $activeAccountTab === 'account-leads' ? 'true' : 'false' }}">
                                        Yêu cầu của tôi <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-purchases' ? 'active' : '' }}" id="account-purchases-tab" type="button" role="tab" data-account-tab="account-purchases-pane" aria-controls="account-purchases-pane" aria-selected="{{ $activeAccountTab === 'account-purchases' ? 'true' : 'false' }}">
                                        Xe đã mua <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-reviews' ? 'active' : '' }}" id="account-reviews-tab" type="button" role="tab" data-account-tab="account-reviews-pane" aria-controls="account-reviews-pane" aria-selected="{{ $activeAccountTab === 'account-reviews' ? 'true' : 'false' }}">
                                        Đánh giá của tôi <span>&rarr;</span>
                                    </button>
                                </li>
                            </ul>

                            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                                @csrf
                                <button type="submit" class="logout-btn">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                </aside>

                <div class="main">
                    <div class="panel hero">
                        <div class="panel-inner">
                            <span class="kicker">Hồ sơ của tôi</span>
                            <h1 class="my-2">{{ $accountUser->name }}</h1>
                            <p>{{ $accountUser->email ?: 'Chưa cập nhật email' }} | {{ $accountUser->phone ?: 'Chưa cập nhật số điện thoại' }}</p>

                            <div class="stats">
                                @foreach ($overviewCards as $card)
                                    <div class="stat">
                                        <strong>{{ $card['value'] }}</strong>
                                        <span>{{ $card['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="tab-content">
                    <div class="account-tab-pane {{ $activeAccountTab === 'account-overview' ? 'is-active' : '' }}" id="account-overview-pane" role="tabpanel" aria-labelledby="account-overview-tab" tabindex="0">
                    <div class="panel">
                        <div class="panel-inner">
                            <div class="section-head">
                                <div>
                                    <h3>Tổng quan hoạt động</h3>
                                    <p>Tóm tắt nhanh để bạn nắm bắt các thông tin và bước tiếp theo.</p>
                                </div>
                            </div>

                            <div class="overview-grid">
                                <div class="mini-card">
                                    <strong>Bước tiếp theo</strong>
                                    @if ($accountSummary['nextAppointment'])
                                        <p>Lịch hẹn gần nhất vào {{ $accountSummary['nextAppointment']->scheduled_at_label }} cho {{ $accountSummary['nextAppointment']->context_label }}.</p>
                                        <div class="meta">
                                            <span class="chip {{ $accountSummary['nextAppointment']->status_tone }}">{{ $accountSummary['nextAppointment']->status_label }}</span>
                                            <a href="{{ $accountSummary['nextAppointment']->context_url }}" class="action-link">Xem chi tiết</a>
                                        </div>
                                    @else
                                        <p>Bạn chưa có lịch hẹn sắp tới. Hãy khám phá kho xe và đăng ký lái thử trải nghiệm ngay!</p>
                                    @endif
                                </div>

                                <div class="mini-card">
                                    <strong>Tình trạng hồ sơ</strong>
                                    <p>Hồ sơ hiện đạt {{ $accountSummary['profileCompletion'] }}%. Cập nhật đầy đủ email và số điện thoại để nhận tư vấn và đặt lịch nhanh chóng hơn.</p>
                                    <div class="meta">
                                        <span class="chip neutral">Chờ gửi đánh giá: {{ $accountSummary['reviewableCount'] }}</span>
                                        <a href="{{ route('account.show', ['tab' => 'account-profile']) }}" class="action-link">Cập nhật ngay</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>

                    <div class="account-tab-pane {{ $activeAccountTab === 'account-profile' ? 'is-active' : '' }}" id="account-profile-pane" role="tabpanel" aria-labelledby="account-profile-tab" tabindex="0">
                    <div class="panel">
                        <div class="panel-inner">
                            @php
                                $hasEmail = filled($accountUser->email);
                                $hasPhone = filled($accountUser->phone);
                                $contactReady = $hasEmail || $hasPhone;
                            @endphp
                            <div class="section-head">
                                <div>
                                    <h3>Thông tin cá nhân</h3>
                                    <p>Xem trạng thái hiện tại, chỉnh sửa liên hệ và quản lý bảo mật theo từng bước rõ ràng.</p>
                                </div>
                            </div>

                            <div class="profile-shell">
                                <div class="profile-overview">
                                    <div class="form-card">
                                        <div class="profile-card-head">
                                            <div>
                                                <span class="profile-step">Trạng thái hiện tại</span>
                                                <h4>Thông tin đang được sử dụng</h4>
                                                <p>Đây là thông tin showroom sử dụng khi tiếp nhận tư vấn, đặt lịch và liên hệ lại với bạn.</p>
                                            </div>
                                            <span class="chip {{ $contactReady ? 'success' : 'warning' }}">{{ $contactReady ? 'Sẵn sàng liên hệ' : 'Cần bổ sung liên hệ' }}</span>
                                        </div>

                                        <div class="profile-summary-grid">
                                            <div class="profile-summary-item">
                                                <label>Họ và tên</label>
                                                <strong>{{ $accountUser->name }}</strong>
                                                <span>Tên này hiển thị trên thông tin đặt lịch, yêu cầu tư vấn và đánh giá của bạn.</span>
                                            </div>
                                            <div class="profile-summary-item">
                                                <label>Email</label>
                                                <strong>{{ $accountUser->email ?: 'Chưa cập nhật' }}</strong>
                                                <span>{{ $hasEmail ? 'Đã sẵn sàng cho email xác nhận và thông báo.' : 'Nên bổ sung nếu muốn nhận xác nhận qua email.' }}</span>
                                            </div>
                                            <div class="profile-summary-item">
                                                <label>Số điện thoại</label>
                                                <strong>{{ $accountUser->phone ?: 'Chưa cập nhật' }}</strong>
                                                <span>{{ $hasPhone ? 'Đã sẵn sàng cho tư vấn và xác nhận nhanh chóng.' : 'Nên bổ sung để showroom có thể gọi điện trực tiếp.' }}</span>
                                            </div>
                                            <div class="profile-summary-item">
                                                <label>Hồ sơ</label>
                                                <strong>{{ $accountSummary['profileCompletion'] }}% hoàn thiện</strong>
                                                <span>Thành viên từ {{ $accountSummary['memberSinceLabel'] }}.</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-card">
                                        <span class="profile-step">Hướng dẫn</span>
                                        <h4>Bạn nên thao tác như thế nào?</h4>
                                        <p>Làm theo các bước dưới đây để cập nhật nhanh chóng mà không bỏ sót thông tin quan trọng.</p>
                                        <ul class="profile-guide-list">
                                            <li>Bước 1: Kiểm tra thông tin hiện tại ở bên trái để biết trường nào còn thiếu.</li>
                                            <li>Bước 2: Cập nhật form liên hệ bên dưới. Tài khoản cần ít nhất email hoặc số điện thoại.</li>
                                            <li>Bước 3: Nếu cần đổi mật khẩu, thao tác tại khối Bảo mật tài khoản ở cột bên phải.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="profile-editor-grid">
                                    <div class="form-card">
                                        <div class="profile-card-head">
                                            <div>
                                                <span class="profile-step">Bước 1</span>
                                                <h4>Chỉnh sửa thông tin liên hệ</h4>
                                                <p>Form này được dùng để chỉnh sửa thông tin người dùng. Sau khi lưu, thay đổi sẽ áp dụng cho các yêu cầu mới.</p>
                                            </div>
                                            <span class="chip info">Form chỉnh sửa</span>
                                        </div>

                                        <form class="row" method="POST" action="{{ route('account.profile.update') }}">
                                            @csrf
                                            <input type="hidden" name="form_mode" value="account_profile">

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Họ và tên</label>
                                                    <input class="@error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name', $accountUser->name) }}" placeholder="Nguyễn Văn A" required>
                                                    <small class="field-note">Đây là tên xuất hiện trên thông tin đặt lịch, tư vấn và đánh giá của bạn.</small>
                                                    @error('name')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Email</label>
                                                    <input class="@error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email', $accountUser->email) }}" placeholder="name@email.com">
                                                    <small class="field-note">Nên nhập email để nhận xác nhận và các cập nhật quan trọng từ showroom.</small>
                                                    @error('email')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Số điện thoại</label>
                                                    <input class="@error('phone') is-invalid @enderror" name="phone" type="text" value="{{ old('phone', $accountUser->phone) }}" placeholder="0901234567">
                                                    <small class="field-note">Bạn cần ít nhất email hoặc số điện thoại để showroom có thể liên hệ hỗ trợ.</small>
                                                    @error('phone')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="profile-actions">
                                                    <p>Lưu xong, hệ thống sẽ giữ bạn ở lại trang này để kiểm tra thông tin ngay lập tức.</p>
                                                    <div class="form-submit m-0">
                                                        <button type="submit" class="theme-btn">Lưu thông tin <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="form-card">
                                        <div class="profile-card-head">
                                            <div>
                                                <span class="profile-step">Bước 2</span>
                                                <h4>Bảo mật tài khoản</h4>
                                                <p>Đổi mật khẩu ở đây nếu bạn muốn tăng cường bảo mật hoặc vừa chia sẻ tài khoản trên thiết bị khác.</p>
                                            </div>
                                            <span class="chip warning">Bảo mật</span>
                                        </div>

                                        <div class="security-points">
                                            <div class="security-point">
                                                <strong>Nhập mật khẩu hiện tại trước</strong>
                                                <span>Hệ thống cần xác minh chính bạn là người đang thay đổi mật khẩu.</span>
                                            </div>
                                            <div class="security-point">
                                                <strong>Mật khẩu mới tối thiểu 6 ký tự</strong>
                                                <span>Không nên dùng lại mật khẩu cũ và nên chứa các ký tự dễ nhớ với bạn nhưng khó đoán.</span>
                                            </div>
                                        </div>

                                        <form class="row mt-3" method="POST" action="{{ route('account.password.update') }}">
                                            @csrf
                                            <input type="hidden" name="form_mode" value="account_password">

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Mật khẩu hiện tại</label>
                                                    <input class="@error('current_password') is-invalid @enderror" type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại">
                                                    @error('current_password')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Mật khẩu mới</label>
                                                    <input class="@error('new_password') is-invalid @enderror" type="password" name="new_password" placeholder="Tối thiểu 6 ký tự">
                                                    <small class="field-note">Nên sử dụng mật khẩu khác với mật khẩu cũ để tăng mức độ an toàn.</small>
                                                    @error('new_password')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Nhập lại mật khẩu mới</label>
                                                    <input class="@error('new_password') is-invalid @enderror" type="password" name="new_password_confirmation" placeholder="Nhập lại mật khẩu mới">
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="security-note">Nếu bạn đang đăng nhập trên nhiều thiết bị, hãy đảm bảo các thiết bị còn lại an toàn sau khi đổi mật khẩu.</div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-submit">
                                                    <button type="submit" class="theme-btn">Cập nhật mật khẩu <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>

                    <div class="account-tab-pane {{ $activeAccountTab === 'account-appointments' ? 'is-active' : '' }}" id="account-appointments-pane" role="tabpanel" aria-labelledby="account-appointments-tab" tabindex="0">
                    <div class="panel">
                        <div class="panel-inner">
                            <div class="section-head"><div><h3>Lịch hẹn của tôi</h3><p>Theo dõi các lịch xem xe hoặc lái thử đã gửi tới showroom.</p></div></div>
                            @if ($accountAppointments->isEmpty())
                                <div class="empty">Bạn chưa có lịch hẹn nào. Khi đã tìm được mẫu xe ưng ý, hãy vào trang chi tiết xe để đặt lịch nhé!</div>
                            @else
                                <div class="list-grid">
                                    @foreach ($accountAppointments as $appointment)
                                        <div class="item">
                                            <div class="item-top">
                                                <div>
                                                    <a href="{{ $appointment->context_url }}" class="title-link">{{ $appointment->context_label }}</a>
                                                    <div class="meta">
                                                        <span class="chip {{ $appointment->status_tone }}">{{ $appointment->status_label }}</span>
                                                        <span class="chip neutral">{{ $appointment->scheduled_at_label }}</span>
                                                    </div>
                                                </div>
                                                <a href="{{ $appointment->context_url }}" class="action-link">Xem chi tiết</a>
                                            </div>
                                            <p>{{ $appointment->note !== '' ? $appointment->note : 'Không có ghi chú thêm cho lịch hẹn này.' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    </div>

                    <div class="account-tab-pane {{ $activeAccountTab === 'account-leads' ? 'is-active' : '' }}" id="account-leads-pane" role="tabpanel" aria-labelledby="account-leads-tab" tabindex="0">
                    <div class="panel">
                        <div class="panel-inner">
                            <div class="section-head"><div><h3>Yêu cầu tư vấn của tôi</h3><p>Danh sách các yêu cầu báo giá và tư vấn bạn đã gửi từ các trang sản phẩm.</p></div></div>
                            @if ($accountLeads->isEmpty())
                                <div class="empty">Bạn chưa tạo yêu cầu tư vấn nào khi đăng nhập. Các yêu cầu mới sẽ được lưu tại đây để bạn theo dõi trạng thái.</div>
                            @else
                                <div class="list-grid">
                                    @foreach ($accountLeads as $lead)
                                        <div class="item">
                                            <div class="item-top">
                                                <div>
                                                    <a href="{{ $lead->context_url }}" class="title-link">{{ $lead->context_label }}</a>
                                                    <div class="meta">
                                                        <span class="chip neutral">{{ $lead->source_label }}</span>
                                                        <span class="chip {{ $lead->status_tone }}">{{ $lead->status_label }}</span>
                                                        <span class="chip neutral">{{ $lead->created_at_label }}</span>
                                                    </div>
                                                </div>
                                                <a href="{{ $lead->context_url }}" class="action-link">Xem chi tiết</a>
                                            </div>
                                            <p>{{ $lead->message !== '' ? $lead->message : 'Không có ghi chú bổ sung cho yêu cầu này.' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    </div>

                    <div class="account-tab-pane {{ $activeAccountTab === 'account-purchases' ? 'is-active' : '' }}" id="account-purchases-pane" role="tabpanel" aria-labelledby="account-purchases-tab" tabindex="0">
                    <div class="panel">
                        <div class="panel-inner">
                            <div class="section-head"><div><h3>Xe đã mua</h3><p>Lịch sử các xe đã được bàn giao và liên kết với tài khoản của quý khách.</p></div></div>
                            @if ($accountPurchases->isEmpty())
                                <div class="empty">Hiện chưa có hợp đồng mua xe nào được liên kết với tài khoản này. Khi hoàn tất giao dịch tại showroom, thông tin xe sẽ hiển thị ở đây.</div>
                            @else
                                <div class="purchase-grid">
                                    @foreach ($accountPurchases as $purchase)
                                        <div class="purchase">
                                            <img src="{{ $purchase->image_url }}" alt="{{ $purchase->car_label }}">
                                            <div>
                                                <a href="{{ $purchase->trim_url }}" class="title-link">{{ $purchase->car_label }}</a>
                                                <div class="meta">
                                                    <span class="chip neutral">{{ $purchase->sold_at_label }}</span>
                                                    <span class="chip info">{{ $purchase->sold_price_label }}</span>
                                                    <span class="chip {{ $purchase->review_status_tone }}">{{ $purchase->review_status_label }}</span>
                                                </div>
                                                <p>{{ $purchase->trim_label }}</p>
                                                <div class="meta">
                                                    <a href="{{ $purchase->trim_url }}" class="action-link">{{ $purchase->can_review ? 'Gửi đánh giá cho phiên bản' : 'Xem trang phiên bản' }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    </div>

                    <div class="account-tab-pane {{ $activeAccountTab === 'account-reviews' ? 'is-active' : '' }}" id="account-reviews-pane" role="tabpanel" aria-labelledby="account-reviews-tab" tabindex="0">
                    <div class="panel">
                        <div class="panel-inner">
                            <div class="section-head"><div><h3>Đánh giá của tôi</h3><p>Theo dõi đánh giá và nhận xét đã gửi cho các phiên bản xe bạn đã sở hữu.</p></div></div>
                            @if ($accountReviews->isEmpty())
                                <div class="empty">Bạn chưa gửi đánh giá nào. Sau khi hoàn tất mua xe và đăng nhập tài khoản, bạn có thể gửi đánh giá cho từng phiên bản xe.</div>
                            @else
                                <div class="list-grid">
                                    @foreach ($accountReviews as $review)
                                        <div class="review">
                                            <div class="item-top">
                                                <div>
                                                    <a href="{{ $review->trim_url }}" class="title-link">{{ $review->trim_label }}</a>
                                                    <div class="meta">
                                                        <span class="chip {{ $review->status_tone }}">{{ $review->status_label }}</span>
                                                        <span class="chip neutral">{{ $review->created_at_label }}</span>
                                                    </div>
                                                </div>
                                                <a href="{{ $review->trim_url }}" class="action-link">Xem phiên bản</a>
                                            </div>
                                            <div class="rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i>
                                                @endfor
                                                <span class="rating-number ms-2 text-muted">{{ $review->rating }}/5</span>
                                            </div>
                                            <p>{{ $review->comment }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    @include('client.partials.auth.login-section')
@endauth
@endsection
