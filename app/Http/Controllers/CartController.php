<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index()
    {
        $userId = auth()->id();
        $sessionId = $this->getOrCreateSessionId();

        $cartItems = Cart::with(['product.translations', 'product.primaryImage'])
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();

        $cartTotal = $cartItems->sum(function ($item) {
            return $item->subtotal();
        });

        $cartCount = $cartItems->sum('quantity');

        return view('cart.index', compact('cartItems', 'cartTotal', 'cartCount'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check if product is active
        if (!$product->is_active) {
            return back()->with('error', __('This product is not available.'));
        }

        // Check stock
        if ($product->stock_quantity < $validated['quantity']) {
            return back()->with('error', __('Insufficient stock available.'));
        }

        $userId = auth()->id();
        $sessionId = $this->getOrCreateSessionId();

        // Check if product already in cart
        $cartItem = Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            if ($newQuantity > $product->stock_quantity) {
                return back()->with('error', __('Cannot add more items. Stock limit reached.'));
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Add new product to cart
            Cart::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
            ]);
        }

        return back()->with('success', __('Product added to cart successfully!'));
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, Cart $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Verify ownership
        $this->verifyCartOwnership($cartItem);

        // Load product relationship
        $cartItem->load('product');

        // Check stock
        if ($validated['quantity'] > $cartItem->product->stock_quantity) {
            return back()->with('error', __('Insufficient stock. Available: :count', ['count' => $cartItem->product->stock_quantity]));
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return back()->with('success', __('Cart updated successfully!'));
    }

    /**
     * Remove item from cart
     */
    public function remove(Cart $cartItem)
    {
        // Verify ownership
        $this->verifyCartOwnership($cartItem);

        $cartItem->delete();

        return back()->with('success', __('Item removed from cart.'));
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $userId = auth()->id();
        $sessionId = Session::get('cart_session_id');

        Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
            ->delete();

        return back()->with('success', __('Cart cleared successfully!'));
    }

    /**
     * Apply coupon code (placeholder for future implementation)
     */
    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        // TODO: Implement coupon validation logic
        // For now, just return error
        return back()->with('error', __('Invalid coupon code.'));
    }

    /**
     * Get cart count for header (AJAX endpoint)
     */
    public function getCount()
    {
        $userId = auth()->id();
        $sessionId = Session::get('cart_session_id');

        $count = Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
            ->sum('quantity');

        return response()->json(['count' => $count]);
    }

    /**
     * Get or create session ID for guest users
     */
    private function getOrCreateSessionId()
    {
        $sessionId = Session::get('cart_session_id');

        if (!$sessionId) {
            $sessionId = 'cart_' . uniqid() . time();
            Session::put('cart_session_id', $sessionId);
        }

        return $sessionId;
    }

    /**
     * Verify cart item ownership
     */
    private function verifyCartOwnership(Cart $cartItem)
    {
        $userId = auth()->id();
        $sessionId = Session::get('cart_session_id');

        $isOwner = ($userId && $cartItem->user_id == $userId)
            || (!$userId && $cartItem->session_id == $sessionId);

        if (!$isOwner) {
            abort(403, 'Unauthorized access to cart item.');
        }
    }
}
