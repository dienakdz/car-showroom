<div>
    @if (($feedback['message'] ?? '') !== '')
        <div class="c1-alert {{ ($feedback['type'] ?? 'success') === 'error' ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span><i class="fa {{ ($feedback['type'] ?? 'success') === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle' }} me-2"></i>{{ $feedback['message'] }}</span>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="c1-dash-wrapper">
        {{-- Page Header --}}
        <div class="c1-page-header">
            <div>
                <h1 class="c1-page-title">Quản lý kho xe</h1>
                <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                    Theo dõi chi tiết xe trong kho, định giá và quy trình xuất bản, giữ xe.
                </div>
            </div>
            <div class="c1-dash-quick-actions">
                <a href="{{ route('admin.inventory.create') }}" wire:navigate.hover class="c1-btn c1-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Thêm xe mới</span>
                </a>
            </div>
        </div>

        {{-- Status Chips Bar (Interactive Livewire Filter) --}}
        <div class="c1-chips-bar" style="user-select: none;">
            <button
                type="button"
                wire:click="filterByStatus('')"
                class="c1-chip {{ empty($status) ? 'is-active' : '' }}"
                style="background: none; border: none; cursor: pointer;"
            >
                <span>Tất cả ({{ $statusCounts['all'] ?? 0 }})</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('available')"
                class="c1-chip {{ $status === 'available' ? 'is-active' : '' }}"
                style="background: none; border: none; cursor: pointer;"
            >
                <span>Sẵn sàng bán ({{ $statusCounts['available'] ?? 0 }})</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('on_hold')"
                class="c1-chip {{ $status === 'on_hold' ? 'is-active' : '' }}"
                style="background: none; border: none; cursor: pointer;"
            >
                <span>Đang giữ cọc ({{ $statusCounts['on_hold'] ?? 0 }})</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('draft')"
                class="c1-chip {{ $status === 'draft' ? 'is-active' : '' }}"
                style="background: none; border: none; cursor: pointer;"
            >
                <span>Bản nháp ({{ $statusCounts['draft'] ?? 0 }})</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('sold')"
                class="c1-chip {{ $status === 'sold' ? 'is-active' : '' }}"
                style="background: none; border: none; cursor: pointer;"
            >
                <span>Đã giao xe ({{ $statusCounts['sold'] ?? 0 }})</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('archived')"
                class="c1-chip {{ $status === 'archived' ? 'is-active' : '' }}"
                style="background: none; border: none; cursor: pointer;"
            >
                <span>Lưu kho ({{ $statusCounts['archived'] ?? 0 }})</span>
            </button>
        </div>

        {{-- Main Table Panel --}}
        <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
            {{-- Filter Toolbar --}}
            <div class="c1-filter-toolbar" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 20px;">
                <div class="c1-filter-search" style="flex: 1; min-width: 240px; position: relative;">
                    <i class="fa fa-search" aria-hidden="true" style="position: absolute; left: 12px; top: 12px; color: var(--c1-text-muted);"></i>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Tìm kiếm xe, số khung VIN, mã kho..."
                        aria-label="Tìm kiếm xe"
                        class="form-control"
                        style="padding-left: 36px; height: 40px; border-radius: 8px; font-size: 13px;"
                    >
                </div>

                <select wire:model.live="trimId" class="c1-select" style="height: 40px; border-radius: 8px; min-width: 180px; font-size: 13px;">
                    <option value="0">Tất cả phiên bản</option>
                    @foreach ($trims as $t)
                        <option value="{{ $t->id }}">
                            {{ $t->model?->make?->name }} {{ $t->model?->name }} {{ $t->name }}
                        </option>
                    @endforeach
                </select>

                <select wire:model.live="status" class="c1-select" style="height: 40px; border-radius: 8px; min-width: 140px; font-size: 13px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="available">Sẵn sàng bán</option>
                    <option value="on_hold">Đang giữ cọc</option>
                    <option value="draft">Bản nháp</option>
                    <option value="sold">Đã bán</option>
                    <option value="archived">Lưu kho</option>
                </select>

                <select wire:model.live="condition" class="c1-select" style="height: 40px; border-radius: 8px; min-width: 130px; font-size: 13px;">
                    <option value="">Tất cả tình trạng</option>
                    <option value="new">Xe mới (New)</option>
                    <option value="used">Đã qua sử dụng (Used)</option>
                    <option value="cpo">Chính hãng (CPO)</option>
                </select>

                @if ($search !== '' || $status !== '' || $condition !== '' || $trimId > 0)
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="c1-btn c1-btn-ghost"
                        style="height: 40px; padding: 0 14px;"
                        title="Xóa bộ lọc"
                    >
                        <i class="fa fa-times me-1"></i> Xóa lọc
                    </button>
                @endif
            </div>

            {{-- Table --}}
            <div class="c1-table-wrap">
                <table class="c1-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Ảnh</th>
                            <th>Tên xe & Phiên bản</th>
                            <th>Mã kho / Số khung VIN</th>
                            <th>Giá bán</th>
                            <th>Tương tác</th>
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
                                    default => strtoupper((string) $carUnit->condition),
                                };
                            @endphp
                            <tr wire:key="car-unit-row-{{ $carUnit->id }}">
                                <td>
                                    <div class="c1-car-thumb" style="width: 56px; height: 42px; border-radius: 6px; overflow: hidden; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                        @if ($hasRealImage)
                                            <img src="{{ asset($coverMedia->path_or_url) }}" alt="{{ $carUnit->stock_code }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <i class="fa fa-car" aria-hidden="true" style="color: #94a3b8; font-size: 18px;"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="c1-car-info">
                                        <span class="c1-car-name" style="font-weight: 600; font-size: 14px; color: #1e293b;">
                                            {{ $carUnit->trim?->model?->make?->name }} {{ $carUnit->trim?->model?->name }} {{ $carUnit->trim?->name }}
                                        </span>
                                        <span class="c1-car-meta" style="font-size: 12px; color: var(--c1-text-muted); display: block; margin-top: 2px;">
                                            <span class="badge" style="background: #f1f5f9; color: #475569; font-weight: 500;">{{ $conditionLabel }}</span>
                                            • Năm {{ $carUnit->year }}
                                            @if ($carUnit->mileage !== null)
                                                • {{ number_format($carUnit->mileage) }} km
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="c1-car-info">
                                        <span class="c1-cell-primary" style="font-weight: 600; color: var(--c1-primary);">{{ $carUnit->stock_code }}</span>
                                        <span class="c1-car-meta" style="font-size: 12px; color: var(--c1-text-muted); display: block;">
                                            {{ $carUnit->vin ?: 'Chưa cập nhật VIN' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="c1-price-cell" style="font-weight: 700; color: #0f172a;">
                                        {{ $carUnit->price ? number_format($carUnit->price, 0, ',', '.') . ' ' . $carUnit->currency : 'Liên hệ' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; font-size: 11.5px;">
                                        <span class="badge" style="background: #eff6ff; color: #1d4ed8;" title="Số lead liên kết">
                                            <i class="fa fa-user me-1"></i>{{ $carUnit->leads_count ?? 0 }}
                                        </span>
                                        <span class="badge" style="background: #fef3c7; color: #92400e;" title="Số lịch hẹn xem xe">
                                            <i class="fa fa-calendar me-1"></i>{{ $carUnit->appointments_count ?? 0 }}
                                        </span>
                                    </div>
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
                                        <span class="c1-pill c1-pill-gray">{{ strtoupper((string) $carUnit->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        <a href="{{ route('admin.inventory.edit', $carUnit) }}" wire:navigate.hover class="c1-btn c1-btn-sm c1-btn-ghost" title="Chỉnh sửa chi tiết xe">
                                            <i class="fa fa-pencil"></i>
                                            <span>Sửa</span>
                                        </a>

                                        @if ($carUnit->status !== 'available' && $carUnit->status !== 'sold')
                                            <button
                                                type="button"
                                                wire:click="publish({{ $carUnit->id }})"
                                                wire:loading.attr="disabled"
                                                class="c1-btn c1-btn-sm c1-btn-ghost"
                                                title="Đăng bán công khai"
                                            >
                                                <i class="fa fa-upload me-1"></i> Publish
                                            </button>
                                        @endif

                                        @if ($carUnit->status !== 'archived')
                                            <button
                                                type="button"
                                                wire:click="archive({{ $carUnit->id }})"
                                                wire:loading.attr="disabled"
                                                class="c1-btn c1-btn-sm c1-btn-danger-ghost"
                                                title="Lưu kho"
                                            >
                                                <i class="fa fa-archive me-1"></i> Archive
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="c1-empty-cell" style="text-align: center; padding: 40px 16px; color: var(--c1-text-muted);">
                                    <i class="fa fa-car fa-2x mb-2 d-block" style="opacity: 0.4;"></i>
                                    Không tìm thấy xe nào trong kho theo điều kiện lọc hiện tại.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $carUnits->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
</div>
