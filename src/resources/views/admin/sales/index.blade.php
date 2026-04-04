@extends('admin.layouts.app')

@section('title', 'Sales Log')

@section('page-actions')
    <a href="{{ route('admin.sales.create') }}" class="admin-action-btn">Tao sale</a>
@endsection

@section('admin-content')
    <div class="my-listing-table wrap-listing admin-listing-shell">
        <div class="cart-table">
            <div class="title-listing">
                <div>
                    <h5>Sales log</h5>
                    <div class="text">Lich su chot deal tu inventory sang buyer va lead closure.</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Xe</th>
                        <th>Buyer</th>
                        <th>Sold at</th>
                        <th>Sold price</th>
                        <th>Created by</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        @php($trim = $sale->carUnit?->trim)
                        <tr>
                            <td>
                                <div class="shop-cart-product admin-plain-product">
                                    <div class="shop-product-cart-img admin-symbol-badge">
                                        {{ strtoupper(substr($sale->carUnit?->stock_code ?? 'SL', 0, 2)) }}
                                    </div>
                                    <div class="shop-product-cart-info">
                                        <h3>{{ trim(collect([$trim?->model?->make?->name, $trim?->model?->name, $trim?->name])->filter()->implode(' ')) }}</h3>
                                        <p>Stock {{ $sale->carUnit?->stock_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>{{ $sale->buyer?->name ?? 'Khach hang' }}</span>
                                <div class="admin-table-subtext">{{ $sale->buyer?->phone ?: $sale->buyer?->email }}</div>
                            </td>
                            <td><span>{{ optional($sale->sold_at)->format('d/m/Y H:i') }}</span></td>
                            <td><span>{{ $sale->sold_price ? number_format($sale->sold_price, 0, ',', '.') : 'Theo hop dong' }}</span></td>
                            <td><span>{{ $sale->createdBy?->name ?? 'Staff' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="admin-empty-state">Chua co sale nao.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $sales->links('admin.partials.pagination') }}
        </div>
    </div>
@endsection
