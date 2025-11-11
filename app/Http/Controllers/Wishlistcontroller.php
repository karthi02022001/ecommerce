<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist
     */
    public function index()
    {
        $locale = session('locale', 'en');

        $wishlists = Wishlist::with([
            'product' => function ($query) {
                $query->where('is_active', true);
            },
            'product.images',
            'product.category',
            'product.category.translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'product.translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }
        ])
            ->where('user_id', auth('web')->id())
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Add product to wishlist
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
            ], [
                'product_id.required' => __('Product ID is required.'),
                'product_id.exists' => __('Product not found.'),
            ]);

            $userId = auth('web')->id();
            $productId = $validated['product_id'];

            // Check if product exists and is active
            $product = Product::where('id', $productId)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => __('Product not found or inactive.')
                ], 404);
            }

            // Check if already in wishlist
            $exists = Wishlist::where('user_id', $userId)
                ->where('product_id', $productId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => __('Product already in wishlist.')
                ], 200); // Changed to 200 since it's not really an error
            }

            // Add to wishlist
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            // Count wishlist items
            $wishlistCount = Wishlist::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'message' => __('Product added to wishlist successfully.'),
                'wishlist_count' => $wishlistCount
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Wishlist add error: ' . $e->getMessage(), [
                'user_id' => auth('web')->id(),
                'product_id' => $request->product_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Failed to add product to wishlist. Please try again.')
            ], 500);
        }
    }

    /**
     * Remove product from wishlist by wishlist ID
     */
    public function destroy(Request $request, $id)
    {
        try {
            $userId = auth('web')->id();

            $wishlist = Wishlist::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$wishlist) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Wishlist item not found.')
                    ], 404);
                }

                return redirect()->back()->with('error', __('Wishlist item not found.'));
            }

            $wishlist->delete();

            $wishlistCount = Wishlist::where('user_id', $userId)->count();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Product removed from wishlist.'),
                    'wishlist_count' => $wishlistCount
                ], 200);
            }

            return redirect()->back()->with('success', __('Product removed from wishlist.'));
        } catch (\Exception $e) {
            Log::error('Wishlist remove error: ' . $e->getMessage(), [
                'user_id' => auth('web')->id(),
                'wishlist_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to remove product from wishlist.')
                ], 500);
            }

            return redirect()->back()->with('error', __('Failed to remove product from wishlist.'));
        }
    }

    /**
     * Remove product from wishlist by product ID
     */
    public function removeProduct(Request $request, $productId)
    {
        try {
            $userId = auth('web')->id();

            $wishlist = Wishlist::where('product_id', $productId)
                ->where('user_id', $userId)
                ->first();

            if (!$wishlist) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Product not in wishlist.')
                    ], 404);
                }

                return redirect()->back()->with('error', __('Product not in wishlist.'));
            }

            $wishlist->delete();

            $wishlistCount = Wishlist::where('user_id', $userId)->count();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Product removed from wishlist.'),
                    'wishlist_count' => $wishlistCount
                ], 200);
            }

            return redirect()->back()->with('success', __('Product removed from wishlist.'));
        } catch (\Exception $e) {
            Log::error('Wishlist remove by product error: ' . $e->getMessage(), [
                'user_id' => auth('web')->id(),
                'product_id' => $productId,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to remove product from wishlist.')
                ], 500);
            }

            return redirect()->back()->with('error', __('Failed to remove product from wishlist.'));
        }
    }

    /**
     * Move wishlist item to cart
     */
    public function moveToCart(Request $request, $id)
    {
        try {
            $userId = auth('web')->id();
            $locale = session('locale', 'en');

            $wishlist = Wishlist::with([
                'product',
                'product.images',
                'product.translations' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                }
            ])
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$wishlist) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Wishlist item not found.')
                    ], 404);
                }

                return redirect()->back()->with('error', __('Wishlist item not found.'));
            }

            $product = $wishlist->product;

            // Check if product exists
            if (!$product) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Product not found.')
                    ], 404);
                }

                return redirect()->back()->with('error', __('Product not found.'));
            }

            // Check if product is active
            if (!$product->is_active) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Product is not available.')
                    ], 400);
                }

                return redirect()->back()->with('error', __('Product is not available.'));
            }

            // Check stock
            if ($product->stock_quantity < 1) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Product is out of stock.')
                    ], 400);
                }

                return redirect()->back()->with('error', __('Product is out of stock.'));
            }

            // Get product name (with translation if available)
            $productName = $product->name;
            $translation = $product->translations->first();
            if ($translation && $translation->name) {
                $productName = $translation->name;
            }

            // Get product image
            $productImage = null;
            if ($product->images->count() > 0) {
                $productImage = $product->images->first()->image_path;
            }

            // Get or create cart
            $cart = session()->get('cart', []);

            // Add to cart
            if (isset($cart[$product->id])) {
                // Check if adding one more exceeds stock
                if ($cart[$product->id]['quantity'] + 1 > $product->stock_quantity) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => __('Cannot add more items. Stock limit reached.')
                        ], 400);
                    }

                    return redirect()->back()->with('error', __('Cannot add more items. Stock limit reached.'));
                }
                $cart[$product->id]['quantity']++;
            } else {
                $cart[$product->id] = [
                    'product_id' => $product->id,
                    'name' => $productName,
                    'price' => $product->price,
                    'quantity' => 1,
                    'image' => $productImage,
                ];
            }

            session()->put('cart', $cart);

            // Remove from wishlist
            $wishlist->delete();

            $wishlistCount = Wishlist::where('user_id', $userId)->count();
            $cartCount = count($cart);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Product moved to cart successfully.'),
                    'wishlist_count' => $wishlistCount,
                    'cart_count' => $cartCount
                ], 200);
            }

            return redirect()->back()->with('success', __('Product moved to cart successfully.'));
        } catch (\Exception $e) {
            Log::error('Wishlist move to cart error: ' . $e->getMessage(), [
                'user_id' => auth('web')->id(),
                'wishlist_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to move product to cart.')
                ], 500);
            }

            return redirect()->back()->with('error', __('Failed to move product to cart.'));
        }
    }

    /**
     * Clear all wishlist items
     */
    public function clear(Request $request)
    {
        try {
            $userId = auth('web')->id();

            Wishlist::where('user_id', $userId)->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Wishlist cleared successfully.'),
                    'wishlist_count' => 0
                ], 200);
            }

            return redirect()->back()->with('success', __('Wishlist cleared successfully.'));
        } catch (\Exception $e) {
            Log::error('Wishlist clear error: ' . $e->getMessage(), [
                'user_id' => auth('web')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to clear wishlist.')
                ], 500);
            }

            return redirect()->back()->with('error', __('Failed to clear wishlist.'));
        }
    }

    /**
     * Get wishlist count (for AJAX)
     */
    public function count()
    {
        try {
            $count = Wishlist::where('user_id', auth('web')->id())->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ], 200);
        } catch (\Exception $e) {
            Log::error('Wishlist count error: ' . $e->getMessage(), [
                'user_id' => auth('web')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => __('Failed to get wishlist count.')
            ], 500);
        }
    }
}
