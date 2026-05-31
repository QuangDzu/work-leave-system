<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles       = DB::table('roles')->pluck('id', 'name');
        $permissions = DB::table('permissions')->pluck('id', 'name');

        $map = [
            'admin' => [
                'leave.view',
                'leave.create',
                'leave.edit',
                'leave.delete',
                'leave.approve',
                'user.view',
                'user.create',
                'user.edit',
                'user.delete',
                'report.view',
                'setting.manage',
            ],
            'manager' => [
                'leave.view',
                'leave.approve',
                'user.view',
                'report.view',
            ],
            'employee' => [
                'leave.view',
                'leave.create',
                'leave.edit',
                'leave.delete',
            ],
        ];

        // Clear existing
        DB::table('role_permission')->truncate();

        foreach ($map as $roleName => $permNames) {
            $roleId = $roles[$roleName] ?? null;
            if (!$roleId) continue;

            foreach ($permNames as $permName) {
                $permId = $permissions[$permName] ?? null;
                if (!$permId) continue;

                DB::table('role_permission')->insertOrIgnore([
                    'role_id'       => $roleId,
                    'permission_id' => $permId,
                ]);
            }
        }
    }
}
