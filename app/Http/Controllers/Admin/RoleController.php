<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\AdminPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'roles', 'Viewed roles list');

        $roles = AdminRole::withCount(['admins', 'permissions'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        auth('admin')->user()->logActivity('view', 'roles', 'Accessed create role form');

        $permissions = AdminPermission::all()->groupBy('module');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:admin_permissions,id',
        ]);

        try {
            // Generate name from display name
            $name = Str::slug($validated['display_name'], '_');
            $originalName = $name;
            $counter = 1;
            while (AdminRole::where('name', $name)->exists()) {
                $name = $originalName . '_' . $counter;
                $counter++;
            }

            $role = AdminRole::create([
                'name' => $name,
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Attach permissions
            if ($request->filled('permissions')) {
                $role->permissions()->attach($request->permissions);
            }

            auth('admin')->user()->logActivity('create', 'roles', "Created role: {$role->display_name}");

            return redirect()->route('admin.roles.index')
                ->with('success', __('Role created successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating role: ') . $e->getMessage());
        }
    }

    public function show($id)
    {
        $role = AdminRole::with(['permissions', 'admins'])->findOrFail($id);

        auth('admin')->user()->logActivity('view', 'roles', "Viewed role: {$role->display_name}");

        $permissionsByModule = $role->permissions->groupBy('module');

        return view('admin.roles.show', compact('role', 'permissionsByModule'));
    }

    public function edit($id)
    {
        $role = AdminRole::with('permissions')->findOrFail($id);

        // Prevent editing super_admin role
        if ($role->name === 'super_admin') {
            return back()->with('error', __('Super Admin role cannot be edited.'));
        }

        auth('admin')->user()->logActivity('view', 'roles', "Accessed edit form for role: {$role->display_name}");

        $permissions = AdminPermission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = AdminRole::findOrFail($id);

        // Prevent editing super_admin role
        if ($role->name === 'super_admin') {
            return back()->with('error', __('Super Admin role cannot be edited.'));
        }

        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:admin_permissions,id',
        ]);

        try {
            $role->update([
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Sync permissions
            $role->permissions()->sync($request->permissions ?? []);

            auth('admin')->user()->logActivity('update', 'roles', "Updated role: {$role->display_name}");

            return redirect()->route('admin.roles.index')
                ->with('success', __('Role updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating role: ') . $e->getMessage());
        }
    }

    public function assignPermissions(Request $request, $id)
    {
        $role = AdminRole::findOrFail($id);

        // Prevent editing super_admin role
        if ($role->name === 'super_admin') {
            return back()->with('error', __('Super Admin role permissions cannot be modified.'));
        }

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:admin_permissions,id',
        ]);

        try {
            $role->permissions()->sync($request->permissions);

            auth('admin')->user()->logActivity(
                'update',
                'roles',
                "Updated permissions for role: {$role->display_name}"
            );

            return redirect()->back()
                ->with('success', __('Permissions updated successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error updating permissions: ') . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $role = AdminRole::findOrFail($id);

            // Prevent deleting default roles
            if (in_array($role->name, ['super_admin', 'admin', 'manager', 'staff'])) {
                return back()->with('error', __('Default roles cannot be deleted.'));
            }

            // Check if role has admins
            $adminsCount = $role->admins()->count();
            if ($adminsCount > 0) {
                return back()->with('error', __('Cannot delete role with :count admin users.', ['count' => $adminsCount]));
            }

            $roleName = $role->display_name;
            $role->delete();

            auth('admin')->user()->logActivity('delete', 'roles', "Deleted role: {$roleName}");

            return redirect()->route('admin.roles.index')
                ->with('success', __('Role deleted successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting role: ') . $e->getMessage());
        }
    }
}
