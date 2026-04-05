@php($withoutAdminChrome = ($withoutAdminChrome ?? false) || trim((string) $__env->yieldContent('without-admin-chrome')) === '1')
@php($adminCssVersion = file_exists(public_path('boxcar/css/admin.css')) ? filemtime(public_path('boxcar/css/admin.css')) : time())
<!DOCTYPE html>
<html lang="vi" class="{{ $withoutAdminChrome ? 'admin-html-auth' : 'admin-html-shell' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($adminPageTitle ?? 'Admin') . ' | ' . ($adminBrandName ?? 'Car Showroom'))</title>

    <link href="{{ asset('boxcar/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('boxcar/css/fontawesome.css') }}" rel="stylesheet">
    <link href="{{ asset('boxcar/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('boxcar/css/admin.css') }}?v={{ $adminCssVersion }}" rel="stylesheet">
    <link href="{{ asset('vendor/flasher/flasher.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/flasher/toastr.min.css') }}" rel="stylesheet">
    @livewireStyles
    <link rel="shortcut icon" href="{{ asset('boxcar/images/favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('boxcar/images/favicon.png') }}" type="image/x-icon">
    @stack('styles')
</head>
<body class="admin-body {{ $withoutAdminChrome ? 'admin-body-auth' : 'admin-body-shell' }}">
<div class="admin-wrapper {{ $withoutAdminChrome ? 'admin-wrapper-auth' : 'admin-wrapper-shell' }}">
    @unless ($withoutAdminChrome)
        @include('admin.partials.header')
        <section class="dashboard-widget admin-dashboard-widget">
            <div class="right-box admin-shell-grid">
                @include('admin.partials.sidebar')
                <div class="content-column admin-content-column">
                    <div class="inner-column">
                        @include('admin.partials.page-header')

                        @if ($errors->any())
                            <div class="admin-alert admin-alert-danger">
                                <strong>Co loi can xu ly:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @yield('admin-content')
                    </div>
                </div>
            </div>
        </section>
    @else
        <main class="admin-auth-shell">
            @if ($errors->any())
                <div class="admin-auth-errors">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('admin-content')
        </main>
    @endunless
</div>

<script src="{{ asset('boxcar/js/jquery.js') }}"></script>
<script src="{{ asset('boxcar/js/popper.min.js') }}"></script>
<script src="{{ asset('boxcar/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/flasher/flasher.min.js') }}"></script>
<script src="{{ asset('vendor/flasher/flasher-toastr.min.js') }}"></script>
@livewireScripts
@flasher_render
@stack('scripts')
</body>
</html>
