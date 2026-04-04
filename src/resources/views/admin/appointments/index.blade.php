@extends('admin.layouts.app')

@section('title', 'Appointments Admin')

@section('page-actions')
    <a href="{{ route('admin.appointments.create') }}" class="admin-action-btn">Tao appointment</a>
@endsection

@section('admin-content')
    <div class="my-listing-table wrap-listing admin-listing-shell">
        <div class="cart-table">
            <div class="title-listing">
                <div>
                    <h5>Appointments</h5>
                    <div class="text">Lich hen test-drive, showroom visit va follow-up sau tu lead.</div>
                </div>
                <form method="GET" class="admin-template-toolbar admin-template-toolbar-wide">
                    <div class="admin-filter-box">
                        <select name="status">
                            <option value="">Tat ca status</option>
                            @foreach (['pending', 'confirmed', 'cancelled', 'done'] as $status)
                                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ strtoupper($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-filter-box">
                        <select name="handled_by">
                            <option value="">Tat ca staff</option>
                            @foreach ($staffUsers as $staff)
                                <option value="{{ $staff->id }}" @selected($filters['handled_by'] === $staff->id)>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-table-btn">Loc</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Lich hen</th>
                        <th>Context</th>
                        <th>Status</th>
                        <th>Handled by</th>
                        <th>Lead</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        @php($contextTrim = $appointment->carUnit?->trim ?? $appointment->trim)
                        <tr>
                            <td>
                                <div class="shop-cart-product admin-plain-product">
                                    <div class="shop-product-cart-img admin-symbol-badge">
                                        {{ optional($appointment->scheduled_at)->format('d') ?: 'AP' }}
                                    </div>
                                    <div class="shop-product-cart-info">
                                        <h3>{{ optional($appointment->scheduled_at)->format('d/m/Y H:i') }}</h3>
                                        <p>{{ $appointment->user?->name ?? 'Khach chua co tai khoan' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><span>{{ trim(collect([$contextTrim?->model?->make?->name, $contextTrim?->model?->name, $contextTrim?->name])->filter()->implode(' ')) ?: 'Khong co context xe' }}</span></td>
                            <td><span class="admin-badge admin-badge-{{ $appointment->status }}">{{ strtoupper($appointment->status) }}</span></td>
                            <td><span>{{ $appointment->handledBy?->name ?? 'Chua gan staff' }}</span></td>
                            <td><span>{{ $appointment->lead?->name ?? 'Khong linked' }}</span></td>
                            <td><a href="{{ route('admin.appointments.edit', $appointment) }}" class="admin-table-btn">Mo lich hen</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="admin-empty-state">Chua co appointment nao.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $appointments->links('admin.partials.pagination') }}
        </div>
    </div>
@endsection
