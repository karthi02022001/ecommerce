<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'admin_email',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'site_logo',
        'footer_logo',
        'fav_icon',
        'default_language',
        'available_languages',
        'currency_code',
        'currency_symbol',
        'shipping_rate',
        'free_shipping_threshold',
        'discount_type',
        'tax_rate',
        'low_stock_threshold',
        'items_per_page',
        'allow_guest_checkout',
        'primary_color',
        'secondary_color',
        'facebook_link',
        'twitter_link',
        'linkedin_link',
        'instagram_link',
        'youtube_link',
    ];

    protected function casts(): array
    {
        return [
            'available_languages' => 'array',
            'shipping_rate' => 'decimal:2',
            'free_shipping_threshold' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'allow_guest_checkout' => 'boolean',
        ];
    }

    // Get single settings instance
    public static function get()
    {
        return static::first() ?? new static();
    }
}
