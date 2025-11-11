<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\AdminActivityLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminReviewController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('reviews.view')) {
            abort(403, __('You do not have permission to view reviews'));
        }

        $query = ProductReview::with(['product', 'user', 'order', 'adminResponder'])
            ->orderBy('created_at', 'desc');

        // Filter by approval status
        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by verified purchase
        if ($request->filled('verified_purchase')) {
            $query->where('is_verified_purchase', $request->verified_purchase === '1');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        // Get statistics
        $stats = [
            'total' => ProductReview::count(),
            'pending' => ProductReview::where('is_approved', 0)->count(),
            'approved' => ProductReview::where('is_approved', 1)->count(),
            'avg_rating' => round(ProductReview::approved()->avg('rating'), 1) ?? 0,
            'with_response' => ProductReview::whereNotNull('admin_response')->count(),
        ];

        // Get products for filter
        $products = Product::select('id')->with('translations')->orderBy('created_at', 'desc')->get();

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth('admin')->id(),
            'action' => 'viewed',
            'module' => 'ProductReview',
            'description' => 'Viewed reviews list'
        ]);

        return view('admin.reviews.index', compact('reviews', 'stats', 'products'));
    }

    /**
     * Display the specified review
     */
    public function show($id)
    {
        // Check permission
        if (!Auth::guard('admin')->user()->hasPermission('reviews.view')) {
            abort(403, 'Unauthorized action.');
        }

        $review = ProductReview::with([
            'product.translations', 
            'product.primaryImage',
            'user', 
            'order.items',
            'adminResponder'
        ])->findOrFail($id);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve(Request $request, $id)
    {
        // Check permission
        if (!Auth::guard('admin')->user()->hasPermission('reviews.approve')) {
            abort(403, 'Unauthorized action.');
        }

        $review = ProductReview::findOrFail($id);

        try {
            $review->update([
                'is_approved' => 1
            ]);

            // Log activity
            AdminActivityLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'action' => 'update',
                'module' => 'reviews',
                'description' => "Approved review #{$review->id} for product: {$review->product->name()}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->back()
                ->with('success', __('Review approved successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to approve review. Please try again.'));
        }
    }

    /**
     * Reject (unapprove) a review
     */
    public function reject(Request $request, $id)
    {
        // Check permission
        if (!Auth::guard('admin')->user()->hasPermission('reviews.approve')) {
            abort(403, 'Unauthorized action.');
        }

        $review = ProductReview::findOrFail($id);

        try {
            $review->update([
                'is_approved' => 0
            ]);

            // Log activity
            AdminActivityLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'action' => 'update',
                'module' => 'reviews',
                'description' => "Rejected review #{$review->id} for product: {$review->product->name()}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->back()
                ->with('success', __('Review rejected successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to reject review. Please try again.'));
        }
    }

    /**
     * Add or update admin response to a review
     */
    public function respond(Request $request, $id)
    {
        // Check permission
        if (!Auth::guard('admin')->user()->hasPermission('reviews.respond')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'admin_response' => 'required|string|min:10|max:1000'
        ]);

        $review = ProductReview::findOrFail($id);

        try {
            DB::beginTransaction();

            $review->update([
                'admin_response' => $validated['admin_response'],
                'admin_response_at' => now(),
                'admin_response_by' => Auth::guard('admin')->id(),
            ]);

            // Log activity
            AdminActivityLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'action' => 'update',
                'module' => 'reviews',
                'description' => "Responded to review #{$review->id} for product: {$review->product->name()}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Response added successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to add response. Please try again.'));
        }
    }

    /**
     * Remove admin response from a review
     */
    public function removeResponse(Request $request, $id)
    {
        // Check permission
        if (!Auth::guard('admin')->user()->hasPermission('reviews.respond')) {
            abort(403, 'Unauthorized action.');
        }

        $review = ProductReview::findOrFail($id);

        try {
            $review->update([
                'admin_response' => null,
                'admin_response_at' => null,
                'admin_response_by' => null,
            ]);

            // Log activity
            AdminActivityLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'action' => 'update',
                'module' => 'reviews',
                'description' => "Removed response from review #{$review->id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->back()
                ->with('success', __('Response removed successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to remove response. Please try again.'));
        }
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, $id)
    {
        // Check permission
        if (!Auth::guard('admin')->user()->hasPermission('reviews.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $review = ProductReview::findOrFail($id);
        $productName = $review->product->name();

        try {
            $review->delete();

            // Log activity
            AdminActivityLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'action' => 'delete',
                'module' => 'reviews',
                'description' => "Deleted review #{$id} for product: {$productName}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('admin.reviews.index')
                ->with('success', __('Review deleted successfully.'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to delete review. Please try again.'));
        }
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request)
    {
        // Check permission
        if (!Auth::guard('admin')->user()->hasPermission('reviews.approve')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id'
        ]);

        try {
            DB::beginTransaction();

            ProductReview::whereIn('id', $validated['review_ids'])
                ->update(['is_approved' => 1]);

            // Log activity
            AdminActivityLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'action' => 'update',
                'module' => 'reviews',
                'description' => 'Bulk approved ' . count($validated['review_ids']) . ' reviews',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Reviews approved successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', __('Failed to approve reviews. Please try again.'));
        }
    }

    /**
     * Bulk delete reviews
     */
    public function bulkDelete(Request $request)
    {
        // Check permission
        if (!Auth::guard('admin')->user()->hasPermission('reviews.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id'
        ]);

        try {
            DB::beginTransaction();

            ProductReview::whereIn('id', $validated['review_ids'])->delete();

            // Log activity
            AdminActivityLog::create([
                'admin_id' => Auth::guard('admin')->id(),
                'action' => 'delete',
                'module' => 'reviews',
                'description' => 'Bulk deleted ' . count($validated['review_ids']) . ' reviews',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Reviews deleted successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', __('Failed to delete reviews. Please try again.'));
        }
    }
}