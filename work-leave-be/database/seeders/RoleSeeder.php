<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'         => 'admin',
                'display_name' => 'Quản trị viên',
                'description'  => 'Toàn quyền hệ thống',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'manager',
                'display_name' => 'Quản lý',
                'description'  => 'Duyệt đơn nghỉ và xem nhân sự',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'employee',
                'display_name' => 'Nhân viên',
                'description'  => 'Tạo và quản lý đơn nghỉ của bản thân',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['name' => $role['name']], $role);
        }
    }
}
