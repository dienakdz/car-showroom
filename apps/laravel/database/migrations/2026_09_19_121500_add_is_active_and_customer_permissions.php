<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('is_active')->default(true)->after('password');
            });
        }

        $now = now();

        DB::table('permissions')->updateOrInsert(
            ['name' => 'customers.manage'],
            [
                'description' => 'Quản lý tài khoản khách hàng và hồ sơ 360',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $permissionId = DB::table('permissions')->where('name', 'customers.manage')->value('id');
        $roleIds = DB::table('roles')->whereIn('name', ['admin', 'staff'])->pluck('id', 'name');

        if ($permissionId) {
            foreach (['admin', 'staff'] as $roleName) {
                if (isset($roleIds[$roleName])) {
                    DB::table('role_has_permissions')->updateOrInsert(
                        [
                            'role_id' => $roleIds[$roleName],
                            'permission_id' => $permissionId,
                        ],
                        [
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('name', 'customers.manage')->value('id');

        if ($permissionId) {
            DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }

        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('is_active');
            });
        }
    }
};
