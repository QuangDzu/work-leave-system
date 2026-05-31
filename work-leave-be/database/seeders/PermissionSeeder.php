<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Leaves module
            ['name' => 'leave.view',    'display_name' => 'Xem đơn nghỉ phép',   'module' => 'leaves'],
            ['name' => 'leave.create',  'display_name' => 'Tạo đơn nghỉ phép',   'module' => 'leaves'],
            ['name' => 'leave.edit',    'display_name' => 'Sửa đơn nghỉ phép',   'module' => 'leaves'],
            ['name' => 'leave.delete',  'display_name' => 'Xóa đơn nghỉ phép',   'module' => 'leaves'],
            ['name' => 'leave.approve', 'display_name' => 'Duyệt / từ chối đơn', 'module' => 'leaves'],

            // Users module
            ['name' => 'user.view',   'display_name' => 'Xem danh sách nhân viên', 'module' => 'users'],
            ['name' => 'user.create', 'display_name' => 'Tạo tài khoản nhân viên', 'module' => 'users'],
            ['name' => 'user.edit',   'display_name' => 'Sửa thông tin nhân viên', 'module' => 'users'],
            ['name' => 'user.delete', 'display_name' => 'Xóa tài khoản nhân viên', 'module' => 'users'],

            // Reports module
            ['name' => 'report.view', 'display_name' => 'Xem báo cáo thống kê', 'module' => 'reports'],

            // Settings module
            ['name' => 'setting.manage', 'display_name' => 'Quản lý cấu hình hệ thống', 'module' => 'settings'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $perm['name']],
                array_merge($perm, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
