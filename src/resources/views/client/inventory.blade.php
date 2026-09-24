@extends('client.layouts.page')

@section('title', $pageTitle)

@section('content')
@php
    $inventoryAction = route('inventory.index');
    $conditionOptions = collect([
        (object) ['value' => '', 'label' => 'Tất cả tình trạng'],
        (object) ['value' => 'new', 'label' => 'Xe mới 100%'],
        (object) ['value' => 'used', 'label' => 'Xe siêu lướt'],
        (object) ['value' => 'cpo', 'label' => 'Xe CPO kiểm định'],
    ]);
    $priceRangeOptions = collect([
        (object) ['value' => '', 'label' => 'Tất cả mức giá'],
        (object) ['value' => 'under_800', 'label' => 'Dưới 800 triệu'],
        (object) ['value' => '800_1500', 'label' => '800 triệu - 1.5 tỷ'],
        (object) ['value' => '1500_2500', 'label' => '1.5 - 2.5 tỷ'],
        (object) ['value' => 'over_2500', 'label' => 'Trên 2.5 tỷ'],
    ]);
    $sortOptions = collect([
        (object) ['value' => 'newest', 'label' => 'Mới cập nhật nhất'],
        (object) ['value' => 'price_asc', 'label' => 'Giá: Thấp đến cao'],
        (object) ['value' => 'price_desc', 'label' => 'Giá: Cao đến thấp'],
        (object) ['value' => 'year_desc', 'label' => 'Năm sản xuất: Mới nhất'],
        (object) ['value' => 'year_asc', 'label' => 'Năm sản xuất: Cũ nhất'],
        (object) ['value' => 'mileage_asc', 'label' => 'Số Km (Odo): Thấp nhất'],
        (object) ['value' => 'mileage_desc', 'label' => 'Số Km (Odo): Cao nhất'],
    ]);

    $activeChips = [];

    if (request('q')) {
        $activeChips[] = [
            'label' => 'Từ khóa: "' . request('q') . '"',
            'url' => request()->fullUrlWithQuery(['q' => null, 'page' => null]),
        ];
    }

    if (request('condition')) {
        $condLabel = match (request('condition')) {
            'new' => 'Xe mới 100%',
            'used' => 'Xe siêu lướt',
            'cpo' => 'Xe CPO kiểm định',
            default => request('condition'),
        };
        $activeChips[] = [
            'label' => $condLabel,
            'url' => request()->fullUrlWithQuery(['condition' => null, 'page' => null]),
        ];
    }

    if (request('price_range')) {
        $prLabel = match (request('price_range')) {
            'under_800' => 'Dưới 800 triệu',
            '800_1500' => '800 triệu - 1.5 tỷ',
            '1500_2500' => '1.5 - 2.5 tỷ',
            'over_2500' => 'Trên 2.5 tỷ',
            default => request('price_range'),
        };
        $activeChips[] = [
            'label' => 'Giá: ' . $prLabel,
            'url' => request()->fullUrlWithQuery(['price_range' => null, 'page' => null]),
        ];
    }

    if (request('make')) {
        $makeObj = $filters['makes']->firstWhere('slug', request('make'));
        $activeChips[] = [
            'label' => 'Hãng: ' . ($makeObj->name ?? request('make')),
            'url' => request()->fullUrlWithQuery(['make' => null, 'model' => null, 'trim' => null, 'page' => null]),
        ];
    }

    if (request('model')) {
        $modelObj = $filters['models']->firstWhere('slug', request('model'));
        $activeChips[] = [
            'label' => 'Dòng xe: ' . ($modelObj->name ?? request('model')),
            'url' => request()->fullUrlWithQuery(['model' => null, 'trim' => null, 'page' => null]),
        ];
    }

    if (request('trim')) {
        $trimObj = $filters['trims']->firstWhere('slug', request('trim'));
        $activeChips[] = [
            'label' => 'Phiên bản: ' . ($trimObj->name ?? request('trim')),
            'url' => request()->fullUrlWithQuery(['trim' => null, 'page' => null]),
        ];
    }

    if (request('body_type')) {
        $bodyObj = $filters['bodyTypes']->firstWhere('slug', request('body_type'));
        $activeChips[] = [
            'label' => 'Kiểu dáng: ' . ($bodyObj->name ?? request('body_type')),
            'url' => request()->fullUrlWithQuery(['body_type' => null, 'page' => null]),
        ];
    }

    if (request('fuel_type')) {
        $fuelObj = $filters['fuelTypes']->firstWhere('slug', request('fuel_type'));
        $activeChips[] = [
            'label' => 'Nhiên liệu: ' . ($fuelObj->name ?? request('fuel_type')),
            'url' => request()->fullUrlWithQuery(['fuel_type' => null, 'page' => null]),
        ];
    }

    if (request('transmission')) {
        $transObj = $filters['transmissions']->firstWhere('slug', request('transmission'));
        $activeChips[] = [
            'label' => 'Hộp số: ' . ($transObj->name ?? request('transmission')),
            'url' => request()->fullUrlWithQuery(['transmission' => null, 'page' => null]),
        ];
    }

    if (request('drivetrain')) {
        $dtObj = $filters['drivetrains']->firstWhere('slug', request('drivetrain'));
        $activeChips[] = [
            'label' => 'Dẫn động: ' . ($dtObj->name ?? request('drivetrain')),
            'url' => request()->fullUrlWithQuery(['drivetrain' => null, 'page' => null]),
        ];
    }

    if (request('min_year') || request('max_year')) {
        $yearLabel = 'Năm: ' . (request('min_year') ?: '...') . ' - ' . (request('max_year') ?: '...');
        $activeChips[] = [
            'label' => $yearLabel,
            'url' => request()->fullUrlWithQuery(['min_year' => null, 'max_year' => null, 'page' => null]),
        ];
    }

    if (request('min_price') || request('max_price')) {
        $formatPriceChip = function ($val) {
            if (! $val) {
                return '';
            }
            $num = (int) $val;
            if ($num < 100000) {
                $num *= 1000000;
            }
            if ($num >= 1000000000) {
                return ($num / 1000000000) . ' tỷ';
            }

            return ($num / 1000000) . ' tr';
        };
        $priceLabel = 'Giá: ' . ($formatPriceChip(request('min_price')) ?: '0') . ' - ' . ($formatPriceChip(request('max_price')) ?: 'Tối đa');
        $activeChips[] = [
            'label' => $priceLabel,
            'url' => request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]),
        ];
    }

    if (request('min_mileage') || request('max_mileage')) {
        $mileageLabel = 'Odo: ' . (request('min_mileage') ? number_format((int) request('min_mileage'), 0, ',', '.') : '0') . ' - ' . (request('max_mileage') ? number_format((int) request('max_mileage'), 0, ',', '.') : '...') . ' km';
        $activeChips[] = [
            'label' => $mileageLabel,
            'url' => request()->fullUrlWithQuery(['min_mileage' => null, 'max_mileage' => null, 'page' => null]),
        ];
    }

    if (request('exterior_color')) {
        $colorObj = $filters['colors']->firstWhere('slug', request('exterior_color'));
        $activeChips[] = [
            'label' => 'Màu: ' . ($colorObj->name ?? request('exterior_color')),
            'url' => request()->fullUrlWithQuery(['exterior_color' => null, 'page' => null]),
        ];
    }

    $activeChipsCount = count($activeChips);
    $selectedCondition = request('condition', $currentCondition ?? '');

    $advFilterCount = 0;
    if (request('model')) $advFilterCount++;
    if (request('trim')) $advFilterCount++;
    if (request('min_year') || request('max_year')) $advFilterCount++;
    if (request('fuel_type')) $advFilterCount++;
    if (request('transmission')) $advFilterCount++;
    if (request('drivetrain')) $advFilterCount++;
    if (request('min_mileage') || request('max_mileage')) $advFilterCount++;
    if (request('min_price') || request('max_price')) $advFilterCount++;
    if (request('exterior_color')) $advFilterCount++;
@endphp

<div id="inventory-app">
    <div class="inventory-loading-live" role="status" aria-live="polite"></div>
    <div id="inventory-content">
        <section class="inventory-page-wrap">
            <div class="boxcar-container">
                <!-- 1. Minimal Header & Breadcrumb -->
                <div class="inventory-minimal-header wow fadeInUp">
                    <ul class="client-breadcrumb">
                        <li><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li><span>/</span></li>
                        <li><span>{{ $pageTitle }}</span></li>
                    </ul>
                    <div class="inventory-title-row">
                        <h1 class="inventory-compact-title">
                            {{ $pageTitle }} <span class="inventory-total-count">({{ $cars->total() }} xe sẵn sàng giao)</span>
                        </h1>
                    </div>
                </div>

                <!-- 2. Option 2: Top Horizontal Filter Bar (Băng Lọc Ngang Trải Rộng) -->
                <div class="inventory-top-filter-bar wow fadeInUp">
                    <form method="GET" action="{{ $inventoryAction }}" class="inventory-top-filter-form" id="inventoryMainFilterForm">
                        <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
                        @foreach (['model', 'trim', 'min_year', 'max_year', 'fuel_type', 'transmission', 'drivetrain', 'min_mileage', 'max_mileage', 'min_price', 'max_price', 'exterior_color'] as $advKey)
                            @if (request()->filled($advKey))
                                <input type="hidden" name="{{ $advKey }}" value="{{ request($advKey) }}">
                            @endif
                        @endforeach

                        <!-- Primary Controls Row -->
                        <div class="top-filter-grid">
                            <!-- 1. Tìm kiếm từ khóa -->
                            <div class="top-search-wrap">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="q" value="{{ request('q', '') }}" placeholder="Hãng, dòng xe, mã kho...">
                            </div>

                            <!-- 2. Tình trạng xe -->
                            <div class="form_boxes">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'condition',
                                    'options' => $conditionOptions,
                                    'selectedValue' => $selectedCondition,
                                    'valueField' => 'value',
                                    'labelField' => 'label',
                                    'emptyLabel' => 'Tất cả tình trạng',
                                    'includeEmptyOption' => false,
                                    'autoSubmit' => true,
                                ])
                            </div>

                            <!-- 3. Hãng xe -->
                            <div class="form_boxes">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'make',
                                    'options' => $filters['makes'],
                                    'selectedValue' => request('make', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tất cả hãng xe',
                                    'autoSubmit' => true,
                                ])
                            </div>

                            <!-- 4. Mức giá dự toán -->
                            <div class="form_boxes">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'price_range',
                                    'options' => $priceRangeOptions,
                                    'selectedValue' => request('price_range', ''),
                                    'valueField' => 'value',
                                    'labelField' => 'label',
                                    'emptyLabel' => 'Tất cả mức giá',
                                    'includeEmptyOption' => false,
                                    'autoSubmit' => true,
                                ])
                            </div>

                            <!-- 5. Kiểu dáng -->
                            <div class="form_boxes">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'body_type',
                                    'options' => $filters['bodyTypes'],
                                    'selectedValue' => request('body_type', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tất cả kiểu dáng',
                                    'autoSubmit' => true,
                                ])
                            </div>

                            <!-- 6. Actions: Thêm lọc & Tìm xe -->
                            <div class="top-filter-actions-group">
                                <button type="button" class="btn-more-filters filter-popup" title="Mở bộ lọc chi tiết">
                                    <i class="fa-solid fa-sliders"></i>
                                    <span>Thêm lọc</span>
                                    @if ($advFilterCount > 0)
                                        <span class="badge">{{ $advFilterCount }}</span>
                                    @endif
                                </button>
                                <button type="submit" class="btn-top-submit" title="Tìm kiếm xe">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <span>Tìm xe</span>
                                </button>
                            </div>
                        </div>

                        <!-- Quick Chips Row (1-touch filter chips) -->
                        <div class="top-quick-chips-row">
                            <span class="quick-chip-label"><i class="fa-solid fa-bolt"></i> Lọc nhanh:</span>
                            <a href="{{ route('inventory.index') }}" class="quick-chip {{ empty(request()->except(['page', 'sort'])) ? 'active' : '' }}">Tất cả ({{ $cars->total() }})</a>
                            <a href="{{ request()->fullUrlWithQuery(['condition' => 'new', 'page' => null]) }}" class="quick-chip {{ request('condition') === 'new' ? 'active' : '' }}">Xe mới 100%</a>
                            <a href="{{ request()->fullUrlWithQuery(['condition' => 'used', 'page' => null]) }}" class="quick-chip {{ request('condition') === 'used' ? 'active' : '' }}">Xe siêu lướt</a>
                            <a href="{{ request()->fullUrlWithQuery(['condition' => 'cpo', 'page' => null]) }}" class="quick-chip {{ request('condition') === 'cpo' ? 'active' : '' }}">CPO kiểm định</a>
                            <a href="{{ request()->fullUrlWithQuery(['price_range' => 'under_800', 'min_price' => null, 'max_price' => null, 'page' => null]) }}" class="quick-chip {{ request('price_range') === 'under_800' ? 'active' : '' }}">Dưới 800tr</a>
                            <a href="{{ request()->fullUrlWithQuery(['price_range' => '800_1500', 'min_price' => null, 'max_price' => null, 'page' => null]) }}" class="quick-chip {{ request('price_range') === '800_1500' ? 'active' : '' }}">800tr - 1.5 tỷ</a>
                            <a href="{{ request()->fullUrlWithQuery(['price_range' => '1500_2500', 'min_price' => null, 'max_price' => null, 'page' => null]) }}" class="quick-chip {{ request('price_range') === '1500_2500' ? 'active' : '' }}">1.5 - 2.5 tỷ</a>
                            <a href="{{ request()->fullUrlWithQuery(['price_range' => 'over_2500', 'min_price' => null, 'max_price' => null, 'page' => null]) }}" class="quick-chip {{ request('price_range') === 'over_2500' ? 'active' : '' }}">Trên 2.5 tỷ</a>
                            <a href="{{ request()->fullUrlWithQuery(['body_type' => 'suv', 'page' => null]) }}" class="quick-chip {{ request('body_type') === 'suv' ? 'active' : '' }}">SUV Gầm cao</a>
                            <a href="{{ request()->fullUrlWithQuery(['body_type' => 'sedan', 'page' => null]) }}" class="quick-chip {{ request('body_type') === 'sedan' ? 'active' : '' }}">Sedan Hạng Sang</a>
                        </div>
                    </form>
                </div>

                <!-- 3. Active Filters Chips Strip -->
                @if ($activeChipsCount > 0)
                    <div class="inventory-active-chips wow fadeInUp">
                        @foreach ($activeChips as $chip)
                            <a href="{{ $chip['url'] }}" class="inventory-chip" title="Xóa điều kiện này">
                                <span>{{ $chip['label'] }}</span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endforeach
                        <a href="{{ route('inventory.index') }}" class="inventory-chip-clear-all reset-filters-link">
                            Xóa tất cả ({{ $activeChipsCount }})
                        </a>
                    </div>
                @endif

                <!-- 4. Results Toolbar -->
                <div class="inventory-toolbar-card wow fadeInUp">
                    <p class="inventory-results-count">
                        Hiển thị <strong>{{ $cars->firstItem() ?? 0 }} - {{ $cars->lastItem() ?? 0 }}</strong> trên tổng số <strong>{{ $cars->total() }}</strong> xe sẵn có
                    </p>
                    <div class="inventory-toolbar-actions">
                        <button type="button" class="btn-mobile-filter filter-popup" aria-label="Mở bộ lọc chi tiết">
                            <i class="fa-solid fa-sliders"></i>
                            <span>Bộ lọc chi tiết</span>
                            @if ($advFilterCount > 0)
                                <span class="badge">{{ $advFilterCount }}</span>
                            @endif
                        </button>
                        <form method="GET" action="{{ $inventoryAction }}" class="inventory-sort-form">
                            @foreach (request()->except(['sort', 'page']) as $k => $v)
                                @if (!is_array($v) && $v !== null && $v !== '')
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach
                            <div class="inventory-sort-box">
                                <label for="sortDropdown">Sắp xếp:</label>
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'sort',
                                    'options' => $sortOptions,
                                    'selectedValue' => request('sort', 'newest'),
                                    'valueField' => 'value',
                                    'labelField' => 'label',
                                    'emptyLabel' => 'Mới cập nhật nhất',
                                    'includeEmptyOption' => false,
                                    'autoSubmit' => true,
                                ])
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 5. Results Listing: 4-Columns Grid (Full Width) -->
                <div class="inventory-results-stage">
                    <div class="inventory-loading-bar" aria-hidden="true">
                        <span class="inventory-loading-bar__inner"></span>
                    </div>
                    <div class="inventory-loading-status" aria-hidden="true">
                        <span class="inventory-loading-status__spinner"></span>
                        <span class="inventory-loading-status__text">Đang cập nhật kết quả lọc xe...</span>
                    </div>

                    <div class="inventory-results-body">
                        <div class="row wow fadeInUp">
                            @forelse ($cars as $car)
                                @include('client.partials.inventory-card', [
                                    'car' => $car,
                                    'cardColClass' => 'car-block-four col-xl-3 col-lg-4 col-md-6 col-sm-12',
                                ])
                            @empty
                                <div class="col-12">
                                    <div class="inventory-empty-state">
                                        <div class="inventory-empty-icon">
                                            <i class="fa-solid fa-car-tunnel"></i>
                                        </div>
                                        <h4 class="inventory-empty-title">Không tìm thấy mẫu xe nào phù hợp</h4>
                                        <p class="inventory-empty-desc">
                                            Rất tiếc, hiện tại không có chiếc xe nào đáp ứng toàn bộ tiêu chí lọc của quý khách. Quý khách vui lòng thử điều chỉnh khoảng giá, hãng xe hoặc xóa bớt bộ lọc để có thêm kết quả.
                                        </p>
                                        <a href="{{ route('inventory.index') }}" class="inventory-empty-btn reset-filters-link">
                                            <i class="fa-solid fa-rotate-left"></i> Đặt lại tất cả bộ lọc
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        @include('client.partials.boxcar-pagination', [
                            'paginator' => $cars,
                            'window' => \Illuminate\Pagination\UrlWindow::make($cars),
                        ])
                    </div>
                </div>

                <!-- 6. Bottom Consultation CTA Banner -->
                <div class="inventory-cta-banner wow fadeInUp">
                    <div class="inventory-cta-content">
                        <span class="inventory-cta-badge">DỊCH VỤ ĐẶT XE THEO YÊU CẦU</span>
                        <h3 class="inventory-cta-title">Chưa tìm thấy chiếc xe đúng sở thích của quý khách?</h3>
                        <p class="inventory-cta-desc">
                            Hệ thống đối tác phân phối của BoxCar kết nối hơn 300+ showroom trên toàn quốc. Đội ngũ chuyên viên tư vấn sẽ hỗ trợ tìm kiếm, thẩm định xe 160 bước nghiêm ngặt và giao xe tận nhà theo đúng yêu cầu trong 24 giờ.
                        </p>
                    </div>
                    <div class="inventory-cta-actions">
                        <a href="tel:0900000000" class="inventory-cta-btn-primary">
                            <i class="fa-solid fa-phone-volume"></i> Hotline tư vấn 24/7
                        </a>
                        <a href="{{ route('contact') }}" class="inventory-cta-btn-secondary">
                            <i class="fa-solid fa-calendar-check"></i> Đăng ký tìm xe
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Floating Mobile Filter Trigger Button -->
        <button type="button" class="floating-mobile-filter filter-popup" aria-label="Bộ lọc tìm kiếm">
            <i class="fa-solid fa-sliders"></i>
            <span>Bộ lọc xe</span>
            @if ($activeChipsCount > 0)
                <span class="badge">{{ $activeChipsCount }}</span>
            @endif
        </button>

        <!-- 7. Offcanvas Slide-out Drawer for Deep Filters -->
        <div class="wrap-fixed-sidebar">
            <div class="sidebar-backdrop"></div>
            <div class="widget-sidebar-filter">
                <div class="fixed-sidebar-title">
                    <h3>Bộ lọc chi tiết</h3>
                    <a href="#" title="Đóng bộ lọc" class="close-filters"><i class="fa-solid fa-xmark"></i></a>
                </div>
                <div class="inventory-sidebar">
                    <form method="GET" action="{{ $inventoryAction }}" class="drawer-filter-form" id="inventoryDrawerFilterForm">
                        <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        @if(request('condition')) <input type="hidden" name="condition" value="{{ request('condition') }}"> @endif
                        @if(request('make')) <input type="hidden" name="make" value="{{ request('make') }}"> @endif
                        @if(request('body_type')) <input type="hidden" name="body_type" value="{{ request('body_type') }}"> @endif
                        @if(request('price_range')) <input type="hidden" name="price_range" value="{{ request('price_range') }}"> @endif

                        <!-- Dòng xe -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Dòng xe</label>
                            <div class="form_boxes mb-0">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'model',
                                    'options' => $filters['models'],
                                    'selectedValue' => request('model', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tất cả dòng xe',
                                ])
                            </div>
                        </div>

                        <!-- Phiên bản xe -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Phiên bản xe</label>
                            <div class="form_boxes mb-0">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'trim',
                                    'options' => $filters['trims'],
                                    'selectedValue' => request('trim', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tất cả phiên bản',
                                ])
                            </div>
                        </div>

                        <!-- Năm sản xuất -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Năm sản xuất</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form_boxes mb-0">
                                        @include('client.partials.form.custom-dropdown', [
                                            'name' => 'min_year',
                                            'options' => $filters['years'],
                                            'selectedValue' => request('min_year', ''),
                                            'valueField' => 'value',
                                            'labelField' => 'label',
                                            'emptyLabel' => 'Từ năm',
                                        ])
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form_boxes mb-0">
                                        @include('client.partials.form.custom-dropdown', [
                                            'name' => 'max_year',
                                            'options' => $filters['years'],
                                            'selectedValue' => request('max_year', ''),
                                            'valueField' => 'value',
                                            'labelField' => 'label',
                                            'emptyLabel' => 'Đến năm',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loại nhiên liệu -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Loại nhiên liệu</label>
                            <div class="form_boxes mb-0">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'fuel_type',
                                    'options' => $filters['fuelTypes'],
                                    'selectedValue' => request('fuel_type', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tất cả nhiên liệu',
                                ])
                            </div>
                        </div>

                        <!-- Hộp số -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Hộp số</label>
                            <div class="form_boxes mb-0">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'transmission',
                                    'options' => $filters['transmissions'],
                                    'selectedValue' => request('transmission', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tất cả hộp số',
                                ])
                            </div>
                        </div>

                        <!-- Hệ dẫn động -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Hệ dẫn động</label>
                            <div class="form_boxes mb-0">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'drivetrain',
                                    'options' => $filters['drivetrains'],
                                    'selectedValue' => request('drivetrain', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tất cả hệ dẫn động',
                                ])
                            </div>
                        </div>

                        <!-- Khoảng giá chi tiết -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Khoảng giá tùy chỉnh (VNĐ)</label>
                            <div class="inventory-price-range-inputs">
                                <input type="number" name="min_price" value="{{ request('min_price', '') }}" placeholder="Từ (triệu/VNĐ)">
                                <span class="range-sep">-</span>
                                <input type="number" name="max_price" value="{{ request('max_price', '') }}" placeholder="Đến (triệu/VNĐ)">
                            </div>
                        </div>

                        <!-- Số Km đã đi (Odo) -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Số Km đã đi (Odo)</label>
                            <div class="inventory-price-range-inputs">
                                <input type="number" name="min_mileage" value="{{ request('min_mileage', '') }}" placeholder="Từ (km)">
                                <span class="range-sep">-</span>
                                <input type="number" name="max_mileage" value="{{ request('max_mileage', '') }}" placeholder="Đến (km)">
                            </div>
                        </div>

                        <!-- Màu ngoại thất -->
                        <div class="inventory-filter-group">
                            <label class="inventory-filter-label">Màu ngoại thất</label>
                            <div class="form_boxes mb-0">
                                @include('client.partials.form.custom-dropdown', [
                                    'name' => 'exterior_color',
                                    'options' => $filters['colors'],
                                    'selectedValue' => request('exterior_color', ''),
                                    'valueField' => 'slug',
                                    'labelField' => 'name',
                                    'emptyLabel' => 'Tất cả màu',
                                ])
                            </div>
                        </div>

                        <!-- Action Buttons Drawer -->
                        <div class="inventory-filter-actions">
                            <button type="submit" class="inventory-apply-btn">
                                <i class="fa-solid fa-filter"></i>
                                <span>Áp dụng bộ lọc</span>
                            </button>
                            @if ($activeChipsCount > 0)
                                <a href="{{ route('inventory.index') }}" class="inventory-reset-full-btn reset-filters-link">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    <span>Đặt lại bộ lọc</span>
                                </a>
                            @endif
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
                $inventoryLoadingLive.text('Đang cập nhật kết quả lọc xe...');
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
                var $listingSection = $inventoryContent.find('.inventory-results-stage').first();

                if ($listingSection.length) {
                    var targetTop = $listingSection.offset().top - 100;

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

        // Quick Filter Chips, Active Chips, & Reset Links via AJAX
        $(document).on('click.inventoryAjax', '#inventory-content .quick-chip, #inventory-content .inventory-chip, #inventory-content .reset-filters-link', function (event) {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || this.target === '_blank') {
                return;
            }

            event.preventDefault();

            loadInventory(this.href, {
                pushState: true,
                scrollToResults: false,
            });
        });

        // Open Drawer Filters
        $(document).on('click.inventoryAjax', '#inventory-content .filter-popup', function (event) {
            event.preventDefault();
            $inventoryContent.find('.wrap-fixed-sidebar').addClass('active');
        });

        // Close Drawer Filters
        $(document).on('click.inventoryAjax', '#inventory-content .close-filters, #inventory-content .sidebar-backdrop', function (event) {
            event.preventDefault();
            closeInventorySidebar();
        });

        // Form Submit
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

        // Pagination Click
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
