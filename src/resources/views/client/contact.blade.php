@extends('client.layouts.page')

@section('title', $sourceTitle)

@push('styles')
<style>
    .client-contact-flow {
        padding: 48px 0 72px;
        background:
            radial-gradient(circle at top left, rgba(64, 95, 242, 0.12), transparent 34%),
            linear-gradient(180deg, #f7f8fc 0%, #ffffff 38%, #f5f7fb 100%);
    }

    .client-contact-flow .flow-header {
        margin-bottom: 28px;
    }

    .client-contact-flow .flow-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(64, 95, 242, 0.12);
        color: #405ff2;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .client-contact-flow .flow-title {
        margin: 16px 0 10px;
        color: #050b20;
        font-size: clamp(30px, 4vw, 46px);
        line-height: 1.05;
    }

    .client-contact-flow .flow-description {
        max-width: 720px;
        margin: 0;
        color: #5f6980;
        font-size: 16px;
        line-height: 1.7;
    }

    .client-contact-flow .flow-switches {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 22px;
    }

    .client-contact-flow .flow-switch {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 148px;
        padding: 12px 18px;
        border-radius: 16px;
        border: 1px solid #d9dde8;
        color: #050b20;
        font-weight: 600;
        background: #fff;
        transition: all 0.25s ease;
    }

    .client-contact-flow .flow-switch:hover,
    .client-contact-flow .flow-switch.is-active {
        border-color: #405ff2;
        background: #405ff2;
        color: #fff;
    }

    .client-contact-flow .flow-layout {
        display: grid;
        grid-template-columns: minmax(280px, 0.92fr) minmax(0, 1.08fr);
        gap: 24px;
        align-items: start;
    }

    .client-contact-flow .flow-panel {
        border-radius: 28px;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 22px 65px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .client-contact-flow .flow-panel.flow-panel-form {
        overflow: visible;
    }

    .client-contact-flow .flow-main .flow-panel {
        overflow: visible;
    }

    .client-contact-flow .flow-panel-inner {
        padding: 28px;
    }

    .client-contact-flow .flow-panel.flow-panel-accent {
        background: linear-gradient(160deg, #050b20 0%, #13234f 100%);
        color: #fff;
    }

    .client-contact-flow .flow-panel.flow-panel-accent .flow-panel-title,
    .client-contact-flow .flow-panel.flow-panel-accent .flow-panel-text,
    .client-contact-flow .flow-panel.flow-panel-accent .flow-panel-label,
    .client-contact-flow .flow-panel.flow-panel-accent .flow-step strong {
        color: #fff;
    }

    .client-contact-flow .flow-panel-title {
        margin: 0 0 10px;
        color: #050b20;
        font-size: 26px;
        line-height: 1.2;
    }

    .client-contact-flow .flow-panel-text {
        margin: 0;
        color: #5f6980;
        font-size: 15px;
        line-height: 1.7;
    }

    .client-contact-flow .flow-step-list {
        margin: 24px 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 16px;
    }

    .client-contact-flow .flow-step {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .client-contact-flow .flow-step-index {
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-weight: 700;
    }

    .client-contact-flow .flow-step-body {
        flex: 1 1 auto;
        min-width: 0;
        padding-top: 2px;
    }

    .client-contact-flow .flow-step-body strong {
        display: block;
        margin-bottom: 4px;
        color: #050b20;
        font-size: 15px;
    }

    .client-contact-flow .flow-step-body span {
        display: block;
        color: rgba(255, 255, 255, 0.78);
        font-size: 14px;
        line-height: 1.6;
    }

    .client-contact-flow .flow-contact-grid {
        display: grid;
        gap: 14px;
        margin-top: 22px;
    }

    .client-contact-flow .flow-contact-card {
        padding: 18px 20px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .client-contact-flow .flow-contact-card a {
        color: #fff;
    }

    .client-contact-flow .flow-contact-card .flow-panel-label {
        display: block;
        margin-bottom: 6px;
        color: rgba(255, 255, 255, 0.68);
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .client-contact-flow .flow-map {
        height: 260px;
        border: 0;
        width: 100%;
    }

    .client-contact-flow .flow-form-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 22px;
    }

    .client-contact-flow .flow-pill {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: #eef2ff;
        color: #405ff2;
        font-size: 13px;
        font-weight: 700;
    }

    .client-contact-flow .flow-helper {
        margin: 0 0 18px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #f6f8fc;
        color: #4b5568;
        font-size: 14px;
        line-height: 1.65;
    }

    .client-contact-flow .flow-alert {
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 16px;
        border: 1px solid rgba(217, 48, 37, 0.16);
        background: rgba(217, 48, 37, 0.06);
        color: #8c1d18;
        font-size: 14px;
        line-height: 1.6;
    }

    .client-contact-flow .flow-error {
        display: block;
        margin-top: 8px;
        color: #d93025;
        font-size: 13px;
    }

    .client-contact-flow .form_boxes {
        margin-bottom: 18px;
        position: relative;
    }

    .client-contact-flow .form_boxes:has(.drop-menu.active) {
        z-index: 30;
    }

    .client-contact-flow .form_boxes label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: #050b20;
    }

    .client-contact-flow .form_boxes input,
    .client-contact-flow .form_boxes textarea {
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

    .client-contact-flow .form_boxes input {
        height: 58px;
        padding: 0 18px;
    }

    .client-contact-flow .form_boxes textarea {
        min-height: 160px;
        padding: 16px 18px;
        resize: vertical;
    }

    .client-contact-flow .form_boxes input:hover,
    .client-contact-flow .form_boxes textarea:hover,
    .client-contact-flow .form_boxes .drop-menu .select:hover {
        border-color: #94a3b8;
        background: #fff;
    }

    .client-contact-flow .form_boxes input:focus,
    .client-contact-flow .form_boxes textarea:focus {
        outline: none;
        border-color: #405ff2;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(64, 95, 242, 0.14);
    }

    .client-contact-flow .form_boxes input.is-invalid,
    .client-contact-flow .form_boxes textarea.is-invalid,
    .client-contact-flow .form_boxes.is-invalid .drop-menu .select {
        border-color: #d93025;
        background: #fff8f7;
        box-shadow: 0 0 0 4px rgba(217, 48, 37, 0.08);
    }

    .client-contact-flow .form_boxes input::placeholder,
    .client-contact-flow .form_boxes textarea::placeholder {
        color: #98a2b3 !important;
    }

    .client-contact-flow .form_boxes .drop-menu {
        position: relative;
        z-index: 4;
    }

    .client-contact-flow .form_boxes .drop-menu.active {
        z-index: 40;
    }

    .client-contact-flow .form_boxes .drop-menu .select {
        min-height: 58px;
        padding: 0 18px;
        border: 1px solid #cbd5e1;
        border-radius: 16px;
        background: #f8fbff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #0f172a;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .client-contact-flow .form_boxes .drop-menu .select span {
        color: #0f172a;
    }

    .client-contact-flow .form_boxes .drop-menu .select i {
        color: #64748b;
    }

    .client-contact-flow .form_boxes .drop-menu .dropdown {
        width: 100%;
        margin-top: 10px;
        padding: 8px;
        border: 1px solid #d9dde8;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        max-height: 280px;
        overflow-y: auto;
        z-index: 50;
    }

    .client-contact-flow .form_boxes .drop-menu .dropdown li {
        padding: 10px 12px;
        border-radius: 12px;
        color: #0f172a;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .client-contact-flow .form_boxes .drop-menu .dropdown li:hover {
        background: #eef2ff;
        color: #405ff2;
    }

    .client-contact-flow .flow-note-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-top: 18px;
    }

    .client-contact-flow .flow-note-card {
        padding: 18px;
        border-radius: 18px;
        background: #f7f9fc;
        border: 1px solid #e8edf5;
    }

    .client-contact-flow .flow-note-card strong {
        display: block;
        margin-bottom: 6px;
        color: #050b20;
        font-size: 15px;
    }

    .client-contact-flow .flow-note-card span {
        color: #5f6980;
        font-size: 14px;
        line-height: 1.6;
    }

    @media (max-width: 991px) {
        .client-contact-flow .flow-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .client-contact-flow {
            padding: 36px 0 56px;
        }

        .client-contact-flow .flow-panel-inner {
            padding: 22px;
        }

        .client-contact-flow .flow-note-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $defaultName = old('name', auth()->user()->name ?? '');
    $defaultPhone = old('phone', auth()->user()->phone ?? '');
    $defaultEmail = old('email', auth()->user()->email ?? '');
    $mapAddress = urlencode($showroom->address ?? 'Ho Chi Minh City');
    $sourceTabs = [
        ['route' => route('contact'), 'label' => 'Lien he', 'source' => 'contact'],
        ['route' => route('finance'), 'label' => 'Tai chinh', 'source' => 'finance'],
        ['route' => route('tradein'), 'label' => 'Thu cu doi moi', 'source' => 'trade_in'],
    ];
    $sourceMeta = match ($source) {
        'finance' => [
            'eyebrow' => 'Tai chinh',
            'title' => 'Nhan tu van giai phap tai chinh cho mau xe ban quan tam',
            'description' => 'De doi sales tu van dung phuong an vay, website se tao lead co context xe hoac phien ban ma ban dang xem xet.',
            'steps' => [
                'Chon xe hoac phien ban dang quan tam.',
                'De lai nhu cau tai chinh, muc tra truoc hoac nho chung toi goi lai.',
                'Showroom lien he xac nhan va de xuat phuong an phu hop.',
            ],
        ],
        'trade_in' => [
            'eyebrow' => 'Trade-in',
            'title' => 'Thu cu doi moi co context ro rang de dinh gia nhanh hon',
            'description' => 'Khi gan voi xe hoac phien ban muc tieu, doi sale co the tu van chenh lech hop ly va sap xep lich tham dinh nhanh hon.',
            'steps' => [
                'Chon mau xe hoac phien ban muon doi sang.',
                'Mo ta xe dang su dung, tinh trang va ky vong gia.',
                'Showroom lien he de lay thong tin chi tiet va dat lich tham dinh.',
            ],
        ],
        default => [
            'eyebrow' => 'Public lead',
            'title' => 'De lai yeu cau, doi sale se tiep nhan va phan loai theo CRM',
            'description' => 'Trang nay la diem vao chung cho cac yeu cau lien he, tu van, tai chinh va trade-in. Neu ban da co xe quan tam, them context se giup xu ly nhanh hon.',
            'steps' => [
                'Nhap thong tin lien he co the goi lai duoc.',
                'Neu co, chon xe hoac phien ban ban dang quan tam.',
                'Showroom tiep nhan lead, phan cong sale va lien he lai som.',
            ],
        ],
    };
    $contextRequired = $source !== 'contact';
@endphp

<section class="client-contact-flow layout-radius">
    <div class="boxcar-container">
        <div class="flow-header">
            <ul class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chu</a></li>
                <li><span>{{ $sourceTitle }}</span></li>
            </ul>
            <span class="flow-eyebrow">{{ $sourceMeta['eyebrow'] }}</span>
            <h1 class="flow-title">{{ $sourceTitle }}</h1>
            <p class="flow-description">{{ $sourceMeta['description'] }}</p>

            <div class="flow-switches">
                @foreach ($sourceTabs as $tab)
                    <a href="{{ $tab['route'] }}" class="flow-switch {{ $source === $tab['source'] ? 'is-active' : '' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="flow-layout">
            <div class="flow-side">
                <div class="flow-panel flow-panel-accent">
                    <div class="flow-panel-inner">
                        <span class="flow-eyebrow">{{ $sourceMeta['eyebrow'] }}</span>
                        <h2 class="flow-panel-title">{{ $sourceMeta['title'] }}</h2>
                        <p class="flow-panel-text">{{ $sourceMeta['description'] }}</p>

                        <ol class="flow-step-list">
                            @foreach ($sourceMeta['steps'] as $step)
                                <li class="flow-step">
                                    <span class="flow-step-index">{{ $loop->iteration }}</span>
                                    <div class="flow-step-body">
                                        <strong>Buoc {{ $loop->iteration }}</strong>
                                        <span>{{ $step }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>

                        <div class="flow-contact-grid">
                            <div class="flow-contact-card">
                                <span class="flow-panel-label">Hotline</span>
                                <a href="tel:{{ preg_replace('/\D+/', '', (string) ($showroom->phone ?? '0900000000')) }}">{{ $showroom->phone ?? '0900 000 000' }}</a>
                            </div>
                            <div class="flow-contact-card">
                                <span class="flow-panel-label">Email</span>
                                <a href="mailto:{{ $showroom->email ?? 'hello@showroom.test' }}">{{ $showroom->email ?? 'hello@showroom.test' }}</a>
                            </div>
                            <div class="flow-contact-card">
                                <span class="flow-panel-label">Dia chi</span>
                                <div>{{ $showroom->address ?? 'TP. Ho Chi Minh' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flow-panel flow-panel-form">
                    <iframe
                        class="flow-map"
                        src="https://maps.google.com/maps?width=100%25&height=600&hl=vi&q={{ $mapAddress }}&t=&z=14&ie=UTF8&iwloc=B&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                    <div class="flow-panel-inner">
                        <h3 class="flow-panel-title">Thong tin showroom</h3>
                        <p class="flow-panel-text">
                            Website khong co checkout. Toan bo quy trinh ban xe duoc xu ly offline theo flow:
                            inventory -> lead -> appointment -> sale -> review.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flow-main">
                <div class="flow-panel">
                    <div class="flow-panel-inner">
                        <div class="flow-form-head">
                            <div>
                                <h2 class="flow-panel-title">Gui thong tin tu van</h2>
                                <p class="flow-panel-text">
                                    Form nay tao lead truc tiep cho doi sales. Neu ban da dang nhap, thong tin co ban se duoc dien san.
                                </p>
                            </div>
                            <span class="flow-pill">{{ $sourceTitle }}</span>
                        </div>

                        @if ($errors->any())
                            <div class="flow-alert">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        @if ($contextRequired)
                            <div class="flow-helper">
                                Luong nay can gan voi xe hoac phien ban cu the de showroom tu van dung context.
                                Vui long chon it nhat 1 trong 2 truong ben duoi.
                            </div>
                        @endif

                        <form class="row" method="POST" action="{{ route('lead.store') }}">
                            @csrf
                            <input type="hidden" name="source" value="{{ $source }}">

                            <div class="col-lg-6">
                                <div class="form_boxes">
                                    <label>Ho va ten</label>
                                    <input class="@error('name') is-invalid @enderror" type="text" name="name" value="{{ $defaultName }}" placeholder="Nguyen Van A" required>
                                    @error('name')
                                        <span class="flow-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form_boxes">
                                    <label>So dien thoai</label>
                                    <input class="@error('phone') is-invalid @enderror" type="text" name="phone" value="{{ $defaultPhone }}" placeholder="09xxxxxxxx" required>
                                    @error('phone')
                                        <span class="flow-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form_boxes">
                                    <label>Email</label>
                                    <input class="@error('email') is-invalid @enderror" type="email" name="email" value="{{ $defaultEmail }}" placeholder="name@email.com">
                                    @error('email')
                                        <span class="flow-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form_boxes {{ $errors->has('car_unit_id') ? 'is-invalid' : '' }}">
                                    <label>Chon xe dang quan tam</label>
                                    @include('client.partials.form.custom-dropdown', [
                                        'name' => 'car_unit_id',
                                        'options' => $availableCars,
                                        'selectedValue' => old('car_unit_id', ''),
                                        'valueField' => 'id',
                                        'labelField' => 'label',
                                        'emptyLabel' => 'Khong chon xe cu the',
                                    ])
                                    @error('car_unit_id')
                                        <span class="flow-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form_boxes {{ $errors->has('trim_id') ? 'is-invalid' : '' }}">
                                    <label>Hoac chon phien ban</label>
                                    @include('client.partials.form.custom-dropdown', [
                                        'name' => 'trim_id',
                                        'options' => $trims,
                                        'selectedValue' => old('trim_id', ''),
                                        'valueField' => 'id',
                                        'labelField' => 'label',
                                        'emptyLabel' => 'Khong chon phien ban',
                                    ])
                                    @error('trim_id')
                                        <span class="flow-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form_boxes v2">
                                    <label>Noi dung</label>
                                    <textarea class="@error('message') is-invalid @enderror" name="message" placeholder="Mo ta nhu cau, thoi gian co the lien he, muc tai chinh hoac thong tin xe can trade-in">{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="flow-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-submit">
                                    <button type="submit" class="theme-btn">
                                        Gui yeu cau
                                        <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow">
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="flow-note-grid">
                            <div class="flow-note-card">
                                <strong>Rate limit</strong>
                                <span>Form co throttle de tranh spam va giu chat luong lead cho doi sale.</span>
                            </div>
                            <div class="flow-note-card">
                                <strong>Gan context</strong>
                                <span>Lead co the gan voi car unit hoac trim de nhin duoc ngu canh ngay tu public site.</span>
                            </div>
                            <div class="flow-note-card">
                                <strong>Phan hoi</strong>
                                <span>Thong tin se vao CRM voi status moi tao, sau do duoc phan cong cho nhan vien phu trach.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
