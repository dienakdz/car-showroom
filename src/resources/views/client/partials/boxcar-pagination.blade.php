@php
    $pages = collect(array_merge($window['first'] ?? [], $window['slider'] ?? [], $window['last'] ?? []))
        ->sortKeys();
    $previousPage = null;
@endphp

@if ($paginator->hasPages())
    <div class="pagination-sec">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item">
                    @if ($paginator->onFirstPage())
                        <span class="page-link" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.57983 5.99989C2.57983 5.7849 2.66192 5.56987 2.82573 5.4059L7.98559 0.24617C8.31382 -0.0820565 8.84598 -0.0820565 9.17408 0.24617C9.50217 0.574263 9.50217 1.10632 9.17408 1.43457L4.60841 5.99989L9.17376 10.5654C9.50185 10.8935 9.50185 11.4256 9.17376 11.7537C8.84566 12.0821 8.31366 12.0821 7.98544 11.7537L2.82555 6.59404C2.66176 6.42999 2.57983 6.21495 2.57983 5.99989Z" fill="#050B20"/>
                            </svg>
                        </span>
                    @else
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                            <span aria-hidden="true">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.57983 5.99989C2.57983 5.7849 2.66192 5.56987 2.82573 5.4059L7.98559 0.24617C8.31382 -0.0820565 8.84598 -0.0820565 9.17408 0.24617C9.50217 0.574263 9.50217 1.10632 9.17408 1.43457L4.60841 5.99989L9.17376 10.5654C9.50185 10.8935 9.50185 11.4256 9.17376 11.7537C8.84566 12.0821 8.31366 12.0821 7.98544 11.7537L2.82555 6.59404C2.66176 6.42999 2.57983 6.21495 2.57983 5.99989Z" fill="#050B20"/>
                                </svg>
                            </span>
                        </a>
                    @endif
                </li>
                @foreach ($pages as $page => $url)
                    @if ($previousPage !== null && $page > $previousPage + 1)
                        <li class="page-item"><span class="page-link">...</span></li>
                    @endif
                    <li class="page-item">
                        @if ($page == $paginator->currentPage())
                            <span class="page-link">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    </li>
                    @php($previousPage = $page)
                @endforeach
                <li class="page-item">
                    @if ($paginator->hasMorePages())
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                            <span aria-hidden="true">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_boxcar_pagination)">
                                        <path d="M9.42017 6.00011C9.42017 6.2151 9.33808 6.43013 9.17427 6.5941L4.01441 11.7538C3.68618 12.0821 3.15402 12.0821 2.82592 11.7538C2.49783 11.4257 2.49783 10.8937 2.82592 10.5654L7.39159 6.00011L2.82624 1.43461C2.49815 1.10652 2.49815 0.574382 2.82624 0.246315C3.15434 -0.0820709 3.68634 -0.0820709 4.01457 0.246315L9.17446 5.40596C9.33824 5.57001 9.42017 5.78505 9.42017 6.00011Z" fill="#050B20"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_boxcar_pagination">
                                            <rect width="12" height="12" fill="white" transform="translate(12 12) rotate(-180)"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                        </a>
                    @else
                        <span class="page-link" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_boxcar_pagination_disabled)">
                                    <path d="M9.42017 6.00011C9.42017 6.2151 9.33808 6.43013 9.17427 6.5941L4.01441 11.7538C3.68618 12.0821 3.15402 12.0821 2.82592 11.7538C2.49783 11.4257 2.49783 10.8937 2.82592 10.5654L7.39159 6.00011L2.82624 1.43461C2.49815 1.10652 2.49815 0.574382 2.82624 0.246315C3.15434 -0.0820709 3.68634 -0.0820709 4.01457 0.246315L9.17446 5.40596C9.33824 5.57001 9.42017 5.78505 9.42017 6.00011Z" fill="#050B20"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_boxcar_pagination_disabled">
                                        <rect width="12" height="12" fill="white" transform="translate(12 12) rotate(-180)"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    @endif
                </li>
            </ul>
            <div class="text">Hien thi {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} tren tong {{ $paginator->total() }} xe</div>
        </nav>
    </div>
@endif
