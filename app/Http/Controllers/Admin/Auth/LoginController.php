<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->filled('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $admin = auth('admin')->user();

            // Check if admin is active
            if (!$admin->is_active) {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => __('Your account has been deactivated. Please contact the administrator.'),
                ]);
            }

            // Update last login
            $admin->updateLastLogin();

            // Log activity
            $admin->logActivity('login', 'auth', 'Admin logged in');

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', __('Welcome back, :name!', ['name' => $admin->name]));
        }

        return back()->withErrors([
            'email' => __('The provided credentials do not match our records.'),
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $admin = auth('admin')->user();
        
        if ($admin) {
            $admin->logActivity('logout', 'auth', 'Admin logged out');
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', __('You have been logged out successfully.'));
    }
}
