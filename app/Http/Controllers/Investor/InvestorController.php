<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\StoreInvestorRequest;
use App\Http\Requests\Investor\UpdateInvestorRequest;
use App\Models\Investor;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        $investors = Investor::with(['user'])
            ->when($request->search, function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->investment_range, function ($query, $range) {
                $ranges = [
                    'small' => [0, 10000],
                    'medium' => [10001, 50000],
                    'large' => [50001, 100000],
                    'enterprise' => [100001, 999999999]
                ];
                if (isset($ranges[$range])) {
                    $query->whereBetween('total_invested', $ranges[$range]);
                }
            })
            ->latest()
            ->paginate(20);

        return view('investor.index', compact('investors'));
    }

    public function create()
    {
        return view('investor.create');
    }

    public function store(StoreInvestorRequest $request)
    {
        $investor = Investor::create([
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'investor_type' => $request->investor_type,
            'status' => $request->status ?? 'active',
            'total_invested' => $request->total_invested ?? 0,
            'total_returns' => $request->total_returns ?? 0,
            'risk_tolerance' => $request->risk_tolerance,
            'investment_goals' => $request->investment_goals ?? [],
            'preferred_sectors' => $request->preferred_sectors ?? [],
            'experience_years' => $request->experience_years,
            'accredited_investor' => $request->accredited_investor ?? false,
            'verification_status' => $request->verification_status ?? 'pending',
            'address' => $request->address ?? [],
            'social_links' => $request->social_links ?? [],
            'bio' => $request->bio,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $picturePath = $request->file('profile_picture')->store('investor-pictures', 'public');
            $investor->update(['profile_picture' => $picturePath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_investor',
            'details' => "Created investor: {$investor->first_name} {$investor->last_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.show', $investor)
            ->with('success', 'Investor created successfully.');
    }

    public function show(Investor $investor)
    {
        $this->authorize('view', $investor);
        
        $investor->load(['user', 'portfolios', 'transactions']);
        
        return view('investor.show', compact('investor'));
    }

    public function edit(Investor $investor)
    {
        $this->authorize('update', $investor);
        
        return view('investor.edit', compact('investor'));
    }

    public function update(UpdateInvestorRequest $request, Investor $investor)
    {
        $this->authorize('update', $investor);
        
        $investor->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'investor_type' => $request->investor_type,
            'status' => $request->status,
            'total_invested' => $request->total_invested,
            'total_returns' => $request->total_returns,
            'risk_tolerance' => $request->risk_tolerance,
            'investment_goals' => $request->investment_goals ?? [],
            'preferred_sectors' => $request->preferred_sectors ?? [],
            'experience_years' => $request->experience_years,
            'accredited_investor' => $request->accredited_investor,
            'verification_status' => $request->verification_status,
            'address' => $request->address ?? [],
            'social_links' => $request->social_links ?? [],
            'bio' => $request->bio,
            'updated_by' => Auth::id(),
        ]);

        // Handle profile picture update
        if ($request->hasFile('profile_picture')) {
            if ($investor->profile_picture) {
                Storage::disk('public')->delete($investor->profile_picture);
            }
            $picturePath = $request->file('profile_picture')->store('investor-pictures', 'public');
            $investor->update(['profile_picture' => $picturePath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investor',
            'details' => "Updated investor: {$investor->first_name} {$investor->last_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.show', $investor)
            ->with('success', 'Investor updated successfully.');
    }

    public function destroy(Investor $investor)
    {
        $this->authorize('delete', $investor);
        
        $investorName = $investor->first_name . ' ' . $investor->last_name;
        
        // Delete profile picture
        if ($investor->profile_picture) {
            Storage::disk('public')->delete($investor->profile_picture);
        }
        
        $investor->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_investor',
            'details' => "Deleted investor: {$investorName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('investor.index')
            ->with('success', 'Investor deleted successfully.');
    }

    public function updateStatus(Request $request, Investor $investor): JsonResponse
    {
        $this->authorize('update', $investor);
        
        $request->validate([
            'status' => 'required|in:active,inactive,suspended,verified',
        ]);

        $investor->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investor_status',
            'details' => "Updated investor '{$investor->first_name} {$investor->last_name}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Investor status updated successfully'
        ]);
    }

    public function updateVerification(Request $request, Investor $investor): JsonResponse
    {
        $this->authorize('update', $investor);
        
        $request->validate([
            'verification_status' => 'required|in:pending,verified,rejected',
        ]);

        $investor->update(['verification_status' => $request->verification_status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investor_verification',
            'details' => "Updated investor '{$investor->first_name} {$investor->last_name}' verification to {$request->verification_status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'verification_status' => $request->verification_status,
            'message' => 'Investor verification status updated successfully'
        ]);
    }

    public function getInvestorStats(): JsonResponse
    {
        $stats = [
            'total_investors' => Investor::count(),
            'active_investors' => Investor::where('status', 'active')->count(),
            'verified_investors' => Investor::where('verification_status', 'verified')->count(),
            'by_type' => Investor::groupBy('investor_type')->map(function ($group) {
                return $group->count();
            }),
            'by_risk_tolerance' => Investor::groupBy('risk_tolerance')->map(function ($group) {
                return $group->count();
            }),
            'average_investment' => Investor::avg('total_invested'),
            'total_invested' => Investor::sum('total_invested'),
            'total_returns' => Investor::sum('total_returns'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportInvestors(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,inactive,suspended,verified',
            'investor_type' => 'nullable|string|max:50',
        ]);

        $query = Investor::with(['user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->investor_type) {
            $query->where('investor_type', $request->investor_type);
        }

        $investors = $query->get();

        $filename = "investors_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $investors,
            'filename' => $filename,
            'message' => 'Investors exported successfully'
        ]);
    }
}
