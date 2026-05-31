<?php

namespace Database\Seeders;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions first
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $roles = DB::table('roles')->pluck('id', 'name');

        // 2. Admin
        $admin = User::create([
            'id'             => Str::random(10),
            'name'           => 'Admin User',
            'email'          => 'admin@company.com',
            'password'       => Hash::make('password123'),
            'remaining_days' => 12,
        ]);
        DB::table('user_role')->insert(['user_id' => $admin->id, 'role_id' => $roles['admin'], 'assigned_at' => now()]);

        // 3. Manager
        $manager = User::create([
            'id'             => Str::random(10),
            'name'           => 'Manager User',
            'email'          => 'manager@company.com',
            'password'       => Hash::make('password123'),
            'remaining_days' => 12,
        ]);
        DB::table('user_role')->insert(['user_id' => $manager->id, 'role_id' => $roles['manager'], 'assigned_at' => now()]);

        // 4. Employee 1
        $emp1 = User::create([
            'id'             => Str::random(10),
            'name'           => 'Nguyen Van An',
            'email'          => 'an.nguyen@company.com',
            'password'       => Hash::make('password123'),
            'remaining_days' => 10,
        ]);
        DB::table('user_role')->insert(['user_id' => $emp1->id, 'role_id' => $roles['employee'], 'assigned_at' => now()]);

        // 5. Employee 2
        $emp2 = User::create([
            'id'             => Str::random(10),
            'name'           => 'Tran Thi Bich',
            'email'          => 'bich.tran@company.com',
            'password'       => Hash::make('password123'),
            'remaining_days' => 12,
        ]);
        DB::table('user_role')->insert(['user_id' => $emp2->id, 'role_id' => $roles['employee'], 'assigned_at' => now()]);

        $today = now();

        // Leave applications
        $leaves = [
            ['user_id' => $emp1->id, 'start' => $today->copy()->addDays(5),  'end' => $today->copy()->addDays(6),  'days' => 2, 'reason' => 'Family vacation trip.',      'type' => 'annual',  'status' => 'pending',  'by' => $emp1->id,    'rejection_reason' => null],
            ['user_id' => $emp1->id, 'start' => $today->copy()->addDays(15), 'end' => $today->copy()->addDays(15), 'days' => 1, 'reason' => 'Doctor appointment.',        'type' => 'sick',    'status' => 'new',      'by' => $emp1->id,    'rejection_reason' => null],
            ['user_id' => $emp1->id, 'start' => $today->copy()->subDays(20), 'end' => $today->copy()->subDays(18), 'days' => 2, 'reason' => 'Personal matters.',          'type' => 'annual',  'status' => 'approved', 'by' => $manager->id, 'rejection_reason' => null],
            ['user_id' => $emp2->id, 'start' => $today->copy()->addDays(10), 'end' => $today->copy()->addDays(11), 'days' => 2, 'reason' => 'Wedding ceremony.',          'type' => 'annual',  'status' => 'pending',  'by' => $emp2->id,    'rejection_reason' => null],
            ['user_id' => $emp2->id, 'start' => $today->copy()->subDays(10), 'end' => $today->copy()->subDays(9),  'days' => 2, 'reason' => 'Sick - fever and cold.',    'type' => 'sick',    'status' => 'approved', 'by' => $admin->id,   'rejection_reason' => null],
            ['user_id' => $emp2->id, 'start' => $today->copy()->addDays(30), 'end' => $today->copy()->addDays(31), 'days' => 2, 'reason' => 'Moving to new house.',      'type' => 'unpaid',  'status' => 'new',      'by' => $emp2->id,    'rejection_reason' => null],
            ['user_id' => $emp1->id, 'start' => $today->copy()->subDays(5),  'end' => $today->copy()->subDays(3),  'days' => 3, 'reason' => 'Trip abroad - 1 week.',    'type' => 'annual',  'status' => 'rejected', 'by' => $manager->id, 'rejection_reason' => 'Thời gian này phòng ban đang có dự án quan trọng, không thể vắng mặt.'],
        ];

        foreach ($leaves as $l) {
            LeaveApplication::create([
                'id'               => Str::random(10),
                'user_id'          => $l['user_id'],
                'start_date'       => $l['start']->toDateString(),
                'end_date'         => $l['end']->toDateString(),
                'total_days'       => $l['days'],
                'reason'           => $l['reason'],
                'rejection_reason' => $l['rejection_reason'],
                'type'             => $l['type'],
                'status'           => $l['status'],
                'created_by'       => $l['user_id'],
                'updated_by'       => $l['by'],
            ]);
        }

        $this->command->info('Seeding complete.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',      'admin@company.com',     'password123'],
                ['Manager',    'manager@company.com',   'password123'],
                ['Employee 1', 'an.nguyen@company.com', 'password123'],
                ['Employee 2', 'bich.tran@company.com', 'password123'],
            ]
        );
    }
}
