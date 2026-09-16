<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    protected int $perPage = 15;

    public function index()
    {
        $params = request()->all();
        $search = $params['search'] ?? null;

        $data = Department::withCount('users')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($params['per_page'] ?? $this->perPage)
            ->withQueryString()
            ->through(fn ($department) => [
                'id' => $department->id,
                'name' => $department->name,
                'code' => $department->code,
                'fixed' => $department->fixed,
                'users_count' => $department->users_count,
            ]);

        return Inertia::render('department/DepartmentIndex', [
            'departments' => $data,
            'search' => $search,
            'status' => session('status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('department/DepartmentForm');
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'code' => ['required', 'max:50', 'unique:departments,code'],
        ]);

        Department::create($fields);

        return redirect()->route('departments.index')->with('status', 'Department created successfully');
    }

    public function edit(Department $department)
    {
        return Inertia::render('department/DepartmentForm', [
            'department' => $department,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Department $department)
    {
        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'code' => ['required', 'max:50', 'unique:departments,code,' . $department->id],
        ]);

        $department->update($fields);

        return redirect()->route('departments.index')->with('status', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        if ($department->fixed) {
            return redirect()->route('departments.index')->with('status', 'This department is protected and cannot be deleted');
        }

        if ($department->users()->exists()) {
            return redirect()->route('departments.index')->with('status', 'Cannot delete a department that still has users assigned to it');
        }

        $department->delete();

        return redirect()->route('departments.index')->with('status', 'Department deleted successfully');
    }
}
