<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Notifications\AdminSystemNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NotificationService
{
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * @return Collection<int, DatabaseNotification>
     */
    public function getRecentNotifications(User $user, int $limit = 8, string $tab = 'all'): Collection
    {
        $query = $user->notifications();

        if ($tab === 'unread') {
            $query->whereNull('read_at');
        }

        return $query->latest()->limit($limit)->get();
    }

    /**
     * @return LengthAwarePaginator<int, DatabaseNotification>
     */
    public function getPaginatedNotifications(User $user, int $perPage = 15, string $category = 'all', ?string $search = null): LengthAwarePaginator
    {
        $query = $user->notifications();

        if ($category === 'unread') {
            $query->whereNull('read_at');
        } elseif ($category !== 'all' && $category !== '') {
            $query->where('data->category', $category);
        }

        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('data->title', 'like', $term)
                    ->orWhere('data->message', 'like', $term);
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function markAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if (! $notification instanceof DatabaseNotification) {
            return false;
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return true;
    }

    public function markAllAsRead(User $user): int
    {
        $unread = $user->unreadNotifications();
        $count = $unread->count();
        $unread->update(['read_at' => now()]);

        return $count;
    }

    public function deleteNotification(User $user, string $notificationId): bool
    {
        return (bool) $user->notifications()->where('id', $notificationId)->delete();
    }

    public function deleteReadNotifications(User $user): int
    {
        return $user->notifications()->whereNotNull('read_at')->delete();
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function notifyAdmins(
        string $category,
        string $title,
        string $message,
        string $actionUrl,
        ?string $icon = null,
        array $meta = []
    ): void {
        $defaultIcons = [
            'appointment' => 'fa fa-calendar-check-o',
            'sale' => 'fa fa-handshake-o',
            'lead' => 'fa fa-user-plus',
            'review' => 'fa fa-star',
            'inventory' => 'fa fa-car',
        ];

        $payload = [
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'icon' => $icon ?? ($defaultIcons[$category] ?? 'fa fa-bell'),
            'meta' => $meta,
        ];

        $adminUsers = User::query()
            ->where('is_active', true)
            ->whereHas('userRoles.role', function (Builder $q): void {
                $q->whereIn('name', ['admin', 'staff']);
            })
            ->get();

        foreach ($adminUsers as $admin) {
            $admin->notify(new AdminSystemNotification($payload));
        }
    }
}
