@extends('admin.layouts.app')

@section('title', 'Quản lý kho xe | Admin')

@section('admin-content')
    <div class="c1-dash-wrapper">
        {{-- Page Header --}}
        <div class="c1-page-header">
            <h1 class="c1-page-title">Quản lý kho xe</h1>
            <div class="c1-dash-quick-actions">
                <a href="{{ route('admin.inventory.create') }}" class="c1-btn c1-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Thêm xe mới</span>
                </a>
            </div>
        </div>

        {{-- Status Chips Bar --}}
        <div class="c1-chips-bar">
            <a href="{{ route('admin.inventory.index') }}" class="c1-chip {{ empty($filters['status']) ? 'is-active' : '' }}">
                <span>Tất cả ({{ $statusCounts['all'] ?? 0 }})</span>
            </a>
            <a href="{{ route('admin.inventory.index', array_merge(request()->query(), ['status' => 'available'])) }}" class="c1-chip {{ ($filters['status'] ?? '') === 'available' ? 'is-active' : '' }}">
                <span>Sẵn sàng bán ({{ $statusCounts['available'] ?? 0 }})</span>
            </a>
            <a href="{{ route('admin.inventory.index', array_merge(request()->query(), ['status' => 'on_hold'])) }}" class="c1-chip {{ ($filters['status'] ?? '') === 'on_hold' ? 'is-active' : '' }}">
                <span>Đang giữ cọc ({{ $statusCounts['on_hold'] ?? 0 }})</span>
            </a>
            <a href="{{ route('admin.inventory.index', array_merge(request()->query(), ['status' => 'sold'])) }}" class="c1-chip {{ ($filters['status'] ?? '') === 'sold' ? 'is-active' : '' }}">
                <span>Đã giao xe ({{ $statusCounts['sold'] ?? 0 }})</span>
            </a>
        </div>

        {{-- Main Table Panel --}}
        <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
            {{-- Filter Toolbar --}}
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="c1-filter-toolbar">
                <div class="c1-filter-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input
                        type="search"
                        name="q"
                        value="{{ $filters['q'] }}"
                        placeholder="Tìm kiếm xe, số khung VIN, mã kho..."
                        aria-label="Tìm kiếm xe"
                    >
                </div>

                <select name="trim_id" class="c1-select">
                    <option value="">Tất cả phiên bản</option>
                    @foreach ($trims as $trim)
                        <option value="{{ $trim->id }}" @selected(($filters['trim_id'] ?? null) == $trim->id)>
                            {{ $trim->model?->make?->name }} {{ $trim->model?->name }} {{ $trim->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="c1-select">
                    <option value="">Trạng thái</option>
                    <option value="available" @selected(($filters['status'] ?? '') === 'available')>Sẵn sàng bán</option>
                    <option value="on_hold" @selected(($filters['status'] ?? '') === 'on_hold')>Đang giữ cọc</option>
                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Bản nháp</option>
                    <option value="sold" @selected(($filters['status'] ?? '') === 'sold')>Đã bán</option>
                    <option value="archived" @selected(($filters['status'] ?? '') === 'archived')>Lưu kho</option>
                </select>

                <select name="condition" class="c1-select">
                    <option value="">Tình trạng</option>
                    <option value="new" @selected(($filters['condition'] ?? '') === 'new')>Xe mới (New)</option>
                    <option value="used" @selected(($filters['condition'] ?? '') === 'used')>Xe đã qua sử dụng (Used)</option>
                    <option value="cpo" @selected(($filters['condition'] ?? '') === 'cpo')>Chứng nhận chính hãng (CPO)</option>
                </select>

                <button type="submit" class="c1-btn c1-btn-secondary" style="height: 38px;">
                    <i class="fa fa-filter"></i>
                    <span>Lọc</span>
                </button>
            </form>

            {{-- Table --}}
            <div class="c1-table-wrap">
                <table class="c1-table">
                    <thead>
                        <tr>
                            <th style="width: 32px;"><input type="checkbox" aria-label="Chọn tất cả"></th>
                            <th style="width: 70px;">Ảnh</th>
                            <th>Tên xe & Phiên bản</th>
                            <th>Số khung VIN</th>
                            <th>Giá bán</th>
                            <th>Trạng thái</th>
                            <th class="text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carUnits as $carUnit)
                            @php
                                $coverMedia = $carUnit->media->firstWhere('is_cover', true) ?? $carUnit->media->first();
                                $hasRealImage = $coverMedia && !empty($coverMedia->path_or_url) && file_exists(public_path($coverMedia->path_or_url));
                                $conditionLabel = match ($carUnit->condition) {
                                    'new' => 'Mới 100%',
                                    'used' => 'Đã qua sử dụng',
                                    'cpo' => 'Chính hãng CPO',
                                    default => strtoupper($carUnit->condition),
                                };
                            @endphp
                            <tr>
                                <td><input type="checkbox" aria-label="Chọn dòng"></td>
                                <td>
                                    <div class="c1-car-thumb">
                                        @if ($hasRealImage)
                                            <img src="{{ asset($coverMedia->path_or_url) }}" alt="{{ $carUnit->stock_code }}">
                                        @else
                                            <i class="fa fa-car" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="c1-car-info">
                                        <span class="c1-car-name">
                                            {{ $carUnit->trim?->model?->make?->name }} {{ $carUnit->trim?->model?->name }} {{ $carUnit->trim?->name }}
                                        </span>
                                        <span class="c1-car-meta">
                                            {{ $conditionLabel }} • Năm {{ $carUnit->year }}
                                            @if ($carUnit->mileage !== null)
                                                • {{ number_format($carUnit->mileage) }} km
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="c1-car-info">
                                        <span class="c1-cell-primary">{{ $carUnit->stock_code }}</span>
                                        <span class="c1-car-meta">{{ $carUnit->vin ?: 'Chưa cập nhật VIN' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="c1-price-cell">
                                        {{ $carUnit->price ? number_format($carUnit->price, 0, ',', '.') . ' ' . $carUnit->currency : 'Liên hệ' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($carUnit->status === 'available')
                                        <span class="c1-pill c1-pill-green">Sẵn sàng bán</span>
                                    @elseif ($carUnit->status === 'on_hold')
                                        <span class="c1-pill c1-pill-amber">Giữ cọc</span>
                                    @elseif ($carUnit->status === 'sold')
                                        <span class="c1-pill c1-pill-gray">Đã bán</span>
                                    @elseif ($carUnit->status === 'draft')
                                        <span class="c1-pill c1-pill-blue">Bản nháp</span>
                                    @else
                                        <span class="c1-pill c1-pill-gray">{{ strtoupper($carUnit->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        <a href="{{ route('admin.inventory.edit', $carUnit) }}" class="c1-btn c1-btn-sm c1-btn-ghost" title="Chỉnh sửa xe">
                                            <i class="fa fa-pencil"></i>
                                            <span>Sửa</span>
                                        </a>

                                        @if ($carUnit->status !== 'available' && $carUnit->status !== 'sold')
                                            <form action="{{ route('admin.inventory.publish', $carUnit) }}" method="POST" style="margin: 0; display: inline;">
                                                @csrf
                                                <button type="submit" class="c1-btn c1-btn-sm c1-btn-ghost" title="Đăng bán công khai">
                                                    <span>Publish</span>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($carUnit->status !== 'archived')
                                            <form action="{{ route('admin.inventory.archive', $carUnit) }}" method="POST" style="margin: 0; display: inline;">
                                                @csrf
                                                <button type="submit" class="c1-btn c1-btn-sm c1-btn-danger-ghost" title="Lưu trữ xe">
                                                    <span>Archive</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="c1-empty-cell">
                                    Chưa có xe nào trong kho theo điều kiện lọc hiện tại.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px;">
                {{ $carUnits->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
@endsection
