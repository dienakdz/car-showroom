<div class="c1-dash-wrapper">
    {{-- Feedback Alert --}}
    @if (($feedback['message'] ?? '') !== '')
        @php
            $isError = ($feedback['type'] ?? 'success') === 'error';
        @endphp
        <div class="c1-alert {{ $isError ? 'c1-alert-danger' : 'c1-alert-success' }} mb-4" style="padding: 12px 18px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <div class="d-flex align-items-center gap-2">
                <i class="fa {{ $isError ? 'fa-exclamation-circle' : 'fa-check-circle' }}" aria-hidden="true"></i>
                <span>{{ $feedback['message'] }}</span>
            </div>
            <button type="button" class="btn-close" wire:click="dismissFeedback" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="c1-page-header">
        <div>
            <h1 class="c1-page-title">Quản lý Đánh giá xe</h1>
            <div class="c1-page-subtitle" style="color: var(--c1-text-muted); font-size: 13px; margin-top: 4px;">
                Kiểm duyệt và quản lý nhận xét thực tế từ khách hàng theo từng phiên bản xe.
            </div>
        </div>
    </div>

    {{-- 4 Metric Cards --}}
    <div class="c1-kpi-grid">
        <button
            type="button"
            wire:click="setStatus('all')"
            class="c1-kpi-card {{ $status === '' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Tổng nhận xét</span>
                <span class="c1-pill-badge c1-pill-blue">Tất cả</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value">{{ number_format($stats['total']) }}</span>
                    <span class="c1-kpi-unit">Đánh giá</span>
                </div>
            </div>
        </button>

        <button
            type="button"
            wire:click="setStatus('pending')"
            class="c1-kpi-card {{ $status === 'pending' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Chờ kiểm duyệt</span>
                <span class="c1-pill-badge c1-pill-amber">Cần xử lý</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value" style="color: #d97706;">{{ number_format($stats['pending']) }}</span>
                    <span class="c1-kpi-unit">Chờ duyệt</span>
                </div>
            </div>
        </button>

        <button
            type="button"
            wire:click="setStatus('approved')"
            class="c1-kpi-card {{ $status === 'approved' ? 'active' : '' }}"
            style="text-align: left; width: 100%; cursor: pointer;"
        >
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Đã hiển thị</span>
                <span class="c1-pill-badge c1-pill-green">Công khai</span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value text-success">{{ number_format($stats['approved']) }}</span>
                    <span class="c1-kpi-unit">Đã duyệt</span>
                </div>
            </div>
        </button>

        <div class="c1-kpi-card">
            <div class="c1-kpi-head">
                <span class="c1-kpi-title">Điểm trung bình</span>
                <span class="c1-pill-badge c1-pill-amber">
                    <i class="fa fa-star text-warning me-1"></i>Uy tín
                </span>
            </div>
            <div class="c1-kpi-body">
                <div class="c1-kpi-val-group">
                    <span class="c1-kpi-value" style="color: #f59e0b;">{{ number_format($stats['avg_rating'], 1) }}</span>
                    <span class="c1-kpi-unit">/ 5.0 sao</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Panel: Filter Toolbar & Data Table --}}
    <div class="c1-panel c1-table-panel" style="padding: 20px 24px;">
        {{-- Toolbar --}}
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; margin-bottom: 20px;">
            {{-- Search Box --}}
            <div style="position: relative; width: 340px; max-width: 100%;">
                <i class="fa fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--c1-text-light); font-size: 13px;"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    class="form-control"
                    placeholder="Tìm theo xe, khách hàng, nội dung..."
                    style="padding-left: 34px; font-size: 13.5px; height: 40px; border-radius: 8px; border: 1px solid var(--c1-border-card); background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                >
            </div>

            {{-- Select Filters --}}
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                {{-- Status Select --}}
                <select
                    wire:model.live="status"
                    class="form-select"
                    style="width: 180px; font-size: 13.5px; height: 40px; border-radius: 8px; border: 1px solid var(--c1-border-card); background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                    aria-label="Lọc theo trạng thái"
                >
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending">Chờ kiểm duyệt ({{ $stats['pending'] }})</option>
                    <option value="approved">Đã hiển thị ({{ $stats['approved'] }})</option>
                    <option value="hidden">Đã ẩn ({{ $stats['hidden'] }})</option>
                </select>

                {{-- Rating Select --}}
                <select
                    wire:model.live="ratingFilter"
                    class="form-select"
                    style="width: 160px; font-size: 13.5px; height: 40px; border-radius: 8px; border: 1px solid var(--c1-border-card); background-color: var(--c1-bg-input); color: var(--c1-text-heading);"
                    aria-label="Lọc theo số sao"
                >
                    <option value="0">Tất cả số sao</option>
                    <option value="5">★★★★★ (5 sao)</option>
                    <option value="4">★★★★☆ (4 sao)</option>
                    <option value="3">★★★☆☆ (3 sao)</option>
                    <option value="2">★★☆☆☆ (2 sao)</option>
                    <option value="1">★☆☆☆☆ (1 sao)</option>
                </select>

                {{-- Reset Button --}}
                @if ($search !== '' || $status !== '' || $ratingFilter > 0)
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="c1-action-btn"
                        style="height: 40px; padding: 0 14px; font-size: 13px;"
                        title="Đặt lại bộ lọc"
                    >
                        <i class="fa fa-refresh me-1"></i> Đặt lại
                    </button>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="c1-table c1-review-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Phiên bản xe</th>
                        <th style="width: 18%;">Khách hàng</th>
                        <th style="width: 11%; text-align: center;">Đánh giá</th>
                        <th>Nội dung nhận xét</th>
                        <th style="width: 10%; text-align: center;">Ngày gửi</th>
                        <th style="width: 11%; text-align: center;">Trạng thái</th>
                        <th style="width: 155px; text-align: center; white-space: nowrap;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        @php
                            $makeName = $review->trim?->model?->make?->name ?? '';
                            $modelName = $review->trim?->model?->name ?? '';
                            $rawTrimName = $review->trim?->name ?? '';

                            $cleanTrimName = $rawTrimName;
                            if ($modelName !== '' && str_starts_with(strtolower($rawTrimName), strtolower($modelName))) {
                                $cleanTrimName = trim(substr($rawTrimName, strlen($modelName)));
                            }

                            $trimName = trim("{$makeName} {$modelName} {$cleanTrimName}");
                            $customerName = $review->user?->name ?? 'Khách hàng';
                            $initials = mb_strtoupper(mb_substr($customerName, 0, 1));

                            $gradients = [
                                'linear-gradient(135deg, #3b82f6, #1d4ed8)', // Blue
                                'linear-gradient(135deg, #8b5cf6, #6d28d9)', // Indigo
                                'linear-gradient(135deg, #10b981, #059669)', // Emerald
                                'linear-gradient(135deg, #f59e0b, #d97706)', // Amber
                                'linear-gradient(135deg, #ec4899, #be185d)', // Pink
                                'linear-gradient(135deg, #06b6d4, #0891b2)', // Cyan
                            ];
                            $gradient = $gradients[$review->id % count($gradients)];
                        @endphp
                        <tr wire:key="review-row-{{ $review->id }}" style="{{ $review->status === 'pending' ? 'background-color: rgba(245, 158, 11, 0.04);' : '' }}">
                            {{-- Trim Info --}}
                            <td>
                                <div style="max-width: 160px;">
                                    <div class="c1-cell-primary" style="font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $trimName !== '' ? $trimName : 'Phiên bản xe' }}">
                                        <i class="fa fa-car text-primary me-1"></i>
                                        {{ $trimName !== '' ? $trimName : 'Phiên bản xe' }}
                                    </div>
                                    @if ($makeName || $modelName)
                                        <div class="c1-cell-sub" style="margin-top: 2px; font-size: 11.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $makeName }} &bull; {{ $modelName }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Customer Info --}}
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div class="c1-review-table-avatar" style="background: {{ $gradient }};">
                                        {{ $initials }}
                                    </div>
                                    <div style="min-width: 0; max-width: 120px;">
                                        <div class="c1-cell-primary" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; font-size: 13px;" title="{{ $customerName }}">
                                            {{ $customerName }}
                                        </div>
                                        @if ($review->user?->email)
                                            <div class="c1-cell-sub" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 11px;" title="{{ $review->user->email }}">
                                                {{ $review->user->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Star Rating --}}
                            <td style="text-align: center;">
                                <div style="display: inline-flex; flex-direction: column; align-items: center; gap: 2px;">
                                    <div class="c1-review-stars">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <i class="fa fa-star {{ $s <= $review->rating ? 'star-filled' : 'star-empty' }}" style="font-size: 12px;"></i>
                                        @endfor
                                    </div>
                                    <span style="font-size: 11.5px; font-weight: 700; color: #f59e0b;">{{ $review->rating }}.0 / 5</span>
                                </div>
                            </td>

                            {{-- Comment Content --}}
                            <td>
                                <div style="font-size: 13px; color: var(--c1-text-body); line-height: 1.5; max-width: 320px;">
                                    <span style="font-style: italic;">&ldquo;{{ \Illuminate\Support\Str::limit($review->comment, 80) }}&rdquo;</span>
                                    @if (mb_strlen($review->comment) > 80)
                                        <button
                                            type="button"
                                            wire:click="viewReview({{ $review->id }})"
                                            class="btn btn-link p-0 text-primary ms-1"
                                            style="font-size: 12px; text-decoration: none; vertical-align: baseline;"
                                        >
                                            Xem thêm
                                        </button>
                                    @endif
                                </div>
                            </td>

                            {{-- Timestamp --}}
                            <td style="text-align: center;">
                                <div style="font-size: 12px; color: var(--c1-text-heading); font-weight: 500;">
                                    {{ $review->created_at->format('d/m/Y') }}
                                </div>
                                <div class="c1-cell-sub" style="font-size: 11px; margin-top: 2px;">
                                    {{ $review->created_at->format('H:i') }} ({{ $review->created_at->diffForHumans() }})
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td style="text-align: center;">
                                @if ($review->status === 'pending')
                                    <span class="c1-pill-badge c1-pill-amber">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Chờ duyệt</span>
                                    </span>
                                @elseif ($review->status === 'approved')
                                    <span class="c1-pill-badge c1-pill-green">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Đã duyệt</span>
                                    </span>
                                @else
                                    <span class="c1-pill-badge c1-pill-slate">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                        <span>Đã ẩn</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td style="text-align: center; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; gap: 5px;">
                                    @if ($review->status === 'pending')
                                        {{-- Quick Approve --}}
                                        <button
                                            type="button"
                                            wire:click="approve({{ $review->id }})"
                                            wire:loading.attr="disabled"
                                            class="c1-action-btn c1-action-btn-success"
                                            style="height: 30px; padding: 0 9px; font-size: 12px;"
                                            title="Duyệt đánh giá này để hiển thị công khai"
                                        >
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            <span>Duyệt</span>
                                        </button>

                                        {{-- View Modal (icon) --}}
                                        <button
                                            type="button"
                                            wire:click="viewReview({{ $review->id }})"
                                            class="c1-action-btn c1-action-btn-icon c1-action-btn-primary"
                                            style="width: 30px; height: 30px;"
                                            title="Xem chi tiết nội dung đánh giá"
                                        >
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                    @elseif ($review->status === 'approved')
                                        {{-- View Modal (with text) --}}
                                        <button
                                            type="button"
                                            wire:click="viewReview({{ $review->id }})"
                                            class="c1-action-btn c1-action-btn-primary"
                                            style="height: 30px; padding: 0 9px; font-size: 12px;"
                                            title="Xem chi tiết nội dung đánh giá"
                                        >
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>Xem</span>
                                        </button>

                                        {{-- Quick Hide (icon) --}}
                                        <button
                                            type="button"
                                            wire:click="hide({{ $review->id }})"
                                            wire:loading.attr="disabled"
                                            class="c1-action-btn c1-action-btn-icon c1-action-btn-warning"
                                            style="width: 30px; height: 30px;"
                                            title="Tạm ẩn đánh giá khỏi website"
                                        >
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                        </button>
                                    @else {{-- hidden --}}
                                        {{-- Quick Re-approve --}}
                                        <button
                                            type="button"
                                            wire:click="approve({{ $review->id }})"
                                            wire:loading.attr="disabled"
                                            class="c1-action-btn c1-action-btn-success"
                                            style="height: 30px; padding: 0 9px; font-size: 12px;"
                                            title="Duyệt lại để hiển thị công khai"
                                        >
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            <span>Duyệt</span>
                                        </button>

                                        {{-- View Modal (icon) --}}
                                        <button
                                            type="button"
                                            wire:click="viewReview({{ $review->id }})"
                                            class="c1-action-btn c1-action-btn-icon c1-action-btn-primary"
                                            style="width: 30px; height: 30px;"
                                            title="Xem chi tiết nội dung đánh giá"
                                        >
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                    @endif

                                    {{-- Permanent Delete --}}
                                    <button
                                        type="button"
                                        wire:click="delete({{ $review->id }})"
                                        wire:loading.attr="disabled"
                                        wire:confirm="Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá này?"
                                        class="c1-action-btn c1-action-btn-icon c1-action-btn-danger"
                                        style="width: 30px; height: 30px;"
                                        title="Xóa vĩnh viễn đánh giá"
                                    >
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px 20px;">
                                <div style="color: var(--c1-text-light); font-size: 36px; margin-bottom: 12px;">
                                    <i class="fa fa-star" style="opacity: 0.3;"></i>
                                </div>
                                <h4 style="color: var(--c1-text-heading); font-size: 16px; margin-bottom: 6px;">Không tìm thấy đánh giá nào</h4>
                                <p style="color: var(--c1-text-muted); font-size: 13.5px; margin: 0;">
                                    {{ $status === 'pending' ? 'Tuyệt vời! Hiện tại không có đánh giá nào đang chờ duyệt.' : 'Thử thay đổi từ khóa tìm kiếm hoặc đặt lại bộ lọc.' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($reviews->hasPages())
            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--c1-border-card);">
                {{ $reviews->links('admin.partials.pagination') }}
            </div>
        @endif
    </div>

    {{-- Review Detail Modal --}}
    @if ($selectedReview !== null)
        @php
            $mMakeName = $selectedReview->trim?->model?->make?->name ?? '';
            $mModelName = $selectedReview->trim?->model?->name ?? '';
            $mRawTrimName = $selectedReview->trim?->name ?? '';

            $mCleanTrimName = $mRawTrimName;
            if ($mModelName !== '' && str_starts_with(strtolower($mRawTrimName), strtolower($mModelName))) {
                $mCleanTrimName = trim(substr($mRawTrimName, strlen($mModelName)));
            }

            $mTrimName = trim("{$mMakeName} {$mModelName} {$mCleanTrimName}");
            $mCustomerName = $selectedReview->user?->name ?? 'Khách hàng';
        @endphp
        <div
            class="modal fade show"
            style="display: block; background-color: rgba(15, 23, 42, 0.65); z-index: 1060; backdrop-filter: blur(2px);"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
        >
            <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
                <div class="modal-content" style="background-color: var(--c1-bg-card); border: 1px solid var(--c1-border-card); border-radius: 12px; box-shadow: var(--c1-shadow-md); color: var(--c1-text-heading); overflow: hidden;">
                    <div class="modal-header" style="border-bottom: 1px solid var(--c1-border-card); padding: 16px 20px;">
                        <h5 class="modal-title fw-bold" style="font-size: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-star text-warning"></i>
                            <span>Chi tiết Đánh giá xe</span>
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeReviewModal" aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body" style="padding: 20px;">
                        {{-- Trim Card --}}
                        <div style="background-color: var(--c1-bg-page); border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; border: 1px solid var(--c1-border-card);">
                            <div style="font-size: 11.5px; color: var(--c1-text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Phiên bản xe được đánh giá</div>
                            <div style="font-size: 15px; font-weight: 700; color: var(--c1-text-heading); margin-top: 3px; display: flex; align-items: center; gap: 6px;">
                                <i class="fa fa-car text-primary"></i>
                                <span>{{ $mTrimName !== '' ? $mTrimName : 'Phiên bản xe' }}</span>
                            </div>
                        </div>

                        {{-- Customer & Stars --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding: 12px 16px; background-color: var(--c1-bg-page); border-radius: 8px; border: 1px solid var(--c1-border-card);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="c1-review-table-avatar" style="width: 38px; height: 38px; font-size: 14px;">
                                    {{ mb_strtoupper(mb_substr($mCustomerName, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size: 13.5px; font-weight: 700; color: var(--c1-text-heading);">
                                        {{ $mCustomerName }}
                                    </div>
                                    @if ($selectedReview->user?->email)
                                        <div style="font-size: 12px; color: var(--c1-text-muted);">
                                            {{ $selectedReview->user->email }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div style="text-align: right;">
                                <div class="c1-review-stars">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <i class="fa fa-star {{ $s <= $selectedReview->rating ? 'star-filled' : 'star-empty' }}" style="font-size: 13px;"></i>
                                    @endfor
                                </div>
                                <div style="font-size: 11.5px; color: var(--c1-text-muted); margin-top: 3px;">
                                    {{ $selectedReview->created_at->format('H:i d/m/Y') }}
                                </div>
                            </div>
                        </div>

                        {{-- Full Comment --}}
                        <div style="margin-bottom: 16px;">
                            <div style="font-size: 11.5px; color: var(--c1-text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 6px;">Nội dung nhận xét thực tế:</div>
                            <div style="background-color: var(--c1-bg-page); border-radius: 8px; padding: 14px 16px; font-size: 13.5px; line-height: 1.6; color: var(--c1-text-body); border-left: 3px solid var(--c1-primary); font-style: italic;">
                                &ldquo;{{ $selectedReview->comment }}&rdquo;
                            </div>
                        </div>

                        {{-- Current Status --}}
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 12.5px; color: var(--c1-text-muted);">Trạng thái hiện tại:</span>
                            @if ($selectedReview->status === 'pending')
                                <span class="c1-pill-badge c1-pill-amber">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Chờ kiểm duyệt</span>
                                </span>
                            @elseif ($selectedReview->status === 'approved')
                                <span class="c1-pill-badge c1-pill-green">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Đang hiển thị công khai</span>
                                </span>
                            @else
                                <span class="c1-pill-badge c1-pill-slate">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                    <span>Đang tạm ẩn</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid var(--c1-border-card); padding: 14px 20px; display: flex; justify-content: space-between;">
                        <button
                            type="button"
                            class="c1-action-btn"
                            wire:click="closeReviewModal"
                        >
                            Đóng
                        </button>

                        <div style="display: flex; gap: 8px;">
                            @if ($selectedReview->status !== 'approved')
                                <button
                                    type="button"
                                    wire:click="approve({{ $selectedReview->id }})"
                                    class="c1-action-btn c1-action-btn-success"
                                    style="height: 34px; padding: 0 14px; font-weight: 600;"
                                >
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    <span>Duyệt công khai</span>
                                </button>
                            @endif

                            @if ($selectedReview->status !== 'hidden')
                                <button
                                    type="button"
                                    wire:click="hide({{ $selectedReview->id }})"
                                    class="c1-action-btn c1-action-btn-warning"
                                    style="height: 34px; padding: 0 14px; font-weight: 600;"
                                >
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                    <span>Tạm ẩn</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .c1-review-table-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            color: #ffffff;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        }
        .c1-review-stars {
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }
        .c1-review-stars .star-filled {
            color: #f59e0b;
        }
        .c1-review-stars .star-empty {
            color: #e2e8f0;
        }
        [data-theme="dark"] .c1-review-stars .star-empty {
            color: #334155;
        }
        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        .c1-review-table th,
        .c1-review-table td {
            padding: 13px 12px;
        }
        .c1-review-table th:first-child,
        .c1-review-table td:first-child {
            padding-left: 20px;
        }
        .c1-review-table th:last-child,
        .c1-review-table td:last-child {
            padding-right: 20px;
        }
    </style>
</div>
