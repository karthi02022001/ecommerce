<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ThemePreset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    // General Settings
    public function general()
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'settings', 'Viewed general settings');

        // Get settings as object for easier access
        $settings = Setting::asObject();

        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'currency_code' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:5',
            'shipping_rate' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'low_stock_threshold' => 'nullable|integer|min:1',
            'items_per_page' => 'nullable|integer|min:1|max:100',
            'allow_guest_checkout' => 'boolean',
        ]);

        try {
            // Update each setting
            Setting::set('store_name', $validated['site_name'], 'text', 'general');
            Setting::set('admin_email', $validated['admin_email'], 'email', 'general');
            Setting::set('meta_title', $validated['meta_title'] ?? '', 'text', 'general');
            Setting::set('meta_description', $validated['meta_description'] ?? '', 'textarea', 'general');
            Setting::set('meta_keywords', $validated['meta_keywords'] ?? '', 'text', 'general');
            Setting::set('currency_code', $validated['currency_code'], 'text', 'general');
            Setting::set('currency_symbol', $validated['currency_symbol'], 'text', 'general');
            Setting::set('shipping_fee', $validated['shipping_rate'] ?? 0, 'number', 'general');
            Setting::set('free_shipping_threshold', $validated['free_shipping_threshold'] ?? 0, 'number', 'general');
            Setting::set('tax_rate', $validated['tax_rate'] ?? 0, 'number', 'general');
            Setting::set('low_stock_threshold', $validated['low_stock_threshold'] ?? 10, 'number', 'general');
            Setting::set('items_per_page', $validated['items_per_page'] ?? 12, 'number', 'general');
            Setting::set('allow_guest_checkout', $request->has('allow_guest_checkout') ? '1' : '0', 'boolean', 'general');

            auth('admin')->user()->logActivity('update', 'settings', 'Updated general settings');

            return redirect()->back()
                ->with('success', __('Settings updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating settings: ') . $e->getMessage());
        }
    }

    // Theme Settings
    public function theme()
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'settings', 'Viewed theme settings');

        $themes = ThemePreset::active()->ordered()->get();
        $activeTheme = ThemePreset::getActiveTheme();

        return view('admin.settings.theme', compact('themes', 'activeTheme'));
    }

    public function applyTheme(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:theme_presets,id',
        ]);

        try {
            $theme = ThemePreset::findOrFail($validated['theme_id']);

            // Update active theme setting
            Setting::set('active_theme', $theme->name, 'select', 'appearance');
            Setting::set('primary_color', $theme->primary_color, 'color', 'appearance');
            Setting::set('accent_color', $theme->accent_color, 'color', 'appearance');

            auth('admin')->user()->logActivity('update', 'settings', "Applied theme: {$theme->display_name}");

            return redirect()->back()
                ->with('success', __('Theme applied successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error applying theme: ') . $e->getMessage());
        }
    }

    // Translation Settings
    public function translations()
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'settings', 'Viewed translation settings');

        $locales = ['en', 'es'];
        $translations = [];

        foreach ($locales as $locale) {
            $path = resource_path("lang/{$locale}.json");
            if (File::exists($path)) {
                $translations[$locale] = json_decode(File::get($path), true) ?? [];
            } else {
                $translations[$locale] = [];
            }
        }

        return view('admin.settings.translations', compact('locales', 'translations'));
    }

    public function updateTranslations(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|in:en,es',
            'translations' => 'required|array',
            'translations.*' => 'string',
        ]);

        try {
            $locale = $validated['locale'];
            $path = resource_path("lang/{$locale}.json");

            // Ensure directory exists
            if (!File::exists(resource_path('lang'))) {
                File::makeDirectory(resource_path('lang'), 0755, true);
            }

            // Merge with existing translations
            $existing = [];
            if (File::exists($path)) {
                $existing = json_decode(File::get($path), true) ?? [];
            }

            // Remove empty translations
            $newTranslations = array_filter($validated['translations'], function ($value) {
                return !empty(trim($value));
            });

            $merged = array_merge($existing, $newTranslations);

            // Sort alphabetically
            ksort($merged);

            // Save translations
            File::put($path, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            auth('admin')->user()->logActivity('update', 'settings', "Updated {$locale} translations");

            return redirect()->back()
                ->with('success', __('Translations updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating translations: ') . $e->getMessage());
        }
    }
}
