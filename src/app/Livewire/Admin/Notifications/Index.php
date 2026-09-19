<?php

namespace App\Livewire\Admin\Notifications;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\User;
use App\Services\Admin\NotificationService;
use Illuminate\Contracts\View\View;
use Livewire\WithPagination;

class Index extends AdminPageComponent
{
    use WithPagination;

    public string $activeCategory = 'all';

    public string $search = '';

    protected function requiredPermission(): ?string
    {
        return null;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setCategory(string $category): void
    {
        $this->activeCategory = $category;
        $this->resetPage();
    }

    public function markAsRead(string $id, NotificationService $service): void
    {
        $user = auth()->user();
        if ($user instanceof User) {
            $service->markAsRead($user, $id);
            $this->toast('success', 'Đã đánh dấu thông báo là đã đọc.');
        }
    }

    public function markAllAsRead(NotificationService $service): void
    {
        $user = auth()->user();
        if ($user instanceof User) {
            $count = $service->markAllAsRead($user);
            $this->toast('success', "Đã đánh dấu tất cả {$count} thông báo là đã đọc.");
        }
    }

    public function deleteNotification(string $id, NotificationService $service): void
    {
        $user = auth()->user();
        if ($user instanceof User) {
            $service->deleteNotification($user, $id);
            $this->toast('success', 'Đã xóa thông báo.');
        }
    }

    public function deleteRead(NotificationService $service): void
    {
        $user = auth()->user();
        if ($user instanceof User) {
            $count = $service->deleteReadNotifications($user);
            $this->toast('success', "Đã xóa {$count} thông báo đã đọc.");
        }
    }

    public function render(NotificationService $service): View
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $notifications = $service->getPaginatedNotifications(
            $user,
            perPage: 15,
            category: $this->activeCategory,
            search: $this->search
        );

        $totalCount = $user->notifications()->count();
        $unreadCount = $service->getUnreadCount($user);

        return view('livewire.admin.notifications.index', [
            'notifications' => $notifications,
            'totalCount' => $totalCount,
            'unreadCount' => $unreadCount,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Trung tâm thông báo',
        ]));
    }
}
