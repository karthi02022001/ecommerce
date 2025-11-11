<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Show form to create a review for a product from a completed order
     */
    public function create($orderId, $productId)
    {
        // Get the order with items
        $order = Order::with('items.product')
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if order is completed
        if (!$order->canBeReviewed()) {
            return redirect()->route('orders.show', $orderId)
                ->with('error', __('Reviews can only be submitted for completed orders.'));
        }

        // Check if product is in this order
        $orderItem = $order->items()->where('product_id', $productId)->first();
        if (!$orderItem) {
            return redirect()->route('orders.show', $orderId)
                ->with('error', __('This product was not in your order.'));
        }

        // Check if already reviewed
        if ($order->hasReviewedProduct($productId)) {
            return redirect()->route('orders.show', $orderId)
                ->with('error', __('You have already reviewed this product.'));
        }

        $product = Product::with('translations')->findOrFail($productId);

        return view('reviews.create', compact('order', 'product'));
    }

    /**
     * Store a new review
     */
    public function store(Request $request, $orderId, $productId)
    {
        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        // Get the order
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Verify order is completed
        if (!$order->canBeReviewed()) {
            return redirect()->route('orders.show', $orderId)
                ->with('error', __('Reviews can only be submitted for completed orders.'));
        }

        // Verify product is in order
        $orderItem = $order->items()->where('product_id', $productId)->first();
        if (!$orderItem) {
            return redirect()->route('orders.show', $orderId)
                ->with('error', __('This product was not in your order.'));
        }

        // Check if already reviewed
        if ($order->hasReviewedProduct($productId)) {
            return redirect()->route('orders.show', $orderId)
                ->with('error', __('You have already reviewed this product.'));
        }

        // Create the review
        try {
            DB::beginTransaction();

            ProductReview::create([
                'product_id' => $productId,
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'rating' => $validated['rating'],
                'title' => $validated['title'],
                'comment' => $validated['comment'],
                'is_verified_purchase' => 1,
                'is_approved' => 0, // Pending approval by admin
            ]);

            DB::commit();

            return redirect()->route('orders.show', $orderId)
                ->with('success', __('Your review has been submitted and is pending approval. Thank you for your feedback!'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to submit review. Please try again.'));
        }
    }

    /**
     * Show all reviews by the authenticated user
     */
    public function index()
    {
        $reviews = ProductReview::with(['product.translations', 'product.primaryImage', 'order'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show a specific review
     */
    public function show($id)
    {
        $review = ProductReview::with(['product.translations', 'product.primaryImage', 'order', 'adminResponder'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('reviews.show', compact('review'));
    }

    /**
     * Show edit form for a review (only if not yet approved)
     */
    public function edit($id)
    {
        $review = ProductReview::with(['product.translations', 'order'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Only allow editing if not approved yet
        if ($review->is_approved) {
            return redirect()->route('reviews.show', $id)
                ->with('error', __('Approved reviews cannot be edited.'));
        }

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update a review (only if not yet approved)
     */
    public function update(Request $request, $id)
    {
        $review = ProductReview::where('user_id', Auth::id())->findOrFail($id);

        // Only allow updating if not approved yet
        if ($review->is_approved) {
            return redirect()->route('reviews.show', $id)
                ->with('error', __('Approved reviews cannot be edited.'));
        }

        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        try {
            $review->update([
                'rating' => $validated['rating'],
                'title' => $validated['title'],
                'comment' => $validated['comment'],
            ]);

            return redirect()->route('reviews.show', $id)
                ->with('success', __('Your review has been updated.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update review. Please try again.'));
        }
    }

    /**
     * Delete a review (only if not yet approved)
     */
    public function destroy($id)
    {
        $review = ProductReview::where('user_id', Auth::id())->findOrFail($id);

        // Only allow deleting if not approved yet
        if ($review->is_approved) {
            return redirect()->route('reviews.index')
                ->with('error', __('Approved reviews cannot be deleted.'));
        }

        try {
            $review->delete();

            return redirect()->route('reviews.index')
                ->with('success', __('Review deleted successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to delete review. Please try again.'));
        }
    }

    /**
     * Mark review as helpful (increment helpful count)
     */
    public function markHelpful($id)
    {
        $review = ProductReview::where('is_approved', 1)->findOrFail($id);

        $review->increment('helpful_count');

        return response()->json([
            'success' => true,
            'helpful_count' => $review->helpful_count,
            'message' => __('Thank you for your feedback!')
        ]);
    }
}