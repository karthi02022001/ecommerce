<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'orders', 'Viewed orders list');

        $query = Order::with(['customer', 'orderItems.product']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show($id)
    {
        $order = Order::with([
            'customer',
            'orderItems.product.translations',
            'shippingAddress',
            'billingAddress'
        ])->findOrFail($id);

        auth('admin')->user()->logActivity('view', 'orders', "Viewed order #$id");

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $order = Order::findOrFail($id);
            $oldStatus = $order->status;

            $order->update([
                'status' => $validated['status'],
                'notes' => $request->filled('notes')
                    ? ($order->notes ? $order->notes . "\n\n" . $validated['notes'] : $validated['notes'])
                    : $order->notes,
            ]);

            auth('admin')->user()->logActivity(
                'update',
                'orders',
                "Updated order #$id status from {$oldStatus} to {$validated['status']}"
            );

            return redirect()->back()
                ->with('success', __('Order status updated successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error updating order: ') . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            // Only allow deletion of cancelled orders
            if ($order->status !== 'cancelled') {
                return back()->with('error', __('Only cancelled orders can be deleted. Please cancel the order first.'));
            }

            $order->delete();

            auth('admin')->user()->logActivity('delete', 'orders', "Deleted order #$id");

            return redirect()->route('admin.orders.index')
                ->with('success', __('Order deleted successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting order: ') . $e->getMessage());
        }
    }
}
