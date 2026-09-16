<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds one default department and one system user per configured
     * role (config/constants.php). Safe to run multiple times — existing
     * records are matched and left alone rather than duplicated.
     */
    public function run(): void
    {
        $roleNames = config('constants.roles', []);

        $department = Department::firstOrCreate(
            ['code' => 'DEF'],
            ['name' => 'Default', 'fixed' => true],
        );

        foreach ($roleNames as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $email = strtolower("{$roleName}@axolotl.test");

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => 'User',
                    'last_name' => $roleName,
                    'password' => Hash::make(strtolower("{$roleName}@axolotl")),
                    'department_id' => $department->id,
                ],
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
