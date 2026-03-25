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

@push('styles')
<style>
    #inventory-app {
        position: relative;
        --inventory-accent: #405ff2;
    }

    #inventory-content {
        position: relative;
    }

    .inventory-loading-live {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .inventory-results-stage {
        position: relative;
        margin-top: 18px;
        padding-top: 10px;
    }

    .inventory-results-body {
        position: relative;
        z-index: 1;
        transition: opacity 0.18s ease;
    }

    #inventory-app.is-loading .inventory-results-body {
        opacity: 0.52;
    }

    .inventory-loading-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        z-index: 2;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(64, 95, 242, 0.08);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.18s ease, visibility 0.18s ease;
    }

    #inventory-app.is-loading .inventory-loading-bar {
        opacity: 1;
        visibility: visible;
    }

    .inventory-loading-bar__inner {
        display: block;
        width: 34%;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, rgba(126, 147, 255, 0.9) 0%, rgba(64, 95, 242, 1) 100%);
        animation: inventoryLoadingBar 1.2s ease-in-out infinite;
    }

    .inventory-loading-status {
        position: absolute;
        top: 18px;
        right: 0;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid rgba(64, 95, 242, 0.12);
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 12px 28px rgba(5, 11, 32, 0.08);
        backdrop-filter: blur(10px);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-6px);
        pointer-events: none;
        transition: opacity 0.18s ease, visibility 0.18s ease, transform 0.18s ease;
    }

    #inventory-app.is-loading .inventory-loading-status {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .inventory-loading-status__spinner {
        flex: 0 0 auto;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(64, 95, 242, 0.14);
        border-top-color: var(--inventory-accent);
        border-radius: 50%;
        animation: inventoryAjaxSpin 0.7s linear infinite;
    }

    .inventory-loading-status__text {
        color: #050b20;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.2;
    }

    @keyframes inventoryAjaxSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes inventoryLoadingBar {
        0% {
            transform: translateX(-120%);
        }

        100% {
            transform: translateX(320%);
        }
    }

    @media (max-width: 991.98px) {
        .inventory-results-stage {
            padding-top: 44px;
        }

        .inventory-loading-status {
            left: 0;
            right: auto;
        }
    }

    @media (max-width: 575.98px) {
        .inventory-results-stage {
            margin-top: 14px;
            padding-top: 48px;
        }
    }

    .cars-section-four .form-box .form_boxes .drop-menu .select {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        line-height: 1.2;
    }

    .cars-section-four .form-box .form_boxes .drop-menu .select span {
        flex: 1 1 auto;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cars-section-four .form-box .form_boxes .drop-menu .select i {
        flex: 0 0 auto;
        margin-left: auto;
        position: static;
        right: auto;
        float: none;
        color: #5b6479;
    }
</style>
@endpush

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
        (object) ['value' => 'mileage_asc', 'label' => 'Odo thap den cao'],
        (object) ['value' => 'mileage_desc', 'label' => 'Odo cao den thap'],
    ]);
@endphp

<div id="inventory-app">
<div class="inventory-loading-live" role="status" aria-live="polite"></div>
<div id="inventory-content">
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
                <input type="hidden" name="trim" value="{{ request('trim', '') }}">
                <input type="hidden" name="year" value="{{ request('year', '') }}">
                <input type="hidden" name="min_year" value="{{ request('min_year', '') }}">
                <input type="hidden" name="max_year" value="{{ request('max_year', '') }}">
                <input type="hidden" name="transmission" value="{{ request('transmission', '') }}">
                <input type="hidden" name="drivetrain" value="{{ request('drivetrain', '') }}">
                <input type="hidden" name="exterior_color" value="{{ request('exterior_color', '') }}">
                <input type="hidden" name="interior_color" value="{{ request('interior_color', '') }}">
                <input type="hidden" name="min_mileage" value="{{ request('min_mileage', '') }}">
                <input type="hidden" name="max_mileage" value="{{ request('max_mileage', '') }}">
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
                        'name' => 'min_year',
                        'options' => $filters['years'],
                        'selectedValue' => request('min_year', ''),
                        'valueField' => 'value',
                        'labelField' => 'label',
                        'emptyLabel' => 'Nam tu',
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
                    <input type="hidden" name="trim" value="{{ request('trim', '') }}">
                    <input type="hidden" name="year" value="{{ request('year', '') }}">
                    <input type="hidden" name="min_year" value="{{ request('min_year', '') }}">
                    <input type="hidden" name="max_year" value="{{ request('max_year', '') }}">
                    <input type="hidden" name="fuel_type" value="{{ request('fuel_type', '') }}">
                    <input type="hidden" name="transmission" value="{{ request('transmission', '') }}">
                    <input type="hidden" name="drivetrain" value="{{ request('drivetrain', '') }}">
                    <input type="hidden" name="exterior_color" value="{{ request('exterior_color', '') }}">
                    <input type="hidden" name="interior_color" value="{{ request('interior_color', '') }}">
                    <input type="hidden" name="min_mileage" value="{{ request('min_mileage', '') }}">
                    <input type="hidden" name="max_mileage" value="{{ request('max_mileage', '') }}">
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
        <div class="inventory-results-stage">
            <div class="inventory-loading-bar" aria-hidden="true">
                <span class="inventory-loading-bar__inner"></span>
            </div>
            <div class="inventory-loading-status" aria-hidden="true">
                <span class="inventory-loading-status__spinner"></span>
                <span class="inventory-loading-status__text">Dang cap nhat ket qua</span>
            </div>
            <div class="inventory-results-body">
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
        </div>
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
                        <div class="col-lg-12">
                            <div class="form_boxes">
                                <label>Phien ban</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'trim',
                                    'options' => $filters['trims'],
                                    'selectedValue' => request('trim', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tat ca phien ban',
                                ])
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Nam tu</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'min_year',
                                    'options' => $filters['years'],
                                    'selectedValue' => request('min_year', ''),
                                    'valueField' => 'value',
                                    'labelField' => 'label',
                                    'emptyLabel' => 'Tat ca',
                                ])
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Nam den</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'max_year',
                                    'options' => $filters['years'],
                                    'selectedValue' => request('max_year', ''),
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
                            <div class="form_boxes">
                                <label>Dan dong</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'drivetrain',
                                    'options' => $filters['drivetrains'],
                                    'selectedValue' => request('drivetrain', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tat ca dan dong',
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
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Odo tu</label>
                                <input type="number" name="min_mileage" value="{{ request('min_mileage', '') }}" placeholder="0">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Odo den</label>
                                <input type="number" name="max_mileage" value="{{ request('max_mileage', '') }}" placeholder="0">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Mau ngoai that</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'exterior_color',
                                    'options' => $filters['colors'],
                                    'selectedValue' => request('exterior_color', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tat ca mau',
                                ])
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form_boxes">
                                <label>Mau noi that</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'interior_color',
                                    'options' => $filters['colors'],
                                    'selectedValue' => request('interior_color', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tat ca mau',
                                ])
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
</div>
</div>
@endsection

@push('scripts')
<script>
    (function ($) {
        var $inventoryApp = $('#inventory-app');
        var $inventoryContent = $('#inventory-content');
        var $inventoryLoadingLive = $inventoryApp.find('.inventory-loading-live');

        if (!$inventoryApp.length || !$inventoryContent.length) {
            return;
        }

        var pendingRequest = null;
        var latestRequestId = 0;

        var setLoading = function (isLoading) {
            $inventoryApp.toggleClass('is-loading', isLoading);

            if (isLoading) {
                $inventoryApp.attr('aria-busy', 'true');
                $inventoryLoadingLive.text('Dang cap nhat ket qua loc xe.');
                return;
            }

            $inventoryApp.removeAttr('aria-busy');
            $inventoryLoadingLive.text('');
        };

        var closeInventorySidebar = function () {
            $inventoryContent.find('.wrap-fixed-sidebar').removeClass('active');
        };

        var bindInventoryDropdownOptions = function () {
            $inventoryContent.find('.drop-menu .dropdown li').off('click');
            $inventoryContent.find('.drop-menu .dropdown li').off('click.inventoryOption');

            $inventoryContent.find('.drop-menu .dropdown li').on('click.inventoryOption', function (event) {
                var $option = $(this);
                var $menu = $option.closest('.drop-menu');
                var $form = $menu.closest('form');
                var optionValue = $option.data('value');
                var submitOnSelect = $menu.data('auto-submit') === true || $menu.data('auto-submit') === 'true';

                if (typeof optionValue === 'undefined') {
                    optionValue = $option.attr('id');
                }

                $menu.children('.select').find('span').first().text($option.text()).addClass('selected');
                $menu.find('input').first().val(optionValue === undefined ? '' : optionValue).attr('value', optionValue === undefined ? '' : optionValue);
                $menu.removeClass('active');
                $menu.children('.dropdown').stop(true, true).slideUp(150);

                if ($form.length && submitOnSelect) {
                    window.setTimeout(function () {
                        $form.trigger('submit');
                    }, 0);
                }

                event.preventDefault();
                event.stopImmediatePropagation();
                return false;
            });
        };

        var buildInventoryUrlFromForm = function ($form) {
            var action = $form.attr('action') || window.location.href;
            var queryParts = [];

            $.each($form.serializeArray(), function (_index, field) {
                var normalizedValue = $.trim(field.value || '');

                if (normalizedValue === '') {
                    return;
                }

                queryParts.push({
                    name: field.name,
                    value: normalizedValue,
                });
            });

            var queryString = $.param(queryParts);

            if (!queryString) {
                return action.split('?')[0];
            }

            return action.split('?')[0] + '?' + queryString;
        };

        var buildInventoryUrl = function (form) {
            return buildInventoryUrlFromForm($(form));
        };

        var syncInventoryState = function (html, requestUrl, options) {
            var $response = $('<div>').append($.parseHTML(html, document, true));
            var $nextContent = $response.find('#inventory-content').first();
            var titleMatch = html.match(/<title>(.*?)<\/title>/i);

            if (!$nextContent.length) {
                window.location.assign(requestUrl);
                return;
            }

            $inventoryContent.html($nextContent.html());

            if (titleMatch && titleMatch[1]) {
                document.title = $('<textarea/>').html(titleMatch[1]).text();
            }

            if (options.pushState) {
                window.history.pushState({ inventoryAjax: true }, '', requestUrl);
            }

            closeInventorySidebar();
            $('.drop-menu').removeClass('active');
            $('.form_boxes .dropdown').hide();
            bindInventoryDropdownOptions();

            if (options.scrollToResults) {
                var $listingSection = $inventoryContent.find('.cars-section-four').first();

                if ($listingSection.length) {
                    var targetTop = $listingSection.offset().top - 120;

                    $('html, body').stop(true).animate({
                        scrollTop: Math.max(targetTop, 0),
                    }, 280);
                }
            }
        };

        var loadInventory = function (requestUrl, options) {
            var mergedOptions = $.extend({
                pushState: true,
                scrollToResults: false,
            }, options || {});
            var requestId = ++latestRequestId;

            if (pendingRequest) {
                pendingRequest.abort();
            }

            setLoading(true);

            pendingRequest = $.ajax({
                url: requestUrl,
                type: 'GET',
                dataType: 'html',
                cache: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Inventory-Ajax': 'true',
                },
            })
                .done(function (html) {
                    if (requestId !== latestRequestId) {
                        return;
                    }

                    syncInventoryState(html, requestUrl, mergedOptions);
                })
                .fail(function (_xhr, status) {
                    if (status === 'abort') {
                        return;
                    }

                    window.location.assign(requestUrl);
                })
                .always(function () {
                    if (requestId === latestRequestId) {
                        setLoading(false);
                    }

                    pendingRequest = null;
                });
        };

        $(document).on('click.inventoryAjax', '#inventory-content .filter-popup', function (event) {
            event.preventDefault();
            $inventoryContent.find('.wrap-fixed-sidebar').addClass('active');
        });

        $(document).on('click.inventoryAjax', '#inventory-content .close-filters, #inventory-content .sidebar-backdrop', function (event) {
            event.preventDefault();
            closeInventorySidebar();
        });

        $(document).on('submit.inventoryAjax', '#inventory-content form', function (event) {
            if ((this.method || 'get').toLowerCase() !== 'get') {
                return;
            }

            event.preventDefault();

            var shouldScroll = $(this).closest('.inventory-sidebar').length > 0;
            var requestUrl = buildInventoryUrl(this);

            loadInventory(requestUrl, {
                pushState: true,
                scrollToResults: shouldScroll,
            });
        });

        $(document).on('click.inventoryAjax', '#inventory-content .pagination-sec a.page-link', function (event) {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || this.target === '_blank') {
                return;
            }

            event.preventDefault();

            loadInventory(this.href, {
                pushState: true,
                scrollToResults: true,
            });
        });

        window.addEventListener('popstate', function () {
            if (!document.getElementById('inventory-app')) {
                return;
            }

            loadInventory(window.location.href, {
                pushState: false,
                scrollToResults: false,
            });
        });

        bindInventoryDropdownOptions();
    })(window.jQuery);
</script>
@endpush
