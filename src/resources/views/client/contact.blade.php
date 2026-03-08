@extends('client.layouts.page')

@section('title', $sourceTitle)

@section('content')
<section class="contact-us-section layout-radius">
    <div class="boxcar-container">
        <div class="boxcar-title-three wow fadeInUp">
            <ul class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><span>{{ $sourceTitle }}</span></li>
            </ul>
            <h2>{{ $sourceTitle }}</h2>
        </div>

        <div class="map-sec">
            <div class="goole-iframe">
                @php($mapAddress = urlencode($showroom->address ?? 'Ho Chi Minh City'))
                <iframe src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=vi&amp;q={{ $mapAddress }}&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
            </div>
        </div>

        <div class="calculater-sec">
            <div class="right-box">
                <div class="row">
                    <div class="col-lg-6 content-column">
                        <div class="inner-column">
                            <div class="boxcar-title">
                                <h2>Gửi thông tin tư vấn</h2>
                                <p>Form này tạo lead trực tiếp để đội sale xử lý theo quy trình CRM.</p>
                            </div>

                            <div style="margin-bottom: 12px;">
                                <a class="theme-btn" href="{{ route('contact') }}" style="margin-right: 8px; {{ $source === 'contact' ? '' : 'opacity:0.7;' }}">Liên hệ</a>
                                <a class="theme-btn" href="{{ route('finance') }}" style="margin-right: 8px; {{ $source === 'finance' ? '' : 'opacity:0.7;' }}">Tài chính</a>
                                <a class="theme-btn" href="{{ route('tradein') }}" style="{{ $source === 'trade_in' ? '' : 'opacity:0.7;' }}">Thu cũ đổi mới</a>
                            </div>

                            <form class="row" method="POST" action="{{ route('lead.store') }}">
                                @csrf
                                <input type="hidden" name="source" value="{{ $source }}">
                                <div class="col-lg-6">
                                    <div class="form_boxes">
                                        <label>Họ và tên</label>
                                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nguyễn Văn A">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form_boxes">
                                        <label>Số điện thoại</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxx">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form_boxes">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" placeholder="name@email.com">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form_boxes">
                                        <label>Chọn xe đang quan tâm (tuỳ chọn)</label>
                                        @include('client.partials.form.custom-dropdown', [
                                            'name' => 'car_unit_id',
                                            'options' => $availableCars,
                                            'selectedValue' => old('car_unit_id', ''),
                                            'valueField' => 'id',
                                            'labelField' => 'label',
                                            'emptyLabel' => 'Không chọn',
                                        ])
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form_boxes">
                                        <label>Hoặc chọn phiên bản (tuỳ chọn)</label>
                                        @include('client.partials.form.custom-dropdown', [
                                            'name' => 'trim_id',
                                            'options' => $trims,
                                            'selectedValue' => old('trim_id', ''),
                                            'valueField' => 'id',
                                            'labelField' => 'label',
                                            'emptyLabel' => 'Không chọn',
                                        ])
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form_boxes v2">
                                        <label>Nội dung</label>
                                        <textarea name="message" placeholder="Mô tả nhu cầu của bạn" style="color:#000; width: 100%;">{{ old('message') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-submit">
                                    <button type="submit" class="theme-btn">Gửi lead<img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="contact-column col-lg-6 col-md-12 col-sm-12">
                        <div class="inner-column">
                            <div class="boxcar-title">
                                <h6 class="title">Thông tin showroom</h6>
                                <div class="text">Liên hệ trực tiếp hoặc để lại lead, đội sale sẽ gọi lại cho bạn.</div>
                            </div>
                            <div class="content-box">
                                <h6 class="title">Địa chỉ</h6>
                                <div class="text">{{ $showroom->address ?? 'TP. Hồ Chí Minh' }}</div>
                            </div>
                            <div class="content-box">
                                <h6 class="title">Email</h6>
                                <div class="text">{{ $showroom->email ?? 'hello@showroom.test' }}</div>
                            </div>
                            <div class="content-box">
                                <h6 class="title">Điện thoại</h6>
                                <div class="text">{{ $showroom->phone ?? '0900 000 000' }}</div>
                            </div>
                            <div class="social-icons">
                                <h6 class="title">Mục tiêu quy trình</h6>
                                <ul class="user-links style-two">
                                    <li>Tạo lead từ mọi form public</li>
                                    <li>Gán nhân viên xử lý ban đầu</li>
                                    <li>Theo dõi từ liên hệ tới đặt lịch</li>
                                    <li>Hỗ trợ chốt bán offline</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
