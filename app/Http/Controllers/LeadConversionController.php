<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadConversion;
use App\Models\LeadActivity;
use Illuminate\Http\Request;

class LeadConversionController extends Controller
{
    public function index()
    {
        $conversions = LeadConversion::with(['lead.source', 'lead.status', 'convertedBy'])
            ->orderBy('conversion_date', 'desc')
            ->paginate(20);
            
        return view('lead-conversions.index', compact('conversions'));
    }
    
    public function create(Lead $lead)
    {
        $lead->load(['source', 'status', 'assignedUser']);
        
        return view('lead-conversions.create', compact('lead'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'converted_to_type' => 'required|in:client,opportunity,property',
            'converted_to_id' => 'nullable|integer',
            'conversion_value' => 'required|numeric|min:0',
            'conversion_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);
        
        $lead = Lead::findOrFail($request->lead_id);
        
        $conversion = LeadConversion::create([
            'lead_id' => $request->lead_id,
            'converted_to_type' => $request->converted_to_type,
            'converted_to_id' => $request->converted_to_id,
            'conversion_value' => $request->conversion_value,
            'conversion_date' => $request->conversion_date,
            'notes' => $request->notes,
            'converted_by' => auth()->id(),
        ]);
        
        $lead->update([
            'converted_at' => $request->conversion_date,
            'status_id' => \App\Models\LeadStatus::where('name', 'محول')->first()->id,
        ]);
        
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'converted',
            'description' => 'تم تحويل العميل المحتمل إلى ' . $request->converted_to_type,
            'user_id' => auth()->id(),
        ]);
        
        return redirect()->route('lead-conversions.show', $conversion)
            ->with('success', 'تم تحويل العميل المحتمل بنجاح');
    }
    
    public function show(LeadConversion $conversion)
    {
        $conversion->load(['lead.source', 'lead.status', 'lead.assignedUser', 'convertedBy']);
        
        return view('lead-conversions.show', compact('conversion'));
    }
    
    public function edit(LeadConversion $conversion)
    {
        $conversion->load(['lead']);
        
        return view('lead-conversions.edit', compact('conversion'));
    }
    
    public function update(Request $request, LeadConversion $conversion)
    {
        $request->validate([
            'converted_to_type' => 'required|in:client,opportunity,property',
            'converted_to_id' => 'nullable|integer',
            'conversion_value' => 'required|numeric|min:0',
            'conversion_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);
        
        $conversion->update([
            'converted_to_type' => $request->converted_to_type,
            'converted_to_id' => $request->converted_to_id,
            'conversion_value' => $request->conversion_value,
            'conversion_date' => $request->conversion_date,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('lead-conversions.show', $conversion)
            ->with('success', 'تم تحديث التحويل بنجاح');
    }
    
    public function destroy(LeadConversion $conversion)
    {
        $lead = $conversion->lead;
        $conversion->delete();
        
        $lead->update([
            'converted_at' => null,
            'status_id' => \App\Models\LeadStatus::where('name', 'جديد')->first()->id,
        ]);
        
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'conversion_cancelled',
            'description' => 'تم إلغاء تحويل العميل المحتمل',
            'user_id' => auth()->id(),
        ]);
        
        return redirect()->route('lead-conversions.index')
            ->with('success', 'تم إلغاء التحويل بنجاح');
    }
    
    public function analytics()
    {
        $stats = [
            'total_conversions' => LeadConversion::count(),
            'total_value' => LeadConversion::sum('conversion_value'),
            'average_value' => LeadConversion::avg('conversion_value'),
            'conversion_rate' => Lead::whereNotNull('converted_date')->count() / max(Lead::count(), 1) * 100,
            'total_conversion_value' => LeadConversion::sum('conversion_value'),
            'avg_conversion_time' => 0, // Placeholder - calculate from lead created_at to conversion_date
            'best_month_conversions' => LeadConversion::selectRaw('COUNT(*) as count')
                ->selectRaw('MONTH(conversion_date) as month')
                ->groupBy('month')
                ->orderBy('count', 'desc')
                ->value('count'),
            'best_source' => LeadConversion::join('leads', 'lead_conversions.lead_id', '=', 'leads.id')
                ->join('lead_sources', 'leads.source_id', '=', 'lead_sources.id')
                ->groupBy('lead_sources.name')
                ->orderByRaw('COUNT(*) DESC')
                ->value('lead_sources.name'),
            'conversion_trend' => 15.5, // Placeholder - calculate actual trend
            'top_conversion_type' => LeadConversion::groupBy('converted_to_type')
                ->orderByRaw('COUNT(*) DESC')
                ->value('converted_to_type'),
        ];
        
        $monthlyConversions = LeadConversion::selectRaw('MONTH(conversion_date) as month, YEAR(conversion_date) as year, COUNT(*) as count, SUM(conversion_value) as value')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();
            
        $conversionBySource = LeadConversion::join('leads', 'lead_conversions.lead_id', '=', 'leads.id')
            ->join('lead_sources', 'leads.source_id', '=', 'lead_sources.id')
            ->selectRaw('lead_sources.name as source_name, COUNT(*) as count, SUM(lead_conversions.conversion_value) as value')
            ->groupBy('lead_sources.name')
            ->orderBy('value', 'desc')
            ->get();
            
        $conversionByType = LeadConversion::selectRaw('converted_to_type, COUNT(*) as count, AVG(conversion_value) as avg_value')
            ->groupBy('converted_to_type')
            ->get();
        
        return view('lead-conversions.analytics', compact('stats', 'monthlyConversions', 'conversionBySource', 'conversionByType'));
    }
    
    public function conversionFunnel()
    {
        $stages = [
            'new' => Lead::where('status_id', \App\Models\LeadStatus::where('name', 'جديد')->first()->id)->count(),
            'contacted' => Lead::where('status_id', \App\Models\LeadStatus::where('name', 'تم التواصل')->first()->id)->count(),
            'qualified' => Lead::where('status_id', \App\Models\LeadStatus::where('name', 'مؤهل')->first()->id)->count(),
            'proposal' => Lead::where('status_id', \App\Models\LeadStatus::where('name', 'عرض')->first()->id)->count(),
            'negotiation' => Lead::where('status_id', \App\Models\LeadStatus::where('name', 'تفاوض')->first()->id)->count(),
            'converted' => Lead::where('converted_at', '!=', null)->count(),
        ];
        
        return view('lead-conversions.funnel', compact('stages'));
    }
    
    public function conversionReport()
    {
        $request = request();
        
        $conversions = LeadConversion::with(['lead.source', 'lead.status', 'convertedBy'])
            ->when($request->date_range, function($query) use ($request) {
                $dates = explode(' - ', $request->date_range);
                $query->whereBetween('conversion_date', [$dates[0], $dates[1]]);
            })
            ->when($request->source_id, function($query) use ($request) {
                $query->whereHas('lead', function($q) use ($request) {
                    $q->where('source_id', $request->source_id);
                });
            })
            ->when($request->converted_by, function($query) use ($request) {
                $query->where('converted_by', $request->converted_by);
            })
            ->orderBy('conversion_date', 'desc')
            ->paginate(20);
            
        $sources = \App\Models\LeadSource::all();
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('lead-conversions.report', compact('conversions', 'sources', 'users'));
    }
}
