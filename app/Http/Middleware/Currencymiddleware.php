<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Currency;
use App\Models\Setting;

class CurrencyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if multi-currency is enabled
        $multiCurrencyEnabled = Setting::getValue('enable_multi_currency', '1') === '1';

        if (!$multiCurrencyEnabled) {
            // Use default currency only
            $currency = Currency::getDefault();
            session(['currency' => $currency->code]);
            return $next($request);
        }

        // Determine currency in this order:
        // 1. Session (user selected)
        // 2. User preference (logged in)
        // 3. Cookie (previous visit)
        // 4. Auto-detect by IP
        // 5. Default currency

        $currencyCode = $this->determineCurrency($request);

        // Validate and set currency
        $currency = Currency::findByCode($currencyCode);

        if (!$currency || !$currency->is_active) {
            $currency = Currency::getDefault();
        }

        // Store in session
        session(['currency' => $currency->code]);

        // Store full currency object for easy access
        view()->share('currentCurrency', $currency);

        return $next($request);
    }

    /**
     * Determine which currency to use
     */
    protected function determineCurrency(Request $request): string
    {
        // 1. Check session
        if ($request->session()->has('currency')) {
            return $request->session()->get('currency');
        }

        // 2. Check authenticated user preference
        if (auth()->check() && auth()->user()->preferred_currency) {
            return auth()->user()->preferred_currency;
        }

        // 3. Check cookie
        if ($request->hasCookie('currency')) {
            return $request->cookie('currency');
        }

        // 4. Auto-detect by IP (if enabled)
        if (Setting::getValue('auto_detect_currency', '1') === '1') {
            $detectedCurrency = $this->detectCurrencyByIp($request);
            if ($detectedCurrency) {
                return $detectedCurrency;
            }
        }

        // 5. Default currency
        return Currency::getDefault()->code;
    }

    /**
     * Detect currency based on user's IP/location
     */
    protected function detectCurrencyByIp(Request $request): ?string
    {
        // Simple country to currency mapping
        $countryToCurrency = [
            'US' => 'USD',
            'GB' => 'GBP',
            'IN' => 'INR',
            'AU' => 'AUD',
            'CA' => 'CAD',
            'JP' => 'JPY',
            'AE' => 'AED',
            'EU' => 'EUR',
            'DE' => 'EUR',
            'FR' => 'EUR',
            'IT' => 'EUR',
            'ES' => 'EUR',
        ];

        // Get user's country from IP
        // You can use a service like ipinfo.io or geoip2
        // For now, return null (implement as needed)

        return null;
    }
}
