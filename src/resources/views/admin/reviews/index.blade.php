@extends('admin.layouts.app')

@section('title', 'Review Moderation')

@section('admin-content')
    <div class="my-listing-table wrap-listing admin-listing-shell">
        <div class="cart-table">
            <div class="title-listing">
                <div>
                    <h5>Review moderation</h5>
                    <div class="text">Kiem duyet review trim tu khach hang truoc khi hien thi public.</div>
                </div>
                <form method="GET" class="admin-template-toolbar">
                    <div class="admin-filter-box">
                        <select name="status">
                            <option value="">Tat ca status</option>
                            @foreach (['pending', 'approved', 'hidden'] as $status)
                                <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ strtoupper($status) }}</option>
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
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>
                                <div class="shop-cart-product admin-plain-product">
                                    <div class="shop-product-cart-img admin-symbol-badge">
                                        {{ strtoupper(substr($review->trim?->name ?? 'R', 0, 1)) }}
                                    </div>
                                    <div class="shop-product-cart-info">
                                        <h3>{{ trim(collect([$review->trim?->model?->make?->name, $review->trim?->model?->name, $review->trim?->name])->filter()->implode(' ')) }}</h3>
                                        <p>{{ $review->user?->name ?? 'Khach hang' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><span>{{ $review->user?->name ?? 'Khach hang' }}</span></td>
                            <td><span>{{ $review->rating }}/5</span></td>
                            <td><span>{{ $review->comment }}</span></td>
                            <td>
                                <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="admin-inline-form-grid admin-inline-form-grid-compact">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status">
                                        @foreach (['pending', 'approved', 'hidden'] as $status)
                                            <option value="{{ $status }}" @selected($review->status === $status)>{{ strtoupper($status) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="admin-inline-actions">
                                        <button type="submit" class="admin-table-btn">Luu</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="admin-empty-state">Chua co review nao.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $reviews->links('admin.partials.pagination') }}
        </div>
    </div>
@endsection
