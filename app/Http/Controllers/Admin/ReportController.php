<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Sales Report
    public function sales(Request $request)
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'reports', 'Viewed sales report');

        // Date range
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Sales statistics
        $stats = [
            'total_sales' => Order::whereIn('status', ['completed', 'processing', 'shipped'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->sum('total_amount'),

            'total_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),

            'average_order' => Order::whereIn('status', ['completed', 'processing', 'shipped'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->avg('total_amount'),

            'total_tax' => Order::whereIn('status', ['completed', 'processing', 'shipped'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->sum('tax'),

            'total_shipping' => Order::whereIn('status', ['completed', 'processing', 'shipped'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->sum('shipping_fee'),
        ];

        // Daily sales chart
        $dailySales = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->whereIn('status', ['completed', 'processing', 'shipped'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sales by status
        $salesByStatus = Order::selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('status')
            ->get();

        // Top selling products
        $topProducts = Product::withCount(['orderItems' => function ($query) use ($dateFrom, $dateTo) {
            $query->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                $q->whereIn('status', ['completed', 'processing', 'shipped'])
                    ->whereBetween('created_at', [$dateFrom, $dateTo]);
            });
        }])
            ->with(['orderItems' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereIn('status', ['completed', 'processing', 'shipped'])
                        ->whereBetween('created_at', [$dateFrom, $dateTo]);
                });
            }])
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($product) {
                $product->total_revenue = $product->orderItems->sum('total');
                return $product;
            });

        return view('admin.reports.sales', compact('stats', 'dailySales', 'salesByStatus', 'topProducts', 'dateFrom', 'dateTo'));
    }

    // Products Report
    public function products(Request $request)
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'reports', 'Viewed products report');

        // Statistics
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
            'low_stock' => Product::whereBetween('stock_quantity', [1, 10])->count(),
            'total_inventory_value' => Product::selectRaw('SUM(price * stock_quantity) as total')->value('total'),
        ];

        // Products by category
        $productsByCategory = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('category_translations', function ($join) {
                $join->on('categories.id', '=', 'category_translations.category_id')
                    ->where('category_translations.locale', '=', app()->getLocale());
            })
            ->select('category_translations.name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.id', 'category_translations.name')
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 10)
            ->with(['category', 'primaryImage'])
            ->orderBy('stock_quantity')
            ->take(20)
            ->get();

        // Out of stock products
        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)
            ->with(['category', 'primaryImage'])
            ->take(20)
            ->get();

        // Best performing products (by revenue)
        $topPerformers = Product::withCount('orderItems')
            ->with(['orderItems' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->whereIn('status', ['completed', 'processing', 'shipped']);
                });
            }])
            ->get()
            ->map(function ($product) {
                $product->total_revenue = $product->orderItems->sum('total');
                $product->total_quantity = $product->orderItems->sum('quantity');
                return $product;
            })
            ->sortByDesc('total_revenue')
            ->take(10);

        return view('admin.reports.products', compact('stats', 'productsByCategory', 'lowStockProducts', 'outOfStockProducts', 'topPerformers'));
    }

    // Customers Report
    public function customers(Request $request)
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'reports', 'Viewed customers report');

        // Date range
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Statistics
        $stats = [
            'total_customers' => User::count(),
            'new_customers' => User::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'customers_with_orders' => User::has('orders')->count(),
            'average_customer_value' => User::withSum(['orders' => function ($query) {
                $query->whereIn('status', ['completed', 'processing', 'shipped']);
            }], 'total_amount')
                ->get()
                ->avg('orders_sum_total_amount'),
        ];

        // Customer growth chart
        $customerGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top customers by orders
        $topCustomersByOrders = User::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(10)
            ->get();

        // Top customers by spending
        $topCustomersBySpending = User::withSum(['orders' => function ($query) {
            $query->whereIn('status', ['completed', 'processing', 'shipped']);
        }], 'total_amount')
            ->orderBy('orders_sum_total_amount', 'desc')
            ->take(10)
            ->get();

        // Recent customers
        $recentCustomers = User::with(['orders' => function ($query) {
            $query->latest()->take(1);
        }])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.reports.customers', compact(
            'stats',
            'customerGrowth',
            'topCustomersByOrders',
            'topCustomersBySpending',
            'recentCustomers',
            'dateFrom',
            'dateTo'
        ));
    }
}
