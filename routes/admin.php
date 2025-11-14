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
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\CmsController;
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
| IMPORTANT: Routes with {id} parameter must come AFTER specific routes
| like /create, /edit to avoid matching issues.
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

        // ========================================
        // PRODUCTS - FIXED ORDER
        // ========================================
        // CREATE routes must come BEFORE {id} routes
        Route::middleware(['admin.permission:products.create'])->group(function () {
            Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
        });

        // EDIT routes with {id}/edit pattern
        Route::middleware(['admin.permission:products.edit'])->group(function () {
            Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        });

        // DELETE routes
        Route::middleware(['admin.permission:products.delete'])->group(function () {
            Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        });

        // VIEW routes - Must come LAST because {id} matches everything
        Route::middleware(['admin.permission:products.view'])->group(function () {
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
        });

        // ========================================
        // CATEGORIES
        // ========================================
        // Categories (with permissions)
        Route::middleware(['admin.permission:categories.view'])->group(function () {
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
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



        // ===============================
        // ADMIN ORDER MANAGEMENT ROUTES
        // ===============================
        // Order Management Routes
        Route::prefix('orders')->name('orders.')->group(function () {

            // List all orders (requires orders.view permission)
            Route::get('/', [AdminOrderController::class, 'index'])
                ->middleware('admin.permission:orders.view')
                ->name('index');

            // Show order details (requires orders.view permission)
            Route::get('/{id}', [AdminOrderController::class, 'show'])
                ->middleware('admin.permission:orders.view')
                ->name('show');

            // View/Print invoice (requires orders.view permission)
            Route::get('/{id}/invoice', [AdminOrderController::class, 'invoice'])
                ->middleware('admin.permission:orders.view')
                ->name('invoice');

            // Update order status (requires orders.edit permission)
            Route::patch('/{id}/status', [AdminOrderController::class, 'updateStatusModal'])
                ->middleware('admin.permission:orders.edit')
                ->name('updateStatus');

            // Quick status update from index page (requires orders.edit permission)  
            Route::post('/{id}/update-status', [AdminOrderController::class, 'updateStatus'])
                ->middleware('admin.permission:orders.edit')
                ->name('update-status');


            // Add order note (requires orders.edit permission)
            Route::patch('/{id}/note', [AdminOrderController::class, 'addNote'])
                ->middleware('admin.permission:orders.edit')
                ->name('addNote');

            // Refund Routes (requires orders.refund permission)
            Route::middleware('admin.permission:orders.refund')->group(function () {
                Route::get('/{id}/refund', [AdminOrderController::class, 'showRefundForm'])
                    ->name('refund.form');

                Route::patch('/{id}/refund', [AdminOrderController::class, 'refund'])
                    ->name('refund.process');

                Route::get('/{id}/refund-status', [AdminOrderController::class, 'checkRefundStatus'])
                    ->name('refund.status');
            });

            // Delete order (requires orders.delete permission)
            Route::delete('/{id}', [AdminOrderController::class, 'destroy'])
                ->middleware('admin.permission:orders.delete')
                ->name('destroy');

            // Export orders (requires orders.view permission)
            Route::get('/export/csv', [AdminOrderController::class, 'exportCSV'])
                ->middleware('admin.permission:orders.view')
                ->name('export.csv');

            Route::get('/export/excel', [AdminOrderController::class, 'exportExcel'])
                ->middleware('admin.permission:orders.view')
                ->name('export.excel');
            Route::middleware('admin.permission:orders.view')->group(function () {
                // Send invoice email
                Route::post('/{id}/send-invoice', [AdminOrderController::class, 'sendInvoice'])
                    ->name('sendInvoice');

                // Resend order confirmation
                Route::post('/{id}/resend-confirmation', [AdminOrderController::class, 'resendConfirmation'])
                    ->name('resendConfirmation');
            });
        });




        // ========================================
        // CUSTOMERS - FIXED ORDER
        // ========================================
        Route::middleware(['admin.permission:customers.create'])->group(function () {
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        });

        Route::middleware(['admin.permission:customers.edit'])->group(function () {
            Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
        });

        Route::middleware(['admin.permission:customers.delete'])->group(function () {
            Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        });

        Route::middleware(['admin.permission:customers.view'])->group(function () {
            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        });

        // ========================================
        // ADMIN USERS - FIXED ORDER
        // ========================================
        // CREATE routes must come BEFORE {id} routes
        Route::middleware(['admin.permission:admins.create'])->group(function () {
            Route::get('/admins/create', [AdminController::class, 'create'])->name('admins.create');
            Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
        });

        // EDIT routes with {id}/edit pattern
        Route::middleware(['admin.permission:admins.edit'])->group(function () {
            Route::get('/admins/{id}/edit', [AdminController::class, 'edit'])->name('admins.edit');
            Route::put('/admins/{id}', [AdminController::class, 'update'])->name('admins.update');
        });

        // DELETE routes
        Route::middleware(['admin.permission:admins.delete'])->group(function () {
            Route::delete('/admins/{id}', [AdminController::class, 'destroy'])->name('admins.destroy');
        });

        // VIEW routes - Must come LAST because {id} matches everything
        Route::middleware(['admin.permission:admins.view'])->group(function () {
            Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
            Route::get('/admins/{id}', [AdminController::class, 'show'])->name('admins.show');
        });

        // ========================================
        // ROLES & PERMISSIONS - FIXED ORDER
        // ========================================
        // CREATE routes first
        Route::middleware(['admin.permission:roles.create'])->group(function () {
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        });

        // EDIT routes with {id}/edit pattern
        Route::middleware(['admin.permission:roles.edit'])->group(function () {
            Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
        });

        // ASSIGN permissions (special action with {id}/permissions pattern)
        Route::middleware(['admin.permission:roles.assign'])->group(function () {
            Route::post('/roles/{id}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
        });

        // DELETE routes
        Route::middleware(['admin.permission:roles.delete'])->group(function () {
            Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // VIEW routes - Must come LAST
        Route::middleware(['admin.permission:roles.view'])->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
        });

        // ========================================
        // SETTINGS
        // ========================================
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

        // ========================================
        // REPORTS
        // ========================================
        Route::middleware(['admin.permission:reports.view'])->group(function () {
            Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
            Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        });

        // ========================================
        // ACTIVITY LOG
        // ========================================
        Route::middleware(['admin.role:super_admin'])->group(function () {
            Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
        });
    });

    // View routes
    Route::middleware(['admin.permission:reviews.view'])->group(function () {
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{id}', [AdminReviewController::class, 'show'])->name('reviews.show');
    });

    // Approve/Reject routes
    Route::middleware(['admin.permission:reviews.approve'])->group(function () {
        Route::post('/reviews/{id}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{id}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
        Route::post('/reviews/bulk-approve', [AdminReviewController::class, 'bulkApprove'])->name('reviews.bulk-approve');
    });

    // Respond routes
    Route::middleware(['admin.permission:reviews.respond'])->group(function () {
        Route::post('/reviews/{id}/respond', [AdminReviewController::class, 'respond'])->name('reviews.respond');
        Route::delete('/reviews/{id}/response', [AdminReviewController::class, 'removeResponse'])->name('reviews.remove-response');
    });

    // Delete routes
    Route::middleware(['admin.permission:reviews.delete'])->group(function () {
        Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::post('/reviews/bulk-delete', [AdminReviewController::class, 'bulkDelete'])->name('reviews.bulk-delete');
    });




    Route::prefix('cms')->name('cms.')->group(function () {

        // View
        Route::middleware('admin.permission:cms.view')->group(function () {
            Route::get('/', [CmsController::class, 'index'])->name('index');
        });

        // Create
        Route::middleware('admin.permission:cms.create')->group(function () {
            Route::get('/create', [CmsController::class, 'create'])->name('create');
            Route::post('/', [CmsController::class, 'store'])->name('store');
        });

        // Edit / Update / Status
        Route::middleware('admin.permission:cms.edit')->group(function () {
            Route::get('/{id}/edit', [CmsController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CmsController::class, 'update'])->name('update');
            Route::post('/{id}/toggle-status', [CmsController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Delete
        Route::middleware('admin.permission:cms.delete')->group(function () {
            Route::delete('/{id}', [CmsController::class, 'destroy'])->name('destroy');
        });
    });

});
