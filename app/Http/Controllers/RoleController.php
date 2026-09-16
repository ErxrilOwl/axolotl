<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    protected int $perPage = 15;

    public function index()
    {
        $params = request()->all();
        $search = $params['search'] ?? null;

        $data = Role::withCount(['users', 'permissions'])
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($params['per_page'] ?? $this->perPage)
            ->withQueryString()
            ->through(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users_count,
                'permissions_count' => $role->permissions_count,
            ]);

        return Inertia::render('role/RoleIndex', [
            'roles' => $data,
            'search' => $search,
            'status' => session('status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('role/RoleForm', [
            'permissions' => $this->getGroupedPermissions(),
        ]);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $fields['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($fields['permissions'] ?? []);

        return redirect()->route('roles.index')->with('status', 'Role created successfully');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        return Inertia::render('role/RoleForm', [
            'role' => $role,
            'isEdit' => true,
            'permissions' => $this->getGroupedPermissions(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $fields = $request->validate([
            'name' => ['required', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $fields['name']]);
        $role->syncPermissions($fields['permissions'] ?? []);

        return redirect()->route('roles.index')->with('status', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return redirect()->route('roles.index')->with('status', 'Cannot delete a role that is still assigned to users');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('status', 'Role deleted successfully');
    }

    /**
     * Permissions grouped by their prefix (e.g. "users.create" -> group "users"),
     * so the form can render them as labeled checkbox sections instead of one
     * long flat list.
     */
    private function getGroupedPermissions()
    {
        return Permission::orderBy('name')->get(['id', 'name'])
            ->groupBy(fn ($permission) => explode('.', $permission->name)[0] ?? 'general')
            ->map(fn ($group) => $group->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
            ])->values())
            ->toArray();
    }
}
