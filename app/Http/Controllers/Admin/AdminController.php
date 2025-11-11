<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'admins', 'Viewed admin users list');

        $query = Admin::with('role');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $admins = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = AdminRole::active()->get();

        return view('admin.admins.index', compact('admins', 'roles'));
    }

    public function create()
    {
        auth('admin')->user()->logActivity('view', 'admins', 'Accessed create admin form');

        $roles = AdminRole::active()->get();
        return view('admin.admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:admin_roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        try {
            $newAdmin = Admin::create([
                'role_id' => $validated['role_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'is_active' => $request->has('is_active') ? 1 : 0,
                'email_verified_at' => now(),
            ]);

            auth('admin')->user()->logActivity('create', 'admins', "Created admin user: {$newAdmin->name}");

            return redirect()->route('admin.admins.index')
                ->with('success', __('Admin user created successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating admin: ') . $e->getMessage());
        }
    }

    public function show($id)
    {
        $adminUser = Admin::with(['role.permissions', 'activityLogs'])->findOrFail($id);

        // Get recent activity
        $recentActivity = $adminUser->activityLogs()
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        auth('admin')->user()->logActivity('view', 'admins', "Viewed admin user: {$adminUser->name}");

        return view('admin.admins.show', compact('adminUser', 'recentActivity'));
    }

    public function edit($id)
    {
        $adminUser = Admin::findOrFail($id);
        $roles = AdminRole::active()->get();

        // Prevent non-super-admin from editing super-admin
        if ($adminUser->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin()) {
            return back()->with('error', __('You do not have permission to edit this admin.'));
        }

        auth('admin')->user()->logActivity('view', 'admins', "Accessed edit form for admin: {$adminUser->name}");

        return view('admin.admins.edit', compact('adminUser', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $adminUser = Admin::findOrFail($id);

        // Prevent non-super-admin from editing super-admin
        if ($adminUser->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin()) {
            return back()->with('error', __('You do not have permission to edit this admin.'));
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:admin_roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        try {
            $updateData = [
                'role_id' => $validated['role_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $adminUser->update($updateData);

            auth('admin')->user()->logActivity('update', 'admins', "Updated admin user: {$adminUser->name}");

            return redirect()->route('admin.admins.index')
                ->with('success', __('Admin user updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating admin: ') . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $adminUser = Admin::findOrFail($id);

            // Prevent deleting self
            if ($adminUser->id === auth('admin')->id()) {
                return back()->with('error', __('You cannot delete your own account.'));
            }

            // Prevent non-super-admin from deleting super-admin
            if ($adminUser->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin()) {
                return back()->with('error', __('You do not have permission to delete this admin.'));
            }

            $adminName = $adminUser->name;
            $adminUser->delete();

            auth('admin')->user()->logActivity('delete', 'admins', "Deleted admin user: {$adminName}");

            return redirect()->route('admin.admins.index')
                ->with('success', __('Admin user deleted successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting admin: ') . $e->getMessage());
        }
    }
}
