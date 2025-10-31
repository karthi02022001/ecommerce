<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        $admin->updateLastLogin();
        $admin->logActivity('view', 'dashboard', 'Viewed dashboard');

        // Statistics
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_customers' => User::count(),
            'total_categories' => Category::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
        ];

        // Revenue statistics
        $stats['total_revenue'] = Order::whereIn('status', ['completed', 'processing', 'shipped'])
            ->sum('total_amount');
        
        $stats['monthly_revenue'] = Order::whereIn('status', ['completed', 'processing', 'shipped'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $stats['today_revenue'] = Order::whereIn('status', ['completed', 'processing', 'shipped'])
            ->whereDate('created_at', today())
            ->sum('total_amount');

        // Recent orders
        $recentOrders = Order::with(['customer', 'orderItems'])
            ->latest()
            ->take(10)
            ->get();

        // Top selling products
        $topProducts = Product::withCount(['orderItems' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->whereIn('status', ['completed', 'processing', 'shipped']);
                });
            }])
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        // Sales chart data (last 7 days)
        $salesChart = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->whereIn('status', ['completed', 'processing', 'shipped'])
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('stock_quantity', '<', 10)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        // Out of stock products
        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)
            ->count();

        $stats['low_stock_count'] = $lowStockProducts->count();
        $stats['out_of_stock_count'] = $outOfStockProducts;

        // New customers this month
        $stats['new_customers'] = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.dashboard.index', compact(
            'stats',
            'recentOrders',
            'topProducts',
            'salesChart',
            'lowStockProducts'
        ));
    }
}
