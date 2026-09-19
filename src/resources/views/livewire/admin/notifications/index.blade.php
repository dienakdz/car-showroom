<div class="c1-notif-center-page">
    {{-- Page Header --}}
    <div class="c1-page-header mb-4">
        <div>
            <h1 class="c1-page-title mb-1">Trung tâm thông báo</h1>
            <p class="c1-page-subtitle mb-0">Theo dõi mọi sự kiện quan trọng phát sinh từ website và khách hàng.</p>
        </div>
        <div class="c1-header-actions">
            @if ($unreadCount > 0)
                <button
                    type="button"
                    class="c1-btn c1-btn-primary"
                    wire:click="markAllAsRead"
                    wire:loading.attr="disabled"
                >
                    <i class="fa fa-check-double me-1" aria-hidden="true"></i>
                    <span>Đánh dấu tất cả đã đọc</span>
                </button>
            @endif

            <button
                type="button"
                class="c1-action-btn"
                wire:click="deleteRead"
                wire:loading.attr="disabled"
                title="Xóa tất cả các thông báo đã đọc để gọn danh sách"
            >
                <i class="fa fa-trash me-1" aria-hidden="true"></i>
                <span>Xóa đã đọc</span>
            </button>
        </div>
    </div>

    {{-- Filter Toolbar & Search --}}
    <div class="c1-notif-center-toolbar mb-4">
        <div class="c1-notif-center-tabs">
            <button
                type="button"
                class="c1-notif-center-tab {{ $activeCategory === 'all' ? 'active' : '' }}"
                wire:click="setCategory('all')"
            >
                Tất cả <span class="c1-tab-pill">{{ $totalCount }}</span>
            </button>
            <button
                type="button"
                class="c1-notif-center-tab {{ $activeCategory === 'unread' ? 'active' : '' }}"
                wire:click="setCategory('unread')"
            >
                Chưa đọc
                @if ($unreadCount > 0)
                    <span class="c1-tab-pill c1-tab-pill-danger">{{ $unreadCount }}</span>
                @endif
            </button>
            <button
                type="button"
                class="c1-notif-center-tab {{ $activeCategory === 'appointment' ? 'active' : '' }}"
                wire:click="setCategory('appointment')"
            >
                <i class="fa fa-calendar-check me-1 text-primary" aria-hidden="true"></i>
                Lịch hẹn lái thử
            </button>
            <button
                type="button"
                class="c1-notif-center-tab {{ $activeCategory === 'sale' ? 'active' : '' }}"
                wire:click="setCategory('sale')"
            >
                <i class="fa fa-handshake me-1 text-success" aria-hidden="true"></i>
                Hợp đồng bán xe
            </button>
            <button
                type="button"
                class="c1-notif-center-tab {{ $activeCategory === 'lead' ? 'active' : '' }}"
                wire:click="setCategory('lead')"
            >
                <i class="fa fa-user-plus me-1 text-warning" aria-hidden="true"></i>
                Khách tiềm năng Leads
            </button>
            <button
                type="button"
                class="c1-notif-center-tab {{ $activeCategory === 'review' ? 'active' : '' }}"
                wire:click="setCategory('review')"
            >
                <i class="fa fa-star me-1 text-info" aria-hidden="true"></i>
                Đánh giá xe
            </button>
        </div>

        <div class="c1-notif-center-search">
            <i class="fa fa-search" aria-hidden="true"></i>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Tìm theo nội dung..."
                aria-label="Tìm kiếm thông báo"
            >
        </div>
    </div>

    {{-- Notifications List Card --}}
    <div class="c1-notif-center-card">
        @forelse ($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $isUnread = $notification->read_at === null;
                $category = (string) ($data['category'] ?? 'general');
                $actionUrl = (string) ($data['action_url'] ?? '#');
                $actionLabel = match ($category) {
                    'appointment' => 'Xem lịch hẹn',
                    'sale' => 'Xem hợp đồng',
                    'lead' => 'Xem hồ sơ Lead',
                    'review' => 'Xem đánh giá',
                    'inventory' => 'Xem kho xe',
                    default => 'Xem chi tiết',
                };
            @endphp
            <div
                class="c1-notif-row {{ $isUnread ? 'is-unread' : '' }}"
                wire:key="center-notif-{{ $notification->id }}"
            >
                {{-- Category Icon Badge --}}
                <div class="c1-notif-item-icon c1-notif-icon-{{ $category }}">
                    <i class="{{ $data['icon'] ?? 'fa fa-bell' }}" aria-hidden="true"></i>
                </div>

                {{-- Content Body --}}
                <div class="c1-notif-row-body">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="c1-notif-row-title mb-0">{{ $data['title'] ?? 'Thông báo hệ thống' }}</h4>
                        @if ($isUnread)
                            <span class="c1-badge c1-badge-unread">Mới</span>
                        @endif
                    </div>
                    <p class="c1-notif-row-desc mb-2">{{ $data['message'] ?? '' }}</p>
                    <div class="c1-notif-row-meta">
                        <span><i class="fa fa-clock me-1"></i>{{ $notification->created_at->format('d/m/Y H:i') }} ({{ $notification->created_at->diffForHumans() }})</span>
                        @if ($notification->read_at)
                            <span class="text-muted ms-3"><i class="fa fa-check me-1"></i>Đã đọc lúc {{ $notification->read_at->format('H:i d/m') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="c1-notif-row-actions">
                    @if ($actionUrl !== '#')
                        <a
                            href="{{ $actionUrl }}"
                            wire:navigate
                            class="c1-btn c1-btn-sm c1-btn-outline-primary"
                            wire:click="markAsRead('{{ $notification->id }}')"
                        >
                            <span>{{ $actionLabel }}</span>
                            <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    @endif

                    @if ($isUnread)
                        <button
                            type="button"
                            class="c1-action-btn c1-btn-sm"
                            wire:click="markAsRead('{{ $notification->id }}')"
                            title="Đánh dấu đã đọc"
                        >
                            <i class="fa fa-check"></i>
                        </button>
                    @endif

                    <button
                        type="button"
                        class="c1-action-btn c1-btn-sm c1-btn-danger-hover"
                        wire:click="deleteNotification('{{ $notification->id }}')"
                        wire:confirm="Bạn có chắc chắn muốn xóa thông báo này?"
                        title="Xóa thông báo"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="c1-empty-state text-center py-5">
                <div class="c1-empty-icon mb-3">
                    <i class="fa fa-bell-slash fa-3x" style="opacity: 0.35;"></i>
                </div>
                <h5 class="fw-bold mb-1">Không có thông báo nào</h5>
                <p class="text-muted small mb-0">
                    {{ $activeCategory === 'unread' ? 'Bạn đã đọc hết mọi thông báo trong hệ thống.' : 'Hiện tại chưa có sự kiện nào cần xử lý.' }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
