@extends('client.layouts.app')

@section('title', '404 | Khong tim thay trang')

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

@section('content')
    <section class="error-section layout-radius">
        <div class="boxcar-container">
            <div class="right-box">
                <div class="image-box">
                    <img src="{{ asset('boxcar/images/resource/error.png') }}" alt="404 error">
                    <div class="content-box">
                        <h2>Oops! Trang ban tim hien khong co san.</h2>
                        <div class="text">
                            Noi dung nay da duoc di chuyen, da bi go bo hoac URL khong con hop le.
                            Ban co the quay ve trang chu hoac tiep tuc tim xe trong kho.
                        </div>
                        <a href="{{ route('home') }}" class="error-btn">
                            Ve trang chu
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none" aria-hidden="true">
                                <g clip-path="url(#clip0_error_page_cta)">
                                    <path d="M13.6111 0.891113H5.05558C4.84062 0.891113 4.66668 1.06506 4.66668 1.28001C4.66668 1.49497 4.84062 1.66892 5.05558 1.66892H12.6723L0.113941 14.2273C-0.0379805 14.3792 -0.0379805 14.6253 0.113941 14.7772C0.189884 14.8531 0.289415 14.8911 0.38891 14.8911C0.488405 14.8911 0.5879 14.8531 0.663879 14.7772L13.2222 2.21882V9.83558C13.2222 10.0505 13.3962 10.2245 13.6111 10.2245C13.8261 10.2245 14 10.0505 14 9.83558V1.28001C14 1.06506 13.8261 0.891113 13.6111 0.891113Z" fill="#405FF2"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_error_page_cta">
                                        <rect width="14" height="14" fill="white" transform="translate(0 0.891113)"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
