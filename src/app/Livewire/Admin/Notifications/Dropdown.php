<?php

namespace App\Livewire\Admin\Notifications;

use App\Models\User;
use App\Services\Admin\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;

class Dropdown extends Component
{
    public bool $isOpen = false;

    public string $activeTab = 'all';

    public function toggleDropdown(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function closeDropdown(): void
    {
        $this->isOpen = false;
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['all', 'unread'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function markAsRead(string $id, NotificationService $service): void
    {
        $user = auth()->user();
        if ($user instanceof User) {
            $service->markAsRead($user, $id);
        }
    }

    public function markAllAsRead(NotificationService $service): void
    {
        $user = auth()->user();
        if ($user instanceof User) {
            $service->markAllAsRead($user);
        }
    }

    public function openNotification(string $id, string $url, NotificationService $service): RedirectResponse
    {
        $user = auth()->user();
        if ($user instanceof User) {
            $service->markAsRead($user, $id);
        }

        return redirect()->to($url);
    }

    public function render(NotificationService $service): View
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return view('livewire.admin.notifications.dropdown', [
                'unreadCount' => 0,
                'notifications' => collect(),
            ]);
        }

        $unreadCount = $service->getUnreadCount($user);
        $notifications = $service->getRecentNotifications($user, limit: 6, tab: $this->activeTab);

        return view('livewire.admin.notifications.dropdown', [
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
