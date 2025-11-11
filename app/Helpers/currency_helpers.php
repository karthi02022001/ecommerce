<?php

use App\Models\Currency;

if (!function_exists('currentCurrency')) {
    /**
     * Get current currency object
     * 
     * @return Currency
     */
    function currentCurrency()
    {
        $code = session('currency', 'INR');
        $currency = Currency::findByCode($code);

        return $currency ?? Currency::getDefault();
    }
}

if (!function_exists('currencyCode')) {
    /**
     * Get current currency code
     * 
     * @return string
     */
    function currencyCode()
    {
        return currentCurrency()->code;
    }
}

if (!function_exists('currencySymbol')) {
    /**
     * Get current currency symbol
     * 
     * @return string
     */
    function currencySymbol()
    {
        return currentCurrency()->symbol;
    }
}

if (!function_exists('formatPrice')) {
    /**
     * Format price with current currency
     * 
     * @param float $amount
     * @param string|null $currencyCode Custom currency (optional)
     * @param bool $includeSymbol
     * @return string
     */
    function formatPrice($amount, $currencyCode = null, $includeSymbol = true)
    {
        if ($currencyCode) {
            $currency = Currency::findByCode($currencyCode);
        } else {
            $currency = currentCurrency();
        }

        if (!$currency) {
            return number_format($amount, 2);
        }

        return $currency->format($amount, $includeSymbol);
    }
}

if (!function_exists('convertPrice')) {
    /**
     * Convert price from base currency to current currency
     * 
     * @param float $amount Amount in base currency
     * @param string|null $toCurrency Target currency code (default: current)
     * @return float
     */
    function convertPrice($amount, $toCurrency = null)
    {
        $baseCurrency = Currency::getDefault();
        $targetCurrency = $toCurrency
            ? Currency::findByCode($toCurrency)
            : currentCurrency();

        if (!$targetCurrency) {
            return $amount;
        }

        return Currency::convert($amount, $baseCurrency->code, $targetCurrency->code);
    }
}

if (!function_exists('priceWithCurrency')) {
    /**
     * Get formatted price with automatic currency conversion
     * 
     * @param float $amount Amount in base currency
     * @param bool $convert Auto-convert to current currency
     * @return string
     */
    function priceWithCurrency($amount, $convert = true)
    {
        if ($convert) {
            $amount = convertPrice($amount);
        }

        return formatPrice($amount);
    }
}

if (!function_exists('activeCurrencies')) {
    /**
     * Get all active currencies for selection
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function activeCurrencies()
    {
        return Currency::getActiveList();
    }
}

if (!function_exists('isDefaultCurrency')) {
    /**
     * Check if current currency is the default/base currency
     * 
     * @return bool
     */
    function isDefaultCurrency()
    {
        return currentCurrency()->is_default;
    }
}

if (!function_exists('exchangeRate')) {
    /**
     * Get exchange rate between two currencies
     * 
     * @param string $from
     * @param string $to
     * @return float
     */
    function exchangeRate($from, $to)
    {
        $fromCurrency = Currency::findByCode($from);
        $toCurrency = Currency::findByCode($to);

        if (!$fromCurrency || !$toCurrency) {
            return 1.0;
        }

        // Rate = (1 / from_rate) * to_rate
        return ($toCurrency->exchange_rate / $fromCurrency->exchange_rate);
    }
}
