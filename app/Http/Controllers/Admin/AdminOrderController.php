<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of all orders
     */
    public function index(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.view')) {
            abort(403, __('Unauthorized action.'));
        }

        $query = Order::with(['user', 'orderItems'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number, customer name, or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_email', 'like', '%' . $search . '%');
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(20);

        // Statistics for dashboard - matching keys with the view
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => Order::where('payment_status', 'pending')->sum('total_amount'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.view')) {
            abort(403, __('Unauthorized action.'));
        }

        $order = Order::with([
            'user',
            'orderItems.product.images',
            'shippingAddress',
            'billingAddress'
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Display invoice for printing
     */
    public function invoice($id)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.view')) {
            abort(403, __('Unauthorized action.'));
        }

        $order = Order::with([
            'orderItems.product',
            'shippingAddress',
            'billingAddress'
        ])->findOrFail($id);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.edit')) {
            return back()->with('error', __('You do not have permission to edit orders.'));
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,refunded,failed'
        ]);

        $order = Order::findOrFail($id);

        $oldStatus = $order->status;
        $oldPaymentStatus = $order->payment_status;

        try {
            DB::beginTransaction();

            // Update only status if payment_status not provided (for quick updates)
            $updateData = ['status' => $request->status];
            if ($request->filled('payment_status')) {
                $updateData['payment_status'] = $request->payment_status;
            }

            $order->update($updateData);

            // Log activity
            AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => 'order_status_updated',
                'description' => "Updated order #{$order->order_number} status from {$oldStatus} to {$request->status}" .
                    ($request->filled('payment_status') ? ", payment status from {$oldPaymentStatus} to {$request->payment_status}" : ''),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            // TODO: Send email notification to customer about status change

            return back()->with('success', __('Order status updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed: ' . $e->getMessage());
            return back()->with('error', __('Failed to update order status.'));
        }
    }

    /**
     * Update order status (alternative method for modal form)
     */
    public function updateStatusModal(Request $request, $id)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.edit')) {
            return back()->with('error', __('You do not have permission to edit orders.'));
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,refunded,failed'
        ]);

        $order = Order::findOrFail($id);

        $oldStatus = $order->status;
        $oldPaymentStatus = $order->payment_status;

        try {
            DB::beginTransaction();

            $order->update([
                'status' => $request->status,
                'payment_status' => $request->payment_status
            ]);

            // Log activity
            AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => 'order_status_updated',
                'description' => "Updated order #{$order->order_number} status from {$oldStatus} to {$request->status}, payment status from {$oldPaymentStatus} to {$request->payment_status}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            // TODO: Send email notification to customer about status change

            return back()->with('success', __('Order status updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed: ' . $e->getMessage());
            return back()->with('error', __('Failed to update order status.'));
        }
    }

    /**
     * Add or update order note
     */
    public function addNote(Request $request, $id)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.edit')) {
            return back()->with('error', __('You do not have permission to edit orders.'));
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        $order = Order::findOrFail($id);

        try {
            $order->update([
                'notes' => $request->notes
            ]);

            // Log activity
            AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => 'order_note_added',
                'description' => "Added/updated note for order #{$order->order_number}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', __('Order note saved successfully.'));
        } catch (\Exception $e) {
            Log::error('Order note update failed: ' . $e->getMessage());
            return back()->with('error', __('Failed to save order note.'));
        }
    }

    /**
     * Delete order
     */
    public function destroy($id)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.delete')) {
            return back()->with('error', __('You do not have permission to delete orders.'));
        }

        $order = Order::findOrFail($id);

        try {
            DB::beginTransaction();

            // Store order number for logging
            $orderNumber = $order->order_number;

            // Delete related records
            $order->orderItems()->delete();

            // Delete order
            $order->delete();

            // Log activity
            AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => 'order_deleted',
                'description' => "Deleted order #{$orderNumber}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', __('Order deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order deletion failed: ' . $e->getMessage());
            return back()->with('error', __('Failed to delete order.'));
        }
    }

    /**
     * Export orders to CSV
     */
    public function exportCSV(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.view')) {
            abort(403, __('Unauthorized action.'));
        }

        $orders = Order::with(['user', 'orderItems'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'orders_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Order Number',
                'Customer Name',
                'Customer Email',
                'Status',
                'Payment Status',
                'Payment Method',
                'Total Amount',
                'Items Count',
                'Order Date'
            ]);

            // Data rows
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_email,
                    ucfirst($order->status),
                    ucfirst($order->payment_status),
                    ucfirst($order->payment_method),
                    $order->total_amount,
                    $order->orderItems->count(),
                    $order->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show refund form
     */
    public function showRefundForm($id)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.refund')) {
            abort(403, __('Unauthorized action.'));
        }

        $order = Order::with(['orderItems.product', 'user'])->findOrFail($id);

        return view('admin.orders.refund', compact('order'));
    }

    /**
     * Process manual refund from admin panel
     */
    public function refund(Request $request, $id)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('orders.refund')) {
            return back()->with('error', __('You do not have permission to process refunds.'));
        }

        $order = Order::with('orderItems.product')->findOrFail($id);

        // Validate refund request
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $order->total_amount,
            'refund_reason' => 'required|string|max:500',
            'restore_stock' => 'boolean'
        ]);

        $refundAmount = $request->input('refund_amount');
        $refundReason = $request->input('refund_reason');
        $restoreStock = $request->input('restore_stock', true);

        try {
            DB::beginTransaction();

            // Process Stripe refund if applicable
            if (
                $order->payment_method === 'stripe' &&
                $order->payment_status === 'paid' &&
                $order->transaction_id
            ) {

                $isFullRefund = $refundAmount >= $order->total_amount;

                $refundSuccess = $this->processAdminStripeRefund(
                    $order,
                    $refundAmount,
                    $refundReason,
                    $isFullRefund
                );

                if (!$refundSuccess) {
                    DB::rollBack();
                    return back()->with('error', __('Failed to process refund. Please check logs.'));
                }
            }

            // Update order status
            $order->update([
                'status' => $refundAmount >= $order->total_amount ? 'cancelled' : 'refunded',
                'payment_status' => 'refunded',
                'cancelled_at' => now(),
                'cancellation_reason' => $refundReason
            ]);

            // Restore stock if requested
            if ($restoreStock) {
                foreach ($order->orderItems as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            // Log admin activity
            AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => 'refund_processed',
                'description' => "Refunded order #{$order->order_number} - Amount: $refundAmount",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', __('Refund processed successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin refund failed: ' . $e->getMessage());
            return back()->with('error', __('Failed to process refund. Please try again.'));
        }
    }

    /**
     * Process Stripe refund from admin panel
     */
    private function processAdminStripeRefund($order, $amount, $reason, $isFullRefund)
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $refundData = [
                'payment_intent' => $order->transaction_id,
                'amount' => $amount * 100, // Convert to cents
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'refunded_by' => 'admin',
                    'admin_id' => auth('admin')->id(),
                    'admin_reason' => $reason
                ]
            ];

            // For partial refunds, specify amount
            if (!$isFullRefund) {
                $refundData['amount'] = $amount * 100;
            }

            $refund = \Stripe\Refund::create($refundData);

            Log::info('Admin Stripe refund processed', [
                'order_id' => $order->id,
                'refund_id' => $refund->id,
                'amount' => $amount,
                'is_full_refund' => $isFullRefund,
                'admin_id' => auth('admin')->id()
            ]);

            $order->update([
                'refund_id' => $refund->id,
                'refund_status' => $refund->status
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Admin Stripe refund error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check refund status
     */
    public function checkRefundStatus($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->refund_id) {
            return response()->json([
                'status' => 'no_refund',
                'message' => __('No refund processed for this order.')
            ]);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $refund = \Stripe\Refund::retrieve($order->refund_id);

            // Update local status if different
            if ($refund->status !== $order->refund_status) {
                $order->update(['refund_status' => $refund->status]);
            }

            return response()->json([
                'status' => $refund->status,
                'amount' => $refund->amount / 100,
                'created' => date('Y-m-d H:i:s', $refund->created),
                'reason' => $refund->reason
            ]);
        } catch (\Exception $e) {
            Log::error('Refund status check failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to check refund status.')
            ], 500);
        }
    }
}
