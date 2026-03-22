<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Car Showroom')</title>

    <link href="{{ asset('boxcar/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('boxcar/css/slick-theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('boxcar/css/slick.css') }}">
    <link href="{{ asset('boxcar/css/mmenu.css') }}" rel="stylesheet">
    <link href="{{ asset('boxcar/css/style.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('boxcar/images/favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('boxcar/images/favicon.png') }}" type="image/x-icon">
    @stack('styles')
</head>
<body>
<div class="boxcar-wrapper">
    @yield('header')

    @yield('content')

    @yield('footer')
</div>

<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>

<script src="{{ asset('boxcar/js/jquery.js') }}"></script>
<script src="{{ asset('boxcar/js/popper.min.js') }}"></script>
<script src="{{ asset('boxcar/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('boxcar/js/slick.min.js') }}"></script>
<script src="{{ asset('boxcar/js/slick-animation.min.js') }}"></script>
<script src="{{ asset('boxcar/js/jquery.fancybox.js') }}"></script>
<script src="{{ asset('boxcar/js/wow.js') }}"></script>
<script src="{{ asset('boxcar/js/appear.js') }}"></script>
<script src="{{ asset('boxcar/js/mixitup.js') }}"></script>
<script src="{{ asset('boxcar/js/knob.js') }}"></script>
<script src="{{ asset('boxcar/js/mmenu.js') }}"></script>
<script src="{{ asset('boxcar/js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
