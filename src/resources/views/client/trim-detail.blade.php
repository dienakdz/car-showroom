@extends('client.layouts.page')

@section('title', $trim->make_name . ' ' . $trim->model_name . ' ' . $trim->name)

@section('content')
@php
    $defaultName = old('name', auth()->user()->name ?? '');
    $defaultPhone = old('phone', auth()->user()->phone ?? '');
    $defaultEmail = old('email', auth()->user()->email ?? '');
@endphp

<section class="about-inner-one layout-radius" style="padding-bottom: 10px;">
    <div class="upper-box">
        <div class="boxcar-container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="boxcar-title">
                        <ul class="breadcrumb">
                            <li><a href="{{ route('home') }}">Trang chu</a></li>
                            <li><a href="{{ route('inventory.index') }}">Kho xe</a></li>
                            <li><span>{{ $trim->slug }}</span></li>
                        </ul>
                        <h2>{{ $trim->make_name }} {{ $trim->model_name }} - {{ $trim->name }}</h2>
                        <div class="text">Trang phien ban tap trung vao thong so chung, trang bi, review va cac xe dang san co thuoc trim nay.</div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="content-box">
                        <div class="text">Nam ap dung: {{ $trim->year_from ?? 'N/A' }}{{ $trim->year_to ? ' - ' . $trim->year_to : ' - nay' }}</div>
                        <div class="text">MSRP tham khao: {{ $trim->msrp ? number_format((float) $trim->msrp, 0, ',', '.') . ' VND' : 'Lien he' }}</div>
                        <div class="text">{{ $trim->description ?: 'Chua co mo ta chi tiet cho phien ban nay.' }}</div>
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
                    <h4 class="title">Trang bi</h4>
                    @forelse ($features as $groupName => $groupFeatures)
                        <h6 style="margin-top: 16px;">{{ $groupName }}</h6>
                        <ul class="feature-list">
                            @foreach ($groupFeatures as $feature)
                                <li><i class="fa-solid fa-check"></i>{{ $feature->name }}</li>
                            @endforeach
                        </ul>
                    @empty
                        <div class="alert alert-light">Chua co du lieu trang bi.</div>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="overview-sec" style="padding: 24px; border: 1px solid #e9e9e9; border-radius: 16px; margin-bottom: 24px;">
                    <h4 class="title">Thong so ky thuat</h4>
                    <ul class="spects-list">
                        @forelse ($attributes as $attribute)
                            <li><span>{{ $attribute->label }}</span>{{ $attribute->display_value ?? 'N/A' }}</li>
                        @empty
                            <li><span>N/A</span>Chua co thong so</li>
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
            <h2>Xe dang san co cho phien ban nay</h2>
            <div class="text">
                Chi hien thi xe status = available va da publish.
                Hien co {{ $availableCarsCount }} xe phu hop
                @if ($availableCarsCount > $availableCars->count())
                    va dang hien preview {{ $availableCars->count() }} xe moi nhat.
                @else
                    trong kho.
                @endif
            </div>
        </div>
        <div class="row">
            @forelse ($availableCars as $car)
                @include('client.partials.car-card', ['car' => $car])
            @empty
                <div class="col-12">
                    <div class="alert alert-light">Hien chua co xe nao san cho phien ban nay.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="contact-us-section layout-radius" style="padding-top: 0;">
    <div class="boxcar-container">
        <div class="calculater-sec">
            <div class="right-box">
                <div class="row">
                    <div class="col-lg-8 content-column">
                        <div class="inner-column">
                            <div class="boxcar-title">
                                <h2>Lien he tu van mau xe</h2>
                                <p>Lead tu trang trim se duoc tao voi `source=trim_page`, phu hop khi khach dang tim hieu phien ban va chua chon chiec xe cu the.</p>
                            </div>
                            <form class="row" method="POST" action="{{ route('lead.store') }}">
                                @csrf
                                <input type="hidden" name="source" value="trim_page">
                                <input type="hidden" name="trim_id" value="{{ $trim->id }}">
                                <div class="col-lg-6">
                                    <div class="form_boxes">
                                        <label>Ho va ten</label>
                                        <input type="text" name="name" value="{{ $defaultName }}" placeholder="Nguyen Van A" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form_boxes">
                                        <label>So dien thoai</label>
                                        <input type="text" name="phone" value="{{ $defaultPhone }}" placeholder="09xxxxxxxx" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form_boxes">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ $defaultEmail }}" placeholder="example@email.com">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form_boxes">
                                        <label>Phien ban quan tam</label>
                                        <input type="text" value="{{ $trim->make_name }} {{ $trim->model_name }} {{ $trim->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form_boxes v2">
                                        <label>Noi dung</label>
                                        <textarea name="message" placeholder="Toi can tu van them ve xe san co, uu dai va gia lan banh">{{ old('message') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-submit">
                                        <button type="submit" class="theme-btn">Gui yeu cau tu van <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="boxcar-testimonial-section home1" style="padding-top: 30px;">
    <div class="boxcar-container">
        <div class="boxcar-title wow fadeInUp">
            <h2>Danh gia khach hang</h2>
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
                <div class="col-12">
                    <div class="alert alert-light">Chua co danh gia da duyet.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="contact-us-section layout-radius" id="review-form" style="padding-top: 0;">
    <div class="boxcar-container">
        <div class="calculater-sec">
            <div class="right-box">
                <div class="row">
                    <div class="col-lg-8 content-column">
                        <div class="inner-column">
                            <div class="boxcar-title">
                                <h2>Gui danh gia cua ban</h2>
                                <p>Chi khach da mua xe thuoc phien ban nay moi co the gui danh gia. Danh gia moi se o trang thai cho duyet truoc khi hien thi cong khai.</p>
                            </div>

                            @guest
                                <div class="alert alert-light">
                                    Ban can <a href="{{ route('login') }}">dang nhap</a> de he thong kiem tra lich su mua xe truoc khi gui danh gia.
                                </div>
                            @else
                                @php($reviewStatusLabel = match ($userReview->status ?? null) {
                                    'approved' => 'Da duyet',
                                    'hidden' => 'Da an',
                                    'pending' => 'Cho duyet',
                                    default => null,
                                })

                                @if ($canSubmitReview)
                                    <form class="row" method="POST" action="{{ route('trim.reviews.store', ['trimSlug' => $trim->slug]) }}">
                                        @csrf
                                        <div class="col-lg-12">
                                            <div class="form_boxes">
                                                <label>Diem danh gia</label>
                                                <select name="rating" style="width: 100%; height: 60px; border-radius: 16px; border: 1px solid #d9dde3; padding: 0 20px; color: #050b20;">
                                                    <option value="">Chon so sao</option>
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <option value="{{ $i }}" {{ (string) old('rating') === (string) $i ? 'selected' : '' }}>{{ $i }}/5 sao</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form_boxes v2">
                                                <label>Noi dung danh gia</label>
                                                <textarea name="comment" placeholder="Chia se trai nghiem cua ban sau khi mua xe" style="width: 100%;">{{ old('comment') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-submit">
                                            <button type="submit" class="theme-btn">Gui danh gia <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                        </div>
                                    </form>
                                @elseif ($userReview)
                                    <div class="alert alert-light">
                                        Ban da gui danh gia cho phien ban nay.
                                        @if ($reviewStatusLabel)
                                            Trang thai: {{ $reviewStatusLabel }}.
                                        @endif
                                    </div>
                                    <div class="overview-sec" style="padding: 24px; border: 1px solid #e9e9e9; border-radius: 16px;">
                                        <ul class="rating-list">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <li><i class="fa {{ $i <= $userReview->rating ? 'fa-star' : 'fa-star-o' }}"></i></li>
                                            @endfor
                                            <span>{{ $userReview->rating }}/5</span>
                                        </ul>
                                        <div class="text" style="margin-top: 12px;">{{ $userReview->comment }}</div>
                                    </div>
                                @elseif (! $userHasPurchasedTrim)
                                    <div class="alert alert-light">
                                        Chi khach da mua xe thuoc phien ban nay moi co the danh gia.
                                    </div>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
