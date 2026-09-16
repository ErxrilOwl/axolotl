<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Permission;

class PermissionController extends Controller
{
    protected int $perPage = 15;

    public function index()
    {
        $params = request()->all();
        $search = $params['search'] ?? null;

        $data = Permission::withCount('roles')
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($params['per_page'] ?? $this->perPage)
            ->withQueryString()
            ->through(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'roles_count' => $permission->roles_count,
            ]);

        return Inertia::render('permission/PermissionIndex', [
            'permissions' => $data,
            'search' => $search,
            'status' => session('status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('permission/PermissionForm');
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create([
            'name' => $fields['name'],
            'guard_name' => 'web',
        ]);

        return redirect()->route('permissions.index')->with('status', 'Permission created successfully');
    }

    public function edit(Permission $permission)
    {
        return Inertia::render('permission/PermissionForm', [
            'permission' => $permission,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $fields = $request->validate([
            'name' => ['required', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update($fields);

        return redirect()->route('permissions.index')->with('status', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->exists()) {
            return redirect()->route('permissions.index')->with('status', 'Cannot delete a permission that is still assigned to roles');
        }

        $permission->delete();

        return redirect()->route('permissions.index')->with('status', 'Permission deleted successfully');
    }
}
