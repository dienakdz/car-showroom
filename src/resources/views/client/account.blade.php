@extends('client.layouts.page')

@section('title', 'Trung tâm khách hàng & Quản lý tài khoản')

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

            const cleanTabName = paneId.replace('account-', '').replace('-pane', '');
            const url = new URL(window.location.href);
            url.searchParams.set('tab', cleanTabName);
            window.history.replaceState({}, '', url);
        };

        // Click on sidebar buttons
        buttons.forEach((button) => {
            button.addEventListener('click', function () {
                setActiveTab(button.dataset.accountTab);
            });
        });

        // Click on in-page triggers (KPI cards, quick actions)
        document.querySelectorAll('[data-switch-tab]').forEach((trigger) => {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                const targetPaneId = trigger.getAttribute('data-switch-tab');
                if (targetPaneId) {
                    setActiveTab(targetPaneId);
                    window.scrollTo({
                        top: tabList.getBoundingClientRect().top + window.scrollY - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Initial tab check from URL or active class
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            const matchedPaneId = 'account-' + tabParam + '-pane';
            const matchedBtn = buttons.find((btn) => btn.dataset.accountTab === matchedPaneId);
            if (matchedBtn) {
                setActiveTab(matchedPaneId, false);
                return;
            }
        }

        const initialButton = buttons.find((button) => button.classList.contains('active')) ?? buttons[0];
        if (initialButton) {
            setActiveTab(initialButton.dataset.accountTab, false);
        }
    });
</script>
@endpush

@section('content')
@php
    $accountUser = auth()->user();
    $rawTab = request('tab', 'overview');
    $tabMap = [
        'overview' => 'account-overview',
        'profile' => 'account-profile',
        'notifications' => 'account-notifications',
        'appointments' => 'account-appointments',
        'leads' => 'account-leads',
        'purchases' => 'account-purchases',
        'reviews' => 'account-reviews',
    ];
    $activeAccountTab = $tabMap[$rawTab] ?? 'account-overview';

    if (in_array(old('form_mode'), ['account_profile', 'account_password'], true)) {
        $activeAccountTab = 'account-profile';
    }
@endphp

<section class="account-page-wrap">
    <div class="boxcar-container">
        <!-- Master Account Shell Layout -->
        <div class="account-layout-grid">
            <!-- Sidebar Column -->
            <div class="account-sidebar-col">
                @include('client.partials.account.sidebar', [
                    'accountUser' => $accountUser,
                    'accountSummary' => $accountSummary,
                    'activeAccountTab' => $activeAccountTab,
                ])
            </div>

            <!-- Main Content Panes Column -->
            <div class="account-content-col">
                <!-- Tab: Overview -->
                <div class="account-tab-pane {{ $activeAccountTab === 'account-overview' ? 'is-active' : '' }}" 
                     id="account-overview-pane" 
                     role="tabpanel" 
                     aria-labelledby="account-overview-tab" 
                     tabindex="0"
                     {{ $activeAccountTab !== 'account-overview' ? 'hidden' : '' }}>
                    @include('client.partials.account.tab-overview', [
                        'accountSummary' => $accountSummary,
                    ])
                </div>

                <!-- Tab: Profile & Security -->
                <div class="account-tab-pane {{ $activeAccountTab === 'account-profile' ? 'is-active' : '' }}" 
                     id="account-profile-pane" 
                     role="tabpanel" 
                     aria-labelledby="account-profile-tab" 
                     tabindex="0"
                     {{ $activeAccountTab !== 'account-profile' ? 'hidden' : '' }}>
                    @include('client.partials.account.tab-profile', [
                        'accountUser' => $accountUser,
                        'accountSummary' => $accountSummary,
                    ])
                </div>

                <!-- Tab: Notifications -->
                <div class="account-tab-pane {{ $activeAccountTab === 'account-notifications' ? 'is-active' : '' }}" 
                     id="account-notifications-pane" 
                     role="tabpanel" 
                     aria-labelledby="account-notifications-tab" 
                     tabindex="0"
                     {{ $activeAccountTab !== 'account-notifications' ? 'hidden' : '' }}>
                    @include('client.partials.account.tab-notifications', [
                        'accountNotifications' => $accountNotifications,
                        'accountSummary' => $accountSummary,
                    ])
                </div>

                <!-- Tab: Appointments -->
                <div class="account-tab-pane {{ $activeAccountTab === 'account-appointments' ? 'is-active' : '' }}" 
                     id="account-appointments-pane" 
                     role="tabpanel" 
                     aria-labelledby="account-appointments-tab" 
                     tabindex="0"
                     {{ $activeAccountTab !== 'account-appointments' ? 'hidden' : '' }}>
                    @include('client.partials.account.tab-appointments', [
                        'accountAppointments' => $accountAppointments,
                    ])
                </div>

                <!-- Tab: Leads / Consultations -->
                <div class="account-tab-pane {{ $activeAccountTab === 'account-leads' ? 'is-active' : '' }}" 
                     id="account-leads-pane" 
                     role="tabpanel" 
                     aria-labelledby="account-leads-tab" 
                     tabindex="0"
                     {{ $activeAccountTab !== 'account-leads' ? 'hidden' : '' }}>
                    @include('client.partials.account.tab-leads', [
                        'accountLeads' => $accountLeads,
                    ])
                </div>

                <!-- Tab: Purchases / My Garage -->
                <div class="account-tab-pane {{ $activeAccountTab === 'account-purchases' ? 'is-active' : '' }}" 
                     id="account-purchases-pane" 
                     role="tabpanel" 
                     aria-labelledby="account-purchases-tab" 
                     tabindex="0"
                     {{ $activeAccountTab !== 'account-purchases' ? 'hidden' : '' }}>
                    @include('client.partials.account.tab-purchases', [
                        'accountPurchases' => $accountPurchases,
                    ])
                </div>

                <!-- Tab: Reviews -->
                <div class="account-tab-pane {{ $activeAccountTab === 'account-reviews' ? 'is-active' : '' }}" 
                     id="account-reviews-pane" 
                     role="tabpanel" 
                     aria-labelledby="account-reviews-tab" 
                     tabindex="0"
                     {{ $activeAccountTab !== 'account-reviews' ? 'hidden' : '' }}>
                    @include('client.partials.account.tab-reviews', [
                        'accountReviews' => $accountReviews,
                    ])
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
