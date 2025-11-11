<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'slug',
        'price',
        'compare_price',
        'cost_price',
        'stock_quantity',
        'min_stock_level',
        'weight',
        'dimensions',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', 1);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Get translation for current locale
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    // Get translated name
    public function name($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->name : '';
    }

    // Get translated description
    public function description($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->description : '';
    }

    // Get translated short description
    public function shortDescription($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->short_description : '';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= min_stock_level')->where('stock_quantity', '>', 0);
    }

    // Helper methods
    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock_level && $this->stock_quantity > 0;
    }

    public function hasDiscount()
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function discountPercentage()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }
        return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }
    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')
            ->withTimestamps();
    }

    /**
     * Get wishlist count for this product
     */
    public function getWishlistCountAttribute(): int
    {
        return $this->wishlistedBy()->count();
    }

    /**
     * Check if product is in user's wishlist
     */
    public function isInWishlist($userId = null): bool
    {
        if (!$userId && auth('web')->check()) {
            $userId = auth('web')->id();
        }

        if (!$userId) {
            return false;
        }

        return Wishlist::isInWishlist($userId, $this->id);
    }

    /**
     * Get average rating for this product
     */
    public function averageRating()
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get total review count
     */
    public function reviewCount()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get rating distribution (count per rating 1-5)
     */
    public function ratingDistribution()
    {
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $this->approvedReviews()->where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Get percentage for each rating
     */
    public function ratingPercentages()
    {
        $total = $this->reviewCount();
        if ($total == 0) return array_fill(1, 5, 0);

        $distribution = $this->ratingDistribution();
        $percentages = [];

        foreach ($distribution as $rating => $count) {
            $percentages[$rating] = round(($count / $total) * 100);
        }

        return $percentages;
    }

    /**
     * Get star rating HTML with average
     */
    public function getStarsHtmlAttribute()
    {
        $average = $this->averageRating();
        $fullStars = floor($average);
        $hasHalfStar = ($average - $fullStars) >= 0.5;
        $html = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $fullStars) {
                $html .= '<i class="bi bi-star-fill text-warning"></i>';
            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                $html .= '<i class="bi bi-star-half text-warning"></i>';
            } else {
                $html .= '<i class="bi bi-star text-muted"></i>';
            }
        }

        return $html;
    }
    /**
     * Relationship: Approved reviews only
     */
    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', 1);
    }
}
