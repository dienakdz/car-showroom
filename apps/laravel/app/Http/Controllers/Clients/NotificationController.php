<?php

namespace App\Http\Controllers\Clients;

use App\Models\User;
use App\Services\Admin\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends ClientBaseController
{
    public function markAsRead(Request $request, string $id, NotificationService $service): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if (! $user instanceof User) {
            abort(401);
        }

        $service->markAsRead($user, $id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unreadCount' => $service->getUnreadCount($user),
            ]);
        }

        return back();
    }

    public function markAllAsRead(Request $request, NotificationService $service): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if (! $user instanceof User) {
            abort(401);
        }

        $service->markAllAsRead($user);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unreadCount' => 0,
            ]);
        }

        $this->pushSuccessToast('Đã đánh dấu tất cả thông báo là đã đọc.');

        return back();
    }

    public function destroy(Request $request, string $id, NotificationService $service): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if (! $user instanceof User) {
            abort(401);
        }

        $service->deleteNotification($user, $id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unreadCount' => $service->getUnreadCount($user),
            ]);
        }

        $this->pushSuccessToast('Đã xóa thông báo.');

        return back();
    }
}
