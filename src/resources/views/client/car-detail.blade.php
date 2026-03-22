@extends('client.layouts.page')

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

@section('title', $car->make_name . ' ' . $car->model_name . ' ' . $car->trim_name)

@section('content')
@php
    $imageMedia = $media->filter(function (object $item): bool {
        return ($item->type ?? 'image') === 'image';
    })->values();

    if ($imageMedia->isEmpty()) {
        $imageMedia = collect([(object) ['url' => $car->image_url]]);
    }

    $videoMedia = $media->filter(function (object $item): bool {
        return ($item->type ?? null) === 'video';
    })->values();

    $description = trim((string) ($car->trim_description ?? ''));
    $descriptionLead = $description !== '' ? \Illuminate\Support\Str::limit($description, 220, '...') : 'Showroom dang cap nhat mo ta chi tiet cho phien ban nay.';
    $descriptionTail = $description !== '' && \Illuminate\Support\Str::length($description) > 220
        ? \Illuminate\Support\Str::substr($description, 220)
        : 'Lien he showroom de nhan them thong tin, bao gia moi nhat va lich xem xe truc tiep.';

    $attributeColumns = $attributes->isNotEmpty()
        ? $attributes->chunk((int) ceil($attributes->count() / 2))
        : collect();

    $mapsQuery = rawurlencode((string) ($navShowroom->address ?? 'Ho Chi Minh City'));
    $mapsDirectionsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $mapsQuery;
    $mapsEmbedUrl = 'https://maps.google.com/maps?width=100%25&height=600&hl=vi&q=' . $mapsQuery . '&t=&z=14&ie=UTF8&iwloc=B&output=embed';
    $shareUrl = route('car.show', $car->stock_code);
    $shareFacebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);
    $phoneDigits = preg_replace('/\D+/', '', (string) ($navShowroom->phone ?? ''));
    $whatsappDigits = $phoneDigits;

    if ($whatsappDigits !== '' && str_starts_with($whatsappDigits, '0')) {
        $whatsappDigits = '84' . substr($whatsappDigits, 1);
    }

    $reviewAverage = $reviewSummary && $reviewSummary->avg_rating !== null
        ? number_format((float) $reviewSummary->avg_rating, 1)
        : null;

    $reviewMetrics = collect([
        ['label' => 'Tong diem', 'value' => $reviewAverage ? $reviewAverage . '/5' : 'Chua co', 'note' => $reviewAverage ? 'Danh gia tong quan' : 'Dang cap nhat'],
        ['label' => 'Luot danh gia', 'value' => (string) ($reviewSummary->total ?? 0), 'note' => 'Da duyet'],
        ['label' => 'Tinh trang', 'value' => $car->condition_label, 'note' => 'Tinh trang xe'],
        ['label' => 'Trang thai', 'value' => strtoupper((string) $car->status), 'note' => 'Tinh trang giao dich'],
        ['label' => 'Kieu dang', 'value' => $car->body_type_name ?? 'Dang cap nhat', 'note' => 'Phan khuc'],
        ['label' => 'Nhien lieu', 'value' => $car->fuel_type_name ?? 'Dang cap nhat', 'note' => 'Loai dong co'],
    ]);

    $reviewMetricColumns = $reviewMetrics->chunk(3);
    $showroomName = $navShowroom->name ?? 'Showroom';
    $showroomAddress = $navShowroom->address ?? 'Lien he de nhan dia chi showroom';
    $showroomEmail = $navShowroom->email ?? null;
    $defaultName = old('name', auth()->user()->name ?? '');
    $defaultPhone = old('phone', auth()->user()->phone ?? '');
    $defaultEmail = old('email', auth()->user()->email ?? '');
@endphp

<section class="inventory-section pb-0 layout-radius">
    <div class="boxcar-container">
        <div class="boxcar-title-three">
            <ul class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chu</a></li>
                <li><a href="{{ route('inventory.index') }}">Kho xe</a></li>
                <li><span>{{ $car->stock_code }}</span></li>
            </ul>
            <h2>{{ $car->make_name }} {{ $car->model_name }}</h2>
            <div class="text">{{ $car->year }} {{ $car->trim_name }} | Stock: {{ $car->stock_code }} | VIN: {{ $car->vin ?? 'Dang cap nhat' }}</div>
            <ul class="spectes-list">
                <li><span><img src="{{ asset('boxcar/images/resource/spec1-1.svg') }}" alt="year">{{ $car->year }}</span></li>
                <li><span><img src="{{ asset('boxcar/images/resource/spec1-2.svg') }}" alt="mileage">{{ $car->mileage ? number_format((float) $car->mileage, 0, ',', '.') . ' km' : 'Odo thap' }}</span></li>
                <li><span><img src="{{ asset('boxcar/images/resource/spec1-3.svg') }}" alt="transmission">{{ $car->transmission_name ?? 'Dang cap nhat' }}</span></li>
                <li><span><img src="{{ asset('boxcar/images/resource/spec1-4.svg') }}" alt="fuel">{{ $car->fuel_type_name ?? 'Dang cap nhat' }}</span></li>
            </ul>
            <div class="content-box">
                <div class="btn-box v2">
                    <div class="share-btn">
                        <span>Chia se</span>
                        <a href="{{ $shareFacebookUrl }}" class="share" target="_blank" rel="noopener"><img src="{{ asset('boxcar/images/resource/share.svg') }}" alt="share"></a>
                    </div>
                    <div class="share-btn">
                        <span>Kho xe</span>
                        <a href="{{ route('inventory.index', ['make' => $car->make_slug]) }}" class="share"><img src="{{ asset('boxcar/images/resource/share1-1.svg') }}" alt="inventory"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="inspection-column v2 col-xl-8 col-lg-12 col-md-12 col-sm-12">
                <div class="inner-column">
                    <div class="gallery-sec">
                        <div class="image-column wrap-gallery-box">
                            <div class="inner-column inventry-slider-two">
                                @foreach ($imageMedia as $image)
                                    <div class="image-box">
                                        <figure class="image">
                                            <a href="{{ $image->url }}" data-fancybox="gallery"><img src="{{ $image->url }}" alt="{{ $car->make_name }} {{ $car->model_name }}"></a>
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                            <div class="content-box">
                                <ul class="video-list">
                                    @if ($videoMedia->isNotEmpty())
                                        <li><a href="{{ $videoMedia->first()->url }}" data-fancybox="gallery2"><img src="{{ asset('boxcar/images/resource/video1-1.svg') }}" alt="video">Video</a></li>
                                    @endif
                                    <li><a href="#dealer-booking"><img src="{{ asset('boxcar/images/resource/video1-2.svg') }}" alt="contact">Dat lich xem xe</a></li>
                                    <li><a href="{{ $imageMedia->first()->url }}" data-fancybox="gallery"><img src="{{ asset('boxcar/images/resource/video1-4.svg') }}" alt="photos">Tat ca hinh anh</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="overview-sec v2">
                        <h4 class="title">Tong quan xe</h4>
                        <div class="row">
                            <div class="content-column col-lg-6 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <ul class="list">
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-1.svg') }}" alt="body">Kieu dang</span>{{ $car->body_type_name ?? 'Dang cap nhat' }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-2.svg') }}" alt="mileage">So km</span>{{ $car->mileage ? number_format((float) $car->mileage, 0, ',', '.') . ' km' : 'Dang cap nhat' }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-3.svg') }}" alt="fuel">Nhien lieu</span>{{ $car->fuel_type_name ?? 'Dang cap nhat' }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-4.svg') }}" alt="year">Nam sx</span>{{ $car->year }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-5.svg') }}" alt="transmission">Hop so</span>{{ $car->transmission_name ?? 'Dang cap nhat' }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-6.svg') }}" alt="drive">Dan dong</span>{{ $car->drivetrain_name ?? 'Dang cap nhat' }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="content-column col-lg-6 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <ul class="list">
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-7.svg') }}" alt="condition">Tinh trang</span>{{ $car->condition_label }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-8.svg') }}" alt="exterior">Mau ngoai that</span>{{ $car->exterior_color_name ?? 'Dang cap nhat' }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-9.svg') }}" alt="interior">Mau noi that</span>{{ $car->interior_color_name ?? 'Dang cap nhat' }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-10.svg') }}" alt="stock">Ma xe</span>{{ $car->stock_code }}</li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-11.svg') }}" alt="trim">Phien ban</span><a href="{{ route('trim.show', $car->trim_slug) }}">{{ $car->trim_name }}</a></li>
                                        <li><span><img src="{{ asset('boxcar/images/resource/insep1-12.svg') }}" alt="vin">VIN</span>{{ $car->vin ?? 'Dang cap nhat' }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="description-sec">
                        <h4 class="title">Mo ta</h4>
                        <div class="text two">{{ $descriptionLead }}</div>
                        <div class="text">{{ $descriptionTail }}</div>
                        <ul class="des-list">
                            <li><a href="{{ $car->vin ? 'https://www.google.com/search?q=' . rawurlencode($car->vin) : '#dealer-contact' }}" @if ($car->vin) target="_blank" rel="noopener" @endif><span><img src="{{ asset('boxcar/images/resource/book1-1.svg') }}" alt="vin">Tra cuu VIN</span></a></li>
                            <li class="two"><a href="{{ route('trim.show', $car->trim_slug) }}"><span><img src="{{ asset('boxcar/images/resource/book1-2.svg') }}" alt="trim">Xem thong tin phien ban</span></a></li>
                        </ul>
                    </div>

                    <div class="features-sec">
                        <h4 class="title">Trang bi noi bat</h4>
                        <div class="row">
                            @forelse ($features as $groupName => $groupFeatures)
                                <div class="list-column col-lg-3 col-md-6 col-sm-12">
                                    <div class="inner-column">
                                        <h6 class="title">{{ $groupName }}</h6>
                                        <ul class="feature-list">
                                            @foreach ($groupFeatures as $feature)
                                                <li><i class="fa-solid fa-check"></i>{{ $feature->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-light">Chua co du lieu trang bi cho phien ban nay.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="faqs-section pt-0">
                        <div class="inner-container">
                            <h4 class="title">Thong so ky thuat</h4>
                            <div class="faq-column wow fadeInUp" data-wow-delay="400ms">
                                <div class="inner-column">
                                    <ul class="widget-accordion wow fadeInUp">
                                        <li class="accordion block active-block">
                                            <div class="acc-btn active">Thong so chinh<div class="icon fa fa-angle-down"></div></div>
                                            <div class="acc-content current">
                                                <div class="content">
                                                    <div class="row">
                                                        @forelse ($attributeColumns as $attributeColumn)
                                                            <div class="list-column col-lg-6 col-md-6 col-sm-12">
                                                                <div class="inner-column">
                                                                    <ul class="spects-list">
                                                                        @foreach ($attributeColumn as $attribute)
                                                                            <li><span>{{ $attribute->label }}</span>{{ $attribute->display_value ?? 'Dang cap nhat' }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-12">
                                                                <div class="alert alert-light">Chua co thong so ky thuat cho phien ban nay.</div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="location-box">
                        <h4 class="title">Vi tri showroom</h4>
                        <div class="text">
                            {{ $showroomAddress }}
                            <br>
                            Lien he truoc de dat lich xem xe va tu van chi tiet.
                        </div>
                        <a href="{{ $mapsDirectionsUrl }}" class="brand-btn" target="_blank" rel="noopener">
                            Chi duong
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14" fill="none">
                                <g clip-path="url(#clip0_detail_map_arrow)">
                                    <path d="M14.1111 0H5.55558C5.34062 0 5.16668 0.173943 5.16668 0.388901C5.16668 0.603859 5.34062 0.777802 5.55558 0.777802H13.1723L0.613941 13.3362C0.46202 13.4881 0.46202 13.7342 0.613941 13.8861C0.689884 13.962 0.789415 14 0.88891 14C0.988405 14 1.0879 13.962 1.16388 13.8861L13.7222 1.3277V8.94447C13.7222 9.15943 13.8962 9.33337 14.1111 9.33337C14.3261 9.33337 14.5 9.15943 14.5 8.94447V0.388901C14.5 0.173943 14.3261 0 14.1111 0Z" fill="#405FF2"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_detail_map_arrow">
                                        <rect width="14" height="14" fill="white" transform="translate(0.5)"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                        <div class="goole-iframe">
                            <iframe src="{{ $mapsEmbedUrl }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                    <div class="form-box" id="dealer-contact">
                        <h4 class="title">Dat lich xem xe va nhan bao gia</h4>

                        @if ($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        <form class="row" method="POST" action="{{ route('lead.store') }}">
                            @csrf
                            <input type="hidden" name="source" value="unit_detail">
                            <input type="hidden" name="car_unit_id" value="{{ $car->id }}">
                            <input type="hidden" name="trim_id" value="{{ $car->trim_id }}">

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
                                    <label>Nhu cau</label>
                                    <input type="text" value="Nhan bao gia / dat lich xem xe" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form_boxes v2">
                                    <label>Noi dung</label>
                                    <textarea name="message" placeholder="Toi muon xem xe vao cuoi tuan nay">{{ old('message') }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-submit">
                                    <button type="submit" class="theme-btn">Gui yeu cau<img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                </div>
                            </div>
                        </form>

                        <ul class="form-list">
                            <li><span>Muc gia tham khao</span>{{ $car->formatted_price }}</li>
                            <li><span>Tinh trang</span>{{ $car->condition_label }}</li>
                            <li><span>Showroom</span>{{ $showroomName }}</li>
                        </ul>
                    </div>

                    <div class="form-box" id="dealer-booking">
                        <h4 class="title">Dat lich xem xe / lai thu</h4>
                        <form class="row" method="POST" action="{{ route('appointments.store') }}">
                            @csrf
                            <input type="hidden" name="source" value="unit_detail">
                            <input type="hidden" name="car_unit_id" value="{{ $car->id }}">
                            <input type="hidden" name="trim_id" value="{{ $car->trim_id }}">

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
                                    <label>Thoi gian mong muon</label>
                                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" min="{{ now()->addHour()->format('Y-m-d\\TH:i') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form_boxes v2">
                                    <label>Ghi chu</label>
                                    <textarea name="message" placeholder="Toi muon xem xe va lai thu vao cuoi tuan nay">{{ old('message') }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-submit">
                                    <button type="submit" class="theme-btn">Dat lich xem xe<img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="review-sec">
                        <h4 class="title">Danh gia khach hang</h4>
                        <div class="review-box">
                            <div class="rating-box">
                                <div class="content-box">
                                    <span>Diem trung binh</span>
                                    <h3 class="title">{{ $reviewAverage ?? 'N/A' }}</h3>
                                    <small>{{ $reviewSummary->total ?? 0 }} danh gia</small>
                                </div>
                            </div>
                            @foreach ($reviewMetricColumns as $metricColumn)
                                <ul class="review-list{{ $loop->first ? ' two' : '' }}">
                                    @foreach ($metricColumn as $metric)
                                        <li>
                                            <div class="review-title">
                                                <span>{{ $metric['label'] }}</span>
                                                <small>{{ $metric['note'] }}</small>
                                            </div>
                                            <sub>{{ $metric['value'] }}</sub>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    </div>

                    <div class="reviews">
                        @forelse ($reviews as $review)
                            <div class="content-box{{ $loop->index === 1 ? ' two' : '' }}">
                                <div class="auther-name">
                                    <span>{{ strtoupper(substr($review->user_name, 0, 1)) }}</span>
                                    <h6 class="name">{{ $review->user_name }}</h6>
                                    <small>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</small>
                                </div>
                                <div class="rating-list">
                                    <ul class="list">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <li><i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i></li>
                                        @endfor
                                    </ul>
                                    <span>{{ \Illuminate\Support\Str::limit(strip_tags((string) $review->comment), 42, '...') ?: 'Nhan xet tu khach hang' }}</span>
                                </div>
                                <div class="text">{{ $review->comment }}</div>
                                @if ($loop->first)
                                    <div class="image-box">
                                        @foreach ($imageMedia->take(3) as $image)
                                            <img src="{{ $image->url }}" alt="{{ $car->stock_code }}">
                                        @endforeach
                                    </div>
                                @endif
                                <div class="btn-box">
                                    <a href="#dealer-contact" class="like-btn"><i class="fa-solid fa-thumbs-up"></i>Yeu cau tu van</a>
                                    <a href="{{ route('inventory.index', ['model' => $car->model_slug]) }}" class="like-btn"><i class="fa-solid fa-thumbs-down"></i>Xem xe tuong tu</a>
                                </div>
                                @if ($loop->last && $reviews->count() >= 6)
                                    <a href="#dealer-contact" class="review">
                                        Nhan them tu van
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14" fill="none">
                                            <g clip-path="url(#clip0_detail_review_arrow)">
                                                <path d="M14.1106 0H5.55509C5.34013 0 5.16619 0.173943 5.16619 0.388901C5.16619 0.603859 5.34013 0.777802 5.55509 0.777802H13.1719L0.613453 13.3362C0.461531 13.4881 0.461531 13.7342 0.613453 13.8861C0.689396 13.962 0.788927 14 0.888422 14C0.987917 14 1.08741 13.962 1.16339 13.8861L13.7218 1.3277V8.94447C13.7218 9.15943 13.8957 9.33337 14.1107 9.33337C14.3256 9.33337 14.4996 9.15943 14.4996 8.94447V0.388901C14.4995 0.173943 14.3256 0 14.1106 0Z" fill="#405FF2"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_detail_review_arrow">
                                                    <rect width="14" height="14" fill="white" transform="translate(0.5)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-light">Chua co danh gia duoc duyet cho xe nay.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="side-bar-column v2 col-xl-4 col-lg-12 col-md-12 col-sm-12">
                <div class="inner-column">
                    <div class="contact-box-two">
                        <span>Gia ban</span>
                        <h3 class="title">{{ $car->formatted_price }}</h3>
                        <small>{{ $car->condition_label }} | {{ strtoupper((string) $car->status) }}</small>
                        <div class="btn-box">
                            <a href="#dealer-contact" class="side-btn"><img src="{{ asset('boxcar/images/resource/tag.svg') }}" alt="offer">Nhan bao gia</a>
                            <a href="#dealer-booking" class="side-btn two"><img src="{{ asset('boxcar/images/resource/tag1-1.svg') }}" alt="test-drive">Dat lich xem xe</a>
                        </div>
                    </div>

                    <div class="contact-box">
                        <div class="icon-box">
                            <img src="{{ $car->image_url }}" alt="{{ $showroomName }}">
                        </div>
                        <div class="content-box">
                            <h6 class="title">{{ $showroomName }}</h6>
                            <div class="text">{{ $showroomAddress }}</div>
                            <ul class="contact-list">
                                <li><a href="{{ $mapsDirectionsUrl }}" target="_blank" rel="noopener"><div class="image-box"><img src="{{ asset('boxcar/images/resource/phone1-1.svg') }}" alt="map"></div>Chi duong</a></li>
                                <li><a href="{{ $phoneDigits !== '' ? 'tel:' . $phoneDigits : '#dealer-contact' }}"><div class="image-box"><img src="{{ asset('boxcar/images/resource/phone1-2.svg') }}" alt="phone"></div>{{ $navShowroom->phone ?? 'Lien he showroom' }}</a></li>
                            </ul>
                            <div class="btn-box">
                                <a href="{{ $showroomEmail ? 'mailto:' . $showroomEmail : '#dealer-contact' }}" class="side-btn">
                                    Nhan email tu van
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <g clip-path="url(#clip0_detail_email_arrow)">
                                            <path d="M13.6111 0H5.05558C4.84062 0 4.66668 0.173943 4.66668 0.388901C4.66668 0.603859 4.84062 0.777802 5.05558 0.777802H12.6723L0.113941 13.3362C-0.0379805 13.4881 -0.0379805 13.7342 0.113941 13.8861C0.189884 13.962 0.289415 14 0.38891 14C0.488405 14 0.5879 13.962 0.663879 13.8861L13.2222 1.3277V8.94447C13.2222 9.15943 13.3962 9.33337 13.6111 9.33337C13.8261 9.33337 14 9.15943 14 8.94447V0.388901C14 0.173943 13.8261 0 13.6111 0Z" fill="white"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_detail_email_arrow">
                                                <rect width="14" height="14" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                                <a href="{{ $whatsappDigits !== '' ? 'https://wa.me/' . $whatsappDigits : '#dealer-contact' }}" class="side-btn two" @if ($whatsappDigits !== '') target="_blank" rel="noopener" @endif>
                                    Chat Zalo/WhatsApp
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <g clip-path="url(#clip0_detail_whatsapp_arrow)">
                                            <path d="M13.6111 0H5.05558C4.84062 0 4.66668 0.173943 4.66668 0.388901C4.66668 0.603859 4.84062 0.777802 5.05558 0.777802H12.6723L0.113941 13.3362C-0.0379805 13.4881 -0.0379805 13.7342 0.113941 13.8861C0.189884 13.962 0.289415 14 0.38891 14C0.488405 14 0.5879 13.962 0.663879 13.8861L13.2222 1.3277V8.94447C13.2222 9.15943 13.3962 9.33337 13.6111 9.33337C13.8261 9.33337 14 9.15943 14 8.94447V0.388901C14 0.173943 13.8261 0 13.6111 0Z" fill="#60C961"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_detail_whatsapp_arrow">
                                                <rect width="14" height="14" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                                <a href="{{ route('inventory.index') }}" class="side-btn-three">
                                    Xem toan bo kho xe
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <g clip-path="url(#clip0_detail_stock_arrow)">
                                            <path d="M13.6111 0H5.05558C4.84062 0 4.66668 0.173943 4.66668 0.388901C4.66668 0.603859 4.84062 0.777802 5.05558 0.777802H12.6723L0.113941 13.3362C-0.0379805 13.4881 -0.0379805 13.7342 0.113941 13.8861C0.189884 13.962 0.289415 14 0.38891 14C0.488405 14 0.5879 13.962 0.663879 13.8861L13.2222 1.3277V8.94447C13.2222 9.15943 13.3962 9.33337 13.6111 9.33337C13.8261 9.33337 14 9.15943 14 8.94447V0.388901C14 0.173943 13.8261 0 13.6111 0Z" fill="#050B20"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_detail_stock_arrow">
                                                <rect width="14" height="14" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="cars-section-three">
    <div class="boxcar-container">
        <div class="boxcar-title wow fadeInUp">
            <h2>Xe lien quan</h2>
            <a href="{{ route('inventory.index', ['model' => $car->model_slug]) }}" class="btn-title">
                Xem them
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <g clip-path="url(#clip0_detail_related_arrow)">
                        <path d="M13.6109 0H5.05533C4.84037 0 4.66643 0.173943 4.66643 0.388901C4.66643 0.603859 4.84037 0.777802 5.05533 0.777802H12.6721L0.113697 13.3362C-0.0382246 13.4881 -0.0382246 13.7342 0.113697 13.8861C0.18964 13.962 0.289171 14 0.388666 14C0.488161 14 0.587656 13.962 0.663635 13.8861L13.222 1.3277V8.94447C13.222 9.15943 13.3959 9.33337 13.6109 9.33337C13.8259 9.33337 13.9998 9.15943 13.9998 8.94447V0.388901C13.9998 0.173943 13.8258 0 13.6109 0Z" fill="#050B20"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_detail_related_arrow">
                            <rect width="14" height="14" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </a>
        </div>

        <div class="row car-slider-three" data-preview="4">
            @forelse ($relatedCars as $related)
                @include('client.partials.related-car-card', ['car' => $related])
            @empty
                <div class="col-12">
                    <div class="alert alert-light">Hien chua co xe lien quan trong kho.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
