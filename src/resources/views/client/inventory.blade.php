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

@section('title', $pageTitle)

@section('content')
@php
    $inventoryAction = route('inventory.index');
    $conditionOptions = collect([
        (object) ['value' => '', 'label' => 'Tat ca tinh trang'],
        (object) ['value' => 'new', 'label' => 'Xe moi'],
        (object) ['value' => 'used', 'label' => 'Xe cu'],
        (object) ['value' => 'cpo', 'label' => 'Xe CPO'],
    ]);
    $sortOptions = collect([
        (object) ['value' => 'newest', 'label' => 'Moi cap nhat'],
        (object) ['value' => 'price_asc', 'label' => 'Gia thap den cao'],
        (object) ['value' => 'price_desc', 'label' => 'Gia cao den thap'],
        (object) ['value' => 'year_desc', 'label' => 'Nam moi nhat'],
        (object) ['value' => 'year_asc', 'label' => 'Nam cu nhat'],
    ]);
    $statusOptions = collect([
        (object) ['value' => 'available', 'label' => 'Dang ban'],
        (object) ['value' => 'on_hold', 'label' => 'Dang giu cho'],
        (object) ['value' => 'all', 'label' => 'Tat ca'],
    ]);
@endphp

<section class="cars-section-four v1 layout-radius">
    <div class="boxcar-container">
        <div class="boxcar-title-three wow fadeInUp">
            <ul class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chu</a></li>
                <li><span>{{ $pageTitle }}</span></li>
            </ul>
            <h2>{{ $pageTitle }}</h2>
            <form class="form-box" method="GET" action="{{ $inventoryAction }}">
                <input type="hidden" name="q" value="{{ request('q', '') }}">
                <input type="hidden" name="status" value="{{ request('status', 'available') }}">
                <input type="hidden" name="transmission" value="{{ request('transmission', '') }}">
                <input type="hidden" name="min_price" value="{{ request('min_price', '') }}">
                <input type="hidden" name="max_price" value="{{ request('max_price', '') }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
                <div class="form_boxes">
                    @include('client.partials.form.custom-dropdown', [
                        'name' => 'condition',
                        'options' => $conditionOptions,
                        'selectedValue' => request('condition', $currentCondition ?? ''),
                        'valueField' => 'value',
                        'labelField' => 'label',
                        'emptyLabel' => 'Tat ca tinh trang',
                        'includeEmptyOption' => false,
                        'autoSubmit' => true,
                    ])
                </div>
                <div class="form_boxes">
                    @include('client.partials.form.custom-dropdown', [
                        'name' => 'body_type',
                        'options' => $filters['bodyTypes'],
                        'selectedValue' => request('body_type', ''),
                        'valueField' => 'slug',
                        'labelField' => 'name',
                        'emptyLabel' => 'Kieu dang',
                        'autoSubmit' => true,
                    ])
                </div>
                <div class="form_boxes">
                    @include('client.partials.form.custom-dropdown', [
                        'name' => 'make',
                        'options' => $filters['makes'],
                        'selectedValue' => request('make', ''),
                        'valueField' => 'slug',
                        'labelField' => 'name',
                        'emptyLabel' => 'Hang xe',
                        'autoSubmit' => true,
                    ])
                </div>
                <div class="form_boxes">
                    @include('client.partials.form.custom-dropdown', [
                        'name' => 'model',
                        'options' => $filters['models'],
                        'selectedValue' => request('model', ''),
                        'valueField' => 'slug',
                        'labelField' => 'name',
                        'emptyLabel' => 'Dong xe',
                        'autoSubmit' => true,
                    ])
                </div>
                <div class="form_boxes">
                    @include('client.partials.form.custom-dropdown', [
                        'name' => 'year',
                        'options' => $filters['years'],
                        'selectedValue' => request('year', ''),
                        'valueField' => 'value',
                        'labelField' => 'label',
                        'emptyLabel' => 'Nam san xuat',
                        'autoSubmit' => true,
                    ])
                </div>
                <div class="form_boxes">
                    @include('client.partials.form.custom-dropdown', [
                        'name' => 'fuel_type',
                        'options' => $filters['fuelTypes'],
                        'selectedValue' => request('fuel_type', ''),
                        'valueField' => 'slug',
                        'labelField' => 'name',
                        'emptyLabel' => 'Nhien lieu',
                        'autoSubmit' => true,
                    ])
                </div>
                <div class="form_boxes">
                    <a href="#" title="" class="filter-btn filter-popup"><img src="{{ asset('boxcar/images/icons/filter.svg') }}" alt="filter" /> Bo loc nang cao</a>
                </div>
            </form>
            <div class="text-box v1">
                <div class="text">Hien thi {{ $cars->firstItem() ?? 0 }} den {{ $cars->lastItem() ?? 0 }} trong tong {{ $cars->total() }} xe</div>
                <form method="GET" action="{{ $inventoryAction }}">
                    <input type="hidden" name="q" value="{{ request('q', '') }}">
                    <input type="hidden" name="condition" value="{{ request('condition', $currentCondition ?? '') }}">
                    <input type="hidden" name="body_type" value="{{ request('body_type', '') }}">
                    <input type="hidden" name="make" value="{{ request('make', '') }}">
                    <input type="hidden" name="model" value="{{ request('model', '') }}">
                    <input type="hidden" name="year" value="{{ request('year', '') }}">
                    <input type="hidden" name="fuel_type" value="{{ request('fuel_type', '') }}">
                    <input type="hidden" name="transmission" value="{{ request('transmission', '') }}">
                    <input type="hidden" name="status" value="{{ request('status', 'available') }}">
                    <input type="hidden" name="min_price" value="{{ request('min_price', '') }}">
                    <input type="hidden" name="max_price" value="{{ request('max_price', '') }}">
                    <div class="form_boxes v3">
                        <small>Sap xep theo</small>
                        @include('client.partials.form.custom-dropdown', [
                            'name' => 'sort',
                            'options' => $sortOptions,
                            'selectedValue' => request('sort', 'newest'),
                            'valueField' => 'value',
                            'labelField' => 'label',
                            'emptyLabel' => 'Moi cap nhat',
                            'includeEmptyOption' => false,
                            'autoSubmit' => true,
                        ])
                    </div>
                </form>
            </div>
        </div>
        <div class="row wow fadeInUp">
            @forelse ($cars as $car)
                @include('client.partials.inventory-card', ['car' => $car])
            @empty
                <div class="col-12">
                    <div class="alert alert-light">Khong co xe nao phu hop voi bo loc hien tai.</div>
                </div>
            @endforelse
        </div>
        @include('client.partials.boxcar-pagination', [
            'paginator' => $cars,
            'window' => \Illuminate\Pagination\UrlWindow::make($cars),
        ])
    </div>
</section>

<div class="wrap-fixed-sidebar">
    <div class="sidebar-backdrop"></div>
    <div class="widget-sidebar-filter">
        <div class="fixed-sidebar-title">
            <h3>Bo loc nang cao</h3>
            <a href="#" title="" class="close-filters"><img src="{{ asset('boxcar/images/icons/close.svg') }}" alt="close" /></a>
        </div>
        <div class="inventory-sidebar">
            <form method="GET" action="{{ $inventoryAction }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
                <div class="inventroy-widget widget-location">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form_boxes">
                                <label>Tu khoa tim kiem</label>
                                <input type="text" name="q" value="{{ request('q', '') }}" placeholder="Hang xe, dong xe, ma xe">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form_boxes">
                                <label>Tinh trang</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'condition',
                                    'options' => $conditionOptions,
                                    'selectedValue' => request('condition', $currentCondition ?? ''),
                                    'valueField' => 'value',
                                    'labelField' => 'label',
                                    'emptyLabel' => 'Tat ca tinh trang',
                                    'includeEmptyOption' => false,
                                ])
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="categories-box">
                                <h6 class="title">Trang thai</h6>
                                <div class="cheak-box">
                                    @foreach ($statusOptions as $statusOption)
                                        <label class="contain">{{ $statusOption->label }}
                                            <input type="radio" name="status" value="{{ $statusOption->value }}" {{ request('status', 'available') === $statusOption->value ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form_boxes">
                                <label>Hang xe</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'make',
                                    'options' => $filters['makes'],
                                    'selectedValue' => request('make', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tat ca hang xe',
                                ])
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form_boxes">
                                <label>Dong xe</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'model',
                                    'options' => $filters['models'],
                                    'selectedValue' => request('model', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tat ca dong xe',
                                ])
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Nam</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'year',
                                    'options' => $filters['years'],
                                    'selectedValue' => request('year', ''),
                                    'valueField' => 'value',
                                    'labelField' => 'label',
                                    'emptyLabel' => 'Tat ca',
                                ])
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Kieu dang</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'body_type',
                                    'options' => $filters['bodyTypes'],
                                    'selectedValue' => request('body_type', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tat ca',
                                ])
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="categories-box border-none-bottom">
                                <h6 class="title">Hop so</h6>
                                <div class="cheak-box">
                                    @foreach ($filters['transmissions'] as $transmission)
                                        <label class="contain">{{ $transmission->name }}
                                            <input type="radio" name="transmission" value="{{ $transmission->slug }}" {{ request('transmission') === $transmission->slug ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    @endforeach
                                    <label class="contain">Tat ca
                                        <input type="radio" name="transmission" value="" {{ request('transmission', '') === '' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="categories-box border-none-bottom">
                                <h6 class="title">Nhien lieu</h6>
                                <div class="cheak-box">
                                    @foreach ($filters['fuelTypes'] as $fuelType)
                                        <label class="contain">{{ $fuelType->name }}
                                            <input type="radio" name="fuel_type" value="{{ $fuelType->slug }}" {{ request('fuel_type') === $fuelType->slug ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    @endforeach
                                    <label class="contain">Tat ca
                                        <input type="radio" name="fuel_type" value="" {{ request('fuel_type', '') === '' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Gia tu</label>
                                <input type="number" name="min_price" value="{{ request('min_price', '') }}" placeholder="0">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Gia den</label>
                                <input type="number" name="max_price" value="{{ request('max_price', '') }}" placeholder="0">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-submit">
                                <button type="submit" class="theme-btn">Ap dung bo loc<img src="{{ asset('boxcar/images/arrow.svg') }}" alt="arrow"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
