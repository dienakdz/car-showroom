@php($withoutAdminChrome = ($withoutAdminChrome ?? false) || trim((string) $__env->yieldContent('without-admin-chrome')) === '1')
@php($adminCssVersion = file_exists(public_path('boxcar/css/admin.css')) ? filemtime(public_path('boxcar/css/admin.css')) : time())
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($adminPageTitle ?? 'Admin') . ' | ' . ($adminBrandName ?? 'Car Showroom'))</title>

    <link href="{{ asset('boxcar/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('boxcar/css/fontawesome.css') }}" rel="stylesheet">
    <link href="{{ asset('boxcar/css/admin.css') }}?v={{ $adminCssVersion }}" rel="stylesheet">
    <link href="{{ asset('vendor/flasher/flasher.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/flasher/toastr.min.css') }}" rel="stylesheet">
    @livewireStyles
    <link rel="shortcut icon" href="{{ asset('boxcar/images/favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('boxcar/images/favicon.png') }}" type="image/x-icon">
    @stack('styles')
</head>
<body class="c1-body {{ $withoutAdminChrome ? 'c1-auth-body' : '' }}">
@unless ($withoutAdminChrome)
    <div class="c1-layout">
        @include('admin.partials.sidebar')
        <div class="c1-main">
            @include('admin.partials.header')
            <main class="c1-content">
                @if ($errors->any())
                    <div class="c1-alert c1-alert-danger">
                        <strong>Có lỗi cần xử lý:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('admin-content')
            </main>
        </div>
    </div>
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

<script src="{{ asset('boxcar/js/jquery.js') }}"></script>
<script src="{{ asset('boxcar/js/popper.min.js') }}"></script>
<script src="{{ asset('boxcar/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/flasher/flasher.min.js') }}"></script>
<script src="{{ asset('vendor/flasher/toastr.min.js') }}"></script>
@livewireScripts
@flasher_render
@stack('scripts')
</body>
</html>
