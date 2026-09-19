<?php

namespace App\Services\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class StaffManagementService
{
    /**
     * @return array{
     *     total: int,
     *     admins: int,
     *     staff: int,
     *     active: int,
     *     inactive: int
     * }
     */
    public function getStats(): array
    {
        $baseQuery = User::query()->whereHas('roles', function (Builder $query): void {
            $query->whereIn('name', ['admin', 'staff']);
        });

        $total = (clone $baseQuery)->count();
        $admins = User::query()->whereHas('roles', function (Builder $query): void {
            $query->where('name', 'admin');
        })->count();

        $staff = User::query()->whereHas('roles', function (Builder $query): void {
            $query->where('name', 'staff');
        })->count();

        $active = (clone $baseQuery)->where('is_active', true)->count();
        $inactive = (clone $baseQuery)->where('is_active', false)->count();

        return [
            'total' => $total,
            'admins' => $admins,
            'staff' => $staff,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }

    /**
     * @param  array{search?: string, role?: string, status?: string}  $filters
     * @return LengthAwarePaginator<int, User>
     */
    public function getPaginatedStaff(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query()
            ->whereHas('roles', function (Builder $q): void {
                $q->whereIn('name', ['admin', 'staff']);
            })
            ->with(['roles.permissions'])
            ->withCount([
                'salesCreated as sales_count',
                'handledAppointments as handled_appointments_count',
                'assignedLeads as assigned_leads_count',
            ]);

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $roleFilter = trim((string) ($filters['role'] ?? ''));
        if (in_array($roleFilter, ['admin', 'staff'], true)) {
            $query->whereHas('roles', function (Builder $q) use ($roleFilter): void {
                $q->where('name', $roleFilter);
            });
        }

        $statusFilter = trim((string) ($filters['status'] ?? ''));
        if ($statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        return $query
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Role>
     */
    public function getAllRoles(): Collection
    {
        return Role::query()
            ->whereIn('name', ['admin', 'staff'])
            ->with('permissions')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getAllPermissions(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     phone?: ?string,
     *     password: string,
     *     role_id: int,
     *     is_active?: bool
     * }  $data
     */
    public function createStaff(array $data, ?User $actor = null): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'phone' => ! empty($data['phone']) ? trim($data['phone']) : null,
                'password' => Hash::make($data['password']),
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            $role = Role::findOrFail($data['role_id']);
            $user->roles()->sync([$role->id]);

            return $user;
        });
    }

    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     phone?: ?string,
     *     password?: ?string,
     *     role_id: int,
     *     is_active?: bool
     * }  $data
     */
    public function updateStaff(User $staff, array $data, ?User $actor = null): User
    {
        return DB::transaction(function () use ($staff, $data, $actor): User {
            $isSelf = $actor instanceof User && $actor->id === $staff->id;

            // Guardrail: Không cho phép tự hạ quyền admin của chính mình nếu là admin duy nhất
            if ($isSelf && $staff->hasRole('admin')) {
                $newRole = Role::find($data['role_id']);
                if ($newRole && $newRole->name !== 'admin' && $this->countActiveAdmins() <= 1) {
                    throw new InvalidArgumentException('Không thể hạ quyền Quản trị viên của tài khoản này vì hệ thống cần ít nhất 1 Admin hoạt động.');
                }
            }

            $updateData = [
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'phone' => ! empty($data['phone']) ? trim($data['phone']) : null,
            ];

            if (! empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            if (! $isSelf && isset($data['is_active'])) {
                $updateData['is_active'] = (bool) $data['is_active'];
            }

            $staff->update($updateData);

            $role = Role::findOrFail($data['role_id']);
            $staff->roles()->sync([$role->id]);

            return $staff->fresh(['roles.permissions']) ?? $staff;
        });
    }

    public function toggleStatus(User $staff, User $actor): bool
    {
        if ($staff->id === $actor->id) {
            throw new InvalidArgumentException('Bạn không thể tự khóa tài khoản của chính mình.');
        }

        if ($staff->hasRole('admin') && $staff->is_active && $this->countActiveAdmins() <= 1) {
            throw new InvalidArgumentException('Không thể khóa Quản trị viên này vì hệ thống cần ít nhất 1 Admin hoạt động.');
        }

        $newStatus = ! $staff->is_active;
        $staff->update(['is_active' => $newStatus]);

        return $newStatus;
    }

    public function deleteStaff(User $staff, User $actor): bool
    {
        if ($staff->id === $actor->id) {
            throw new InvalidArgumentException('Bạn không thể tự xóa tài khoản của chính mình.');
        }

        if ($staff->hasRole('admin') && $this->countActiveAdmins() <= 1) {
            throw new InvalidArgumentException('Không thể xóa Quản trị viên này vì hệ thống cần ít nhất 1 Admin hoạt động.');
        }

        return DB::transaction(function () use ($staff): bool {
            $staff->roles()->detach();

            return (bool) $staff->delete();
        });
    }

    /**
     * @param  array<int, int>  $permissionIds
     */
    public function updateRolePermissions(int $roleId, array $permissionIds): void
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($permissionIds);
    }

    private function countActiveAdmins(): int
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', function (Builder $query): void {
                $query->where('name', 'admin');
            })
            ->count();
    }
}
