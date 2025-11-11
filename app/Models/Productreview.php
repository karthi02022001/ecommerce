<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'is_verified_purchase',
        'is_approved',
        'admin_response',
        'admin_response_at',
        'admin_response_by',
        'helpful_count'
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'admin_response_at' => 'datetime',
        'helpful_count' => 'integer',
    ];

    /**
     * Relationship: Review belongs to a product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relationship: Review belongs to a user (customer)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for user relationship (for consistency with customer terminology)
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Review belongs to an order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relationship: Admin who responded to the review
     */
    public function adminResponder()
    {
        return $this->belongsTo(Admin::class, 'admin_response_by');
    }

    /**
     * Scope: Approved reviews only
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', 1);
    }

    /**
     * Scope: Pending reviews (not approved yet)
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', 0);
    }

    /**
     * Scope: Verified purchase reviews
     */
    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', 1);
    }

    /**
     * Scope: Reviews by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope: Recent reviews
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    /**
     * Scope: Recent reviews first (ordered by created_at desc)
     */
    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Reviews with admin response
     */
    public function scopeWithResponse($query)
    {
        return $query->whereNotNull('admin_response');
    }

    /**
     * Get rating badge color for UI
     */
    public function getRatingBadgeAttribute()
    {
        return match ($this->rating) {
            5 => 'success',
            4 => 'primary',
            3 => 'warning',
            2 => 'orange',
            1 => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get approval status badge color for UI
     */
    public function getApprovalBadgeAttribute()
    {
        return $this->is_approved ? 'success' : 'warning';
    }

    /**
     * Get star rating HTML
     */
    public function getStarsHtmlAttribute()
    {
        $fullStars = floor($this->rating);
        $html = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $fullStars) {
                $html .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $html .= '<i class="bi bi-star text-muted"></i>';
            }
        }

        return $html;
    }

    /**
     * Check if review has admin response
     */
    public function hasAdminResponse()
    {
        return !is_null($this->admin_response);
    }

    /**
     * Get formatted created date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get formatted response date
     */
    public function getFormattedResponseDateAttribute()
    {
        return $this->admin_response_at ? $this->admin_response_at->format('M d, Y') : null;
    }

    /**
     * Get time ago format for review date
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if review is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if review is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if review is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve the review
     */
    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Reject the review
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Mark as pending
     */
    public function markAsPending(): void
    {
        $this->update(['status' => 'pending']);
    }


    /**
     * Scope: Get only rejected reviews
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }


    /**
     * Get the review status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get star rating display
     */
    public function getStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $stars .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        return $stars;
    }
}
