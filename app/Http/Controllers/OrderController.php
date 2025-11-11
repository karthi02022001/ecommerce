<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        try {
            DB::beginTransaction();

            // Process Stripe refund if payment was made via Stripe
            if (
                $order->payment_method === 'stripe' &&
                $order->payment_status === 'paid' &&
                $order->transaction_id
            ) {

                $refundSuccess = $this->processStripeRefund($order);

                if (!$refundSuccess) {
                    DB::rollBack();
                    return back()->with('error', __('Failed to process refund. Please contact support.'));
                }
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'payment_status' => ($order->payment_status === 'paid') ? 'refunded' : $order->payment_status,
                'cancelled_at' => now(),
                'cancellation_reason' => 'Cancelled by customer'
            ]);

            // Restore product stock
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                }
            }

            DB::commit();

            $message = __('Order cancelled successfully.');
            if ($order->payment_method === 'stripe' && $order->payment_status === 'refunded') {
                $message .= ' ' . __('Refund has been processed and will appear in your account within 5-10 business days.');
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            return back()->with('error', __('Failed to cancel order. Please try again or contact support.'));
        }
    }

    /**
     * Process Stripe refund
     */
    private function processStripeRefund($order)
    {
        try {
            // Initialize Stripe with your secret key
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Create refund
            $refund = \Stripe\Refund::create([
                'payment_intent' => $order->transaction_id,
                'amount' => $order->total_amount * 100, // Convert to cents
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_email' => $order->customer_email
                ]
            ]);

            // Log refund details
            Log::info('Stripe refund processed', [
                'order_id' => $order->id,
                'refund_id' => $refund->id,
                'amount' => $order->total_amount,
                'status' => $refund->status
            ]);

            // Store refund ID in order
            $order->update([
                'refund_id' => $refund->id,
                'refund_status' => $refund->status
            ]);

            return true;
        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe refund card error: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\RateLimitException $e) {
            Log::error('Stripe refund rate limit: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe refund invalid request: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Stripe refund auth error: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error('Stripe refund connection error: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe refund API error: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error('Stripe refund general error: ' . $e->getMessage());
            return false;
        }
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
