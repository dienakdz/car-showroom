<div class="c1-notif-container" style="position: relative;" wire:poll.45s>
    {{-- Bell Button in Header --}}
    <button
        type="button"
        class="c1-notif-wrap {{ $unreadCount > 0 ? 'has-unread' : '' }}"
        wire:click="toggleDropdown"
        title="Thông báo hệ thống"
        aria-label="Thông báo hệ thống ({{ $unreadCount }} chưa đọc)"
        aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
    >
        <i class="fa fa-bell c1-notif-icon" aria-hidden="true"></i>
        @if ($unreadCount > 0)
            <span class="c1-notif-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </button>

    {{-- Dropdown Popup Panel --}}
    @if ($isOpen)
        {{-- Backdrop for clicking outside --}}
        <div class="c1-notif-backdrop" wire:click="closeDropdown"></div>

        <div class="c1-notif-dropdown" role="dialog" aria-label="Danh sách thông báo">
            {{-- Header --}}
            <div class="c1-notif-dropdown-header">
                <div class="c1-notif-dropdown-title">
                    <span>Thông báo</span>
                    @if ($unreadCount > 0)
                        <span class="c1-notif-count-pill">{{ $unreadCount }} mới</span>
                    @endif
                </div>

                @if ($unreadCount > 0)
                    <button
                        type="button"
                        class="c1-notif-btn-readall"
                        wire:click="markAllAsRead"
                        wire:loading.attr="disabled"
                    >
                        <i class="fa fa-check-double me-1" aria-hidden="true"></i>
                        <span>Đánh dấu tất cả đã đọc</span>
                    </button>
                @endif
            </div>

            {{-- Tabs: Tất cả & Chưa đọc --}}
            <div class="c1-notif-tabs">
                <button
                    type="button"
                    class="c1-notif-tab {{ $activeTab === 'all' ? 'active' : '' }}"
                    wire:click="setTab('all')"
                >
                    Tất cả
                </button>
                <button
                    type="button"
                    class="c1-notif-tab {{ $activeTab === 'unread' ? 'active' : '' }}"
                    wire:click="setTab('unread')"
                >
                    Chưa đọc {{ $unreadCount > 0 ? "($unreadCount)" : '' }}
                </button>
            </div>

            {{-- Notifications List --}}
            <div class="c1-notif-list">
                @forelse ($notifications as $notification)
                    @php
                        $data = $notification->data ?? [];
                        $isUnread = $notification->read_at === null;
                        $category = (string) ($data['category'] ?? 'general');
                        $actionUrl = (string) ($data['action_url'] ?? route('admin.notifications.index'));
                    @endphp
                    <div
                        class="c1-notif-item {{ $isUnread ? 'is-unread' : '' }}"
                        wire:key="notif-{{ $notification->id }}"
                        wire:click="openNotification('{{ $notification->id }}', '{{ $actionUrl }}')"
                        role="button"
                        tabindex="0"
                    >
                        {{-- Icon Category Badge --}}
                        <div class="c1-notif-item-icon c1-notif-icon-{{ $category }}">
                            <i class="{{ $data['icon'] ?? 'fa fa-bell' }}" aria-hidden="true"></i>
                        </div>

                        {{-- Content --}}
                        <div class="c1-notif-item-body">
                            <div class="c1-notif-item-header">
                                <span class="c1-notif-item-title">{{ $data['title'] ?? 'Thông báo hệ thống' }}</span>
                                @if ($isUnread)
                                    <span class="c1-notif-unread-dot" title="Chưa đọc"></span>
                                @endif
                            </div>
                            <p class="c1-notif-item-desc">{{ $data['message'] ?? '' }}</p>
                            <span class="c1-notif-item-time">
                                <i class="fa fa-clock me-1" aria-hidden="true"></i>
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="c1-notif-empty">
                        <div class="c1-notif-empty-icon">
                            <i class="fa fa-bell-slash-o" aria-hidden="true"></i>
                        </div>
                        <p class="c1-notif-empty-text">
                            {{ $activeTab === 'unread' ? 'Không có thông báo chưa đọc nào.' : 'Chưa có thông báo nào trong hệ thống.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Footer: Link to Center --}}
            <div class="c1-notif-dropdown-footer">
                <a
                    href="{{ route('admin.notifications.index') }}"
                    wire:navigate
                    wire:click="closeDropdown"
                    class="c1-notif-viewall-link"
                >
                    <span>Xem tất cả thông báo</span>
                    <i class="fa fa-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    @endif
</div>
