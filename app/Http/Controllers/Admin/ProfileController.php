<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'profile', 'Viewed profile');

        // Get recent activity
        $recentActivity = $admin->activityLogs()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.profile.index', compact('admin', 'recentActivity'));
    }

    public function update(Request $request)
    {
        $admin = auth('admin')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_avatar' => 'boolean',
        ]);

        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];

            // Handle avatar
            if ($request->has('remove_avatar') && $request->remove_avatar) {
                if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
                    Storage::disk('public')->delete($admin->avatar);
                }
                $updateData['avatar'] = null;
            }

            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
                    Storage::disk('public')->delete($admin->avatar);
                }
                $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $admin->update($updateData);

            $admin->logActivity('update', 'profile', 'Updated profile information');

            return redirect()->back()
                ->with('success', __('Profile updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating profile: ') . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $admin = auth('admin')->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            // Verify current password
            if (!Hash::check($validated['current_password'], $admin->password)) {
                return back()->withErrors([
                    'current_password' => __('The current password is incorrect.')
                ]);
            }

            // Update password
            $admin->update([
                'password' => Hash::make($validated['password'])
            ]);

            $admin->logActivity('update', 'profile', 'Changed password');

            return redirect()->back()
                ->with('success', __('Password updated successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error updating password: ') . $e->getMessage());
        }
    }
}
