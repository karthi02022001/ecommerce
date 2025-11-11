<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MegaMenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title_en',
        'title_es',
        'url',
        'icon',
        'image',
        'target',
        'type',
        'entity_type',
        'entity_id',
        'is_featured',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'entity_id' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // ============================================
    // Relationships
    // ============================================

    public function parent()
    {
        return $this->belongsTo(MegaMenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MegaMenuItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('priority', 'asc');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'entity_id')
            ->where('entity_type', 'categories');
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'asc')
            ->orderBy('title_en', 'asc');
    }

    // ============================================
    // Accessors
    // ============================================

    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"title_{$locale}"} ?? $this->title_en;
    }

    public function getFullUrlAttribute()
    {
        if ($this->type === 'category' && $this->category) {
            return route('products.index', ['category' => $this->category->slug]);
        }

        return $this->url ?? '#';
    }

    // ============================================
    // Static Methods
    // ============================================

    /**
     * Get main menu items with children
     */
    public static function getMenuStructure()
    {
        return static::active()
            ->parent()
            ->ordered()
            ->with(['children' => function ($query) {
                $query->ordered();
            }])
            ->get();
    }

    /**
     * Get featured menu items for mega menu
     */
    public static function getFeaturedItems($parentId = null)
    {
        return static::active()
            ->featured()
            ->when($parentId, function ($query, $parentId) {
                $query->where('parent_id', $parentId);
            })
            ->ordered()
            ->limit(6)
            ->get();
    }
}
