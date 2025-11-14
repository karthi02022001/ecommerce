<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        $subtotal = $cartItems->sum(function ($item) {
            return $item->subtotal();
        });

        $settings = Setting::first();
        $shippingCost = $subtotal >= $settings->free_shipping_threshold ? 0 : $settings->shipping_rate;
        $taxAmount = ($subtotal * $settings->tax_rate) / 100;
        $total = $subtotal + $shippingCost + $taxAmount;

        // Load user's saved addresses if authenticated
        $addresses = collect();
        if (auth()->check()) {
            $user = auth()->user();
            $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

            // Log for debugging
            Log::info('Loaded addresses for checkout', [
                'user_id' => $user->id,
                'total' => $addresses->count(),
                'shipping' => $addresses->where('address_type', 'shipping')->count(),
                'billing' => $addresses->where('address_type', 'billing')->count(),
            ]);
        }

        return view('checkout.index', compact('cartItems', 'subtotal', 'shippingCost', 'taxAmount', 'total', 'settings', 'addresses'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:100',
            'billing_last_name' => 'required|string|max:100',
            'billing_address_line_1' => 'required|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_postal_code' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'billing_phone' => 'required|string|max:20',
            'shipping_first_name' => 'required|string|max:100',
            'shipping_last_name' => 'required|string|max:100',
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|string|in:cash_on_delivery,stripe',
            'stripe_payment_method_id' => 'required_if:payment_method,stripe|string',
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

        $settings = Setting::first();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->subtotal();
        });
        $shippingAmount = $subtotal >= $settings->free_shipping_threshold ? 0 : $settings->shipping_rate;
        $taxAmount = ($subtotal * $settings->tax_rate) / 100;
        $totalAmount = $subtotal + $shippingAmount + $taxAmount;

        DB::beginTransaction();
        try {
            // Handle Stripe payment if selected
            $paymentStatus = 'pending';
            $stripePaymentIntentId = null;

            if ($request->payment_method === 'stripe') {
                try {
                    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

                    $paymentIntent = \Stripe\PaymentIntent::create([
                        'amount' => $totalAmount * 100,
                        'currency' => strtolower($settings->currency ?? 'usd'),
                        'payment_method' => $request->stripe_payment_method_id,
                        'confirm' => true,
                        'automatic_payment_methods' => [
                            'enabled' => true,
                            'allow_redirects' => 'never',
                        ],
                        'metadata' => [
                            'user_id' => $userId,
                            'user_email' => auth()->user()->email,
                        ],
                    ]);

                    if ($paymentIntent->status === 'succeeded') {
                        $paymentStatus = 'paid';
                        $stripePaymentIntentId = $paymentIntent->id;
                        Log::info('Stripe payment succeeded', [
                            'payment_intent_id' => $paymentIntent->id,
                            'amount' => $totalAmount,
                        ]);
                    } else {
                        throw new \Exception('Payment failed: ' . $paymentIntent->status);
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    DB::rollBack();
                    Log::error('Stripe card error: ' . $e->getMessage());
                    return back()->with('error', 'Payment failed: ' . $e->getError()->message)->withInput();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Stripe payment error: ' . $e->getMessage());
                    return back()->with('error', 'Payment processing failed. Please try again.')->withInput();
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => $paymentStatus,
                'payment_method' => $request->payment_method,
                'stripe_payment_intent_id' => $stripePaymentIntentId,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'currency' => $settings->currency ?? 'USD',
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

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name(),
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->subtotal(),
                ]);

                // Update product stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // ==========================================
            // SEND ORDER CONFIRMATION EMAILS
            // ==========================================

            // Reload order with relationships
            $order->load(['items', 'billingAddress', 'shippingAddress', 'user']);

            // Get locale
            $locale = session('locale', app()->getLocale());

          

            try {
                Mail::to(auth()->user()->email)
                    ->send(new OrderConfirmation($order, $locale));

                // Admin notification - CHANGED FROM ->queue() TO ->send()
                $adminEmail = config('mail.admin_email', env('MAIL_ADMIN_EMAIL'));
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new NewOrderNotification($order));
                }

                Log::info('Order emails sent successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            } catch (\Exception $e) {
                Log::error('Email sending failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the order if email fails
            }

            // Clear cart
            Cart::where('user_id', $userId)
                ->orWhere('session_id', $sessionId)
                ->delete();

            DB::commit();

            return redirect()->route('checkout.success', $order)
                ->with('success', __('messages.order_placed'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order processing failed: ' . $e->getMessage());
            return back()->with('error', 'Order processing failed: ' . $e->getMessage())->withInput();
        }
    }
    public function success(Order $order)
    {
        // Verify user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Eager load relationships for the success page
        $order->load(['orderItems', 'shippingAddress', 'billingAddress']);

        return view('checkout.success', compact('order'));
    }
}
