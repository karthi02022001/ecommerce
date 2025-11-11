<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Check if admin is authenticated
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login')
                ->with('error', __('Please login to continue.'));
        }

        $admin = auth('admin')->user();

        // Check if admin is active
        if (!$admin->is_active) {
            auth('admin')->logout();
            return redirect()->route('admin.login')
                ->with('error', __('Your account has been deactivated.'));
        }

        // Super admin has all permissions
        if ($admin->isSuperAdmin()) {
            return $next($request);
        }

        // Check if admin has the required permission
        if (!$admin->hasPermission($permission)) {
            abort(403, __('You do not have permission to access this resource.'));
        }

        return $next($request);
    }
}
