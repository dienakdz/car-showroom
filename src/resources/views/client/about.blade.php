@extends('client.layouts.page')

@section('title', 'Về chúng tôi')

@section('content')
<section class="about-inner-one layout-radius">
    <div class="upper-box">
        <div class="boxcar-container">
            <div class="row wow fadeInUp">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="boxcar-title">
                        <ul class="breadcrumb">
                            <li><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li><span>Về chúng tôi</span></li>
                        </ul>
                        <h2>{{ $showroom->name ?? 'Car Showroom' }}</h2>
                        <div class="text">Website showroom tập trung trải nghiệm chọn xe, tạo lead và đặt lịch xem xe.</div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="content-box">
                        <div class="text">Địa chỉ: {{ $showroom->address ?? 'TP. Hồ Chí Minh' }}</div>
                        <div class="text">Điện thoại: {{ $showroom->phone ?? '0900 000 000' }} • Email: {{ $showroom->email ?? 'hello@showroom.test' }}</div>
                        <div class="text">{{ $showroom->description ?? 'Showroom 1 seller, tập trung xe mới và xe đã qua sử dụng được chọn lọc.' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="galler-section">
        <div class="boxcar-container">
            <div class="row">
                <div class="exp-block col-md-2 col-sm-12">
                    <div class="inner-box">
                        <div class="exp-box">
                            <h2 class="title">{{ number_format($stats['cars_for_sale']) }}</h2>
                            <div class="text">Xe đang available</div>
                        </div>
                        <div class="image-box">
                            <figure class="image"><img src="{{ asset('boxcar/images/resource/about-inner1-1.jpg') }}" alt="about"></figure>
                        </div>
                    </div>
                </div>
                <div class="image-block style-center col-md-5 col-sm-12">
                    <div class="image-box">
                        <figure class="image"><img src="{{ asset('boxcar/images/resource/about-inner1-2.jpg') }}" alt="about"></figure>
                    </div>
                </div>
                <div class="image-block col-md-5 col-sm-12">
                    <div class="image-box two">
                        <figure class="image"><img src="{{ asset('boxcar/images/resource/about-inner1-3.jpg') }}" alt="about"></figure>
                    </div>
                    <div class="row box-double-img">
                        <div class="image-block col-lg-5 col-5">
                            <div class="image-box"><figure class="image"><img src="{{ asset('boxcar/images/resource/about-inner1-4.jpg') }}" alt="about"></figure></div>
                        </div>
                        <div class="image-block col-lg-7 col-7">
                            <div class="image-box"><figure class="image"><img src="{{ asset('boxcar/images/resource/about-inner1-5.jpg') }}" alt="about"></figure></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="boxcar-fun-fact-section">
    <div class="large-container">
        <div class="fact-counter">
            <div class="row">
                <div class="counter-block col-lg-3 col-md-3 col-sm-6">
                    <div class="inner"><div class="content"><div class="widget-counter">{{ number_format($stats['cars_for_sale']) }}</div><h6 class="counter-title">XE ĐANG BÁN</h6></div></div>
                </div>
                <div class="counter-block col-lg-3 col-md-3 col-sm-6">
                    <div class="inner"><div class="content"><div class="widget-counter">{{ number_format($stats['trims']) }}</div><h6 class="counter-title">PHIÊN BẢN</h6></div></div>
                </div>
                <div class="counter-block col-lg-3 col-md-3 col-sm-6">
                    <div class="inner"><div class="content"><div class="widget-counter">{{ number_format($stats['reviews']) }}</div><h6 class="counter-title">ĐÁNH GIÁ</h6></div></div>
                </div>
                <div class="counter-block col-lg-3 col-md-3 col-sm-6">
                    <div class="inner"><div class="content"><div class="widget-counter">{{ number_format($stats['leads']) }}</div><h6 class="counter-title">LEAD ĐÃ TẠO</h6></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="why-choose-us-section">
    <div class="boxcar-container">
        <div class="boxcar-title wow fadeInUp">
            <h2 class="title">Lý do chọn showroom</h2>
        </div>
        <div class="row">
            <div class="choose-us-block col-lg-4 col-md-6 col-sm-12">
                <div class="inner-box wow fadeInUp">
                    <div class="content-box">
                        <h6 class="title">Thông tin minh bạch</h6>
                        <div class="text">Mỗi chiếc xe đều có mã stock, tình trạng, lịch sử giá và thông số rõ ràng.</div>
                    </div>
                </div>
            </div>
            <div class="choose-us-block col-lg-4 col-md-6 col-sm-12">
                <div class="inner-box wow fadeInUp" data-wow-delay="100ms">
                    <div class="content-box">
                        <h6 class="title">Quy trình CRM rõ ràng</h6>
                        <div class="text">Lead, lịch hẹn và trạng thái xử lý được theo dõi tập trung để không bỏ sót khách.</div>
                    </div>
                </div>
            </div>
            <div class="choose-us-block col-lg-4 col-md-6 col-sm-12">
                <div class="inner-box wow fadeInUp" data-wow-delay="200ms">
                    <div class="content-box">
                        <h6 class="title">Hỗ trợ trước và sau mua</h6>
                        <div class="text">Tư vấn tài chính, thu cũ đổi mới và hỗ trợ đặt lịch xem xe linh hoạt.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
