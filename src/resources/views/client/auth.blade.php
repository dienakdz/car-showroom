@extends('client.layouts.app')

@section('title', auth()->check() ? 'Tài khoản khách hàng' : 'Đăng nhập & Đăng ký')

@section('header')
    @include('client.partials.layout.header', [
        'headerClasses' => 'boxcar-header header-style-v1 style-two inner-header cus-style-1',
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
            'memberSinceLabel' => 'Moi tham gia',
            'nextAppointment' => null,
        ];
        $accountAppointments = $accountAppointments ?? collect();
        $accountLeads = $accountLeads ?? collect();
        $accountPurchases = $accountPurchases ?? collect();
        $accountReviews = $accountReviews ?? collect();
        $overviewCards = [
            ['value' => $accountSummary['leadCount'], 'label' => 'Yeu cau da tao'],
            ['value' => $accountSummary['upcomingAppointmentsCount'], 'label' => 'Lich hen sap toi'],
            ['value' => $accountSummary['purchaseCount'], 'label' => 'Xe da mua'],
            ['value' => $accountSummary['reviewCount'], 'label' => 'Danh gia da gui'],
        ];
    @endphp

    <section class="client-account-shell">
        <div class="boxcar-container">
            <div class="shell">
                <aside class="sidebar">
                    <div class="panel hero">
                        <div class="panel-inner">
                            <span class="kicker">Customer dashboard</span>
                            <div class="avatar">{{ strtoupper(substr($accountUser->name, 0, 1)) }}</div>
                            <h2>Quan ly tai khoan</h2>
                            <p>Theo doi lead, lich hen, lich su mua xe va review trong mot dashboard duy nhat.</p>

                            <div class="list-grid" style="margin-top: 20px;">
                                <div><span>Ho so:</span> <strong>{{ $accountSummary['profileCompletion'] }}%</strong></div>
                                <div><span>Thanh vien tu:</span> <strong>{{ $accountSummary['memberSinceLabel'] }}</strong></div>
                                <div><span>Cho gui review:</span> <strong>{{ $accountSummary['reviewableCount'] }}</strong></div>
                            </div>

                            <div class="meta" style="margin-top: 20px;">
                                <a href="{{ route('inventory.index') }}" class="chip neutral">Xem kho xe</a>
                                <a href="{{ route('contact') }}" class="chip neutral">Gui yeu cau moi</a>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-inner">
                            <ul class="nav nav-pills flex-column nav-list" id="account-tablist" role="tablist">
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-overview' ? 'active' : '' }}" id="account-overview-tab" type="button" role="tab" data-account-tab="account-overview-pane" aria-controls="account-overview-pane" aria-selected="{{ $activeAccountTab === 'account-overview' ? 'true' : 'false' }}">
                                        Tong quan <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-profile' ? 'active' : '' }}" id="account-profile-tab" type="button" role="tab" data-account-tab="account-profile-pane" aria-controls="account-profile-pane" aria-selected="{{ $activeAccountTab === 'account-profile' ? 'true' : 'false' }}">
                                        Thong tin ca nhan <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-appointments' ? 'active' : '' }}" id="account-appointments-tab" type="button" role="tab" data-account-tab="account-appointments-pane" aria-controls="account-appointments-pane" aria-selected="{{ $activeAccountTab === 'account-appointments' ? 'true' : 'false' }}">
                                        Lich hen cua toi <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-leads' ? 'active' : '' }}" id="account-leads-tab" type="button" role="tab" data-account-tab="account-leads-pane" aria-controls="account-leads-pane" aria-selected="{{ $activeAccountTab === 'account-leads' ? 'true' : 'false' }}">
                                        Yeu cau cua toi <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-purchases' ? 'active' : '' }}" id="account-purchases-tab" type="button" role="tab" data-account-tab="account-purchases-pane" aria-controls="account-purchases-pane" aria-selected="{{ $activeAccountTab === 'account-purchases' ? 'true' : 'false' }}">
                                        Xe da mua <span>&rarr;</span>
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button class="nav-link {{ $activeAccountTab === 'account-reviews' ? 'active' : '' }}" id="account-reviews-tab" type="button" role="tab" data-account-tab="account-reviews-pane" aria-controls="account-reviews-pane" aria-selected="{{ $activeAccountTab === 'account-reviews' ? 'true' : 'false' }}">
                                        Danh gia cua toi <span>&rarr;</span>
                                    </button>
                                </li>
                            </ul>

                            <form method="POST" action="{{ route('logout') }}" style="margin-top: 18px;">
                                @csrf
                                <button type="submit" class="logout-btn">Dang xuat</button>
                            </form>
                        </div>
                    </div>
                </aside>

                <div class="main">
                    <div class="panel hero">
                        <div class="panel-inner">
                            <span class="kicker">Ho so cua toi</span>
                            <h1 style="margin: 14px 0 8px;">{{ $accountUser->name }}</h1>
                            <p>{{ $accountUser->email ?: 'Chua co email' }} | {{ $accountUser->phone ?: 'Chua co so dien thoai' }}</p>

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
                                    <h3>Tong quan hoat dong</h3>
                                    <p>Tom tat nhanh de ban biet viec gi can xu ly tiep theo.</p>
                                </div>
                            </div>

                            <div class="overview-grid">
                                <div class="mini-card">
                                    <strong>Buoc tiep theo</strong>
                                    @if ($accountSummary['nextAppointment'])
                                        <p>Lich hen gan nhat vao {{ $accountSummary['nextAppointment']->scheduled_at_label }} cho {{ $accountSummary['nextAppointment']->context_label }}.</p>
                                        <div class="meta">
                                            <span class="chip {{ $accountSummary['nextAppointment']->status_tone }}">{{ $accountSummary['nextAppointment']->status_label }}</span>
                                            <a href="{{ $accountSummary['nextAppointment']->context_url }}" class="action-link">Mo context</a>
                                        </div>
                                    @else
                                        <p>Ban chua co lich hen sap toi. Khi da tim duoc xe phu hop, hay dat lich xem xe hoac lai thu.</p>
                                    @endif
                                </div>

                                <div class="mini-card">
                                    <strong>Tinh trang ho so</strong>
                                    <p>Ho so hien dat {{ $accountSummary['profileCompletion'] }}%. Cap nhat day du email va so dien thoai de lead va booking duoc dien nhanh hon.</p>
                                    <div class="meta">
                                        <span class="chip neutral">Review cho gui: {{ $accountSummary['reviewableCount'] }}</span>
                                        <a href="{{ route('account.show', ['tab' => 'account-profile']) }}" class="action-link">Cap nhat ngay</a>
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
                                    <h3>Thong tin ca nhan</h3>
                                    <p>Xem trang thai hien tai, chinh sua lien he va cap nhat bao mat theo tung buoc ro rang.</p>
                                </div>
                            </div>

                            <div class="profile-shell">
                                <div class="profile-overview">
                                    <div class="form-card">
                                        <div class="profile-card-head">
                                            <div>
                                                <span class="profile-step">Trang thai hien tai</span>
                                                <h4>Thong tin dang duoc su dung</h4>
                                                <p>Day la bo du lieu showroom se dung khi tiep nhan lead, dat lich va lien he lai voi ban.</p>
                                            </div>
                                            <span class="chip {{ $contactReady ? 'success' : 'warning' }}">{{ $contactReady ? 'San sang lien he' : 'Can bo sung lien he' }}</span>
                                        </div>

                                        <div class="profile-summary-grid">
                                            <div class="profile-summary-item">
                                                <label>Ho ten</label>
                                                <strong>{{ $accountUser->name }}</strong>
                                                <span>Ten nay hien tren lead va lich hen cua ban.</span>
                                            </div>
                                            <div class="profile-summary-item">
                                                <label>Email</label>
                                                <strong>{{ $accountUser->email ?: 'Chua cap nhat' }}</strong>
                                                <span>{{ $hasEmail ? 'Da san sang cho email xac nhan va thong bao.' : 'Nen bo sung neu muon nhan xac nhan qua email.' }}</span>
                                            </div>
                                            <div class="profile-summary-item">
                                                <label>So dien thoai</label>
                                                <strong>{{ $accountUser->phone ?: 'Chua cap nhat' }}</strong>
                                                <span>{{ $hasPhone ? 'Da san sang cho tu van va xac nhan nhanh.' : 'Nen bo sung de showroom goi hoac nhan tin.' }}</span>
                                            </div>
                                            <div class="profile-summary-item">
                                                <label>Ho so</label>
                                                <strong>{{ $accountSummary['profileCompletion'] }}% hoan thien</strong>
                                                <span>Thanh vien tu {{ $accountSummary['memberSinceLabel'] }}.</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-card">
                                        <span class="profile-step">Huong dan</span>
                                        <h4>Ban nen thao tac nhu the nao?</h4>
                                        <p>Lam theo 2 buoc duoi day de cap nhat nhanh ma khong bo sot thong tin quan trong.</p>
                                        <ul class="profile-guide-list">
                                            <li>Buoc 1: kiem tra thong tin hien tai o ben trai de biet truong nao dang thieu.</li>
                                            <li>Buoc 2: cap nhat form lien he ben duoi. Tai khoan can it nhat email hoac so dien thoai.</li>
                                            <li>Buoc 3: neu can doi mat khau, thao tac tai block Bao mat tai khoan o cot ben phai.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="profile-editor-grid">
                                    <div class="form-card">
                                        <div class="profile-card-head">
                                            <div>
                                                <span class="profile-step">Buoc 1</span>
                                                <h4>Chinh sua thong tin lien he</h4>
                                                <p>Form nay duoc dung de chinh sua truc tiep thong tin nguoi dung. Sau khi luu, thay doi se ap dung cho cac yeu cau moi.</p>
                                            </div>
                                            <span class="chip info">Form chinh sua</span>
                                        </div>

                                        <form class="row" method="POST" action="{{ route('account.profile.update') }}">
                                            @csrf
                                            <input type="hidden" name="form_mode" value="account_profile">

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Ho ten</label>
                                                    <input class="@error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name', $accountUser->name) }}" placeholder="Nguyen Van A" required>
                                                    <small class="field-note">Day la ten xuat hien tren thong tin booking, lead va review cua ban.</small>
                                                    @error('name')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Email</label>
                                                    <input class="@error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email', $accountUser->email) }}" placeholder="name@email.com">
                                                    <small class="field-note">Nen nhap email de nhan xac nhan va cac cap nhat quan trong.</small>
                                                    @error('email')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>So dien thoai</label>
                                                    <input class="@error('phone') is-invalid @enderror" name="phone" type="text" value="{{ old('phone', $accountUser->phone) }}" placeholder="0901234567">
                                                    <small class="field-note">Ban can it nhat email hoac so dien thoai de showroom co the lien he lai.</small>
                                                    @error('phone')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="profile-actions">
                                                    <p>Luu xong, he thong se giu ban o lai tab nay de ban kiem tra thong tin ngay lap tuc.</p>
                                                    <div class="form-submit" style="margin: 0;">
                                                        <button type="submit" class="theme-btn">Luu thong tin <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="form-card">
                                        <div class="profile-card-head">
                                            <div>
                                                <span class="profile-step">Buoc 2</span>
                                                <h4>Bao mat tai khoan</h4>
                                                <p>Doi mat khau o day neu ban muon tang bao mat hoac vua chia se tai khoan tren thiet bi khac.</p>
                                            </div>
                                            <span class="chip warning">Bao mat</span>
                                        </div>

                                        <div class="security-points">
                                            <div class="security-point">
                                                <strong>Nhap mat khau hien tai truoc</strong>
                                                <span>He thong can xac minh chinh ban la nguoi dang thay doi mat khau.</span>
                                            </div>
                                            <div class="security-point">
                                                <strong>Mat khau moi toi thieu 6 ky tu</strong>
                                                <span>Khong nen dung lai mat khau cu va nen chua ky tu de de nho nhung kho doan.</span>
                                            </div>
                                        </div>

                                        <form class="row" method="POST" action="{{ route('account.password.update') }}" style="margin-top: 18px;">
                                            @csrf
                                            <input type="hidden" name="form_mode" value="account_password">

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Mat khau hien tai</label>
                                                    <input class="@error('current_password') is-invalid @enderror" type="password" name="current_password" placeholder="Nhap mat khau hien tai">
                                                    @error('current_password')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Mat khau moi</label>
                                                    <input class="@error('new_password') is-invalid @enderror" type="password" name="new_password" placeholder="Toi thieu 6 ky tu">
                                                    <small class="field-note">Nen su dung mat khau khac voi mat khau cu de tang muc do an toan.</small>
                                                    @error('new_password')<span class="error-text">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form_boxes">
                                                    <label>Nhap lai mat khau moi</label>
                                                    <input class="@error('new_password') is-invalid @enderror" type="password" name="new_password_confirmation" placeholder="Nhap lai mat khau moi">
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="security-note">Neu ban dang dang nhap tren nhieu thiet bi, hay dam bao cac thiet bi con lai van thuoc quyen su dung cua ban sau khi doi mat khau.</div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-submit">
                                                    <button type="submit" class="theme-btn">Cap nhat mat khau <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
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
                            <div class="section-head"><div><h3>Lich hen cua toi</h3><p>Theo doi cac lich xem xe hoac lai thu da gui tu public site.</p></div></div>
                            @if ($accountAppointments->isEmpty())
                                <div class="empty">Ban chua co lich hen nao. Khi da tim duoc xe phu hop, hay vao trang chi tiet xe de dat lich.</div>
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
                                                <a href="{{ $appointment->context_url }}" class="action-link">Mo context</a>
                                            </div>
                                            <p>{{ $appointment->note !== '' ? $appointment->note : 'Chua co ghi chu them cho lich hen nay.' }}</p>
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
                            <div class="section-head"><div><h3>Yeu cau cua toi</h3><p>Danh sach cac lead ban da tao tu trang chi tiet xe, trang phien ban hoac form lien he.</p></div></div>
                            @if ($accountLeads->isEmpty())
                                <div class="empty">Ban chua tao yeu cau nao khi dang nhap. Cac yeu cau moi se duoc luu tai day de ban theo doi trang thai.</div>
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
                                                <a href="{{ $lead->context_url }}" class="action-link">Xem context</a>
                                            </div>
                                            <p>{{ $lead->message !== '' ? $lead->message : 'Lead nay khong co ghi chu bo sung.' }}</p>
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
                            <div class="section-head"><div><h3>Xe da mua</h3><p>Lich su cac xe da duoc gan cho tai khoan cua ban sau khi sale offline duoc tao.</p></div></div>
                            @if ($accountPurchases->isEmpty())
                                <div class="empty">Hien chua co sale nao duoc lien ket voi tai khoan nay. Khi showroom gan sale cho tai khoan, thong tin se hien o day.</div>
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
                                                    <a href="{{ $purchase->trim_url }}" class="action-link">{{ $purchase->can_review ? 'Gui review cho trim' : 'Xem trang trim' }}</a>
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
                            <div class="section-head"><div><h3>Danh gia cua toi</h3><p>Theo doi review da gui cho cac trim ban da mua.</p></div></div>
                            @if ($accountReviews->isEmpty())
                                <div class="empty">Ban chua gui review nao. Sau khi mua xe va dang nhap dung tai khoan, ban co the vao trang trim de gui danh gia.</div>
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
                                                <a href="{{ $review->trim_url }}" class="action-link">Mo trim</a>
                                            </div>
                                            <div class="rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i>
                                                @endfor
                                                <span style="margin-left: 8px; color: #667085;">{{ $review->rating }}/5</span>
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
