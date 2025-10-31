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

    // Cache key for settings
    const CACHE_KEY = 'store_settings';
    const CACHE_TTL = 3600; // 1 hour

    // Scopes
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group_name', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Static methods for easy access
    public static function get(string $key, $default = null)
    {
        $settings = self::getAllCached();
        return $settings[$key] ?? $default;
    }

    public static function set(string $key, $value)
    {
        $setting = self::where('key_name', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create([
                'key_name' => $key,
                'value' => $value,
                'type' => 'text',
                'group_name' => 'general',
            ]);
        }

        self::clearCache();
        
        return $value;
    }

    public static function has(string $key): bool
    {
        $settings = self::getAllCached();
        return isset($settings[$key]);
    }

    // Get all settings as key-value array
    public static function getAllCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::pluck('value', 'key_name')->toArray();
        });
    }

    // Get settings by group
    public static function getByGroup(string $group): array
    {
        return self::where('group_name', $group)
            ->pluck('value', 'key_name')
            ->toArray();
    }

    // Clear settings cache
    public static function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }

    // Get all groups
    public static function getGroups(): array
    {
        return self::distinct('group_name')
            ->pluck('group_name')
            ->toArray();
    }

    // Cast value based on type
    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
            case 'integer':
                return is_numeric($value) ? (int) $value : $value;
            case 'decimal':
            case 'float':
                return is_numeric($value) ? (float) $value : $value;
            case 'json':
            case 'array':
                return json_decode($value, true) ?? [];
            default:
                return $value;
        }
    }

    // Set value based on type
    public function setValueAttribute($value)
    {
        if ($this->type === 'json' || $this->type === 'array') {
            $this->attributes['value'] = json_encode($value);
        } elseif ($this->type === 'boolean') {
            $this->attributes['value'] = $value ? '1' : '0';
        } else {
            $this->attributes['value'] = $value;
        }
    }

    // Boot method to clear cache on save/delete
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
