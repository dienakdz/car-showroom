<div>
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

        {{-- Status Segmented Tabs Bar --}}
        <div class="c1-inventory-tabs mb-4" role="tablist" aria-label="Bộ lọc trạng thái kho xe">
            <button
                type="button"
                wire:click="filterByStatus('')"
                class="c1-inventory-tab-btn {{ empty($status) ? 'active' : '' }}"
            >
                <span>Tất cả</span>
                <span class="c1-inventory-tab-badge">{{ $statusCounts['all'] ?? 0 }}</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('available')"
                class="c1-inventory-tab-btn {{ $status === 'available' ? 'active' : '' }}"
            >
                <span class="c1-status-dot c1-dot-green"></span>
                <span>Sẵn sàng bán</span>
                <span class="c1-inventory-tab-badge">{{ $statusCounts['available'] ?? 0 }}</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('on_hold')"
                class="c1-inventory-tab-btn {{ $status === 'on_hold' ? 'active' : '' }}"
            >
                <span class="c1-status-dot c1-dot-amber"></span>
                <span>Đang giữ cọc</span>
                <span class="c1-inventory-tab-badge">{{ $statusCounts['on_hold'] ?? 0 }}</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('draft')"
                class="c1-inventory-tab-btn {{ $status === 'draft' ? 'active' : '' }}"
            >
                <span class="c1-status-dot c1-dot-slate"></span>
                <span>Bản nháp</span>
                <span class="c1-inventory-tab-badge">{{ $statusCounts['draft'] ?? 0 }}</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('sold')"
                class="c1-inventory-tab-btn {{ $status === 'sold' ? 'active' : '' }}"
            >
                <span class="c1-status-dot c1-dot-purple"></span>
                <span>Đã giao xe</span>
                <span class="c1-inventory-tab-badge">{{ $statusCounts['sold'] ?? 0 }}</span>
            </button>
            <button
                type="button"
                wire:click="filterByStatus('archived')"
                class="c1-inventory-tab-btn {{ $status === 'archived' ? 'active' : '' }}"
            >
                <span class="c1-status-dot c1-dot-red"></span>
                <span>Lưu kho</span>
                <span class="c1-inventory-tab-badge">{{ $statusCounts['archived'] ?? 0 }}</span>
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
                            <th style="text-align: right; min-width: 170px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carUnits as $carUnit)
                            @php
                                $imageMedia = $carUnit->media->where('type', 'image');
                                $coverMedia = $imageMedia->firstWhere('is_cover', true) ?? $imageMedia->first();
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
                                        @if ($coverMedia && filled($coverMedia->path_or_url))
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
                                <td style="text-align: right; white-space: nowrap;">
                                    <div class="c1-actions-cell">
                                        @if ($carUnit->status === 'sold')
                                            <a href="{{ route('admin.inventory.edit', $carUnit) }}" wire:navigate.hover class="c1-action-btn c1-action-btn-primary" title="Xem chi tiết xe đã bán">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Xem</span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.inventory.edit', $carUnit) }}" wire:navigate.hover class="c1-action-btn c1-action-btn-edit" title="Chỉnh sửa thông tin xe">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                                <span>Sửa</span>
                                            </a>
                                        @endif

                                        @if (in_array($carUnit->status, ['draft', 'archived'], true))
                                            <button
                                                type="button"
                                                wire:click="publish({{ $carUnit->id }})"
                                                wire:loading.attr="disabled"
                                                class="c1-action-btn c1-action-btn-success"
                                                title="Đăng bán xe lên sàn"
                                            >
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                <span>Đăng bán</span>
                                            </button>
                                        @endif

                                        @if (in_array($carUnit->status, ['available', 'draft'], true))
                                            <button
                                                type="button"
                                                wire:click="archive({{ $carUnit->id }})"
                                                wire:loading.attr="disabled"
                                                class="c1-action-btn c1-action-btn-icon c1-action-btn-warning"
                                                title="Chuyển vào lưu kho"
                                            >
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                            </button>
                                        @endif

                                        @if ($carUnit->status !== 'sold' && $carUnit->status !== 'on_hold')
                                            <button
                                                type="button"
                                                wire:click="delete({{ $carUnit->id }})"
                                                wire:loading.attr="disabled"
                                                onclick="return confirm('Xác nhận xóa xe [{{ $carUnit->stock_code }}]?')"
                                                class="c1-action-btn c1-action-btn-icon c1-action-btn-danger"
                                                title="Xóa xe"
                                            >
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
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

            <div class="c1-pagination-bar" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 12px;">
                <div class="c1-pagination-info" style="font-size: 13px; color: var(--c1-text-muted);">
                    @if ($carUnits->total() > 0)
                        Hiển thị <strong>{{ $carUnits->firstItem() }}</strong> - <strong>{{ $carUnits->lastItem() }}</strong> trên tổng số <strong>{{ number_format($carUnits->total()) }}</strong> xe
                    @else
                        0 xe trong kho
                    @endif
                </div>
                <div>
                    {{ $carUnits->links('admin.partials.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
