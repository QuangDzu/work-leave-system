 <?php

    // namespace Database\Seeders;

    use App\Models\LeaveApplication;
    use App\Models\User;
    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Str;

    class DatabaseSeeder extends Seeder
    {
        public function run(): void
        {
            // Admin
            $admin = User::create([
                'id'             => Str::random(10),
                'name'           => 'Admin User',
                'email'          => 'admin@company.com',
                'password'       => Hash::make('password123'),
                'type'           => User::TYPE_ADMIN,
                'remaining_days' => 12,
            ]);

            // Manager
            $manager = User::create([
                'id'             => Str::random(10),
                'name'           => 'Manager User',
                'email'          => 'manager@company.com',
                'password'       => Hash::make('password123'),
                'type'           => User::TYPE_MANAGER,
                'remaining_days' => 12,
            ]);

            // Employee 1
            $emp1 = User::create([
                'id'             => Str::random(10),
                'name'           => 'Nguyen Van An',
                'email'          => 'an.nguyen@company.com',
                'password'       => Hash::make('password123'),
                'type'           => User::TYPE_EMPLOYEE,
                'remaining_days' => 10,
            ]);

            // Employee 2
            $emp2 = User::create([
                'id'             => Str::random(10),
                'name'           => 'Tran Thi Bich',
                'email'          => 'bich.tran@company.com',
                'password'       => Hash::make('password123'),
                'type'           => User::TYPE_EMPLOYEE,
                'remaining_days' => 12,
            ]);

            $today = now();

            // Employee 1 - Leave applications
            LeaveApplication::create([
                'id'         => Str::random(10),
                'user_id'    => $emp1->id,
                'start_date' => $today->copy()->addDays(5)->toDateString(),
                'end_date'   => $today->copy()->addDays(6)->toDateString(),
                'total_days' => 2,
                'reason'     => 'Family vacation trip to Da Nang.',
                'type'       => LeaveApplication::TYPE_ANNUAL,
                'status'     => LeaveApplication::STATUS_PENDING,
                'created_by' => $emp1->id,
                'updated_by' => $emp1->id,
            ]);

            LeaveApplication::create([
                'id'         => Str::random(10),
                'user_id'    => $emp1->id,
                'start_date' => $today->copy()->addDays(15)->toDateString(),
                'end_date'   => $today->copy()->addDays(15)->toDateString(),
                'total_days' => 1,
                'reason'     => 'Doctor appointment for annual checkup.',
                'type'       => LeaveApplication::TYPE_SICK,
                'status'     => LeaveApplication::STATUS_NEW,
                'created_by' => $emp1->id,
                'updated_by' => $emp1->id,
            ]);

            LeaveApplication::create([
                'id'         => Str::random(10),
                'user_id'    => $emp1->id,
                'start_date' => $today->copy()->subDays(20)->toDateString(),
                'end_date'   => $today->copy()->subDays(18)->toDateString(),
                'total_days' => 3,
                'reason'     => 'Personal matters.',
                'type'       => LeaveApplication::TYPE_ANNUAL,
                'status'     => LeaveApplication::STATUS_APPROVED,
                'created_by' => $emp1->id,
                'updated_by' => $manager->id,
            ]);

            // Employee 2 - Leave applications
            LeaveApplication::create([
                'id'         => Str::random(10),
                'user_id'    => $emp2->id,
                'start_date' => $today->copy()->addDays(10)->toDateString(),
                'end_date'   => $today->copy()->addDays(11)->toDateString(),
                'total_days' => 2,
                'reason'     => 'Attending a wedding ceremony.',
                'type'       => LeaveApplication::TYPE_ANNUAL,
                'status'     => LeaveApplication::STATUS_PENDING,
                'created_by' => $emp2->id,
                'updated_by' => $emp2->id,
            ]);

            LeaveApplication::create([
                'id'         => Str::random(10),
                'user_id'    => $emp2->id,
                'start_date' => $today->copy()->subDays(10)->toDateString(),
                'end_date'   => $today->copy()->subDays(9)->toDateString(),
                'total_days' => 2,
                'reason'     => 'Sick - fever and cold.',
                'type'       => LeaveApplication::TYPE_SICK,
                'status'     => LeaveApplication::STATUS_APPROVED,
                'created_by' => $emp2->id,
                'updated_by' => $admin->id,
            ]);

            LeaveApplication::create([
                'id'         => Str::random(10),
                'user_id'    => $emp2->id,
                'start_date' => $today->copy()->addDays(30)->toDateString(),
                'end_date'   => $today->copy()->addDays(31)->toDateString(),
                'total_days' => 2,
                'reason'     => 'Moving to new house - unpaid.',
                'type'       => LeaveApplication::TYPE_UNPAID,
                'status'     => LeaveApplication::STATUS_NEW,
                'created_by' => $emp2->id,
                'updated_by' => $emp2->id,
            ]);

            $this->command->info('Seeding complete.');
            $this->command->table(
                ['Role', 'Email', 'Password'],
                [
                    ['Admin',      'admin@company.com',      'password123'],
                    ['Manager',    'manager@company.com',    'password123'],
                    ['Employee 1', 'an.nguyen@company.com',  'password123'],
                    ['Employee 2', 'bich.tran@company.com',  'password123'],
                ]
            );
        }
    }
