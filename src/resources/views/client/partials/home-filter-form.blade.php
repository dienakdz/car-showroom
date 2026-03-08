@php
    $selectedMake = collect($makes)->firstWhere('slug', request('make'));
    $selectedBodyType = collect($bodyTypes)->firstWhere('slug', request('body_type'));
    $selectedFuelType = collect($fuelTypes)->firstWhere('slug', request('fuel_type'));
@endphp

<form action="{{ $action }}" method="GET">
    <div class="form_boxes line-r">
        <div class="drop-menu">
            <div class="select">
                <span>{{ $selectedMake->name ?? 'Tất cả hãng' }}</span>
                <i class="fa fa-angle-down"></i>
            </div>
            <input type="hidden" name="make" value="{{ request('make', '') }}">
            <ul class="dropdown" style="display: none;">
                <li data-value="">Tất cả hãng</li>
                @foreach ($makes as $make)
                    <li data-value="{{ $make->slug }}">{{ $make->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="form_boxes line-r">
        <div class="drop-menu">
            <div class="select">
                <span>{{ $selectedBodyType->name ?? 'Tất cả kiểu dáng' }}</span>
                <i class="fa fa-angle-down"></i>
            </div>
            <input type="hidden" name="body_type" value="{{ request('body_type', '') }}">
            <ul class="dropdown" style="display: none;">
                <li data-value="">Tất cả kiểu dáng</li>
                @foreach ($bodyTypes as $bodyType)
                    <li data-value="{{ $bodyType->slug }}">{{ $bodyType->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="form_boxes">
        <div class="drop-menu">
            <div class="select">
                <span>{{ $selectedFuelType->name ?? 'Tất cả nhiên liệu' }}</span>
                <i class="fa fa-angle-down"></i>
            </div>
            <input type="hidden" name="fuel_type" value="{{ request('fuel_type', '') }}">
            <ul class="dropdown" style="display: none;">
                <li data-value="">Tất cả nhiên liệu</li>
                @foreach ($fuelTypes as $fuelType)
                    <li data-value="{{ $fuelType->slug }}">{{ $fuelType->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="form-submit">
        <button type="submit" class="theme-btn"><i class="flaticon-search"></i>{{ $buttonLabel ?? 'Tìm xe ngay' }}</button>
    </div>
</form>
