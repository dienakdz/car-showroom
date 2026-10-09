@if ($paginator->hasPages())
    @php
        $pageName = $paginator->getPageName();
        $isLivewire = isset($this) && method_exists($this, 'gotoPage');
        $formatUrl = static function (?string $rawUrl): string {
            if (! $rawUrl) {
                return '#';
            }
            if (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://')) {
                return $rawUrl;
            }
            return url('/' . ltrim($rawUrl, '/'));
        };
    @endphp
    <div class="pagination-sec">
        <nav aria-label="Admin pagination">
            <ul class="pagination">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        @if ($isLivewire)
                            <button
                                type="button"
                                class="page-link"
                                wire:click="previousPage('{{ $pageName }}')"
                                wire:loading.attr="disabled"
                                x-on:click="($el.closest('.c1-table-panel') || document.querySelector('.c1-table-panel') || document.body).scrollIntoView({ behavior: 'smooth' })"
                                rel="prev"
                                aria-label="Trang trước"
                            >&lsaquo;</button>
                        @else
                            <a class="page-link" href="{{ $formatUrl($paginator->previousPageUrl()) }}" rel="prev" aria-label="Trang trước">&lsaquo;</a>
                        @endif
                    </li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" wire:key="paginator-{{ $pageName }}-page-{{ $page }}" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item" wire:key="paginator-{{ $pageName }}-page-{{ $page }}">
                                    @if ($isLivewire)
                                        <button
                                            type="button"
                                            class="page-link"
                                            wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                            wire:loading.attr="disabled"
                                            x-on:click="($el.closest('.c1-table-panel') || document.querySelector('.c1-table-panel') || document.body).scrollIntoView({ behavior: 'smooth' })"
                                        >{{ $page }}</button>
                                    @else
                                        <a class="page-link" href="{{ $formatUrl($url) }}">{{ $page }}</a>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        @if ($isLivewire)
                            <button
                                type="button"
                                class="page-link"
                                wire:click="nextPage('{{ $pageName }}')"
                                wire:loading.attr="disabled"
                                x-on:click="($el.closest('.c1-table-panel') || document.querySelector('.c1-table-panel') || document.body).scrollIntoView({ behavior: 'smooth' })"
                                rel="next"
                                aria-label="Trang sau"
                            >&rsaquo;</button>
                        @else
                            <a class="page-link" href="{{ $formatUrl($paginator->nextPageUrl()) }}" rel="next" aria-label="Trang sau">&rsaquo;</a>
                        @endif
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
