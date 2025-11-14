<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'customers', 'Viewed customers list');

        $query = User::withCount('orders');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $customers = $query->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::with(['orders', 'addresses'])->findOrFail($id);

        // Get order statistics
        $stats = [
            'total_orders' => $customer->orders()->count(),
            'completed_orders' => $customer->orders()->where('status', 'delivered')->count(),
            'pending_orders' => $customer->orders()->where('status', 'pending')->count(),
            'total_spent' => $customer->orders()
                ->whereIn('status', ['delivered', 'processing', 'shipped'])
                ->sum('total_amount'),
        ];

        auth('admin')->user()->logActivity('view', 'customers', "Viewed customer: {$customer->name}");

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $customer = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);

            auth('admin')->user()->logActivity('create', 'customers', "Created customer: {$customer->name}");

            return redirect()->route('admin.customers.index')
                ->with('success', __('Customer created successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating customer: ') . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $customer = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $customer->update($updateData);

            auth('admin')->user()->logActivity('update', 'customers', "Updated customer: {$customer->name}");

            return redirect()->route('admin.customers.index')
                ->with('success', __('Customer updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating customer: ') . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $customer = User::findOrFail($id);
            $customerName = $customer->name;

            // Check if customer has orders
            $ordersCount = $customer->orders()->count();
            if ($ordersCount > 0) {
                return back()->with('error', __('Cannot delete customer with :count orders.', ['count' => $ordersCount]));
            }

            $customer->delete();

            auth('admin')->user()->logActivity('delete', 'customers', "Deleted customer: {$customerName}");

            return redirect()->route('admin.customers.index')
                ->with('success', __('Customer deleted successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting customer: ') . $e->getMessage());
        }
    }
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
                $query->whereIn('status', ['delivered', 'processing', 'shipped']);
            }], 'total_amount')
                ->get()
                ->avg('orders_sum_total_amount') ?? 0,
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
            $query->whereIn('status', ['delivered', 'processing', 'shipped']);
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
