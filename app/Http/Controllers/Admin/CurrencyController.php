<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\CurrencyExchangeLog;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display all currencies
     */
    public function index()
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.view')) {
            abort(403, __('Unauthorized action.'));
        }

        $admin->logActivity('view', 'currencies', 'Viewed currency list');

        $currencies = Currency::orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(20);

        $defaultCurrency = Currency::getDefault();

        return view('admin.currencies.index', compact('currencies', 'defaultCurrency'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.edit')) {
            abort(403, __('Unauthorized action.'));
        }

        return view('admin.currencies.create');
    }

    /**
     * Store new currency
     */
    public function store(Request $request)
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.edit')) {
            abort(403, __('Unauthorized action.'));
        }

        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code|uppercase',
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:4',
            'decimal_separator' => 'required|string|max:5',
            'thousand_separator' => 'nullable|string|max:5',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        try {
            $currency = Currency::create($validated);

            // Log initial rate
            CurrencyExchangeLog::create([
                'currency_id' => $currency->id,
                'old_rate' => 0,
                'new_rate' => $validated['exchange_rate'],
                'updated_by' => $admin->id,
                'source' => 'manual',
            ]);

            $admin->logActivity('create', 'currencies', "Created currency: {$currency->code}");

            return redirect()->route('admin.currencies.index')
                ->with('success', __('Currency created successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error creating currency: ') . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit(Currency $currency)
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.edit')) {
            abort(403, __('Unauthorized action.'));
        }

        return view('admin.currencies.edit', compact('currency'));
    }

    /**
     * Update currency
     */
    public function update(Request $request, Currency $currency)
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.edit')) {
            abort(403, __('Unauthorized action.'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'required|integer|min:0|max:4',
            'decimal_separator' => 'required|string|max:5',
            'thousand_separator' => 'nullable|string|max:5',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        try {
            // Check if exchange rate changed
            if ($currency->exchange_rate != $validated['exchange_rate']) {
                $currency->updateRate(
                    $validated['exchange_rate'],
                    'manual',
                    $admin->id
                );
                unset($validated['exchange_rate']); // Already updated
            }

            $currency->update($validated);

            $admin->logActivity('update', 'currencies', "Updated currency: {$currency->code}");

            return redirect()->route('admin.currencies.index')
                ->with('success', __('Currency updated successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Error updating currency: ') . $e->getMessage());
        }
    }

    /**
     * Delete currency
     */
    public function destroy(Currency $currency)
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.edit')) {
            abort(403, __('Unauthorized action.'));
        }

        // Prevent deletion of default currency
        if ($currency->is_default) {
            return back()->with('error', __('Cannot delete the default currency!'));
        }

        try {
            $code = $currency->code;
            $currency->delete();

            $admin->logActivity('delete', 'currencies', "Deleted currency: {$code}");

            return redirect()->route('admin.currencies.index')
                ->with('success', __('Currency deleted successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting currency: ') . $e->getMessage());
        }
    }

    /**
     * Set currency as default
     */
    public function setDefault(Currency $currency)
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.edit')) {
            abort(403, __('Unauthorized action.'));
        }

        try {
            $currency->setAsDefault();

            $admin->logActivity('update', 'currencies', "Set {$currency->code} as default currency");

            return redirect()->route('admin.currencies.index')
                ->with('success', __('Default currency updated successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error setting default currency: ') . $e->getMessage());
        }
    }

    /**
     * View exchange rate logs
     */
    public function logs(Currency $currency)
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.view')) {
            abort(403, __('Unauthorized action.'));
        }

        $logs = $currency->exchangeLogs()
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.currencies.logs', compact('currency', 'logs'));
    }

    /**
     * Bulk update exchange rates
     */
    public function bulkUpdateRates(Request $request)
    {
        $admin = auth('admin')->user();

        if (!$admin->hasPermission('settings.edit')) {
            abort(403, __('Unauthorized action.'));
        }

        $validated = $request->validate([
            'rates' => 'required|array',
            'rates.*.currency_id' => 'required|exists:currencies,id',
            'rates.*.exchange_rate' => 'required|numeric|min:0.000001',
        ]);

        try {
            $updated = 0;

            foreach ($validated['rates'] as $rateData) {
                $currency = Currency::find($rateData['currency_id']);

                if ($currency && !$currency->is_default) {
                    $currency->updateRate(
                        $rateData['exchange_rate'],
                        'manual',
                        $admin->id
                    );
                    $updated++;
                }
            }

            $admin->logActivity('update', 'currencies', "Bulk updated {$updated} exchange rates");

            return redirect()->route('admin.currencies.index')
                ->with('success', __('Exchange rates updated successfully!') . " ({$updated} " . __('currencies') . ")");
        } catch (\Exception $e) {
            return back()->with('error', __('Error updating rates: ') . $e->getMessage());
        }
    }
}
