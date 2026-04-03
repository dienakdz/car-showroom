@extends('client.layouts.page')

@section('title', $trim->make_name . ' ' . $trim->model_name . ' ' . $trim->name)

@push('styles')
<style>
    .trim-review-shell {
        padding-top: 24px;
    }

    .trim-review-shell .boxcar-title {
        margin-bottom: 24px;
    }

    .trim-review-shell .boxcar-title p {
        max-width: 760px;
        color: #667085;
        line-height: 1.7;
    }

    .trim-review-overview {
        display: grid;
        grid-template-columns: minmax(260px, 320px) minmax(0, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .trim-review-summary-card,
    .trim-review-breakdown,
    .trim-review-card,
    .trim-review-empty,
    .trim-review-form-card,
    .trim-review-status-card,
    .trim-review-note {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.06);
    }

    .trim-review-summary-card {
        position: relative;
        overflow: hidden;
        padding: 28px;
        background: linear-gradient(145deg, #050b20 0%, #13224a 54%, #2746d8 100%);
        color: #ffffff;
    }

    .trim-review-summary-card::before {
        content: "";
        position: absolute;
        inset: auto -40px -40px auto;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .trim-review-summary-card > * {
        position: relative;
        z-index: 1;
    }

    .trim-review-summary-card__eyebrow {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .trim-review-summary-card__score {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        margin-top: 18px;
        color: #ffffff;
        line-height: 1;
    }

    .trim-review-summary-card__score strong {
        font-size: 58px;
        font-weight: 800;
    }

    .trim-review-summary-card__score span {
        padding-bottom: 8px;
        color: rgba(255, 255, 255, 0.78);
        font-size: 16px;
        font-weight: 600;
    }

    .trim-review-stars {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        padding: 0;
        margin: 14px 0 0;
        list-style: none;
        color: #fbbf24;
    }

    .trim-review-stars li {
        line-height: 1;
    }

    .trim-review-stars li .fa-star {
        color: #fbbf24;
    }

    .trim-review-stars li .fa-star-o {
        color: rgba(251, 191, 36, 0.34);
    }

    .trim-review-stars--muted {
        color: #d0d5dd;
    }

    .trim-review-stars--muted li .fa-star {
        color: #f59e0b;
    }

    .trim-review-stars--muted li .fa-star-o {
        color: #d0d5dd;
    }

    .trim-review-summary-card p {
        margin: 14px 0 0;
        color: rgba(255, 255, 255, 0.82);
        line-height: 1.7;
    }

    .trim-review-breakdown {
        padding: 24px 26px;
    }

    .trim-review-breakdown__head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .trim-review-breakdown__head h4,
    .trim-review-form-card__head h4 {
        margin: 0;
        color: #050b20;
        font-size: 24px;
    }

    .trim-review-breakdown__head p,
    .trim-review-form-card__head p {
        margin: 8px 0 0;
        color: #667085;
        line-height: 1.7;
    }

    .trim-review-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .trim-review-meta {
        padding: 16px 18px;
        border-radius: 18px;
        background: #f8fbff;
        border: 1px solid #e5eefb;
    }

    .trim-review-meta span {
        display: block;
        margin-bottom: 8px;
        color: #667085;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .trim-review-meta strong {
        display: block;
        color: #050b20;
        font-size: 20px;
        line-height: 1.3;
    }

    .trim-review-bars {
        display: grid;
        gap: 12px;
    }

    .trim-review-bar {
        display: grid;
        grid-template-columns: 56px minmax(0, 1fr) 40px;
        gap: 12px;
        align-items: center;
    }

    .trim-review-bar__label,
    .trim-review-bar__count {
        color: #344054;
        font-size: 14px;
        font-weight: 600;
    }

    .trim-review-bar__track {
        position: relative;
        height: 10px;
        border-radius: 999px;
        overflow: hidden;
        background: #eaecf0;
    }

    .trim-review-bar__track::after {
        content: "";
        position: absolute;
        inset: 0;
        width: var(--trim-review-fill, 0%);
        border-radius: inherit;
        background: linear-gradient(90deg, #405ff2 0%, #7c90ff 100%);
    }

    .trim-review-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .trim-review-card {
        padding: 22px;
    }

    .trim-review-card__header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .trim-review-card__author {
        display: flex;
        gap: 14px;
        align-items: center;
        min-width: 0;
    }

    .trim-review-card__avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 52px;
        width: 52px;
        height: 52px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(64, 95, 242, 0.14) 0%, rgba(64, 95, 242, 0.24) 100%);
        color: #2440cb;
        font-size: 22px;
        font-weight: 800;
    }

    .trim-review-card__author h6 {
        margin: 0;
        color: #050b20;
        font-size: 18px;
        line-height: 1.4;
    }

    .trim-review-card__author span {
        display: block;
        margin-top: 4px;
        color: #667085;
        font-size: 14px;
    }

    .trim-review-card__rating {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        text-align: right;
    }

    .trim-review-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(64, 95, 242, 0.1);
        color: #2440cb;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .trim-review-card__comment {
        margin: 0;
        color: #344054;
        font-size: 15px;
        line-height: 1.85;
    }

    .trim-review-empty {
        grid-column: 1 / -1;
        padding: 28px;
        text-align: center;
    }

    .trim-review-empty strong {
        display: block;
        color: #050b20;
        font-size: 22px;
        margin-bottom: 8px;
    }

    .trim-review-empty p {
        margin: 0;
        color: #667085;
        line-height: 1.8;
    }

    .trim-review-form-shell {
        padding-top: 12px;
    }

    .trim-review-form-card {
        padding: 28px;
    }

    .trim-review-form-card__head {
        margin-bottom: 22px;
    }

    .trim-review-form-grid {
        display: grid;
        gap: 18px;
    }

    .trim-review-field {
        display: grid;
        gap: 10px;
    }

    .trim-review-field label {
        color: #101828;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.4;
    }

    .trim-review-field select,
    .trim-review-field textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 16px;
        background: #f8fbff;
        color: #0f172a;
        font-size: 15px;
        line-height: 1.6;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        appearance: none;
        -webkit-appearance: none;
    }

    .trim-review-field select {
        height: 58px;
        padding: 0 18px;
    }

    .trim-review-field textarea {
        min-height: 170px;
        padding: 16px 18px;
        resize: vertical;
    }

    .trim-review-field select:hover,
    .trim-review-field textarea:hover {
        border-color: #94a3b8;
        background: #ffffff;
    }

    .trim-review-field select:focus,
    .trim-review-field textarea:focus {
        outline: none;
        border-color: #405ff2;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(64, 95, 242, 0.14);
    }

    .trim-review-field select.is-invalid,
    .trim-review-field textarea.is-invalid {
        border-color: #d93025;
        background: #fff8f7;
        box-shadow: 0 0 0 4px rgba(217, 48, 37, 0.08);
    }

    .trim-review-field textarea::placeholder {
        color: #98a2b3;
    }

    .trim-review-field-note,
    .trim-review-note p,
    .trim-review-status-card p {
        margin: 0;
        color: #667085;
        line-height: 1.7;
    }

    .trim-review-error {
        color: #d93025;
        font-size: 13px;
        line-height: 1.5;
    }

    .trim-review-actions {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }

    .trim-review-note,
    .trim-review-status-card {
        padding: 22px 24px;
    }

    .trim-review-note strong,
    .trim-review-status-card strong {
        display: block;
        margin-bottom: 8px;
        color: #050b20;
        font-size: 20px;
    }

    .trim-review-note a {
        color: #2440cb;
        font-weight: 700;
    }

    .trim-review-status-card__top {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .trim-review-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: #eef2ff;
        color: #405ff2;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .trim-review-status-card__comment {
        margin-top: 16px;
        padding: 16px 18px;
        border-radius: 18px;
        background: #f8fbff;
        border: 1px solid #e5eefb;
        color: #344054;
        line-height: 1.8;
    }

    @media (max-width: 991px) {
        .trim-review-overview,
        .trim-review-grid {
            grid-template-columns: 1fr;
        }

        .trim-review-meta-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .trim-review-summary-card,
        .trim-review-breakdown,
        .trim-review-card,
        .trim-review-form-card,
        .trim-review-note,
        .trim-review-status-card,
        .trim-review-empty {
            padding: 20px;
            border-radius: 20px;
        }

        .trim-review-card__header,
        .trim-review-actions {
            flex-direction: column;
            align-items: flex-start;
        }

        .trim-review-card__rating {
            align-items: flex-start;
            text-align: left;
        }

        .trim-review-summary-card__score strong {
            font-size: 46px;
        }
    }
</style>
@endpush

@section('content')
@php
    $defaultName = old('name', auth()->user()->name ?? '');
    $defaultPhone = old('phone', auth()->user()->phone ?? '');
    $defaultEmail = old('email', auth()->user()->email ?? '');
    $reviewCount = $reviews->count();
    $reviewAverage = $reviewCount > 0 ? number_format((float) $reviews->avg('rating'), 1) : null;
    $latestReviewDate = $reviewCount > 0
        ? \Carbon\Carbon::parse($reviews->first()->created_at)->format('d/m/Y')
        : null;
    $reviewDistribution = collect(range(5, 1))->map(function (int $rating) use ($reviews, $reviewCount): array {
        $count = $reviews->where('rating', $rating)->count();

        return [
            'rating' => $rating,
            'count' => $count,
            'raw_percent' => $reviewCount > 0 ? (int) round(($count / $reviewCount) * 100) : 0,
        ];
    });
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

<section class="boxcar-testimonial-section home1 trim-review-shell">
    <div class="boxcar-container">
        <div class="boxcar-title wow fadeInUp">
            <h2>Danh gia khach hang</h2>
            <p>Review ben duoi chi hien thi cac danh gia da duyet cua khach hang da mua xe thuoc phien ban nay, giup nguoi xem co them context truoc khi gui lead hoac dat lich.</p>
        </div>

        <div class="trim-review-overview">
            <div class="trim-review-summary-card">
                <span class="trim-review-summary-card__eyebrow">Tong quan review</span>
                <div class="trim-review-summary-card__score">
                    <strong>{{ $reviewAverage ?? 'N/A' }}</strong>
                    <span>/5</span>
                </div>
                <ul class="trim-review-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <li><i class="fa {{ $reviewAverage !== null && $i <= round((float) $reviewAverage) ? 'fa-star' : 'fa-star-o' }}"></i></li>
                    @endfor
                </ul>
                <p>
                    @if ($reviewCount > 0)
                        Co {{ $reviewCount }} danh gia da duyet. Muc do hai long hien tai o muc {{ $reviewAverage }}/5 va duoc cap nhat den {{ $latestReviewDate }}.
                    @else
                        Chua co danh gia da duyet. Khi co review hop le, phan tong quan nay se cap nhat ngay de nguoi xem co them diem tham chieu.
                    @endif
                </p>
            </div>

            <div class="trim-review-breakdown">
                <div class="trim-review-breakdown__head">
                    <div>
                        <h4>Phan bo danh gia</h4>
                        <p>Tom tat nhanh de thay muc do hai long theo tung moc sao, ben canh thong tin ve luot review va xe dang san co.</p>
                    </div>
                </div>

                <div class="trim-review-meta-grid">
                    <div class="trim-review-meta">
                        <span>Danh gia da duyet</span>
                        <strong>{{ $reviewCount }}</strong>
                    </div>
                    <div class="trim-review-meta">
                        <span>Review moi nhat</span>
                        <strong>{{ $latestReviewDate ?? 'Dang cap nhat' }}</strong>
                    </div>
                    <div class="trim-review-meta">
                        <span>Xe san co</span>
                        <strong>{{ $availableCarsCount }}</strong>
                    </div>
                </div>

                <div class="trim-review-bars">
                    @foreach ($reviewDistribution as $distribution)
                        <div class="trim-review-bar">
                            <span class="trim-review-bar__label">{{ $distribution['rating'] }} sao</span>
                            <div class="trim-review-bar__track" style="--trim-review-fill: {{ $distribution['raw_percent'] }}%;"></div>
                            <span class="trim-review-bar__count">{{ $distribution['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="trim-review-grid">
            @forelse ($reviews as $review)
                <article class="trim-review-card">
                    <div class="trim-review-card__header">
                        <div class="trim-review-card__author">
                            <div class="trim-review-card__avatar">{{ strtoupper(substr($review->user_name, 0, 1)) }}</div>
                            <div>
                                <h6>{{ $review->user_name }}</h6>
                                <span>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="trim-review-card__rating">
                            <ul class="trim-review-stars trim-review-stars--muted">
                                @for ($i = 1; $i <= 5; $i++)
                                    <li><i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i></li>
                                @endfor
                            </ul>
                            <span class="trim-review-chip">{{ $review->rating }}/5 diem</span>
                        </div>
                    </div>
                    <p class="trim-review-card__comment">{{ $review->comment }}</p>
                </article>
            @empty
                <div class="trim-review-empty">
                    <strong>Chua co danh gia duoc duyet</strong>
                    <p>Phien ban nay hien chua co review cong khai. Ban van co the xem thong so, xe dang san co va gui yeu cau tu van de nhan them thong tin thuc te tu showroom.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="contact-us-section layout-radius trim-review-form-shell" id="review-form">
    <div class="boxcar-container">
        <div class="calculater-sec">
            <div class="right-box">
                <div class="row">
                    <div class="col-lg-8 content-column">
                        <div class="inner-column">
                            @php($reviewStatusLabel = match ($userReview->status ?? null) {
                                'approved' => 'Da duyet',
                                'hidden' => 'Da an',
                                'pending' => 'Cho duyet',
                                default => null,
                            })

                            <div class="trim-review-form-card">
                                <div class="trim-review-form-card__head">
                                    <h4>Gui danh gia cua ban</h4>
                                    <p>Chi khach da mua xe thuoc phien ban nay moi co the gui review. Noi dung moi se o trang thai cho duyet truoc khi hien thi cong khai tren site.</p>
                                </div>

                                @if ($errors->has('review'))
                                    <div class="trim-review-note" style="margin-bottom: 18px;">
                                        <strong>Luu y</strong>
                                        <p>{{ $errors->first('review') }}</p>
                                    </div>
                                @endif

                                @guest
                                    <div class="trim-review-note">
                                        <strong>Dang nhap de gui review</strong>
                                        <p>Ban can <a href="{{ route('login') }}">dang nhap</a> bang tai khoan da mua xe thuoc phien ban nay de he thong kiem tra dieu kien va luu danh gia dung lich su giao dich.</p>
                                    </div>
                                @else
                                    @if ($canSubmitReview)
                                        <form method="POST" action="{{ route('trim.reviews.store', ['trimSlug' => $trim->slug]) }}">
                                            @csrf
                                            <div class="trim-review-form-grid">
                                                <div class="trim-review-field">
                                                    <label for="trim-review-rating">Diem danh gia</label>
                                                    <select id="trim-review-rating" class="@error('rating') is-invalid @enderror" name="rating" required>
                                                        <option value="">Chon so sao</option>
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            <option value="{{ $i }}" {{ (string) old('rating') === (string) $i ? 'selected' : '' }}>{{ $i }}/5 sao</option>
                                                        @endfor
                                                    </select>
                                                    <p class="trim-review-field-note">Hay chon muc sao phu hop voi trai nghiem tong the cua ban ve phien ban nay.</p>
                                                    @error('rating')<span class="trim-review-error">{{ $message }}</span>@enderror
                                                </div>

                                                <div class="trim-review-field">
                                                    <label for="trim-review-comment">Noi dung danh gia</label>
                                                    <textarea id="trim-review-comment" class="@error('comment') is-invalid @enderror" name="comment" placeholder="Chia se trai nghiem cua ban sau khi mua xe" required>{{ old('comment') }}</textarea>
                                                    <p class="trim-review-field-note">Review cu the ve van hanh, khong gian, muc tieu hao hay gia tri su dung se huu ich hon cho nguoi xem sau.</p>
                                                    @error('comment')<span class="trim-review-error">{{ $message }}</span>@enderror
                                                </div>

                                                <div class="trim-review-actions">
                                                    <p class="trim-review-field-note">Sau khi gui, danh gia se duoc ghi nhan o trang thai cho duyet. Ban khong can gui lai neu da thay review xuat hien trong tai khoan cua minh.</p>
                                                    <div class="form-submit" style="margin: 0;">
                                                        <button type="submit" class="theme-btn">Gui danh gia <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    @elseif ($userReview)
                                        <div class="trim-review-status-card">
                                            <div class="trim-review-status-card__top">
                                                <div>
                                                    <strong>Ban da gui review cho phien ban nay</strong>
                                                    <p>Thong tin duoi day la noi dung review gan nhat duoc luu voi tai khoan cua ban.</p>
                                                </div>
                                                @if ($reviewStatusLabel)
                                                    <span class="trim-review-status-badge">{{ $reviewStatusLabel }}</span>
                                                @endif
                                            </div>
                                            <ul class="trim-review-stars">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <li><i class="fa {{ $i <= $userReview->rating ? 'fa-star' : 'fa-star-o' }}"></i></li>
                                                @endfor
                                                <li style="margin-left: 6px; color: #344054; font-weight: 700;">{{ $userReview->rating }}/5</li>
                                            </ul>
                                            <div class="trim-review-status-card__comment">{{ $userReview->comment }}</div>
                                        </div>
                                    @elseif (! $userHasPurchasedTrim)
                                        <div class="trim-review-note">
                                            <strong>Chua du dieu kien gui review</strong>
                                            <p>Chi khach da mua xe thuoc phien ban nay moi co the danh gia. Dieu kien nay giup phan review phan anh dung trai nghiem sau mua va han che spam.</p>
                                        </div>
                                    @endif
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
