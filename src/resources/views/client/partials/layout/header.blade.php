@php($headerClasses = $headerClasses ?? 'boxcar-header header-style-v1 style-two inner-header')
@php($showSearch = $showSearch ?? false)
@php($accountLabel = auth()->check() ? 'Tai khoan' : 'Dang nhap')
@php($accountUrl = auth()->check() ? route('account.show') : route('login'))
@php($inventoryMenuActive = request()->routeIs('inventory.*'))

<header class="{{ $headerClasses }}">
    <div class="header-inner">
        <div class="inner-container">
            <div class="c-box">
                <div class="logo-inner">
                    <div class="logo">
                        <a href="{{ route('home') }}"><img src="{{ asset('boxcar/images/logo.svg') }}" alt="Car Showroom" title="Car Showroom"></a>
                    </div>

                    @if ($showSearch)
                        <div class="layout-search style1">
                            <form action="{{ route('inventory.index') }}" method="GET" class="search-box">
                                <svg class="icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.29301 1.2876C3.9872 1.2876 1.29431 3.98048 1.29431 7.28631C1.29431 10.5921 3.9872 13.2902 7.29301 13.2902C8.70502 13.2902 10.0036 12.7954 11.03 11.9738L13.5287 14.4712C13.6548 14.5921 13.8232 14.6588 13.9979 14.657C14.1725 14.6552 14.3395 14.5851 14.4631 14.4617C14.5867 14.3382 14.6571 14.1713 14.6591 13.9967C14.6611 13.822 14.5947 13.6535 14.474 13.5272L11.9753 11.0285C12.7976 10.0006 13.293 8.69995 13.293 7.28631C13.293 3.98048 10.5988 1.2876 7.29301 1.2876ZM7.29301 2.62095C9.87824 2.62095 11.9584 4.70108 11.9584 7.28631C11.9584 9.87153 9.87824 11.9569 7.29301 11.9569C4.70778 11.9569 2.62764 9.87153 2.62764 7.28631C2.62764 4.70108 4.70778 2.62095 7.29301 2.62095Z" fill="white"/>
                                </svg>
                                <input type="search" name="q" placeholder="Tim xe theo hang, dong hoac ma xe" value="{{ request('q') }}">
                            </form>
                        </div>
                    @endif
                </div>

                <div class="nav-out-bar">
                    <nav class="nav main-menu">
                        <ul class="navigation" id="navbar">
                            <li class="{{ request()->routeIs('home') ? 'current' : '' }}"><a href="{{ route('home') }}">Trang chu</a></li>
                            <li class="current-dropdown {{ $inventoryMenuActive ? 'current' : '' }}">
                                <a href="{{ route('inventory.index') }}">Kho xe <i class="fa-solid fa-angle-down"></i></a>
                                <ul class="dropdown">
                                    <li class="{{ request()->routeIs('inventory.new') ? 'current' : '' }}"><a href="{{ route('inventory.new') }}">Xe moi</a></li>
                                    <li class="{{ request()->routeIs('inventory.used') ? 'current' : '' }}"><a href="{{ route('inventory.used') }}">Xe cu</a></li>
                                </ul>
                            </li>
                            <li class="current-dropdown {{ request()->routeIs('finance') || request()->routeIs('tradein') ? 'current' : '' }}">
                                <span>Dich vu <i class="fa-solid fa-angle-down"></i></span>
                                <ul class="dropdown">
                                    <li><a href="{{ route('finance') }}">Tu van tai chinh</a></li>
                                    <li><a href="{{ route('tradein') }}">Thu cu doi moi</a></li>
                                </ul>
                            </li>
                            <li class="{{ request()->routeIs('about') ? 'current' : '' }}"><a href="{{ route('about') }}">Ve chung toi</a></li>
                            <li class="{{ request()->routeIs('contact') ? 'current' : '' }}"><a href="{{ route('contact') }}">Lien he</a></li>
                            <li class="d-lg-none {{ request()->routeIs('login') || request()->routeIs('account.show') ? 'current' : '' }}"><a href="{{ $accountUrl }}">{{ $accountLabel }}</a></li>
                        </ul>
                    </nav>
                </div>

                <div class="right-box">
                    <a href="tel:{{ $navShowroom->phone ?? '0900000000' }}" class="box-account">
                        <span class="icon"><i class="fa-solid fa-phone"></i></span>
                        {{ $navShowroom->phone ?? '0900 000 000' }}
                    </a>
                    <a href="{{ $accountUrl }}" class="box-account">
                        <span class="icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_header_account)">
                                    <path d="M7.99998 9.01221C3.19258 9.01221 0.544983 11.2865 0.544983 15.4161C0.544983 15.7386 0.806389 16.0001 1.12892 16.0001H14.871C15.1935 16.0001 15.455 15.7386 15.455 15.4161C15.455 11.2867 12.8074 9.01221 7.99998 9.01221ZM1.73411 14.8322C1.9638 11.7445 4.06889 10.1801 7.99998 10.1801C11.9311 10.1801 14.0362 11.7445 14.2661 14.8322H1.73411Z" fill="white"/>
                                    <path d="M7.99999 0C5.79171 0 4.12653 1.69869 4.12653 3.95116C4.12653 6.26959 5.86415 8.15553 7.99999 8.15553C10.1358 8.15553 11.8735 6.26959 11.8735 3.95134C11.8735 1.69869 10.2083 0 7.99999 0ZM7.99999 6.98784C6.50803 6.98784 5.2944 5.62569 5.2944 3.95134C5.2944 2.3385 6.43231 1.16788 7.99999 1.16788C9.54259 1.16788 10.7056 2.36438 10.7056 3.95134C10.7056 5.62569 9.49196 6.98784 7.99999 6.98784Z" fill="white"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_header_account">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                        {{ $accountLabel }}
                    </a>
                    <div class="btn">
                        <a href="{{ route('inventory.index') }}" class="header-btn-two btn-anim">Xem kho xe</a>
                    </div>
                    <div class="mobile-navigation">
                        <a href="#nav-mobile" title="Mo menu">
                            <svg width="22" height="11" viewBox="0 0 22 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="22" height="2" fill="white"/>
                                <rect y="9" width="22" height="2" fill="white"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="nav-mobile"></div>
</header>
