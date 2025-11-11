<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key_name',
        'value',
        'type',
        'group_name',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Cache settings for better performance
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    /**
     * Get a single setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAllCached();
        return $settings[$key] ?? $default;
    }

    /**
     * Set a single setting value
     */
    public static function set(string $key, $value, string $type = 'text', string $group = 'general')
    {
        return self::updateOrCreate(
            ['key_name' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group_name' => $group,
            ]
        );
    }

    /**
     * Get all settings as key-value array (cached)
     */
    public static function getAllCached(): array
    {
        return Cache::remember('settings', 3600, function () {
            return self::pluck('value', 'key_name')->toArray();
        });
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group)
    {
        return self::where('group_name', $group)
            ->pluck('value', 'key_name')
            ->toArray();
    }

    /**
     * Get settings as object for easier access
     */
    public static function asObject()
    {
        $settings = self::getAllCached();
        return (object) $settings;
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group_name', $group);
    }
}
