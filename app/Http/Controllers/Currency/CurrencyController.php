<?php

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function index()
    {
        $currencies = Currency::active()->get();
        
        // Add default currencies if none exist
        if ($currencies->isEmpty()) {
            $currencies = collect([
                'USD' => [
                    'code' => 'USD',
                    'name' => 'US Dollar',
                    'symbol' => '$'
                ],
                'EUR' => [
                    'code' => 'EUR',
                    'name' => 'Euro',
                    'symbol' => '€'
                ],
                'GBP' => [
                    'code' => 'GBP',
                    'name' => 'British Pound',
                    'symbol' => '£'
                ],
                'JPY' => [
                    'code' => 'JPY',
                    'name' => 'Japanese Yen',
                    'symbol' => '¥'
                ],
                'SAR' => [
                    'code' => 'SAR',
                    'name' => 'Saudi Riyal',
                    'symbol' => 'ر.س'
                ],
                'AED' => [
                    'code' => 'AED',
                    'name' => 'UAE Dirham',
                    'symbol' => 'د.إ'
                ],
                'EGP' => [
                    'code' => 'EGP',
                    'name' => 'Egyptian Pound',
                    'symbol' => 'ج.م'
                ],
                'KWD' => [
                    'code' => 'KWD',
                    'name' => 'Kuwaiti Dinar',
                    'symbol' => 'د.ك'
                ],
                'QAR' => [
                    'code' => 'QAR',
                    'name' => 'Qatari Riyal',
                    'symbol' => 'ر.ق'
                ],
                'BHD' => [
                    'code' => 'BHD',
                    'name' => 'Bahraini Dinar',
                    'symbol' => 'د.ب'
                ]
            ]);
        }
        
        return view('currency.index', compact('currencies'));
    }

    public function converter()
    {
        $currencies = Currency::active()->get();
        return view('currency.converter', compact('currencies'));
    }

    public function create()
    {
        return view('currency.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:currencies',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'precision' => 'required|integer|min:0|max:8',
            'exchange_rate_provider' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $currency = Currency::create($validated);

        return redirect()->route('currency.index')
            ->with('success', 'Currency created successfully.');
    }

    public function show(Currency $currency)
    {
        return view('currency.show', compact('currency'));
    }

    public function edit(Currency $currency)
    {
        return view('currency.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'precision' => 'required|integer|min:0|max:8',
            'exchange_rate_provider' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $currency->update($validated);

        return redirect()->route('currency.index')
            ->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()->route('currency.index')
            ->with('success', 'Currency deleted successfully.');
    }

    public function convert(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'from_currency' => 'required|string|size:3',
                'to_currency' => 'required|string|size:3'
            ]);

            $result = $this->currencyService->convertCurrency(
                $request->amount,
                strtoupper($request->from_currency),
                strtoupper($request->to_currency)
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Currency conversion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRates(): JsonResponse
    {
        try {
            $currencies = Currency::active()->get();
            $rates = [];

            foreach ($currencies as $currency) {
                $rates[$currency->code] = $this->currencyService->getExchangeRate('USD', $currency->code);
            }

            return response()->json([
                'success' => true,
                'base_currency' => 'USD',
                'rates' => $rates,
                'updated_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get rates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateRates(): JsonResponse
    {
        try {
            $result = $this->currencyService->updateExchangeRates();
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update rates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'from_currency' => 'required|string|size:3',
                'to_currency' => 'required|string|size:3',
                'days' => 'integer|min:1|max:365'
            ]);

            $result = $this->currencyService->getExchangeRateHistory(
                strtoupper($request->from_currency),
                strtoupper($request->to_currency),
                $request->days ?? 30
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics(): JsonResponse
    {
        try {
            $result = $this->currencyService->getCurrencyStatistics();
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function setPreferredCurrency(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'currency' => 'required|string|size:3'
            ]);

            $result = $this->currencyService->setUserPreferredCurrency(
                auth()->id(),
                strtoupper($request->currency)
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set preferred currency',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSupported(): JsonResponse
    {
        try {
            $currencies = $this->currencyService->getSupportedCurrencies();
            
            return response()->json([
                'success' => true,
                'currencies' => $currencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get supported currencies',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
