<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\LeadScore;
use App\Models\LeadActivity;
use App\Models\LeadNote;
use App\Models\LeadTag;
use App\Models\LeadCampaign;
use App\Models\LeadConversion;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\ConvertLeadRequest;
use App\Http\Requests\ScoreLeadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::with(['source', 'status', 'assignedUser'])
            ->filter(request(['search', 'status', 'source', 'assigned_to', 'date_range']))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $sources = LeadSource::active()->get();
        $statuses = LeadStatus::active()->get();
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('leads.index', compact('leads', 'sources', 'statuses', 'users'));
    }
    
    public function dashboard()
    {
        $stats = [
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('status_id', LeadStatus::where('name', 'جديد')->first()->id)->count(),
            'qualified_leads' => Lead::where('status_id', LeadStatus::where('name', 'مؤهل')->first()->id)->count(),
            'converted_leads' => Lead::where('converted_at', '!=', null)->count(),
            'conversion_rate' => Lead::where('converted_at', '!=', null)->count() / max(Lead::count(), 1) * 100,
        ];
        
        $recentLeads = Lead::with(['source', 'status', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $leadSources = LeadSource::withCount('leads')
            ->orderBy('leads_count', 'desc')
            ->take(5)
            ->get();
            
        $leadStatuses = LeadStatus::withCount('leads')
            ->orderBy('leads_count', 'desc')
            ->get();
            
        return view('leads.dashboard', compact('stats', 'recentLeads', 'leadSources', 'leadStatuses'));
    }
    
    public function pipeline()
    {
        $statuses = LeadStatus::with(['leads' => function($query) {
            $query->with(['source', 'assignedUser'])
                  ->orderBy('priority', 'desc')
                  ->orderBy('created_at', 'desc');
        }])->orderBy('order')->get();
        
        return view('leads.pipeline', compact('statuses'));
    }
    
    public function create()
    {
        $sources = LeadSource::active()->get();
        $statuses = LeadStatus::active()->get();
        $campaigns = LeadCampaign::active()->get();
        $tags = LeadTag::all();
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('leads.create', compact('sources', 'statuses', 'campaigns', 'tags', 'users'));
    }
    
    public function store(StoreLeadRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $lead = Lead::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'position' => $request->position,
                'source_id' => $request->source_id,
                'status_id' => $request->status_id,
                'campaign_id' => $request->campaign_id,
                'assigned_to' => $request->assigned_to,
                'priority' => $request->priority,
                'estimated_value' => $request->estimated_value,
                'expected_close_date' => $request->expected_close_date,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);
            
            // Attach tags
            if ($request->has('tags')) {
                $lead->tags()->attach($request->tags);
            }
            
            // Create initial activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'type' => 'created',
                'description' => 'تم إنشاء العميل المحتمل',
                'user_id' => auth()->id(),
            ]);
            
            // Calculate initial score
            $this->calculateLeadScore($lead);
            
            DB::commit();
            
            return redirect()->route('leads.show', $lead)
                ->with('success', 'تم إضافة العميل المحتمل بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العميل المحتمل');
        }
    }
    
    public function show(Lead $lead)
    {
        $lead->load(['source', 'status', 'assignedUser', 'tags', 'activities.user', 'notes.user', 'conversions']);
        
        $score = $lead->scores()->latest()->first();
        $activities = $lead->activities()->with('user')->orderBy('created_at', 'desc')->get();
        $notes = $lead->notes()->with('user')->orderBy('created_at', 'desc')->get();
        
        return view('leads.show', compact('lead', 'score', 'activities', 'notes'));
    }
    
    public function edit(Lead $lead)
    {
        $sources = LeadSource::active()->get();
        $statuses = LeadStatus::active()->get();
        $campaigns = LeadCampaign::active()->get();
        $tags = LeadTag::all();
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('leads.edit', compact('lead', 'sources', 'statuses', 'campaigns', 'tags', 'users'));
    }
    
    public function update(StoreLeadRequest $request, Lead $lead)
    {
        $oldStatus = $lead->status_id;
        
        $lead->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'position' => $request->position,
            'source_id' => $request->source_id,
            'status_id' => $request->status_id,
            'campaign_id' => $request->campaign_id,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'estimated_value' => $request->estimated_value,
            'expected_close_date' => $request->expected_close_date,
            'notes' => $request->notes,
        ]);
        
        // Sync tags
        if ($request->has('tags')) {
            $lead->tags()->sync($request->tags);
        }
        
        // Create activity if status changed
        if ($oldStatus != $lead->status_id) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'type' => 'status_changed',
                'description' => 'تم تغيير الحالة إلى ' . $lead->status->name,
                'user_id' => auth()->id(),
            ]);
        }
        
        // Recalculate score
        $this->calculateLeadScore($lead);
        
        return redirect()->route('leads.show', $lead)
            ->with('success', 'تم تحديث العميل المحتمل بنجاح');
    }
    
    public function destroy(Lead $lead)
    {
        $lead->delete();
        
        return redirect()->route('leads.index')
            ->with('success', 'تم حذف العميل المحتمل بنجاح');
    }
    
    public function convert(ConvertLeadRequest $request, Lead $lead)
    {
        DB::beginTransaction();
        
        try {
            $conversion = LeadConversion::create([
                'lead_id' => $lead->id,
                'converted_to_type' => $request->converted_to_type,
                'converted_to_id' => $request->converted_to_id,
                'conversion_value' => $request->conversion_value,
                'conversion_date' => now(),
                'notes' => $request->notes,
                'converted_by' => auth()->id(),
            ]);
            
            $lead->update([
                'converted_at' => now(),
                'status_id' => LeadStatus::where('name', 'محول')->first()->id,
            ]);
            
            LeadActivity::create([
                'lead_id' => $lead->id,
                'type' => 'converted',
                'description' => 'تم تحويل العميل المحتمل إلى ' . $request->converted_to_type,
                'user_id' => auth()->id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('leads.show', $lead)
                ->with('success', 'تم تحويل العميل المحتمل بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحويل العميل المحتمل');
        }
    }
    
    public function score(ScoreLeadRequest $request, Lead $lead)
    {
        $score = LeadScore::create([
            'lead_id' => $lead->id,
            'score' => $request->score,
            'factors' => $request->factors,
            'calculated_by' => auth()->id(),
        ]);
        
        $lead->update(['score' => $request->score]);
        
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'scored',
            'description' => 'تم تقييم العميل المحتمل بدرجة ' . $request->score,
            'user_id' => auth()->id(),
        ]);
        
        return redirect()->route('leads.show', $lead)
            ->with('success', 'تم تقييم العميل المحتمل بنجاح');
    }
    
    private function calculateLeadScore(Lead $lead)
    {
        $score = 0;
        $factors = [];
        
        // Base score based on source
        if ($lead->source) {
            $score += $lead->source->weight;
            $factors[] = 'المصدر: ' . $lead->source->name . ' (+' . $lead->source->weight . ')';
        }
        
        // Score based on completeness
        $completeness = 0;
        if ($lead->email) $completeness += 20;
        if ($lead->phone) $completeness += 20;
        if ($lead->company) $completeness += 15;
        if ($lead->position) $completeness += 15;
        if ($lead->estimated_value) $completeness += 30;
        
        $score += $completeness;
        $factors[] = 'اكتمال البيانات: ' . $completeness . '%';
        
        // Score based on priority
        $priorityScores = [
            'low' => 5,
            'medium' => 10,
            'high' => 20,
            'critical' => 30,
        ];
        
        if (isset($priorityScores[$lead->priority])) {
            $score += $priorityScores[$lead->priority];
            $factors[] = 'الأولوية: ' . __('leads.priority.' . $lead->priority) . ' (+' . $priorityScores[$lead->priority] . ')';
        }
        
        // Create or update score record
        LeadScore::updateOrCreate(
            ['lead_id' => $lead->id],
            [
                'score' => $score,
                'factors' => $factors,
                'calculated_by' => auth()->id(),
            ]
        );
        
        $lead->update(['score' => $score]);
    }
}
