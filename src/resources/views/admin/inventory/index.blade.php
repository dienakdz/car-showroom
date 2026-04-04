@extends('admin.layouts.app')

@section('title', 'Inventory Admin')

@section('page-actions')
    <a href="{{ route('admin.inventory.create') }}" class="admin-action-btn">Them xe</a>
@endsection

@section('admin-content')
    <div class="my-listing-table wrap-listing admin-listing-shell">
        <div class="cart-table">
            <div class="title-listing">
                <div>
                    <h5>Inventory listing</h5>
                    <div class="text">Theo doi stock, pricing va workflow publish/hold/archive.</div>
                </div>
                <form method="GET" class="admin-template-toolbar admin-template-toolbar-wide">
                    <div class="box-ip-search">
                        <span class="icon"><i class="fa fa-search"></i></span>
                        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Tim theo stock code hoac VIN">
                    </div>
                    <div class="admin-filter-box">
                        <select name="status">
                            <option value="">Tat ca status</option>
                            @foreach (['draft', 'available', 'on_hold', 'sold', 'archived'] as $status)
                                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ strtoupper($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-filter-box">
                        <select name="condition">
                            <option value="">Tat ca condition</option>
                            @foreach (['new', 'used', 'cpo'] as $condition)
                                <option value="{{ $condition }}" @selected($filters['condition'] === $condition)>{{ strtoupper($condition) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-filter-box">
                        <select name="trim_id">
                            <option value="">Tat ca trim</option>
                            @foreach ($trims as $trim)
                                <option value="{{ $trim->id }}" @selected($filters['trim_id'] === $trim->id)>{{ $trim->model?->make?->name }} {{ $trim->model?->name }} {{ $trim->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-table-btn">Loc</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Xe</th>
                        <th>Stock / VIN</th>
                        <th>Status</th>
                        <th>Gia</th>
                        <th>Lead / Appointment</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($carUnits as $carUnit)
                        <tr>
                            <td>
                                <div class="shop-cart-product admin-plain-product">
                                    <div class="shop-product-cart-img admin-symbol-badge">
                                        {{ strtoupper(substr($carUnit->stock_code, 0, 2)) }}
                                    </div>
                                    <div class="shop-product-cart-info">
                                        <h3>{{ $carUnit->trim?->model?->make?->name }} {{ $carUnit->trim?->model?->name }} {{ $carUnit->trim?->name }}</h3>
                                        <p>{{ strtoupper($carUnit->condition) }} / {{ $carUnit->year }}{{ $carUnit->mileage !== null ? ' / ' . number_format($carUnit->mileage) . ' km' : '' }}</p>
                                        <div class="price">
                                            <span>{{ $carUnit->price ? number_format($carUnit->price, 0, ',', '.') . ' ' . $carUnit->currency : 'Lien he' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><strong>{{ $carUnit->stock_code }}</strong></div>
                                <div class="admin-table-subtext">{{ $carUnit->vin ?: 'Khong co VIN' }}</div>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $carUnit->status }}">{{ strtoupper($carUnit->status) }}</span>
                            </td>
                            <td><span>{{ $carUnit->price ? number_format($carUnit->price, 0, ',', '.') . ' ' . $carUnit->currency : 'Lien he' }}</span></td>
                            <td><span>{{ $carUnit->leads_count }} lead / {{ $carUnit->appointments_count }} lich</span></td>
                            <td class="admin-table-actions">
                                <a href="{{ route('admin.inventory.edit', $carUnit) }}" class="admin-table-btn">Sua</a>

                                @if ($carUnit->status !== 'available' && $carUnit->status !== 'sold')
                                    <form action="{{ route('admin.inventory.publish', $carUnit) }}" method="POST" class="admin-inline-delete">
                                        @csrf
                                        <button type="submit" class="admin-table-btn">Publish</button>
                                    </form>
                                @endif

                                @if ($carUnit->status !== 'archived')
                                    <form action="{{ route('admin.inventory.archive', $carUnit) }}" method="POST" class="admin-inline-delete">
                                        @csrf
                                        <button type="submit" class="admin-table-btn admin-table-btn-danger">Archive</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="admin-empty-state">Chua co xe nao trong kho.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $carUnits->links('admin.partials.pagination') }}
        </div>
    </div>
@endsection
