<?php

namespace App\Services\Admin;

use App\Models\User;

class CustomerManagementService
{
    /**
     * @return array{total: int, purchased: int, pending_appointments: int, new_this_month: int}
     */
    public function getStats(): array
    {
        $total = User::query()->customer()->count();
        $purchased = User::query()->customer()->whereHas('purchases')->count();
        $pendingAppointments = User::query()->customer()
            ->whereHas('appointments', function ($query): void {
                $query->whereIn('status', ['pending', 'confirmed'])
                    ->where('scheduled_at', '>=', now());
            })
            ->count();
        $newThisMonth = User::query()->customer()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return [
            'total' => $total,
            'purchased' => $purchased,
            'pending_appointments' => $pendingAppointments,
            'new_this_month' => $newThisMonth,
        ];
    }

    public function toggleStatus(User $customer, ?User $actor = null): bool
    {
        $newStatus = ! $customer->is_active;

        $customer->update([
            'is_active' => $newStatus,
        ]);

        return $newStatus;
    }
}
