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
                'name' => 'Nguyễn Văn Quản',
                'email' => 'staff@showroom.test',
                'phone' => '0900000002',
                'password' => $password,
            ],
            [
                'name' => 'Hoàng Văn Nam',
                'email' => 'hoang.sales@showroom.test',
                'phone' => '0900000010',
                'password' => $password,
            ],
            [
                'name' => 'Trần Thùy Linh',
                'email' => 'linh.sales@showroom.test',
                'phone' => '0900000011',
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
            [
                'name' => 'Nguyễn Anh Tuấn',
                'email' => 'tuan.nguyen@gmail.com',
                'phone' => '0912345678',
                'password' => $password,
            ],
            [
                'name' => 'Lê Thu Hương',
                'email' => 'huong.le@gmail.com',
                'phone' => '0983222333',
                'password' => $password,
            ],
            [
                'name' => 'Trần Đình Quang',
                'email' => 'quang.tran@gmail.com',
                'phone' => '0904555666',
                'password' => $password,
            ],
            [
                'name' => 'Phạm Phương Thảo',
                'email' => 'thao.pham@gmail.com',
                'phone' => '0978111222',
                'password' => $password,
            ],
            [
                'name' => 'Hoàng Trung Dũng',
                'email' => 'dung.hoang@gmail.com',
                'phone' => '0936777888',
                'password' => $password,
            ],
            [
                'name' => 'Vũ Tuyết Mai',
                'email' => 'mai.vu@gmail.com',
                'phone' => '0965999000',
                'password' => $password,
            ],
            [
                'name' => 'Đỗ Thành Đạt',
                'email' => 'dat.do@gmail.com',
                'phone' => '0942888111',
                'password' => $password,
            ],
            [
                'name' => 'Bùi Hải Yến',
                'email' => 'yen.bui@gmail.com',
                'phone' => '0925333444',
                'password' => $password,
            ],
            [
                'name' => 'Nguyễn Đăng Khoa',
                'email' => 'khoa.nguyen@gmail.com',
                'phone' => '0918666777',
                'password' => $password,
            ],
            [
                'name' => 'David Miller',
                'email' => 'david.miller@example.com',
                'phone' => '0909123456',
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
            ['name' => 'customers.manage', 'description' => 'Quản lý tài khoản khách hàng và hồ sơ 360'],
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

        $userIds = DB::table('users')->pluck('id', 'email');
        $roleIds = DB::table('roles')->pluck('id', 'name');

        $staffEmails = [
            'staff@showroom.test',
            'hoang.sales@showroom.test',
            'linh.sales@showroom.test',
        ];

        $customerEmails = [
            'john@example.com',
            'jane@example.com',
            'tuan.nguyen@gmail.com',
            'huong.le@gmail.com',
            'quang.tran@gmail.com',
            'thao.pham@gmail.com',
            'dung.hoang@gmail.com',
            'mai.vu@gmail.com',
            'dat.do@gmail.com',
            'yen.bui@gmail.com',
            'khoa.nguyen@gmail.com',
            'david.miller@example.com',
        ];

        DB::table('user_roles')->delete();

        $userRoles = [];
        if (isset($userIds['admin@showroom.test'])) {
            $userRoles[] = [
                'user_id' => $userIds['admin@showroom.test'],
                'role_id' => $roleIds['admin'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($staffEmails as $email) {
            if (isset($userIds[$email])) {
                $userRoles[] = [
                    'user_id' => $userIds[$email],
                    'role_id' => $roleIds['staff'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach ($customerEmails as $email) {
            if (isset($userIds[$email])) {
                $userRoles[] = [
                    'user_id' => $userIds[$email],
                    'role_id' => $roleIds['customer'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('user_roles')->insert($userRoles);

        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_column($permissions, 'name'))
            ->pluck('id', 'name');

        $staffPermissionNames = [
            'catalog.manage',
            'inventory.manage',
            'customers.manage',
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
