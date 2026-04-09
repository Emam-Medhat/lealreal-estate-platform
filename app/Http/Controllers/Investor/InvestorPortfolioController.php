<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\InvestorPortfolio;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvestorPortfolioController extends Controller
{
    public function index(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $portfolios = $investor->portfolios()
            ->when($request->search, function ($query, $search) {
                $query->where('investment_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->investment_type, function ($query, $type) {
                $query->where('investment_type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->risk_level, function ($query, $risk) {
                $query->where('risk_level', $risk);
            })
            ->latest()
            ->paginate(20);

        return view('investor.portfolio.index', compact('portfolios'));
    }

    public function create()
    {
        return view('investor.portfolio.create');
    }

    public function store(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'investment_name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'investment_type' => 'required|in:stocks,bonds,real_estate,commodities,crypto,mutual_funds,etf,alternative',
            'sector' => 'required|string|max:100',
            'amount_invested' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'expected_return_rate' => 'nullable|numeric|min:0|max:100',
            'expected_return_date' => 'nullable|date|after:today',
            'risk_level' => 'required|in:low,medium,high,very_high',
            'status' => 'nullable|in:active,completed,terminated,pending',
            'auto_reinvest' => 'nullable|boolean',
            'minimum_holding_period' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $portfolio = InvestorPortfolio::create([
            'investor_id' => $investor->id,
            'investment_name' => $request->investment_name,
            'description' => $request->description,
            'investment_type' => $request->investment_type,
            'sector' => $request->sector,
            'amount_invested' => $request->amount_invested,
            'current_value' => $request->current_value ?? $request->amount_invested,
            'expected_return_rate' => $request->expected_return_rate,
            'expected_return_date' => $request->expected_return_date,
            'expected_return_amount' => $this->calculateExpectedReturn($request),
            'risk_level' => $request->risk_level,
            'status' => $request->status ?? 'active',
            'auto_reinvest' => $request->auto_reinvest ?? false,
            'minimum_holding_period' => $request->minimum_holding_period,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle documents upload
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('portfolio-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $portfolio->update(['documents' => $documents]);
        }

        // Handle images upload
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('portfolio-images', 'public');
                $images[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $portfolio->update(['images' => $images]);
        }

        // Update investor total invested
        $investor->increment('total_invested', $request->amount_invested);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_investment_portfolio',
            'details' => "Created investment portfolio: {$portfolio->investment_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.portfolio.show', $portfolio)
            ->with('success', 'Investment portfolio created successfully.');
    }

    public function show(InvestorPortfolio $portfolio)
    {
        $this->authorize('view', $portfolio);
        
        $portfolio->load(['investor', 'transactions', 'roiCalculations']);
        
        return view('investor.portfolio.show', compact('portfolio'));
    }

    public function edit(InvestorPortfolio $portfolio)
    {
        $this->authorize('update', $portfolio);
        
        return view('investor.portfolio.edit', compact('portfolio'));
    }

    public function update(Request $request, InvestorPortfolio $portfolio)
    {
        $this->authorize('update', $portfolio);
        
        $request->validate([
            'investment_name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'investment_type' => 'required|in:stocks,bonds,real_estate,commodities,crypto,mutual_funds,etf,alternative',
            'sector' => 'required|string|max:100',
            'amount_invested' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'expected_return_rate' => 'nullable|numeric|min:0|max:100',
            'expected_return_date' => 'nullable|date|after:today',
            'risk_level' => 'required|in:low,medium,high,very_high',
            'status' => 'required|in:active,completed,terminated,pending',
            'auto_reinvest' => 'nullable|boolean',
            'minimum_holding_period' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldAmount = $portfolio->amount_invested;
        $newAmount = $request->amount_invested;
        
        $portfolio->update([
            'investment_name' => $request->investment_name,
            'description' => $request->description,
            'investment_type' => $request->investment_type,
            'sector' => $request->sector,
            'amount_invested' => $newAmount,
            'current_value' => $request->current_value,
            'expected_return_rate' => $request->expected_return_rate,
            'expected_return_date' => $request->expected_return_date,
            'expected_return_amount' => $this->calculateExpectedReturn($request),
            'risk_level' => $request->risk_level,
            'status' => $request->status,
            'auto_reinvest' => $request->auto_reinvest,
            'minimum_holding_period' => $request->minimum_holding_period,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Update investor total invested if amount changed
        if ($oldAmount != $newAmount) {
            $portfolio->investor->increment('total_invested', $newAmount - $oldAmount);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investment_portfolio',
            'details' => "Updated investment portfolio: {$portfolio->investment_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.portfolio.show', $portfolio)
            ->with('success', 'Investment portfolio updated successfully.');
    }

    public function destroy(InvestorPortfolio $portfolio)
    {
        $this->authorize('delete', $portfolio);
        
        $portfolioName = $portfolio->investment_name;
        $amountInvested = $portfolio->amount_invested;
        
        // Delete documents
        if ($portfolio->documents) {
            foreach ($portfolio->documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        // Delete images
        if ($portfolio->images) {
            foreach ($portfolio->images as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }
        
        $portfolio->delete();

        // Update investor total invested
        $portfolio->investor->decrement('total_invested', $amountInvested);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_investment_portfolio',
            'details' => "Deleted investment portfolio: {$portfolioName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('investor.portfolio.index')
            ->with('success', 'Investment portfolio deleted successfully.');
    }

    public function updateValue(Request $request, InvestorPortfolio $portfolio): JsonResponse
    {
        $this->authorize('update', $portfolio);
        
        $request->validate([
            'current_value' => 'required|numeric|min:0',
        ]);

        $portfolio->update(['current_value' => $request->current_value]);

        return response()->json([
            'success' => true,
            'current_value' => $request->current_value,
            'message' => 'Portfolio value updated successfully'
        ]);
    }

    public function getPortfolioStats(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $stats = [
            'total_portfolios' => $investor->portfolios()->count(),
            'active_portfolios' => $investor->portfolios()->where('status', 'active')->count(),
            'completed_portfolios' => $investor->portfolios()->where('status', 'completed')->count(),
            'total_invested' => $investor->portfolios()->sum('amount_invested'),
            'current_total_value' => $investor->portfolios()->sum('current_value'),
            'total_returns' => $investor->portfolios()->sum('total_returns'),
            'by_type' => $investor->portfolios()
                ->groupBy('investment_type')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_invested' => $group->sum('amount_invested'),
                        'current_value' => $group->sum('current_value'),
                    ];
                }),
            'by_risk_level' => $investor->portfolios()
                ->groupBy('risk_level')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_invested' => $group->sum('amount_invested'),
                        'current_value' => $group->sum('current_value'),
                    ];
                }),
            'by_sector' => $investor->portfolios()
                ->groupBy('sector')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_invested' => $group->sum('amount_invested'),
                        'current_value' => $group->sum('current_value'),
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportPortfolios(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'investment_type' => 'nullable|in:stocks,bonds,real_estate,commodities,crypto,mutual_funds,etf,alternative',
            'status' => 'nullable|in:active,completed,terminated,pending',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $query = $investor->portfolios();

        if ($request->investment_type) {
            $query->where('investment_type', $request->investment_type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $portfolios = $query->get();

        $filename = "investor_portfolios_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $portfolios,
            'filename' => $filename,
            'message' => 'Portfolios exported successfully'
        ]);
    }

    private function calculateExpectedReturn(Request $request): ?float
    {
        if (!$request->amount_invested || !$request->expected_return_rate) {
            return null;
        }

        return ($request->amount_invested * $request->expected_return_rate) / 100;
    }
}
