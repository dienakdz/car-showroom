@extends('admin.layouts.app')

@section('title', 'Catalog Models')

@section('admin-content')
    @include('admin.catalog._nav')

    <div class="row admin-module-row">
        <div class="col-xl-4">
            <div class="form-box admin-template-form-box">
                <div class="admin-template-form-head">
                    <h4>Tao model moi</h4>
                    <p>Gan model vao make de quan ly trim va inventory ve sau.</p>
                </div>

                <form action="{{ route('admin.catalog.models.store') }}" method="POST" class="row">
                    @csrf
                    <div class="form-column col-12">
                        <div class="form_boxes">
                            <label>Make</label>
                            <select name="make_id" required>
                                <option value="">Chon make</option>
                                @foreach ($makes as $make)
                                    <option value="{{ $make->id }}" @selected((string) old('make_id') === (string) $make->id)>{{ $make->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-column col-12">
                        <div class="form_boxes">
                            <label>Ten model</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Corolla Cross" required>
                        </div>
                    </div>
                    <div class="form-column col-12">
                        <div class="form_boxes">
                            <label>Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}" placeholder="corolla-cross">
                        </div>
                    </div>
                    <div class="form-submit">
                        <button type="submit" class="theme-btn">
                            Tao model
                            <img src="{{ asset('boxcar/images/arrow.svg') }}" alt="Arrow">
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="my-listing-table wrap-listing admin-listing-shell">
                <div class="cart-table">
                    <div class="title-listing">
                        <div>
                            <h5>Model directory</h5>
                            <div class="text">Loc nhanh theo make va cap nhat tai cho.</div>
                        </div>
                        <form method="GET" class="admin-template-toolbar admin-template-toolbar-wide">
                            <div class="box-ip-search">
                                <span class="icon"><i class="fa fa-search"></i></span>
                                <input type="search" name="q" value="{{ $catalogSearch }}" placeholder="Tim model">
                            </div>
                            <div class="admin-filter-box">
                                <select name="make_id">
                                    <option value="">Tat ca make</option>
                                    @foreach ($makes as $make)
                                        <option value="{{ $make->id }}" @selected($selectedMakeId === $make->id)>{{ $make->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="admin-table-btn">Loc</button>
                        </form>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Model</th>
                                <th>Make</th>
                                <th>Slug</th>
                                <th>Trims</th>
                                <th>Cap nhat nhanh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($models as $model)
                                <tr>
                                    <td>
                                        <div class="shop-cart-product admin-plain-product">
                                            <div class="shop-product-cart-img admin-symbol-badge">
                                                {{ strtoupper(substr($model->name, 0, 1)) }}
                                            </div>
                                            <div class="shop-product-cart-info">
                                                <h3>{{ $model->name }}</h3>
                                                <p>{{ $model->make?->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span>{{ $model->make?->name }}</span></td>
                                    <td><span>{{ $model->slug }}</span></td>
                                    <td><span>{{ $model->trims_count }}</span></td>
                                    <td>
                                        <form action="{{ route('admin.catalog.models.update', $model) }}" method="POST" class="admin-inline-form-grid">
                                            @csrf
                                            @method('PATCH')
                                            <select name="make_id" required>
                                                @foreach ($makes as $make)
                                                    <option value="{{ $make->id }}" @selected($model->make_id === $make->id)>{{ $make->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="name" value="{{ $model->name }}" required>
                                            <input type="text" name="slug" value="{{ $model->slug }}" required>
                                            <div class="admin-inline-actions">
                                                <button type="submit" class="admin-table-btn">Luu</button>
                                            </div>
                                        </form>
                                        <form action="{{ route('admin.catalog.models.destroy', $model) }}" method="POST" class="admin-inline-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-table-btn admin-table-btn-danger">Xoa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="admin-empty-state">Chua co model nao.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $models->links('admin.partials.pagination') }}
                </div>
            </div>
        </div>
    </div>
@endsection
