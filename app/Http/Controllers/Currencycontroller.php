<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CurrencyController extends Controller
{
    /**
     * Switch currency
     * 
     * @param string $code Currency code (USD, EUR, INR, etc.)
     */
    public function switch(Request $request, $code)
    {
        $currency = Currency::findByCode($code);

        if (!$currency || !$currency->is_active) {
            return redirect()->back()
                ->with('error', __('Invalid or inactive currency selected.'));
        }

        // Store in session
        session(['currency' => $currency->code]);

        // Store in cookie (30 days)
        Cookie::queue('currency', $currency->code, 43200); // 30 days in minutes

        // Update user preference if logged in
        if (auth()->check()) {
            auth()->user()->update([
                'preferred_currency' => $currency->code
            ]);
        }

        return redirect()->back()
            ->with('success', __('Currency changed to') . ' ' . $currency->name);
    }

    /**
     * Get current exchange rate for AJAX
     */
    public function getRate(Request $request)
    {
        $from = $request->input('from', 'INR');
        $to = $request->input('to', 'USD');
        $amount = $request->input('amount', 1);

        $converted = Currency::convert($amount, $from, $to);

        $toCurrency = Currency::findByCode($to);

        return response()->json([
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'converted' => $converted,
            'formatted' => $toCurrency ? $toCurrency->format($converted) : $converted,
            'rate' => $toCurrency ? $toCurrency->exchange_rate : 1,
        ]);
    }
}
