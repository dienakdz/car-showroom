@extends('client.layouts.page')

@section('title', $trim->make_name . ' ' . $trim->model_name . ' ' . $trim->name)

@section('content')
<section class="about-inner-one layout-radius" style="padding-bottom: 10px;">
    <div class="upper-box">
        <div class="boxcar-container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="boxcar-title">
                        <ul class="breadcrumb">
                            <li><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li><a href="{{ route('inventory.index') }}">Kho xe</a></li>
                            <li><span>{{ $trim->slug }}</span></li>
                        </ul>
                        <h2>{{ $trim->make_name }} {{ $trim->model_name }} - {{ $trim->name }}</h2>
                        <div class="text">Thông tin phiên bản để khách tham khảo trước khi chọn chiếc xe cụ thể.</div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="content-box">
                        <div class="text">Năm áp dụng: {{ $trim->year_from ?? 'N/A' }}{{ $trim->year_to ? ' - ' . $trim->year_to : ' - nay' }}</div>
                        <div class="text">MSRP tham khảo: {{ $trim->msrp ? number_format((float) $trim->msrp, 0, ',', '.') . ' VND' : 'Liên hệ' }}</div>
                        <div class="text">{{ $trim->description ?: 'Chưa có mô tả chi tiết cho phiên bản này.' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="why-choose-us-section" style="padding-top: 10px;">
    <div class="boxcar-container">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="overview-sec" style="padding: 24px; border: 1px solid #e9e9e9; border-radius: 16px; margin-bottom: 24px;">
                    <h4 class="title">Trang bị</h4>
                    @forelse ($features as $groupName => $groupFeatures)
                        <h6 style="margin-top: 16px;">{{ $groupName }}</h6>
                        <ul class="feature-list">
                            @foreach ($groupFeatures as $feature)
                                <li><i class="fa-solid fa-check"></i>{{ $feature->name }}</li>
                            @endforeach
                        </ul>
                    @empty
                        <div class="alert alert-light">Chưa có dữ liệu trang bị.</div>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="overview-sec" style="padding: 24px; border: 1px solid #e9e9e9; border-radius: 16px; margin-bottom: 24px;">
                    <h4 class="title">Thông số kỹ thuật</h4>
                    <ul class="spects-list">
                        @forelse ($attributes as $attribute)
                            <li><span>{{ $attribute->label }}</span>{{ $attribute->display_value ?? 'N/A' }}</li>
                        @empty
                            <li><span>N/A</span>Chưa có thông số</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cars-section-four v1 layout-radius" style="padding-top: 20px;">
    <div class="boxcar-container">
        <div class="boxcar-title-three">
            <h2>Xe đang có sẵn cho phiên bản này</h2>
            <div class="text">Chỉ hiển thị xe đang ở trạng thái available.</div>
        </div>
        <div class="row">
            @forelse ($availableCars as $car)
                @include('client.partials.car-card', ['car' => $car])
            @empty
                <div class="col-12"><div class="alert alert-light">Hiện chưa có xe nào sẵn cho phiên bản này.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="boxcar-testimonial-section home1" style="padding-top: 30px;">
    <div class="boxcar-container">
        <div class="boxcar-title wow fadeInUp">
            <h2>Đánh giá khách hàng</h2>
        </div>
        <div class="row">
            @forelse ($reviews as $review)
                <div class="testimonial-block-two col-lg-6 col-md-6 col-sm-12">
                    <div class="inner-box">
                        <div class="content-box">
                            <ul class="rating-list">
                                @for ($i = 1; $i <= 5; $i++)
                                    <li><i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i></li>
                                @endfor
                                <span>{{ $review->rating }}/5</span>
                            </ul>
                            <h6 class="title">{{ $review->user_name }}</h6>
                            <span>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</span>
                            <div class="text">{{ $review->comment }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-light">Chưa có đánh giá đã duyệt.</div></div>
            @endforelse
        </div>
    </div>
</section>
@endsection
