<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersAndRbacSeeder extends Seeder
{
    /**
     * Seed users, roles, permissions, and RBAC pivots.
     */
    public function run(): void
    {
        $now = now();
        $password = Hash::make('123456');

        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@showroom.test',
                'phone' => '0900000001',
                'password' => $password,
            ],
            [
                'name' => 'Sales Staff',
                'email' => 'staff@showroom.test',
                'phone' => '0900000002',
                'password' => $password,
            ],
            [
                'name' => 'John Buyer',
                'email' => 'john@example.com',
                'phone' => '0900000003',
                'password' => $password,
            ],
            [
                'name' => 'Jane Buyer',
                'email' => 'jane@example.com',
                'phone' => '0900000004',
                'password' => $password,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        $roles = [
            ['name' => 'admin', 'description' => 'Toàn quyền hệ thống'],
            ['name' => 'staff', 'description' => 'Nhân viên kinh doanh và vận hành'],
            ['name' => 'customer', 'description' => 'Tài khoản khách hàng'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                array_merge($role, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        $permissions = [
            ['name' => 'catalog.manage', 'description' => 'Quản lý hãng xe, dòng xe, phiên bản'],
            ['name' => 'inventory.manage', 'description' => 'Quản lý xe trong kho và media'],
            ['name' => 'leads.manage', 'description' => 'Quản lý lead và ghi chú lead'],
            ['name' => 'appointments.manage', 'description' => 'Quản lý lịch hẹn'],
            ['name' => 'sales.manage', 'description' => 'Tạo và cập nhật giao dịch bán xe'],
            ['name' => 'reviews.approve', 'description' => 'Kiểm duyệt đánh giá theo trim'],
            ['name' => 'settings.manage', 'description' => 'Quản lý cài đặt showroom'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                array_merge($permission, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        $userIds = DB::table('users')
            ->whereIn('email', ['admin@showroom.test', 'staff@showroom.test', 'john@example.com', 'jane@example.com'])
            ->pluck('id', 'email');

        $roleIds = DB::table('roles')
            ->whereIn('name', ['admin', 'staff', 'customer'])
            ->pluck('id', 'name');

        DB::table('user_roles')->delete();
        DB::table('user_roles')->insert([
            [
                'user_id' => $userIds['admin@showroom.test'],
                'role_id' => $roleIds['admin'],
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $userIds['staff@showroom.test'],
                'role_id' => $roleIds['staff'],
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $userIds['john@example.com'],
                'role_id' => $roleIds['customer'],
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $userIds['jane@example.com'],
                'role_id' => $roleIds['customer'],
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_column($permissions, 'name'))
            ->pluck('id', 'name');

        $staffPermissionNames = [
            'catalog.manage',
            'inventory.manage',
            'leads.manage',
            'appointments.manage',
            'sales.manage',
            'reviews.approve',
        ];

        DB::table('role_has_permissions')->delete();

        $rolePermissions = [];
        foreach ($permissionIds as $permissionName => $permissionId) {
            $rolePermissions[] = [
                'role_id' => $roleIds['admin'],
                'permission_id' => $permissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (in_array($permissionName, $staffPermissionNames, true)) {
                $rolePermissions[] = [
                    'role_id' => $roleIds['staff'],
                    'permission_id' => $permissionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('role_has_permissions')->insert($rolePermissions);
    }
}
