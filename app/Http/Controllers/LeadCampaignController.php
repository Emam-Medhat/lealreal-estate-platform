<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadCampaign;
use App\Models\LeadActivity;
use Illuminate\Http\Request;

class LeadCampaignController extends Controller
{
    public function index()
    {
        $campaigns = LeadCampaign::withCount('leads')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('lead-campaigns.index', compact('campaigns'));
    }
    
    public function create()
    {
        return view('lead-campaigns.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:email,social,web,referral,advertising',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'target_audience' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        
        $campaign = LeadCampaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'target_audience' => $request->target_audience,
            'goals' => $request->goals,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);
        
        return redirect()->route('lead-campaigns.show', $campaign)
            ->with('success', 'تم إنشاء الحملة بنجاح');
    }
    
    public function show(LeadCampaign $campaign)
    {
        $campaign->load(['leads' => function($query) {
            $query->with(['source', 'status', 'assignedUser'])
                  ->orderBy('created_at', 'desc')
                  ->take(20);
        }]);
        
        $stats = [
            'total_leads' => $campaign->leads()->count(),
            'converted_leads' => $campaign->leads()->whereNotNull('converted_at')->count(),
            'conversion_rate' => $campaign->leads()->whereNotNull('converted_at')->count() / max($campaign->leads()->count(), 1) * 100,
            'total_value' => $campaign->leads()->whereNotNull('converted_at')->sum('estimated_value'),
        ];
        
        return view('lead-campaigns.show', compact('campaign', 'stats'));
    }
    
    public function edit(LeadCampaign $campaign)
    {
        return view('lead-campaigns.edit', compact('campaign'));
    }
    
    public function update(Request $request, LeadCampaign $campaign)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:email,social,web,referral,advertising',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'target_audience' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        
        $campaign->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'target_audience' => $request->target_audience,
            'goals' => $request->goals,
            'is_active' => $request->boolean('is_active', $campaign->is_active),
        ]);
        
        return redirect()->route('lead-campaigns.show', $campaign)
            ->with('success', 'تم تحديث الحملة بنجاح');
    }
    
    public function destroy(LeadCampaign $campaign)
    {
        if ($campaign->leads()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الحملة لوجود عملاء مرتبطين بها');
        }
        
        $campaign->delete();
        
        return redirect()->route('lead-campaigns.index')
            ->with('success', 'تم حذف الحملة بنجاح');
    }
    
    public function toggleStatus(LeadCampaign $campaign)
    {
        $campaign->update([
            'is_active' => !$campaign->is_active
        ]);
        
        return back()->with('success', 'تم تحديث حالة الحملة بنجاح');
    }
    
    public function addLeads(Request $request, LeadCampaign $campaign)
    {
        $request->validate([
            'leads' => 'required|array',
            'leads.*' => 'exists:leads,id',
        ]);
        
        $count = 0;
        foreach ($request->leads as $leadId) {
            $lead = Lead::findOrFail($leadId);
            if (!$lead->campaign_id) {
                $lead->update(['campaign_id' => $campaign->id]);
                
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'type' => 'added_to_campaign',
                    'description' => 'تم إضافة العميل المحتمل إلى حملة: ' . $campaign->name,
                    'user_id' => auth()->id(),
                ]);
                
                $count++;
            }
        }
        
        return redirect()->back()
            ->with('success', 'تم إضافة ' . $count . ' عميل إلى الحملة بنجاح');
    }
    
    public function removeLead(LeadCampaign $campaign, Lead $lead)
    {
        if ($lead->campaign_id == $campaign->id) {
            $lead->update(['campaign_id' => null]);
            
            LeadActivity::create([
                'lead_id' => $lead->id,
                'type' => 'removed_from_campaign',
                'description' => 'تم إزالة العميل المحتمل من حملة: ' . $campaign->name,
                'user_id' => auth()->id(),
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'تم إزالة العميل من الحملة بنجاح');
    }
    
    public function analytics(LeadCampaign $campaign)
    {
        $stats = [
            'total_leads' => $campaign->leads()->count(),
            'new_leads' => $campaign->leads()->where('status_id', \App\Models\LeadStatus::where('name', 'جديد')->first()->id)->count(),
            'converted_leads' => $campaign->leads()->whereNotNull('converted_at')->count(),
            'conversion_rate' => $campaign->leads()->whereNotNull('converted_at')->count() / max($campaign->leads()->count(), 1) * 100,
            'total_value' => $campaign->leads()->whereNotNull('converted_at')->sum('estimated_value'),
            'roi' => $campaign->budget > 0 ? ($campaign->leads()->whereNotNull('converted_at')->sum('estimated_value') - $campaign->budget) / $campaign->budget * 100 : 0,
        ];
        
        $dailyLeads = $campaign->leads()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $conversionByStatus = $campaign->leads()
            ->join('lead_statuses', 'leads.status_id', '=', 'lead_statuses.id')
            ->selectRaw('lead_statuses.name as status, COUNT(*) as count')
            ->groupBy('lead_statuses.name')
            ->get();
        
        return view('lead-campaigns.analytics', compact('campaign', 'stats', 'dailyLeads', 'conversionByStatus'));
    }
    
    public function duplicate(LeadCampaign $campaign)
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->name = $campaign->name . ' (نسخة)';
        $newCampaign->is_active = false;
        $newCampaign->created_by = auth()->id();
        $newCampaign->save();
        
        return redirect()->route('lead-campaigns.show', $newCampaign)
            ->with('success', 'تم نسخ الحملة بنجاح');
    }
}
