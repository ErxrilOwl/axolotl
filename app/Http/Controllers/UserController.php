<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected int $perPage = 15;

    public function index()
    {
        $params = request()->all();
        $sortBy = $params['sort_by'] ?? 'updated_at';
        $sortDir = $params['sort_dir'] ?? 'DESC';
        $perPage = $params['per_page'] ?? $this->perPage;

        $data = User::with(['department', 'roles'])
            ->filter($params)
            ->where('id', '<>', Auth::id())
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('user/UserIndex', [
            'users' => UserResource::collection($data),
            'user_name' => request()->user_name,
            'role' => request()->role,
            'status' => session('status'),
            'email' => session('email'),
            'default_password' => session('default_password'),
            'roles' => $this->getRoleOptions(),
        ]);
    }

    public function create()
    {
        return Inertia::render('user/UserForm', [
            'roles' => $this->getRoleOptions(),
            'departments' => $this->getDepartmentOptions(),
            'status' => session('status'),
            'email' => session('email'),
            'default_password' => session('default_password'),
        ]);
    }

    public function store(Request $request)
    {
        $defaultPassword = Str::random(12);

        $fields = $request->validate([
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required'],
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        $role = $fields['role'];
        unset($fields['role']);

        $fields['password'] = Hash::make($defaultPassword);

        $user = User::create($fields);
        $user->assignRole($role);

        return redirect()->route('users.create')->with([
            'status' => 'User created successfully',
            'email' => $fields['email'],
            'default_password' => $defaultPassword,
        ]);
    }

    public function edit(User $user)
    {
        $user->load(['department', 'roles']);

        return Inertia::render('user/UserForm', [
            'user' => $user,
            'isEdit' => true,
            'roles' => $this->getRoleOptions(),
            'departments' => $this->getDepartmentOptions(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validations = [
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role' => ['required'],
            'department_id' => ['required', 'exists:departments,id'],
        ];

        if ($request->password) {
            $validations['password'] = [Rules\Password::defaults()];
        }

        $fields = $request->validate($validations);

        $role = $fields['role'];
        unset($fields['role']);

        if (! empty($fields['password'])) {
            $fields['password'] = Hash::make($fields['password']);
        } else {
            unset($fields['password']);
        }

        $user->update($fields);
        $user->syncRoles([$role]);

        return redirect()->route('users.index')->with('status', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('status', 'User deleted successfully');
    }

    public function resetPassword(User $user)
    {
        $defaultPassword = Str::random(12);

        $user->update([
            'password' => Hash::make($defaultPassword),
        ]);

        return redirect()->back()->with([
            'status' => 'Password reset successfully',
            'email' => $user->email,
            'default_password' => $defaultPassword,
        ]);
    }

    private function getDepartmentOptions()
    {
        return Department::all(['id', 'name']);
    }

    private function getRoleOptions()
    {
        return Role::all(['id', 'name'])->map(fn ($role) => [
            'value' => $role->name,
            'label' => $role->name,
        ]);
    }
}
