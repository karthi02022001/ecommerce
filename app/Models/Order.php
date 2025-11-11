<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'tax_amount',
        'stripe_payment_intent_id',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Generate unique order number
     * Format: ORD-XXX-YYYYMMDD-NNNN
     */
    public static function generateOrderNumber()
    {
        do {
            // Generate random 3-letter code
            $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));

            // Get current date
            $date = date('Ymd');

            // Generate random 4-digit number
            $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Combine into order number
            $orderNumber = "ORD-{$letters}-{$date}-{$random}";

            // Check if it exists
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Relationship: Order belongs to a user (customer)
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
     * Relationship: Order has many items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Alias for items relationship (for consistency)
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship: Order has billing address
     */
    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'billing');
    }

    /**
     * Relationship: Order has shipping address
     */
    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'shipping');
    }

    /**
     * Relationship: Order has many addresses (billing + shipping)
     */
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    /**
     * Relationship: Order has many reviews
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get formatted order number for display
     */
    public function getFormattedOrderNumberAttribute()
    {
        return $this->order_number;
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get payment status badge color for UI
     */
    public function getPaymentStatusBadgeAttribute()
    {
        return match ($this->payment_status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Scope: Recent orders
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    /**
     * Scope: Orders by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if order is completed and can be reviewed
     */
    public function canBeReviewed()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if user has already reviewed a specific product in this order
     */
    public function hasReviewedProduct($productId)
    {
        return $this->reviews()->where('product_id', $productId)->exists();
    }

    /**
     * Get products that can be reviewed (not yet reviewed)
     */
    public function reviewableProducts()
    {
        $reviewedProductIds = $this->reviews()->pluck('product_id')->toArray();
        
        return $this->items()
            ->with('product')
            ->whereNotIn('product_id', $reviewedProductIds)
            ->get()
            ->pluck('product')
            ->filter();
    }
}