@extends('client.layouts.page')

@section('title', 'Ve chung toi')

@push('styles')
<style>
    .client-about-page {
        padding: 48px 0 72px;
        background:
            radial-gradient(circle at top left, rgba(64, 95, 242, 0.12), transparent 34%),
            linear-gradient(180deg, #f7f8fc 0%, #ffffff 40%, #f5f7fb 100%);
    }

    .client-about-page .about-hero,
    .client-about-page .about-grid,
    .client-about-page .about-media-grid,
    .client-about-page .about-stat-grid,
    .client-about-page .about-feature-grid,
    .client-about-page .about-info-stack,
    .client-about-page .about-checklist,
    .client-about-page .about-note-grid,
    .client-about-page .about-image-grid {
        display: grid;
        gap: 16px;
    }

    .client-about-page .about-hero {
        grid-template-columns: minmax(0, 1.12fr) minmax(320px, 0.88fr);
        align-items: stretch;
        margin-bottom: 24px;
    }

    .client-about-page .about-grid,
    .client-about-page .about-media-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-bottom: 24px;
    }

    .client-about-page .about-stat-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-top: 24px;
    }

    .client-about-page .about-feature-grid,
    .client-about-page .about-note-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .client-about-page .about-image-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    }

    .client-about-page .about-panel {
        border-radius: 28px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #fff;
        box-shadow: 0 22px 65px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .client-about-page .about-panel-inner {
        padding: 28px;
    }

    .client-about-page .about-hero-card {
        background: linear-gradient(160deg, #050b20 0%, #13234f 56%, #2847da 100%);
        color: #fff;
    }

    .client-about-page .about-hero-card h1,
    .client-about-page .about-hero-card p,
    .client-about-page .about-hero-card strong,
    .client-about-page .about-hero-card span,
    .client-about-page .about-hero-card a {
        color: #fff;
    }

    .client-about-page .about-eyebrow {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .client-about-page .about-title {
        margin: 18px 0 12px;
        font-size: clamp(34px, 4vw, 52px);
        line-height: 1.02;
    }

    .client-about-page .about-text {
        margin: 0;
        font-size: 16px;
        line-height: 1.75;
        color: #5f6980;
    }

    .client-about-page .about-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .client-about-page .about-action-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 50px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        font-weight: 700;
    }

    .client-about-page .about-action-link.alt {
        background: #fff;
        border-color: #fff;
        color: #050b20;
    }

    .client-about-page .about-stat {
        padding: 18px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.08);
    }

    .client-about-page .about-stat strong {
        display: block;
        font-size: 30px;
        line-height: 1;
        margin-bottom: 8px;
    }

    .client-about-page .about-info-card,
    .client-about-page .about-note,
    .client-about-page .about-feature {
        padding: 18px 20px;
        border-radius: 20px;
        border: 1px solid #e5eaf2;
        background: #f8fbff;
    }

    .client-about-page .about-info-card label {
        display: block;
        margin-bottom: 8px;
        color: #667085;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .client-about-page .about-info-card strong,
    .client-about-page .about-note strong,
    .client-about-page .about-feature strong {
        display: block;
        margin-bottom: 6px;
        color: #050b20;
        font-size: 18px;
        line-height: 1.4;
    }

    .client-about-page .about-info-card span,
    .client-about-page .about-note span,
    .client-about-page .about-feature span {
        display: block;
        color: #5f6980;
        line-height: 1.7;
    }

    .client-about-page .about-section-title {
        margin: 0 0 10px;
        color: #050b20;
        font-size: 28px;
        line-height: 1.2;
    }

    .client-about-page .about-checklist {
        margin-top: 22px;
    }

    .client-about-page .about-check {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr);
        gap: 14px;
        align-items: start;
        padding: 18px 20px;
        border-radius: 20px;
        background: #f8fbff;
        border: 1px solid #e5eaf2;
    }

    .client-about-page .about-check-index {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(64, 95, 242, 0.12);
        color: #405ff2;
        font-weight: 700;
    }

    .client-about-page .about-image-grid img {
        width: 100%;
        height: 100%;
        min-height: 220px;
        object-fit: cover;
        border-radius: 22px;
    }

    .client-about-page .about-image-grid .stacked {
        display: grid;
        gap: 16px;
    }

    .client-about-page .about-cta {
        background: linear-gradient(160deg, #050b20 0%, #142552 100%);
        color: #fff;
    }

    .client-about-page .about-cta .about-section-title,
    .client-about-page .about-cta .about-text {
        color: #fff;
    }

    @media (max-width: 1199px) {
        .client-about-page .about-stat-grid,
        .client-about-page .about-feature-grid,
        .client-about-page .about-note-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991px) {
        .client-about-page .about-hero,
        .client-about-page .about-grid,
        .client-about-page .about-media-grid,
        .client-about-page .about-image-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .client-about-page {
            padding: 36px 0 56px;
        }

        .client-about-page .about-panel-inner {
            padding: 22px;
        }

        .client-about-page .about-stat-grid,
        .client-about-page .about-feature-grid,
        .client-about-page .about-note-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $showroomName = $showroom->name ?? 'Car Showroom';
    $showroomAddress = $showroom->address ?? 'TP. Ho Chi Minh';
    $showroomPhone = $showroom->phone ?? '0900 000 000';
    $showroomEmail = $showroom->email ?? 'hello@showroom.test';
    $showroomDescription = $showroom->description ?? 'Showroom tap trung vao trai nghiem chon xe, tao lead, dat lich va chot sale offline theo mot quy trinh ro rang.';
@endphp

<section class="client-about-page layout-radius">
    <div class="boxcar-container">
        <div class="about-hero">
            <div class="about-panel about-hero-card">
                <div class="about-panel-inner">
                    <ul class="breadcrumb">
                        <li><a href="{{ route('home') }}">Trang chu</a></li>
                        <li><span>Ve chung toi</span></li>
                    </ul>

                    <span class="about-eyebrow">Public showroom</span>
                    <h1 class="about-title">{{ $showroomName }}</h1>
                    <p class="about-text">{{ $showroomDescription }}</p>

                    <div class="about-actions">
                        <a href="{{ route('inventory.index') }}" class="about-action-link alt">Xem kho xe</a>
                        <a href="{{ route('contact') }}" class="about-action-link">Lien he showroom</a>
                    </div>

                    <div class="about-stat-grid">
                        <div class="about-stat">
                            <strong>{{ number_format($stats['cars_for_sale']) }}</strong>
                            <span>Xe dang san sang ban</span>
                        </div>
                        <div class="about-stat">
                            <strong>{{ number_format($stats['trims']) }}</strong>
                            <span>Phien ban trong catalog</span>
                        </div>
                        <div class="about-stat">
                            <strong>{{ number_format($stats['reviews']) }}</strong>
                            <span>Danh gia tu khach da mua</span>
                        </div>
                        <div class="about-stat">
                            <strong>{{ number_format($stats['leads']) }}</strong>
                            <span>Lead da duoc tiep nhan</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-panel">
                <div class="about-panel-inner">
                    <h2 class="about-section-title">Thong tin lien he va van hanh</h2>
                    <p class="about-text">Trang nay tom tat cach showroom van hanh tren public site: inventory -> lead -> appointment -> sale -> review.</p>

                    <div class="about-info-stack" style="margin-top: 22px;">
                        <div class="about-info-card">
                            <label>Dia chi</label>
                            <strong>{{ $showroomAddress }}</strong>
                            <span>Thong tin dia diem duoc hien xuyen suot o header, footer va trang lien he.</span>
                        </div>
                        <div class="about-info-card">
                            <label>Hotline</label>
                            <strong>{{ $showroomPhone }}</strong>
                            <span>Khach co the de lai lead online hoac goi truc tiep de duoc xac nhan nhanh hon.</span>
                        </div>
                        <div class="about-info-card">
                            <label>Email</label>
                            <strong>{{ $showroomEmail }}</strong>
                            <span>Kenh nhan xac nhan, ho tro tai chinh va theo doi cac yeu cau phat sinh.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-grid">
            <div class="about-panel">
                <div class="about-panel-inner">
                    <h2 class="about-section-title">Showroom nay tap trung vao dieu gi?</h2>
                    <p class="about-text">Muc tieu chinh cua public site la giup nguoi dung tim duoc xe phu hop, de lai dung context va duoc doi sale xu ly khong mat thong tin.</p>

                    <div class="about-checklist">
                        <div class="about-check">
                            <span class="about-check-index">1</span>
                            <div>
                                <strong>Kho xe ro rang va de loc</strong>
                                <span>Xe dang ban, phien ban, gia, nam san xuat va cac thuoc tinh chinh duoc hien minh bach de quyet dinh nhanh hon.</span>
                            </div>
                        </div>
                        <div class="about-check">
                            <span class="about-check-index">2</span>
                            <div>
                                <strong>Lead di vao CRM co context</strong>
                                <span>Moi form lien he, tai chinh hay trade-in deu co the gan voi xe hoac phien ban cu the de doi sale lam viec nhanh.</span>
                            </div>
                        </div>
                        <div class="about-check">
                            <span class="about-check-index">3</span>
                            <div>
                                <strong>Toan bo giao dich van xu ly offline</strong>
                                <span>Website khong checkout. Viec dat coc, hop dong va giao xe duoc chot tai showroom theo quy trinh thuc te.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-panel">
                <div class="about-panel-inner">
                    <h2 class="about-section-title">Gia tri van hanh ma khach nhin thay</h2>
                    <p class="about-text">Khong chi la mot landing page, day la diem tiep nhan nhu cau thuc te de chuyen thanh lich hen va sale.</p>

                    <div class="about-note-grid" style="margin-top: 22px;">
                        <div class="about-note">
                            <strong>Thong tin minh bach</strong>
                            <span>Ma xe, tinh trang, gia va context trim duoc hien ro de giam sai lech khi tu van.</span>
                        </div>
                        <div class="about-note">
                            <strong>Phan loai lead dung luc</strong>
                            <span>Lien he chung, tai chinh va thu cu doi moi duoc tach luong nhung van dung chung mot bo du lieu.</span>
                        </div>
                        <div class="about-note">
                            <strong>Danh gia sau mua</strong>
                            <span>Review chi mo cho nguoi da co sale hop le, giup phan hoi tren site co gia tri tham khao thuc su.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-media-grid">
            <div class="about-panel">
                <div class="about-panel-inner">
                    <h2 class="about-section-title">Khong gian va hinh anh showroom</h2>
                    <p class="about-text">Mot page gioi thieu can cho thay cam giac ve thuong hieu, nhung van giu trong tam la kha nang chuyen doi sang lead va lich hen.</p>

                    <div class="about-image-grid" style="margin-top: 22px;">
                        <img src="{{ asset('boxcar/images/resource/about-inner1-2.jpg') }}" alt="Showroom exterior">
                        <div class="stacked">
                            <img src="{{ asset('boxcar/images/resource/about-inner1-3.jpg') }}" alt="Showroom inventory">
                            <img src="{{ asset('boxcar/images/resource/about-inner1-5.jpg') }}" alt="Showroom experience">
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-panel">
                <div class="about-panel-inner">
                    <h2 class="about-section-title">Nhung gi khach co the lam ngay tu day</h2>
                    <p class="about-text">Noi dung gioi thieu khong nen bi tach roi khoi hanh dong. Sau khi doc xong, khach co the di tiep den kho xe hoac gui yeu cau ngay.</p>

                    <div class="about-feature-grid" style="margin-top: 22px;">
                        <div class="about-feature">
                            <strong>Xem kho xe theo nhu cau</strong>
                            <span>Loc theo tinh trang, trim, nam, gia, odo va vao thang trang chi tiet xe.</span>
                        </div>
                        <div class="about-feature">
                            <strong>Gui yeu cau tai chinh</strong>
                            <span>De lai muc tieu tai chinh tren dung mau xe ban dang can nhac de sales tu van sat hon.</span>
                        </div>
                        <div class="about-feature">
                            <strong>Thu cu doi moi</strong>
                            <span>Mo ta xe dang su dung va ky vong doi sang xe moi de doi sale co du context tham dinh.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-panel about-cta">
            <div class="about-panel-inner">
                <h2 class="about-section-title">San sang bat dau tu nhu cau cu the?</h2>
                <p class="about-text">Neu ban da co xe dang quan tam, hay vao kho xe de loc nhanh. Neu ban muon duoc goi lai, dung form lien he hoac tai chinh de tao lead ngay tren public site.</p>

                <div class="about-actions">
                    <a href="{{ route('inventory.index') }}" class="about-action-link alt">Di den kho xe</a>
                    <a href="{{ route('contact') }}" class="about-action-link">Gui yeu cau lien he</a>
                    <a href="{{ route('finance') }}" class="about-action-link">Tu van tai chinh</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
