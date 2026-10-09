@php
    $unreadCount = $accountSummary['unreadNotificationCount'] ?? 0;
@endphp

<div class="account-card">
    <div class="account-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="account-card-title">
                <i class="fa-solid fa-bell me-2 text-primary"></i> Hộp thư thông báo
                @if ($unreadCount > 0)
                    <span class="badge-status status-danger ms-2">{{ $unreadCount }} tin mới</span>
                @endif
            </h3>
            <p class="account-card-subtitle">
                Theo dõi cập nhật về lịch hẹn lái thử, hợp đồng bàn giao xe và phản hồi từ ban quản trị.
            </p>
        </div>

        @if ($unreadCount > 0)
            <form action="{{ route('account.notifications.read-all') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-outline-custom btn-sm">
                    <i class="fa-solid fa-check-double me-1 text-primary"></i> Đánh dấu đã đọc tất cả
                </button>
            </form>
        @endif
    </div>

    @if ($accountNotifications->isEmpty())
        <div class="account-empty-state py-5">
            <div class="empty-icon-wrap">
                <i class="fa-regular fa-bell-slash text-muted"></i>
            </div>
            <h4 class="empty-title">Không có thông báo mới</h4>
            <p class="empty-desc">
                Hiện tại bạn không có thông báo nào. Các cập nhật quan trọng từ Showroom sẽ xuất hiện tại đây.
            </p>
        </div>
    @else
        <div class="account-notification-stream">
            @foreach ($accountNotifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = $notification->read_at === null;
                    $actionUrl = $data['action_url'] ?? null;
                    $iconClass = $data['icon'] ?? 'fa-solid fa-bell';
                @endphp
                <div class="notification-stream-item {{ $isUnread ? 'is-unread' : '' }}">
                    <div class="notification-icon-wrap">
                        <i class="{{ $iconClass }}"></i>
                    </div>

                    <div class="notification-main">
                        <div class="notification-head">
                            <h4 class="notification-title">
                                {{ $data['title'] ?? 'Thông báo từ Showroom' }}
                                @if ($isUnread)
                                    <span class="unread-dot" title="Chưa đọc"></span>
                                @endif
                            </h4>
                            <span class="notification-time">
                                <i class="fa-regular fa-clock me-1"></i> {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('H:i d/m/Y') }})
                            </span>
                        </div>

                        <p class="notification-message">{{ $data['message'] ?? '' }}</p>

                        <div class="notification-footer">
                            <div class="footer-left">
                                @if ($actionUrl)
                                    <a href="{{ $actionUrl }}" class="notification-action-link">
                                        Xem chi tiết <i class="fa-solid fa-angle-right ms-1"></i>
                                    </a>
                                @endif
                            </div>

                            <div class="footer-right">
                                @if ($isUnread)
                                    <form action="{{ route('account.notifications.read', $notification->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="notification-btn-action" title="Đánh dấu đã đọc">
                                            <i class="fa-solid fa-check me-1"></i> Đã đọc
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('account.notifications.destroy', $notification->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="notification-btn-action text-danger" title="Xóa thông báo">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($accountNotifications->hasPages())
            <div class="account-pagination mt-4">
                {{ $accountNotifications->links() }}
            </div>
        @endif
    @endif
</div>
