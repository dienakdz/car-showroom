<div class="{{ $cardColClass ?? 'car-block-four col-xl-4 col-lg-6 col-md-6 col-sm-12' }}">
    <div class="inner-box">
        <div class="image-box{{ $car->condition !== 'new' ? ' two' : '' }}">
            <figure class="image"><a href="{{ route('car.show', $car->stock_code) }}"><img src="{{ $car->image_url }}" alt="{{ $car->make_name }} {{ $car->model_name }}"></a></figure>
            <span>{{ $car->condition_label }}</span>
            <a href="{{ route('car.show', $car->stock_code) }}" class="icon-box" aria-label="Xem {{ $car->make_name }} {{ $car->model_name }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <g clip-path="url(#clip0_inventory_card_bookmark_{{ $car->id ?? $car->stock_code }})">
                        <path d="M9.39062 12C9.15156 12 8.91671 11.9312 8.71128 11.8009L6.11794 10.1543C6.04701 10.1091 5.95296 10.1096 5.88256 10.1543L3.28869 11.8009C2.8048 12.1082 2.13755 12.0368 1.72722 11.6454C1.47556 11.4047 1.33685 11.079 1.33685 10.728V1.2704C1.33738 0.570053 1.90743 0 2.60778 0H9.39272C10.0931 0 10.6631 0.570053 10.6631 1.2704V10.728C10.6631 11.4294 10.0925 12 9.39062 12Z" fill="black"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_inventory_card_bookmark_{{ $car->id ?? $car->stock_code }}">
                            <rect width="12" height="12" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </a>
        </div>
        <div class="content-box">
            <h6 class="title"><a href="{{ route('car.show', $car->stock_code) }}">{{ $car->make_name }} {{ $car->model_name }}</a></h6>
            <div class="text">{{ $car->year }} • {{ $car->trim_name }}</div>
            <ul>
                <li><i class="flaticon-gasoline-pump"></i>{{ $car->mileage ? number_format((float) $car->mileage, 0, ',', '.') . ' km' : ($car->condition === 'new' ? 'Xe mới 100%' : 'Odo lướt') }}</li>
                <li><i class="flaticon-speedometer"></i>{{ $car->fuel_label ?? $car->fuel_type_name ?? 'N/A' }}</li>
                <li><i class="flaticon-gearbox"></i>{{ $car->transmission_label ?? $car->transmission_name ?? 'N/A' }}</li>
            </ul>
            <div class="inventory-card-footer">
                <div class="card-price-group">
                    <span class="card-price-main">{{ $car->formatted_price }}</span>
                    @if(isset($car->price) && $car->price > 0)
                        <span class="card-price-sub">Góp từ ~{{ number_format(round($car->price * 0.01 / 1000000, 1), 1, ',', '.') }} tr/tháng</span>
                    @else
                        <span class="card-price-sub">{{ $car->body_type_name ?? $car->condition_label }}</span>
                    @endif
                </div>
                <a href="{{ route('car.show', $car->stock_code) }}" class="card-action-btn">
                    <span>Xem chi tiết</span>
                    <svg width="12" height="12" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.16669 12.8334L12.8334 1.16669M12.8334 1.16669H3.50002M12.8334 1.16669V10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
