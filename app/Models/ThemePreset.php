<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemePreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'primary_color',
        'primary_light',
        'primary_dark',
        'accent_color',
        'success_color',
        'warning_color',
        'danger_color',
        'preview_image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Get active theme
    public static function getActiveTheme()
    {
        $activeThemeName = Setting::get('active_theme', 'teal_dark');
        return self::where('name', $activeThemeName)->first() 
            ?? self::where('name', 'teal_dark')->first();
    }

    // Get CSS variables for theme
    public function getCssVariablesAttribute(): string
    {
        return ":root {
            --primary-color: {$this->primary_color};
            --primary-light: {$this->primary_light};
            --primary-dark: {$this->primary_dark};
            --accent-color: {$this->accent_color};
            --success-color: {$this->success_color};
            --warning-color: {$this->warning_color};
            --danger-color: {$this->danger_color};
            --primary-rgb: " . $this->hexToRgb($this->primary_color) . ";
            --accent-rgb: " . $this->hexToRgb($this->accent_color) . ";
        }";
    }

    // Helper to convert hex to RGB
    protected function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "$r, $g, $b";
    }

    // Get preview image URL
    public function getPreviewUrlAttribute(): string
    {
        if ($this->preview_image) {
            return asset('storage/' . $this->preview_image);
        }

        // Generate a simple gradient preview
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='120'%3E%3Cdefs%3E%3ClinearGradient id='grad' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' style='stop-color:" . urlencode($this->primary_color) . ";stop-opacity:1' /%3E%3Cstop offset='100%25' style='stop-color:" . urlencode($this->accent_color) . ";stop-opacity:1' /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='200' height='120' fill='url(%23grad)' /%3E%3C/svg%3E";
    }
}
