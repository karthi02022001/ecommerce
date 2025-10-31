<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Show all orders for the authenticated user
     */
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->with(['orderItems.product', 'shippingAddress'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('orders.index', compact('orders'));
    }
    
    /**
     * Show a specific order
     */
    public function show($id)
    {
        $order = auth()->user()
            ->orders()
            ->with(['orderItems.product.translations', 'shippingAddress', 'billingAddress'])
            ->findOrFail($id);
        
        return view('orders.show', compact('order'));
    }
    
    /**
     * Cancel an order (only if status is pending)
     */
    public function cancel($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);
        
        if ($order->status !== 'pending') {
            return back()->with('error', __('Only pending orders can be cancelled.'));
        }
        
        $order->update(['status' => 'cancelled']);
        
        // Restore product stock
        foreach ($order->orderItems as $item) {
            $product = $item->product;
            $product->increment('stock_quantity', $item->quantity);
        }
        
        return back()->with('success', __('Order cancelled successfully.'));
    }
    
    /**
     * Download invoice (future implementation)
     */
    public function invoice($id)
    {
        $order = auth()->user()
            ->orders()
            ->with(['orderItems.product.translations', 'shippingAddress', 'billingAddress'])
            ->findOrFail($id);
        
        // For now, return a view. Later can be converted to PDF
        return view('orders.invoice', compact('order'));
    }
}
