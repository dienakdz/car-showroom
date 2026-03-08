@extends('client.layouts.home')

@section('title', 'Trang chủ')

@section('content')
@php
    $heroBodyTypeIcons = [
        'flaticon-car',
        'flaticon-car-1',
        'flaticon-van',
        'flaticon-convertible-car',
        'flaticon-electric-car-1',
    ];
    $bodyTypeCardImages = [
        'brand-1.png',
        'brand-2.png',
        'brand-3.png',
        'brand-4.png',
        'brand-5.png',
        'brand-6.png',
    ];
@endphp

<section class="boxcar-banner-section-v1">
    <div class="container">
        <div class="banner-content">
            <span class="wow fadeInUp">Showroom ô tô dành cho nhu cầu mua xe thực tế</span>
            <h2 class="wow fadeInUp" data-wow-delay="100ms">Tìm chiếc xe phù hợp với bạn</h2>
            <div class="form-tabs">
                <ul class="form-tabs-list wow fadeInUp" data-wow-delay="200ms">
                    <li class="current" data-tab="tab-1">Tất cả</li>
                    <li data-tab="tab-2">Xe mới</li>
                    <li data-tab="tab-3">Xe cũ</li>
                </ul>
                <div class="form-tab-content">
                    <div class="form-tab-content wow fadeInUp" data-wow-delay="300ms">
                        <div class="form-tab-pane current" id="tab-1">
                            @include('client.partials.home-filter-form', [
                                'action' => route('inventory.index'),
                                'buttonLabel' => 'Tìm kho xe',
                            ])
                        </div>
                        <div class="form-tab-pane" id="tab-2">
                            @include('client.partials.home-filter-form', [
                                'action' => route('inventory.new'),
                                'buttonLabel' => 'Tìm xe mới',
                            ])
                        </div>
                        <div class="form-tab-pane" id="tab-3">
                            @include('client.partials.home-filter-form', [
                                'action' => route('inventory.used'),
                                'buttonLabel' => 'Tìm xe cũ',
                            ])
                        </div>
                    </div>
                    <span class="wow fadeInUp" data-wow-delay="400ms">Lối tắt nhanh</span>
                    <ul class="model-links">
                        @forelse ($bodyTypes->take(5) as $bodyType)
                            <li>
                                <a href="{{ route('inventory.index', ['body_type' => $bodyType->slug]) }}" title="{{ $bodyType->name }}">
                                    <i class="{{ $heroBodyTypeIcons[$loop->index % count($heroBodyTypeIcons)] }}"></i>{{ $bodyType->name }}
                                </a>
                            </li>
                        @empty
                            <li><a href="{{ route('inventory.index') }}" title=""><i class="flaticon-car"></i>Tất cả xe</a></li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="boxcar-brand-section section-radius-top bg-1">
    <div class="boxcar-container">
        <div class="boxcar-title">
            <h2 class="wow fadeInUp">Khám phá theo loại xe</h2>
            <a href="{{ route('inventory.index') }}" class="btn-title">Xem toàn bộ kho xe
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M13.6109 0H5.05533C4.84037 0 4.66643 0.173943 4.66643 0.388901C4.66643 0.603859 4.84037 0.777802 5.05533 0.777802H12.6721L0.113697 13.3362C-0.0382246 13.4881 -0.0382246 13.7342 0.113697 13.8861C0.18964 13.962 0.289171 14 0.388666 14C0.488161 14 0.587656 13.962 0.663635 13.8861L13.222 1.3277V8.94447C13.222 9.15943 13.3959 9.33337 13.6109 9.33337C13.8259 9.33337 13.9998 9.15943 13.9998 8.94447V0.388901C13.9998 0.173943 13.8258 0 13.6109 0Z" fill="#050B20"/>
                </svg>
            </a>
        </div>
        <div class="row">
            @forelse ($bodyTypes->take(6) as $bodyType)
                <div class="cars-block style-1 col-lg-2 col-md-6 col-sm-6">
                    <div class="inner-box wow fadeInUp" @if ($loop->index > 0) data-wow-delay="{{ $loop->index * 100 }}ms" @endif>
                        <div class="image-box">
                            <figure class="image">
                                <a href="{{ route('inventory.index', ['body_type' => $bodyType->slug]) }}">
                                    <img src="{{ asset('boxcar/images/resource/' . $bodyTypeCardImages[$loop->index % count($bodyTypeCardImages)]) }}" alt="{{ $bodyType->name }}">
                                </a>
                            </figure>
                        </div>
                        <div class="content-box">
                            <h6 class="title"><a href="{{ route('inventory.index', ['body_type' => $bodyType->slug]) }}">{{ $bodyType->name }}</a></h6>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light">Chưa có loại xe để hiển thị.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="cars-section-three">
    <div class="boxcar-container">
        <div class="boxcar-title wow fadeInUp">
            <h2>Xe nổi bật</h2>
            <a href="{{ route('inventory.index') }}" class="btn-title">Xem tất cả
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <g clip-path="url(#clip0_home_featured_title)">
                        <path d="M13.6109 0H5.05533C4.84037 0 4.66643 0.173943 4.66643 0.388901C4.66643 0.603859 4.84037 0.777802 5.05533 0.777802H12.6721L0.113697 13.3362C-0.0382246 13.4881 -0.0382246 13.7342 0.113697 13.8861C0.18964 13.962 0.289171 14 0.388666 14C0.488161 14 0.587656 13.962 0.663635 13.8861L13.222 1.3277V8.94447C13.222 9.15943 13.3959 9.33337 13.6109 9.33337C13.8259 9.33337 13.9998 9.15943 13.9998 8.94447V0.388901C13.9998 0.173943 13.8258 0 13.6109 0Z" fill="#050B20"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_home_featured_title">
                            <rect width="14" height="14" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </a>
        </div>
        <nav class="wow fadeInUp" data-wow-delay="100ms">
            <div class="nav nav-tabs" id="home-vehicles-tabs" role="tablist">
                <button class="nav-link active" id="tab-featured-btn" data-bs-toggle="tab" data-bs-target="#tab-featured" type="button" role="tab" aria-controls="tab-featured" aria-selected="true">Xe nổi bật</button>
                <button class="nav-link" id="tab-new-btn" data-bs-toggle="tab" data-bs-target="#tab-new" type="button" role="tab" aria-controls="tab-new" aria-selected="false">Xe mới</button>
                <button class="nav-link" id="tab-used-btn" data-bs-toggle="tab" data-bs-target="#tab-used" type="button" role="tab" aria-controls="tab-used" aria-selected="false">Xe used/CPO</button>
            </div>
        </nav>
    </div>

    <div class="tab-content wow fadeInUp" data-wow-delay="200ms" id="home-vehicles-tabs-content">
        <div class="tab-pane fade show active" id="tab-featured" role="tabpanel" aria-labelledby="tab-featured-btn">
            <div class="row car-slider-three slider-layout-1" data-preview="4.8">
                @forelse ($featuredCars as $car)
                    @include('client.partials.home-featured-car-card', ['car' => $car])
                @empty
                    <div class="col-12">
                        <div class="alert alert-light">Chưa có xe nổi bật để hiển thị.</div>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="tab-pane fade" id="tab-new" role="tabpanel" aria-labelledby="tab-new-btn">
            <div class="row car-slider-three slider-layout-1" data-preview="4.8">
                @forelse ($newCars as $car)
                    @include('client.partials.home-featured-car-card', ['car' => $car])
                @empty
                    <div class="col-12">
                        <div class="alert alert-light">Chưa có xe mới.</div>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="tab-pane fade" id="tab-used" role="tabpanel" aria-labelledby="tab-used-btn">
            <div class="row car-slider-three slider-layout-1" data-preview="4.8">
                @forelse ($usedCars as $car)
                    @include('client.partials.home-featured-car-card', ['car' => $car])
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light">Chưa có xe used/CPO.</div>
                        </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<section class="boxcar-pricing-section pb-0 pt-0">
    <div class="large-container">
        <div class="row g-0">
            <div class="image-column col-lg-6 col-md-12 col-sm-12">
                <div class="inner-column">
                    <div class="image-box">
                        <figure class="image"><a href="{{ route('tradein') }}"><img src="{{ asset('boxcar/images/resource/pricing1-1.jpg') }}" alt="Thu cũ đổi mới"></a></figure>
                        <a href="https://www.youtube.com/watch?v=AC1cREPIw_o&amp;autoplay=1&amp;rel=0&amp;controls=0&amp;showinfo=0" class="play-now" data-fancybox="gallery" data-caption=""><i class="fa fa-play" aria-hidden="true"></i><span class="ripple"></span></a>
                    </div>
                </div>
            </div>
            <div class="content-column col-lg-6 col-md-12 col-sm-12">
                <div class="inner-column">
                    <div class="boxcar-title wow fadeInUp">
                        <h2>Định giá xe công bằng, bán xe cho chúng tôi ngay hôm nay</h2>
                        <div class="text">Quy trình thu cũ đổi mới minh bạch, báo giá nhanh và hỗ trợ khách hàng chốt giao dịch thuận tiện tại showroom.</div>
                    </div>
                    <ul class="list-style-one wow fadeInUp" data-wow-delay="100ms">
                        <li><i class="fa-solid fa-check"></i>Định giá theo tình trạng xe thực tế và lịch sử sử dụng</li>
                        <li><i class="fa-solid fa-check"></i>Hỗ trợ thủ tục sang tên, giải chấp và hồ sơ vay</li>
                        <li><i class="fa-solid fa-check"></i>Có thể đổi trực tiếp sang xe mới hoặc xe đã qua sử dụng</li>
                    </ul>
                    <a href="{{ route('tradein') }}" class="read-more wow fadeInUp" data-wow-delay="200ms">Bắt đầu ngay<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <g clip-path="url(#clip0_home_pricing)">
                                <path d="M13.6106 0H5.05509C4.84013 0 4.66619 0.173943 4.66619 0.388901C4.66619 0.603859 4.84013 0.777802 5.05509 0.777802H12.6719L0.113453 13.3362C-0.0384687 13.4881 -0.0384687 13.7342 0.113453 13.8861C0.189396 13.962 0.288927 14 0.388422 14C0.487917 14 0.587411 13.962 0.663391 13.8861L13.2218 1.3277V8.94447C13.2218 9.15943 13.3957 9.33337 13.6107 9.33337C13.8256 9.33337 13.9996 9.15943 13.9996 8.94447V0.388901C13.9995 0.173943 13.8256 0 13.6106 0Z" fill="white"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_home_pricing">
                                    <rect width="14" height="14" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="boxcar-fun-fact-section">
    <div class="large-container">
        <div class="fact-counter">
            <div class="row">
                <div class="counter-block col-lg-3 col-md-3 col-sm-6 wow fadeInUp">
                    <div class="inner wow fadeInUp">
                        <div class="content">
                            <div class="widget-counter"><span class="count-text" data-speed="3000" data-stop="{{ $stats['available'] }}">0</span>+</div>
                            <h6 class="counter-title">XE ĐANG BÁN</h6>
                        </div>
                    </div>
                </div>
                <div class="counter-block col-lg-3 col-md-3 col-sm-6 wow fadeInUp" data-wow-delay="300ms">
                    <div class="inner wow fadeInUp" data-wow-delay="100ms">
                        <div class="content">
                            <div class="widget-counter"><span class="count-text" data-speed="3000" data-stop="{{ $stats['new'] }}">0</span>+</div>
                            <h6 class="counter-title">XE MỚI</h6>
                        </div>
                    </div>
                </div>
                <div class="counter-block col-lg-3 col-md-3 col-sm-6 wow fadeInUp" data-wow-delay="600ms">
                    <div class="inner wow fadeInUp" data-wow-delay="200ms">
                        <div class="content">
                            <div class="widget-counter"><span class="count-text" data-speed="3000" data-stop="{{ $stats['used'] }}">0</span>+</div>
                            <h6 class="counter-title">XE USED/CPO</h6>
                        </div>
                    </div>
                </div>
                <div class="counter-block col-lg-3 col-md-3 col-sm-6 wow fadeInUp" data-wow-delay="900ms">
                    <div class="inner wow fadeInUp" data-wow-delay="300ms">
                        <div class="content">
                            <div class="widget-counter"><span class="count-text" data-speed="3000" data-stop="{{ $stats['makes'] }}">0</span>+</div>
                            <h6 class="counter-title">HÃNG XE</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="why-choose-us-section">
    <div class="boxcar-container">
        <div class="boxcar-title wow fadeInUp">
            <h2 class="title">Vì sao nên chọn chúng tôi?</h2>
        </div>
        <div class="row">
            <div class="choose-us-block col-lg-3 col-md-6 col-sm-12">
                <div class="inner-box wow fadeInUp">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="51" height="60" viewBox="0 0 51 60" fill="none">
                        <g clip-path="url(#clip0_24_628)">
                            <path d="M22.9688 52.9676C22.9688 52.732 22.827 52.5195 22.6096 52.4289C20.0682 51.3695 18.2812 48.8627 18.2812 45.9375V23.4375C18.2812 20.5123 20.0682 18.0054 22.6096 16.9461C22.827 16.8555 22.9688 16.6429 22.9688 16.4074V16.4062H18.2812C14.398 16.4062 11.25 19.5543 11.25 23.4375V45.9375C11.25 49.8207 14.398 52.9688 18.2812 52.9688H22.9688V52.9676Z" fill="#EEF1FB"></path>
                            <path d="M23.3708 41.3167L36.6292 28.0583" stroke="#FF5CF4" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M30 21.0938L44.0625 2.34375" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M15.9375 2.34375L25.3895 12.9483" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M48.75 30V23.4375C48.75 19.5543 45.602 16.4062 41.7188 16.4062H38.0747C36.4508 13.6159 33.4612 11.7188 30 11.7188C26.5388 11.7188 23.5493 13.6159 21.9253 16.4062H18.2812C14.398 16.4062 11.25 19.5543 11.25 23.4375V45.9375C11.25 49.8207 14.398 52.9688 18.2812 52.9688H21.9253C23.5492 55.7591 26.5388 57.6562 30 57.6562C33.4612 57.6562 36.4507 55.7591 38.0747 52.9688H41.7188C45.602 52.9688 48.75 49.8207 48.75 45.9375V39.375" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        </g>
                        <defs>
                            <clipPath id="clip0_24_628">
                                <rect width="51" height="60" fill="white"></rect>
                            </clipPath>
                        </defs>
                    </svg></div>
                    <div class="content-box">
                        <h6 class="title">Gói tài chính linh hoạt</h6>
                        <div class="text">Đội ngũ tư vấn tài chính hỗ trợ nhanh các phương án trả góp phù hợp ngân sách của bạn.</div>
                    </div>
                </div>
            </div>
            <div class="choose-us-block col-lg-3 col-md-6 col-sm-12">
                <div class="inner-box wow fadeInUp" data-wow-delay="100ms">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                        <path d="M30 2.34375V7.03125" stroke="#FF5CF4" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M48.75 2.34375L44.0625 7.03125" stroke="#FF5CF4" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M15.4738 36.6607C14.3072 35.4056 13.5938 33.7236 13.5938 31.875C13.5938 30.7464 13.8596 29.68 14.3323 28.7347L19.0198 19.3597C20.1732 17.0529 22.5579 15.4688 25.3125 15.4688H18.2812C15.5266 15.4688 13.142 17.0529 11.9885 19.3597L7.30102 28.7347C6.8284 29.68 6.5625 30.7464 6.5625 31.875C6.5625 33.7236 7.27594 35.4056 8.44254 36.6607L26.5658 56.1592C27.4218 57.0802 28.6436 57.6562 30 57.6562C31.3564 57.6562 32.5782 57.0802 33.4342 56.1593L33.5156 56.0716L15.4738 36.6607Z" fill="#EEF1FB"></path>
                        <path d="M48.0115 19.3597L52.699 28.7347C53.1716 29.6798 53.4375 30.7464 53.4375 31.875C53.4375 33.7236 52.7241 35.4057 51.5575 36.6608L33.4342 56.1593C32.5782 57.0802 31.3564 57.6562 30 57.6562C28.6436 57.6562 27.4218 57.0802 26.5658 56.1593L8.44254 36.6608C7.27594 35.4057 6.5625 33.7236 6.5625 31.875C6.5625 30.7464 6.8284 29.6798 7.30102 28.7347L11.9885 19.3597C13.142 17.0528 15.5266 15.4688 18.2812 15.4688H41.7188C44.4734 15.4688 46.858 17.0528 48.0115 19.3597Z" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M11.25 2.34375L15.9375 7.03125" stroke="#FF5CF4" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M17.3849 29.5312H42.6151" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M25.3125 24.8438L30 29.5312L34.6875 24.8438" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M30 43.5938V29.7306" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg></div>
                    <div class="content-box">
                        <h6 class="title">Showroom đáng tin cậy</h6>
                        <div class="text">Thông tin xe, hồ sơ pháp lý và lịch sử tình trạng xe được trình bày rõ ràng, dễ kiểm chứng.</div>
                    </div>
                </div>
            </div>
            <div class="choose-us-block col-lg-3 col-md-6 col-sm-12">
                <div class="inner-box wow fadeInUp" data-wow-delay="200ms">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                        <g clip-path="url(#clip0_24_681)">
                            <path d="M8.75576 36.7478L35.3054 10.198C37.136 8.36741 40.104 8.36741 41.9346 10.198L36.8955 5.15894C35.0649 3.32837 32.097 3.32837 30.2664 5.15894L3.71671 31.7087C1.88613 33.5393 1.88613 36.5073 3.71671 38.3378L8.75576 43.3768C6.92518 41.5462 6.92518 38.5783 8.75576 36.7478Z" fill="#EEF1FB"></path>
                            <path d="M50.1537 18.4171C51.9843 20.2477 51.9843 23.2157 50.1537 25.0463L23.6039 51.5959C21.7734 53.4265 18.8054 53.4265 16.9748 51.5959L3.71671 38.3378C1.88613 36.5072 1.88613 33.5392 3.71671 31.7086L30.2664 5.15894C32.097 3.32836 35.0649 3.32836 36.8955 5.15894L43.5247 11.7881L52.9689 2.34387" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M18.9633 31.0458C18.7631 32.4554 19.2051 33.9388 20.2894 35.0231C22.12 36.8537 25.088 36.8537 26.9186 35.0231C28.7492 33.1926 28.7492 30.2246 26.9186 28.394C25.088 26.5634 25.088 23.5954 26.9186 21.7648C28.7492 19.9342 31.7172 19.9342 33.5478 21.7648C34.6321 22.8491 35.0741 24.3325 34.8739 25.7421" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M16.9749 38.3378L20.2894 35.0232" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M33.5476 21.765L36.8621 18.4504" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M43.5938 57.6562L57.6563 43.5937" stroke="#FF5CF4" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        </g>
                        <defs>
                            <clipPath id="clip0_24_681">
                                <rect width="60" height="60" fill="white"></rect>
                            </clipPath>
                        </defs>
                    </svg></div>
                    <div class="content-box">
                        <h6 class="title">Giá bán minh bạch</h6>
                        <div class="text">Giá xe, ưu đãi và chi phí lăn bánh được tư vấn rõ ràng để bạn dễ so sánh và ra quyết định.</div>
                    </div>
                </div>
            </div>
            <div class="choose-us-block col-lg-3 col-md-6 col-sm-12">
                <div class="inner-box wow fadeInUp" data-wow-delay="300ms">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                        <path d="M23.5465 4.45312C19.8452 4.45312 16.4904 6.63082 14.9836 10.0114L8.88656 23.6906C5.23148 23.9418 2.34375 26.9843 2.34375 30.7031V36.0938C2.34375 39.3298 4.96711 41.9531 8.20312 41.9531H9.80918C9.81785 41.5022 9.82934 41.0514 9.84375 40.6005C9.4623 39.823 9.24727 38.949 9.24727 38.0245L9.14062 33.8672C9.14062 30.7927 9.76617 29.6094 12.0483 29.1497C13.1331 28.9311 14.0413 28.192 14.4858 27.1786L22.0148 10.0114C23.5215 6.63082 26.8764 4.45312 30.5777 4.45312H23.5465Z" fill="#EEF1FB"></path>
                        <path d="M8.20312 41.9531C4.96711 41.9531 2.34375 39.3298 2.34375 36.0938V30.7031C2.34375 26.9843 5.23148 23.9418 8.88656 23.6906L14.9836 10.0114C16.4903 6.63082 19.8451 4.45312 23.5465 4.45312H34.2217C37.7441 4.45312 40.9692 6.4275 42.5711 9.56461L45.5859 15.4688M57.6562 30.7031C57.6562 26.8199 54.5082 23.6719 50.625 23.6719H18.6328M28.2422 15.4688V4.57031M32.4609 41.9531H27.1873M20.742 37.2656C18.1532 37.2656 16.0545 39.3643 16.0545 41.9531C16.0545 44.5419 18.1532 46.6406 20.742 46.6406C23.3307 46.6406 25.4295 44.5419 25.4295 41.9531C25.4295 39.3643 23.3309 37.2656 20.742 37.2656Z" stroke="#405FF2" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M57.6562 41.6016C57.6562 46.0997 54.0098 49.8047 49.5117 49.8047C45.0136 49.8047 41.3672 46.1583 41.3672 41.6602C41.3672 37.162 45.0722 33.5156 49.5703 33.5156M43.5352 48.1055L36.0938 55.5469" stroke="#FF5CF3" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg></div>
                    <div class="content-box">
                        <h6 class="title">Hậu mãi và dịch vụ kỹ thuật</h6>
                        <div class="text">Hỗ trợ kiểm tra xe, lịch bảo dưỡng và tư vấn sau mua để bạn yên tâm sử dụng lâu dài.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cars-section-two">
    <div class="boxcar-container">
        <div class="boxcar-title light wow fadeInUp">
            <h2>Hãng xe nổi bật</h2>
            <a href="{{ route('inventory.index') }}" class="btn-title">Xem tất cả<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <g clip-path="url(#clip0_home_popular_makes_title)">
                        <path d="M13.6109 0H5.05533C4.84037 0 4.66643 0.173943 4.66643 0.388901C4.66643 0.603859 4.84037 0.777802 5.05533 0.777802H12.6721L0.113697 13.3362C-0.0382246 13.4881 -0.0382246 13.7342 0.113697 13.8861C0.18964 13.962 0.289171 14 0.388666 14C0.488161 14 0.587656 13.962 0.663635 13.8861L13.222 1.3277V8.94447C13.222 9.15943 13.3959 9.33337 13.6109 9.33337C13.8259 9.33337 13.9998 9.15943 13.9998 8.94447V0.388901C13.9998 0.173943 13.8258 0 13.6109 0Z" fill="white"></path>
                    </g>
                    <defs>
                        <clipPath id="clip0_home_popular_makes_title">
                            <rect width="14" height="14" fill="white"></rect>
                        </clipPath>
                    </defs>
                </svg>
            </a>
        </div>
        @if ($popularMakes->isNotEmpty())
            <nav class="wow fadeInUp" data-wow-delay="100ms">
                <div class="nav nav-tabs" id="popular-makes-tabs" role="tablist">
                    @foreach ($popularMakes as $make)
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="popular-make-tab-{{ $loop->index }}" data-bs-toggle="tab" data-bs-target="#popular-make-pane-{{ $loop->index }}" type="button" role="tab" aria-controls="popular-make-pane-{{ $loop->index }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $make->name }}</button>
                    @endforeach
                </div>
            </nav>
        @endif
    </div>
    <div class="tab-content wow fadeInUp" data-wow-delay="200ms" id="popular-makes-tabs-content">
        @forelse ($popularMakes as $make)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="popular-make-pane-{{ $loop->index }}" role="tabpanel" aria-labelledby="popular-make-tab-{{ $loop->index }}">
                <div class="row car-slider slider-layout-1" data-preview="2.3">
                    @forelse ($make->cars as $car)
                        @include('client.partials.home-popular-make-car-card', ['car' => $car])
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light">Chưa có xe để hiển thị cho hãng {{ $make->name }}.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="boxcar-container">
                <div class="alert alert-light">Chưa có hãng xe nổi bật để hiển thị.</div>
            </div>
        @endforelse
    </div>
</section>
@endsection
