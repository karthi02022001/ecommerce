<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum(function($item) {
            return $item->subtotal();
        });

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock_quantity,
        ]);

        if (!$product->isInStock()) {
            return back()->with('error', __('messages.out_of_stock'));
        }

        $userId = auth()->id();
        $sessionId = $this->getSessionId();

        $cart = Cart::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->where('product_id', $product->id)->first();

        if ($cart) {
            $newQuantity = $cart->quantity + $request->quantity;
            if ($newQuantity > $product->stock_quantity) {
                return back()->with('error', __('messages.insufficient_stock'));
            }
            $cart->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return back()->with('success', __('messages.cart_added'));
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cart->product->stock_quantity,
        ]);

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', __('messages.cart_updated'));
    }

    public function remove(Cart $cart)
    {
        $cart->delete();

        return back()->with('success', __('messages.cart_removed'));
    }

    public function clear()
    {
        $userId = auth()->id();
        $sessionId = $this->getSessionId();

        Cart::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        return back()->with('success', __('messages.cart_cleared'));
    }

    private function getCartItems()
    {
        $userId = auth()->id();
        $sessionId = $this->getSessionId();

        return Cart::with(['product.translations', 'product.primaryImage'])
            ->where(function($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->get();
    }

    private function getSessionId()
    {
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', uniqid('cart_', true));
        }
        return Session::get('cart_session_id');
    }
}
