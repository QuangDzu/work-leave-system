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
        // Gán role cho tất cả users hiện tại dựa theo type cũ
        $roleMap = [
            0 => 'admin',
            1 => 'manager',
            2 => 'employee',
        ];

        $roles = DB::table('roles')->pluck('id', 'name');

        $users = DB::table('users')->whereNull('deleted_at')->get(['id', 'type']);

        foreach ($users as $user) {
            $roleName = $roleMap[$user->type] ?? 'employee';
            $roleId   = $roles[$roleName] ?? null;

            if ($roleId) {
                DB::table('user_role')->insertOrIgnore([
                    'user_id'     => $user->id,
                    'role_id'     => $roleId,
                    'assigned_at' => now(),
                ]);
            }
        }

        // Xóa column type cũ
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('type')->default(2)->after('email_verified_at');
        });

        // Restore type từ user_role
        $roleNames = DB::table('roles')->pluck('name', 'id');
        $typeMap   = ['admin' => 0, 'manager' => 1, 'employee' => 2];

        $userRoles = DB::table('user_role')->get();
        foreach ($userRoles as $ur) {
            $roleName = $roleNames[$ur->role_id] ?? 'employee';
            DB::table('users')->where('id', $ur->user_id)->update([
                'type' => $typeMap[$roleName] ?? 2,
            ]);
        }
    }
};
