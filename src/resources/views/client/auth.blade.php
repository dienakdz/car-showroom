@extends('client.layouts.app')

@section('title', auth()->check() ? 'Tai khoan' : 'Dang nhap')

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

@push('styles')
<style>
    .client-account-shell {
        padding: 42px 0 72px;
        background: linear-gradient(180deg, #f7f8fc 0%, #ffffff 42%, #f5f7fb 100%);
    }
    .client-account-shell .shell {
        display: grid;
        grid-template-columns: 290px minmax(0, 1fr);
        gap: 24px;
    }
    .client-account-shell .panel {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 28px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
    }
    .client-account-shell .panel-inner { padding: 24px; }
    .client-account-shell .hero {
        background: linear-gradient(155deg, #050b20 0%, #13224a 55%, #2746d8 100%);
        color: #fff;
    }
    .client-account-shell .hero h1,
    .client-account-shell .hero h2,
    .client-account-shell .hero p,
    .client-account-shell .hero strong,
    .client-account-shell .hero span,
    .client-account-shell .hero a { color: #fff; }
    .client-account-shell .sidebar {
        position: sticky;
        top: 118px;
        display: grid;
        gap: 18px;
        align-self: start;
    }
    .client-account-shell .kicker,
    .client-login-shell .kicker {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(64, 95, 242, 0.12);
        color: #405ff2;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .client-account-shell .hero .kicker { background: rgba(255, 255, 255, 0.12); color: #fff; }
    .client-account-shell .avatar {
        width: 62px;
        height: 62px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 16px 0;
        background: rgba(255, 255, 255, 0.12);
        font-size: 24px;
        font-weight: 700;
    }
    .client-account-shell .nav-list,
    .client-account-shell .list-grid { display: grid; gap: 12px; }
    .client-account-shell .nav-list { list-style: none; padding: 0; margin: 0; }
    .client-account-shell .nav-link {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 18px;
        background: #f7f9fc;
        color: #050b20;
        font-weight: 600;
    }
    .client-account-shell button.nav-link {
        width: 100%;
        border: 0;
        text-align: left;
    }
    .client-account-shell .nav-link:hover { background: #eef2ff; color: #405ff2; }
    .client-account-shell .nav-link.active {
        background: #405ff2;
        color: #fff;
    }
    .client-account-shell .logout-btn {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid rgba(217, 48, 37, 0.18);
        border-radius: 18px;
        background: rgba(217, 48, 37, 0.04);
        color: #b42318;
        font-weight: 700;
        text-align: left;
    }
    .client-account-shell .main { display: grid; gap: 24px; }
    .client-account-shell .stats,
    .client-account-shell .overview-grid,
    .client-account-shell .forms,
    .client-account-shell .purchase-grid { display: grid; gap: 16px; }
    .client-account-shell .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 20px; }
    .client-account-shell .overview-grid,
    .client-account-shell .forms { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .client-account-shell .purchase-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .client-account-shell .stat,
    .client-account-shell .mini-card,
    .client-account-shell .form-card,
    .client-account-shell .item,
    .client-account-shell .purchase,
    .client-account-shell .review {
        border: 1px solid #e5eaf2;
        border-radius: 22px;
        background: #fff;
    }
    .client-account-shell .stat { padding: 18px; background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.14); }
    .client-account-shell .stat strong { display: block; font-size: 28px; line-height: 1; margin-bottom: 6px; }
    .client-account-shell .section-head { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
    .client-account-shell .section-head h3,
    .client-account-shell .form-card h4 { margin: 0; font-size: 24px; color: #050b20; }
    .client-account-shell .section-head p,
    .client-account-shell .form-card p,
    .client-account-shell .item p,
    .client-account-shell .mini-card p,
    .client-account-shell .purchase p,
    .client-account-shell .review p { margin: 8px 0 0; color: #667085; line-height: 1.7; }
    .client-account-shell .mini-card,
    .client-account-shell .form-card,
    .client-account-shell .item,
    .client-account-shell .review { padding: 20px; }
    .client-account-shell .item-top { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 10px; }
    .client-account-shell .title-link { color: #050b20; font-size: 17px; font-weight: 700; line-height: 1.45; }
    .client-account-shell .title-link:hover,
    .client-account-shell .action-link { color: #405ff2; }
    .client-account-shell .meta { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
    .client-account-shell .chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }
    .client-account-shell .chip.info { background: rgba(64, 95, 242, 0.12); color: #405ff2; }
    .client-account-shell .chip.warning { background: rgba(245, 158, 11, 0.16); color: #b45309; }
    .client-account-shell .chip.success { background: rgba(22, 163, 74, 0.14); color: #15803d; }
    .client-account-shell .chip.danger { background: rgba(217, 48, 37, 0.1); color: #b42318; }
    .client-account-shell .chip.neutral { background: #eef2f6; color: #475467; }
    .client-account-shell .error-text { display: block; margin-top: 8px; color: #d93025; font-size: 13px; }
    .client-account-shell .purchase {
        display: grid;
        grid-template-columns: 124px minmax(0, 1fr);
        gap: 16px;
        padding: 18px;
    }
    .client-account-shell .purchase img { width: 100%; height: 116px; object-fit: cover; border-radius: 18px; }
    .client-account-shell .rating { display: flex; gap: 4px; flex-wrap: wrap; color: #f59e0b; margin-bottom: 10px; }
    .client-account-shell .account-tab-pane { display: none; }
    .client-account-shell .account-tab-pane.is-active { display: grid; gap: 24px; }
    .client-account-shell .profile-shell,
    .client-account-shell .profile-overview,
    .client-account-shell .profile-editor-grid,
    .client-account-shell .profile-summary-grid,
    .client-account-shell .security-points { display: grid; gap: 16px; }
    .client-account-shell .profile-overview,
    .client-account-shell .profile-editor-grid { grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr); }
    .client-account-shell .profile-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .client-account-shell .profile-step {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(64, 95, 242, 0.12);
        color: #405ff2;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }
    .client-account-shell .profile-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .client-account-shell .profile-summary-item,
    .client-account-shell .security-point {
        padding: 16px 18px;
        border-radius: 18px;
        border: 1px solid #e5eaf2;
        background: #f8faff;
    }
    .client-account-shell .profile-summary-item label {
        display: block;
        margin-bottom: 8px;
        color: #667085;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .client-account-shell .profile-summary-item strong,
    .client-account-shell .security-point strong {
        display: block;
        color: #050b20;
        font-size: 17px;
        line-height: 1.45;
    }
    .client-account-shell .profile-summary-item span,
    .client-account-shell .security-point span {
        display: block;
        margin-top: 6px;
        color: #667085;
        line-height: 1.6;
    }
    .client-account-shell .profile-guide-list {
        margin: 0;
        padding-left: 18px;
        display: grid;
        gap: 12px;
        color: #475467;
        line-height: 1.7;
    }
    .client-account-shell .field-note {
        display: block;
        margin-top: 8px;
        color: #667085;
        font-size: 13px;
        line-height: 1.6;
    }
    .client-account-shell .form_boxes {
        margin-bottom: 18px;
    }
    .client-account-shell .form_boxes label {
        display: block;
        margin-bottom: 10px;
        color: #101828;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.4;
    }
    .client-account-shell .form_boxes input {
        width: 100%;
        height: 58px;
        padding: 0 18px;
        border: 1px solid #cbd5e1;
        border-radius: 16px;
        background: #f8fbff;
        color: #0f172a;
        font-size: 16px;
        line-height: 1.5;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        appearance: none;
        -webkit-appearance: none;
    }
    .client-account-shell .form_boxes input:hover {
        border-color: #94a3b8;
        background: #fff;
    }
    .client-account-shell .form_boxes input:focus {
        outline: none;
        border-color: #405ff2;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(64, 95, 242, 0.14);
    }
    .client-account-shell .form_boxes input.is-invalid {
        border-color: #d93025;
        background: #fff8f7;
        box-shadow: 0 0 0 4px rgba(217, 48, 37, 0.08);
    }
    .client-account-shell .form_boxes input::placeholder {
        color: #98a2b3 !important;
    }
    .client-account-shell .profile-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 10px;
    }
    .client-account-shell .profile-actions p {
        margin: 0;
        max-width: 360px;
        color: #667085;
        line-height: 1.7;
    }
    .client-account-shell .security-note {
        padding: 16px 18px;
        border-radius: 18px;
        background: #fff9eb;
        border: 1px solid rgba(245, 158, 11, 0.24);
        color: #92400e;
        line-height: 1.7;
    }
    .client-account-shell .empty {
        padding: 22px;
        border-radius: 22px;
        background: #f7f9fc;
        border: 1px dashed #cfd8e6;
        color: #667085;
        line-height: 1.7;
    }
    .client-login-shell {
        padding: 48px 0 72px;
        background: linear-gradient(180deg, #f7f8fc 0%, #ffffff 42%, #f5f7fb 100%);
    }
    .client-login-shell .login-panel {
        max-width: 1080px;
        margin: 0 auto;
        border-radius: 32px;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 24px 80px rgba(15, 23, 42, 0.12);
        display: grid;
        grid-template-columns: 360px minmax(0, 1fr);
    }
    .client-login-shell .intro {
        padding: 36px;
        background: linear-gradient(155deg, #050b20 0%, #13224a 55%, #2746d8 100%);
        color: #fff;
    }
    .client-login-shell .intro h2 { margin: 18px 0 10px; color: #fff; font-size: 38px; line-height: 1.05; }
    .client-login-shell .intro p,
    .client-login-shell .intro li { color: rgba(255, 255, 255, 0.8); line-height: 1.7; }
    .client-login-shell .intro ul { margin: 24px 0 0; padding-left: 18px; }
    .client-login-shell .form-pane { padding: 34px; }
    .client-login-shell .nav-tabs { gap: 10px; border: 0; margin-bottom: 20px; }
    .client-login-shell .nav-tabs .nav-link {
        border: 1px solid #d9dde8;
        border-radius: 16px;
        min-width: 140px;
        color: #050b20;
        font-weight: 700;
        padding: 12px 18px;
    }
    .client-login-shell .nav-tabs .nav-link.active { border-color: #405ff2; background: #405ff2; color: #fff; }
    @media (max-width: 1199px) {
        .client-account-shell .stats,
        .client-account-shell .purchase-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 991px) {
        .client-account-shell .shell,
        .client-account-shell .overview-grid,
        .client-account-shell .forms,
        .client-account-shell .profile-overview,
        .client-account-shell .profile-editor-grid,
        .client-login-shell .login-panel { grid-template-columns: 1fr; }
        .client-account-shell .sidebar { position: static; }
    }
    @media (max-width: 767px) {
        .client-account-shell .panel-inner,
        .client-login-shell .intro,
        .client-login-shell .form-pane { padding: 22px; }
        .client-account-shell .stats,
        .client-account-shell .purchase-grid,
        .client-account-shell .profile-summary-grid { grid-template-columns: 1fr; }
        .client-account-shell .purchase { grid-template-columns: 1fr; }
    }
</style>
@endpush

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

    <section class="client-account-shell layout-radius">
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
    <section class="client-login-shell layout-radius">
        <div class="boxcar-container">
            <div class="login-panel">
                <div class="intro">
                    <span class="kicker">Client access</span>
                    <h2>Dang nhap de theo doi lead, booking va review</h2>
                    <p>Tai khoan client giup ban luu thong tin lien he, xem lich hen, theo doi lead da gui va danh gia trim sau khi mua xe.</p>
                    <ul>
                        <li>Dang nhap bang email, so dien thoai hoac ten tai khoan.</li>
                        <li>Tai khoan moi co the tao bang email hoac so dien thoai.</li>
                        <li>Review trim chi mo cho user da co sale hop le.</li>
                    </ul>
                </div>

                <div class="form-pane">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link {{ $activeTab !== 'register' ? 'active' : '' }}" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="{{ $activeTab !== 'register' ? 'true' : 'false' }}">Dang nhap</button>
                            <button class="nav-link {{ $activeTab === 'register' ? 'active' : '' }}" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="{{ $activeTab === 'register' ? 'true' : 'false' }}">Tao tai khoan</button>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade {{ $activeTab !== 'register' ? 'show active' : '' }}" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="form-box">
                                <form method="POST" action="{{ route('login.attempt') }}">
                                    @csrf
                                    <input type="hidden" name="form_mode" value="login">
                                    <div class="form_boxes">
                                        <label>Email, so dien thoai hoac ten tai khoan</label>
                                        <input type="text" name="identifier" value="{{ $activeTab !== 'register' ? old('identifier') : '' }}" placeholder="admin@showroom.test">
                                        @error('identifier')<span class="error-text">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="form_boxes">
                                        <label>Mat khau</label>
                                        <input type="password" name="password" placeholder="********">
                                        @error('password')<span class="error-text">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="btn-box">
                                        <label class="contain">Ghi nho dang nhap
                                            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                        <a href="{{ route('contact') }}" class="pasword-btn">Can ho tro?</a>
                                    </div>
                                    <div class="form-submit">
                                        <button type="submit" class="theme-btn">Dang nhap <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $activeTab === 'register' ? 'show active' : '' }}" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="form-box two">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <input type="hidden" name="form_mode" value="register">
                                    <div class="form_boxes">
                                        <label>Ho ten</label>
                                        <input type="text" name="name" value="{{ $activeTab === 'register' ? old('name') : '' }}" placeholder="Nguyen Van A">
                                        @error('name')<span class="error-text">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="form_boxes">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ $activeTab === 'register' ? old('email') : '' }}" placeholder="name@email.com">
                                        @error('email')<span class="error-text">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="form_boxes">
                                        <label>So dien thoai</label>
                                        <input type="text" name="phone" value="{{ $activeTab === 'register' ? old('phone') : '' }}" placeholder="0901234567">
                                        @error('phone')<span class="error-text">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="form_boxes">
                                        <label>Mat khau</label>
                                        <input type="password" name="password" placeholder="Toi thieu 6 ky tu">
                                        @error('password')<span class="error-text">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="btn-box">
                                        <label class="contain">Toi dong y voi chinh sach bao mat
                                            <input type="checkbox" name="accept_privacy" value="1" {{ old('accept_privacy') ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="form-submit">
                                        <button type="submit" class="theme-btn">Tao tai khoan <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endauth
@endsection
