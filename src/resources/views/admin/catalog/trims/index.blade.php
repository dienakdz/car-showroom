@extends('admin.layouts.app')

@section('title', 'Catalog Trims')

@section('page-actions')
    <a href="{{ route('admin.catalog.trims.create') }}" class="admin-action-btn">Tao trim</a>
@endsection

@section('admin-content')
    @include('admin.catalog._nav')

    <div class="my-listing-table wrap-listing admin-listing-shell">
        <div class="cart-table">
            <div class="title-listing">
                <div>
                    <h5>Trim catalog</h5>
                    <div class="text">Quan ly cac trim chi tiet va lien ket sang inventory, review.</div>
                </div>
                <form method="GET" class="admin-template-toolbar admin-template-toolbar-wide">
                    <div class="box-ip-search">
                        <span class="icon"><i class="fa fa-search"></i></span>
                        <input type="search" name="q" value="{{ $catalogSearch }}" placeholder="Tim trim, slug hoac description">
                    </div>
                    <div class="admin-filter-box">
                        <select name="model_id">
                            <option value="">Tat ca model</option>
                            @foreach ($models as $model)
                                <option value="{{ $model->id }}" @selected($selectedModelId === $model->id)>{{ $model->make?->name }} / {{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-table-btn">Loc</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Trim</th>
                        <th>Model</th>
                        <th>MSRP</th>
                        <th>Inventory</th>
                        <th>Reviews</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trims as $trim)
                        <tr>
                            <td>
                                <div class="shop-cart-product admin-plain-product">
                                    <div class="shop-product-cart-img admin-symbol-badge">
                                        {{ strtoupper(substr($trim->name, 0, 1)) }}
                                    </div>
                                    <div class="shop-product-cart-info">
                                        <h3>{{ $trim->name }}</h3>
                                        <p>{{ $trim->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><span>{{ $trim->model?->make?->name }} / {{ $trim->model?->name }}</span></td>
                            <td><span>{{ $trim->msrp ? number_format($trim->msrp, 0, ',', '.') . ' VND' : 'Chua khai bao' }}</span></td>
                            <td><span>{{ $trim->car_units_count }}</span></td>
                            <td><span>{{ $trim->reviews_count }}</span></td>
                            <td class="admin-table-actions">
                                <a href="{{ route('admin.catalog.trims.edit', $trim) }}" class="admin-table-btn">Sua</a>
                                <form action="{{ route('admin.catalog.trims.destroy', $trim) }}" method="POST" class="admin-inline-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-table-btn admin-table-btn-danger">Xoa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="admin-empty-state">Chua co trim nao.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $trims->links('admin.partials.pagination') }}
        </div>
    </div>
@endsection
