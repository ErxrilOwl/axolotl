<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Permission;
use App\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Modules and actions this app has permissions for. Extend this list
     * as new sections get access-controlled — the naming convention
     * "{module}.{action}" is also what RoleController groups permissions
     * by in the role-assignment form.
     */
    private array $permissionModules = ['users', 'departments', 'roles', 'permissions'];

    private array $permissionActions = ['view', 'create', 'edit', 'delete'];

    /**
     * Run the database seeds.
     *
     * Seeds one default department, the full set of module.action
     * permissions, one system user per configured role (config/constants.php),
     * and grants every permission to the Admin role. Safe to run multiple
     * times — existing records are matched and left alone rather than
     * duplicated.
     */
    public function run(): void
    {
        $roleNames = config('constants.roles', []);

        $department = Department::firstOrCreate(
            ['code' => 'DEF'],
            ['name' => 'Default', 'fixed' => true],
        );

        $allPermissions = $this->seedPermissions();

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
                    // Dev/staging convenience only — never rely on a
                    // predictable password pattern in production seeding.
                    'password' => Hash::make(strtolower("{$roleName}@axolotl")),
                    'department_id' => $department->id,
                ],
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }

            // Admin gets every permission. Other seeded roles (Manager,
            // Staff) start with none — assign what they need as the app
            // grows, either here or through the Roles screen in the UI.
            if ($roleName === 'Admin') {
                $role->syncPermissions($allPermissions);
            }
        }
    }

    /**
     * Create every "{module}.{action}" permission this app currently has,
     * returning the full collection so callers don't need to re-query it.
     */
    private function seedPermissions()
    {
        $permissions = collect();

        foreach ($this->permissionModules as $module) {
            foreach ($this->permissionActions as $action) {
                $permissions->push(
                    Permission::firstOrCreate([
                        'name' => "{$module}.{$action}",
                        'guard_name' => 'web',
                    ])
                );
            }
        }

        return $permissions;
    }
}
