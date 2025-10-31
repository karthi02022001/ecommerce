<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are for the admin panel with separate authentication guard.
| All routes use the 'admin' middleware for authentication.
|
*/

// Admin Authentication Routes (No middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Protected Admin Routes
    Route::middleware(['auth:admin'])->group(function () {
        // Logout
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);
        
        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
        
        // Products (with permissions)
        Route::middleware(['admin.permission:products.view'])->group(function () {
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
        });
        
        Route::middleware(['admin.permission:products.create'])->group(function () {
            Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        });
        
        Route::middleware(['admin.permission:products.edit'])->group(function () {
            Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        });
        
        Route::middleware(['admin.permission:products.delete'])->group(function () {
            Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        });
        
        // Categories (with permissions)
        Route::middleware(['admin.permission:categories.view'])->group(function () {
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        });
        
        Route::middleware(['admin.permission:categories.create'])->group(function () {
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        });
        
        Route::middleware(['admin.permission:categories.edit'])->group(function () {
            Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        });
        
        Route::middleware(['admin.permission:categories.delete'])->group(function () {
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        });
        
        // Orders (with permissions)
        Route::middleware(['admin.permission:orders.view'])->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        });
        
        Route::middleware(['admin.permission:orders.process'])->group(function () {
            Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        });
        
        Route::middleware(['admin.permission:orders.delete'])->group(function () {
            Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
        });
        
        // Customers (with permissions)
        Route::middleware(['admin.permission:customers.view'])->group(function () {
            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        });
        
        Route::middleware(['admin.permission:customers.create'])->group(function () {
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        });
        
        Route::middleware(['admin.permission:customers.edit'])->group(function () {
            Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
        });
        
        Route::middleware(['admin.permission:customers.delete'])->group(function () {
            Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        });
        
        // Admin Users (Super Admin and Admin only)
        Route::middleware(['admin.permission:admins.view'])->group(function () {
            Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
            Route::get('/admins/{id}', [AdminController::class, 'show'])->name('admins.show');
        });
        
        Route::middleware(['admin.permission:admins.create'])->group(function () {
            Route::get('/admins/create', [AdminController::class, 'create'])->name('admins.create');
            Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
        });
        
        Route::middleware(['admin.permission:admins.edit'])->group(function () {
            Route::get('/admins/{id}/edit', [AdminController::class, 'edit'])->name('admins.edit');
            Route::put('/admins/{id}', [AdminController::class, 'update'])->name('admins.update');
        });
        
        Route::middleware(['admin.permission:admins.delete'])->group(function () {
            Route::delete('/admins/{id}', [AdminController::class, 'destroy'])->name('admins.destroy');
        });
        
        // Roles & Permissions (Super Admin only)
        Route::middleware(['admin.permission:roles.view'])->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
        });
        
        Route::middleware(['admin.permission:roles.create'])->group(function () {
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        });
        
        Route::middleware(['admin.permission:roles.edit'])->group(function () {
            Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
        });
        
        Route::middleware(['admin.permission:roles.assign'])->group(function () {
            Route::post('/roles/{id}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
        });
        
        Route::middleware(['admin.permission:roles.delete'])->group(function () {
            Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });
        
        // Settings
        Route::middleware(['admin.permission:settings.view'])->group(function () {
            Route::get('/settings/general', [SettingsController::class, 'general'])->name('settings.general');
            Route::get('/settings/translations', [SettingsController::class, 'translations'])->name('settings.translations');
        });
        
        Route::middleware(['admin.permission:settings.edit'])->group(function () {
            Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.update-general');
            Route::put('/settings/translations', [SettingsController::class, 'updateTranslations'])->name('settings.update-translations');
        });
        
        Route::middleware(['admin.permission:settings.theme'])->group(function () {
            Route::get('/settings/theme', [SettingsController::class, 'theme'])->name('settings.theme');
            Route::post('/settings/theme', [SettingsController::class, 'applyTheme'])->name('settings.apply-theme');
        });
        
        // Reports
        Route::middleware(['admin.permission:reports.view'])->group(function () {
            Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
            Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        });
        
        // Activity Log (Super Admin only)
        Route::middleware(['admin.role:super_admin'])->group(function () {
            Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
        });
    });
});
