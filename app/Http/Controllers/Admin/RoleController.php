<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderByDesc('priority')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Role::availablePermissions();
        return view('admin.roles.form', [
            'role' => null,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|alpha_dash',
            'display_name' => 'required|string|max:100',
            'color' => 'required|string|max:7',
            'priority' => 'required|integer|min:0|max:999',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'color' => $validated['color'],
            'priority' => $validated['priority'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created!');
    }

    public function edit(Role $role)
    {
        $permissions = Role::availablePermissions();
        return view('admin.roles.form', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|alpha_dash|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:100',
            'color' => 'required|string|max:7',
            'priority' => 'required|integer|min:0|max:999',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'color' => $validated['color'],
            'priority' => $validated['priority'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated!');
    }

    public function destroy(Role $role)
    {
        $role->users()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted!');
    }
}
