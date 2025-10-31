<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $sessionId = Session::get('cart_session_id');

        $cartItems = Cart::with(['product.translations', 'product.primaryImage'])
            ->where('user_id', $userId)
            ->orWhere('session_id', $sessionId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', __('empty_cart'));
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->subtotal();
        });

        $settings = AdminSetting::first();
        $shippingCost = $subtotal >= $settings->free_shipping_threshold ? 0 : $settings->shipping_rate;
        $taxAmount = ($subtotal * $settings->tax_rate) / 100;
        $total = $subtotal + $shippingCost + $taxAmount;

        return view('checkout.index', compact('cartItems', 'subtotal', 'shippingCost', 'taxAmount', 'total', 'settings'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:100',
            'billing_last_name' => 'required|string|max:100',
            'billing_address_line_1' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_postal_code' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'billing_phone' => 'required|string|max:20',
            'shipping_first_name' => 'required|string|max:100',
            'shipping_last_name' => 'required|string|max:100',
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $userId = auth()->id();
        $sessionId = Session::get('cart_session_id');

        $cartItems = Cart::with('product')
            ->where('user_id', $userId)
            ->orWhere('session_id', $sessionId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', __('empty_cart'));
        }

        $settings = AdminSetting::first();
        $subtotal = $cartItems->sum(function($item) {
            return $item->subtotal();
        });
        $shippingAmount = $subtotal >= $settings->free_shipping_threshold ? 0 : $settings->shipping_rate;
        $taxAmount = ($subtotal * $settings->tax_rate) / 100;
        $totalAmount = $subtotal + $shippingAmount + $taxAmount;

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'paid',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'currency' => $settings->currency_code,
                'notes' => $request->notes,
            ]);

            // Create billing address
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'billing',
                'first_name' => $request->billing_first_name,
                'last_name' => $request->billing_last_name,
                'company' => $request->billing_company,
                'address_line_1' => $request->billing_address_line_1,
                'address_line_2' => $request->billing_address_line_2,
                'city' => $request->billing_city,
                'state' => $request->billing_state,
                'postal_code' => $request->billing_postal_code,
                'country' => $request->billing_country,
                'phone' => $request->billing_phone,
            ]);

            // Create shipping address
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'shipping',
                'first_name' => $request->shipping_first_name,
                'last_name' => $request->shipping_last_name,
                'company' => $request->shipping_company,
                'address_line_1' => $request->shipping_address_line_1,
                'address_line_2' => $request->shipping_address_line_2,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'postal_code' => $request->shipping_postal_code,
                'country' => $request->shipping_country,
                'phone' => $request->shipping_phone,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name(),
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price,
                    'total_price' => $cartItem->subtotal(),
                ]);

                // Update product stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            Cart::where('user_id', $userId)
                ->orWhere('session_id', $sessionId)
                ->delete();

            DB::commit();

            return redirect()->route('checkout.success', $order)->with('success', __('messages.order_placed'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Order processing failed. Please try again.');
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}
