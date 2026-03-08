<div class="car-block-two col-lg-4 col-md-6 col-sm-12">
    <div class="inner-box">
        <div class="image-box">
            <figure class="image">
                <a href="{{ route('car.show', $car->stock_code) }}">
                    <img src="{{ $car->image_url }}" alt="{{ $car->make_name }} {{ $car->model_name }}">
                </a>
            </figure>
            <span>{{ $car->mileage ? number_format((float) $car->mileage, 0, ',', '.') . ' km' : $car->condition_label }}</span>
            <a href="{{ route('car.show', $car->stock_code) }}" class="icon-box" aria-label="View {{ $car->make_name }} {{ $car->model_name }}">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_home_popular_make)">
                        <path d="M9.39062 12C9.15156 12 8.91671 11.9312 8.71128 11.8009L6.11794 10.1543C6.04701 10.1091 5.95296 10.1096 5.88256 10.1543L3.28869 11.8009C2.8048 12.1082 2.13755 12.0368 1.72722 11.6454C1.47556 11.4047 1.33685 11.079 1.33685 10.728V1.2704C1.33738 0.570053 1.90743 0 2.60778 0H9.39272C10.0931 0 10.6631 0.570053 10.6631 1.2704V10.728C10.6631 11.4294 10.0925 12 9.39062 12ZM6.00025 9.06935C6.24193 9.06935 6.47783 9.13765 6.68169 9.26743L9.27503 10.9135C9.31233 10.9371 9.35069 10.9487 9.39114 10.9487C9.48046 10.9487 9.61286 10.8788 9.61286 10.728V1.2704C9.61233 1.14956 9.51356 1.05079 9.39272 1.05079H2.60778C2.48642 1.05079 2.38817 1.14956 2.38817 1.2704V10.728C2.38817 10.7911 2.41023 10.8436 2.45384 10.8851C2.52582 10.9539 2.63563 10.9708 2.72599 10.9135L5.31934 9.2669C5.52267 9.13765 5.75857 9.06935 6.00025 9.06935Z" fill="black"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_home_popular_make">
                            <rect width="12" height="12" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </a>
        </div>
        <div class="content-box">
            <h6 class="title">
                <a href="{{ route('car.show', $car->stock_code) }}">{{ $car->make_name }}, {{ $car->model_name }}</a>
            </h6>
            <div class="text">{{ $car->year }} {{ $car->trim_name }}</div>
            <ul>
                <li><i class="flaticon-speedometer"></i>{{ $car->mileage ? number_format((float) $car->mileage, 0, ',', '.') . ' km' : 'Xe mới về' }}</li>
                <li><i class="flaticon-gasoline-pump"></i>{{ $car->fuel_type_name ?? 'N/A' }}</li>
                <li><i class="flaticon-gearbox"></i>{{ $car->transmission_name ?? 'N/A' }}</li>
            </ul>
            <div class="btn-box">
                <span>{{ $car->formatted_price }}</span>
                <small>{{ $car->body_type_name ?? $car->condition_label }}</small>
                <a href="{{ route('car.show', $car->stock_code) }}" class="details">Xem chi tiết
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <g clip-path="url(#clip0_home_popular_make_arrow)">
                            <path d="M13.6109 0H5.05533C4.84037 0 4.66642 0.173943 4.66642 0.388901C4.66642 0.603859 4.84037 0.777802 5.05533 0.777802H12.6721L0.11369 13.3362C-0.0382322 13.4881 -0.0382322 13.7342 0.11369 13.8861C0.189632 13.962 0.289164 14 0.388658 14C0.488153 14 0.587648 13.962 0.663627 13.8861L13.222 1.3277V8.94447C13.222 9.15943 13.3959 9.33337 13.6109 9.33337C13.8259 9.33337 13.9998 9.15943 13.9998 8.94447V0.388901C13.9998 0.173943 13.8258 0 13.6109 0Z" fill="white"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_home_popular_make_arrow">
                                <rect width="14" height="14" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
