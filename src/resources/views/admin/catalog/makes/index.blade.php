@extends('admin.layouts.app')

@section('title', 'Catalog Makes')

@section('admin-content')
    @include('admin.catalog._nav')

    <div class="row admin-module-row">
        <div class="col-xl-4">
            <div class="form-box admin-template-form-box">
                <div class="admin-template-form-head">
                    <h4>Tao make moi</h4>
                    <p>Khai bao thuong hieu de mo rong catalog theo module.</p>
                </div>

                <form action="{{ route('admin.catalog.makes.store') }}" method="POST" class="row">
                    @csrf
                    <div class="form-column col-12">
                        <div class="form_boxes">
                            <label>Ten hang xe</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Toyota" required>
                        </div>
                    </div>
                    <div class="form-column col-12">
                        <div class="form_boxes">
                            <label>Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}" placeholder="toyota">
                        </div>
                    </div>
                    <div class="form-column col-12">
                        <div class="form_boxes">
                            <label>Logo path / URL</label>
                            <input type="text" name="logo_path" value="{{ old('logo_path') }}" placeholder="boxcar/images/brands/toyota.svg">
                        </div>
                    </div>
                    <div class="form-submit">
                        <button type="submit" class="theme-btn btn-style-one">
                            <span>Tao make</span>
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
                            <h5>Make directory</h5>
                            <div class="text">Danh sach hang xe dang duoc su dung tren website.</div>
                        </div>
                        <form method="GET" class="admin-template-toolbar">
                            <div class="box-ip-search">
                                <span class="icon"><i class="fa fa-search"></i></span>
                                <input type="search" name="q" value="{{ $catalogSearch }}" placeholder="Tim theo ten hoac slug">
                            </div>
                            <button type="submit" class="admin-table-btn">Loc</button>
                        </form>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Make</th>
                                <th>Slug</th>
                                <th>Models</th>
                                <th>Cap nhat nhanh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($makes as $make)
                                <tr>
                                    <td>
                                        <div class="shop-cart-product admin-plain-product">
                                            <div class="shop-product-cart-img admin-symbol-badge">
                                                {{ strtoupper(substr($make->name, 0, 1)) }}
                                            </div>
                                            <div class="shop-product-cart-info">
                                                <h3>{{ $make->name }}</h3>
                                                <p>{{ $make->logo_path ?: 'Chua khai bao logo' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span>{{ $make->slug }}</span></td>
                                    <td><span>{{ $make->models_count }}</span></td>
                                    <td>
                                        <form action="{{ route('admin.catalog.makes.update', $make) }}" method="POST" class="admin-inline-form-grid">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="name" value="{{ $make->name }}" required>
                                            <input type="text" name="slug" value="{{ $make->slug }}" required>
                                            <input type="text" name="logo_path" value="{{ $make->logo_path }}">
                                            <div class="admin-inline-actions">
                                                <button type="submit" class="admin-table-btn">Luu</button>
                                            </div>
                                        </form>
                                        <form action="{{ route('admin.catalog.makes.destroy', $make) }}" method="POST" class="admin-inline-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-table-btn admin-table-btn-danger">Xoa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="admin-empty-state">Chua co make nao.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $makes->links('admin.partials.pagination') }}
                </div>
            </div>
        </div>
    </div>
@endsection
