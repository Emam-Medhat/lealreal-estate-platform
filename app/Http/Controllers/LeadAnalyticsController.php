<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\LeadConversion;
use App\Models\LeadActivity;
use Illuminate\Http\Request;

class LeadAnalyticsController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('created_at', '>', now()->subDays(30))->count(),
            'converted_leads' => Lead::whereNotNull('converted_at')->count(),
            'conversion_rate' => Lead::whereNotNull('converted_at')->count() / max(Lead::count(), 1) * 100,
            'avg_lead_value' => Lead::avg('estimated_value'),
            'total_value' => Lead::sum('estimated_value'),
        ];
        
        $monthlyLeads = Lead::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();
            
        $conversionBySource = LeadConversion::join('leads', 'lead_conversions.lead_id', '=', 'leads.id')
            ->join('lead_sources', 'leads.source_id', '=', 'lead_sources.id')
            ->selectRaw('lead_sources.name as source, COUNT(*) as conversions, SUM(lead_conversions.conversion_value) as value')
            ->groupBy('lead_sources.name')
            ->orderBy('value', 'desc')
            ->get();
            
        $leadByStatus = Lead::join('lead_statuses', 'leads.status_id', '=', 'lead_statuses.id')
            ->selectRaw('lead_statuses.name as status, COUNT(*) as count')
            ->groupBy('lead_statuses.name')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('lead-analytics.dashboard', compact('stats', 'monthlyLeads', 'conversionBySource', 'leadByStatus'));
    }
    
    public function conversionAnalytics()
    {
        $stats = [
            'total_conversions' => LeadConversion::count(),
            'conversion_rate' => Lead::whereNotNull('converted_at')->count() / max(Lead::count(), 1) * 100,
            'avg_conversion_time' => $this->getAverageConversionTime(),
            'avg_conversion_value' => LeadConversion::avg('conversion_value'),
            'total_conversion_value' => LeadConversion::sum('conversion_value'),
        ];
        
        $monthlyConversions = LeadConversion::selectRaw('YEAR(conversion_date) as year, MONTH(conversion_date) as month, COUNT(*) as count, SUM(conversion_value) as value')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();
            
        $conversionByType = LeadConversion::selectRaw('converted_to_type, COUNT(*) as count, AVG(conversion_value) as avg_value')
            ->groupBy('converted_to_type')
            ->get();
            
        $conversionFunnel = $this->getConversionFunnel();
        
        return view('lead-analytics.conversions', compact('stats', 'monthlyConversions', 'conversionByType', 'conversionFunnel'));
    }
    
    public function sourceAnalytics()
    {
        $sources = LeadSource::withCount('leads')
            ->with(['leads' => function($query) {
                $query->selectRaw('source_id, COUNT(*) as count, SUM(estimated_value) as value')
                      ->whereNotNull('converted_at')
                      ->groupBy('source_id');
            }])
            ->get();
            
        $sourcePerformance = [];
        foreach ($sources as $source) {
            $convertedLeads = $source->leads->where('converted_at', '!=', null);
            $sourcePerformance[] = [
                'source' => $source->name,
                'total_leads' => $source->leads_count,
                'converted_leads' => $convertedLeads->count(),
                'conversion_rate' => $convertedLeads->count() / max($source->leads_count, 1) * 100,
                'total_value' => $convertedLeads->sum('estimated_value'),
                'avg_value' => $convertedLeads->avg('estimated_value'),
            ];
        }
        
        return view('lead-analytics.sources', compact('sources', 'sourcePerformance'));
    }
    
    public function activityAnalytics()
    {
        $activities = LeadActivity::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();
            
        $dailyActivities = LeadActivity::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $topPerformers = \App\Models\User::withCount(['leadActivities' => function($query) {
            $query->where('created_at', '>', now()->subDays(30));
        }])->whereHas('leadActivities', function($query) {
            $query->where('created_at', '>', now()->subDays(30));
        })->orderBy('lead_activities_count', 'desc')
          ->take(10)
          ->get();
        
        return view('lead-analytics.activities', compact('activities', 'dailyActivities', 'topPerformers'));
    }
    
    public function performanceAnalytics()
    {
        $agents = \App\Models\User::where('role', 'agent')
            ->with(['assignedLeads' => function($query) {
                $query->where('assigned_at', '>', now()->subDays(30));
            }])
            ->get();
            
        $agentPerformance = [];
        foreach ($agents as $agent) {
            $leads = $agent->assignedLeads;
            $convertedLeads = $leads->whereNotNull('converted_at');
            
            $agentPerformance[] = [
                'agent' => $agent->name,
                'total_leads' => $leads->count(),
                'converted_leads' => $convertedLeads->count(),
                'conversion_rate' => $convertedLeads->count() / max($leads->count(), 1) * 100,
                'total_value' => $convertedLeads->sum('estimated_value'),
                'avg_response_time' => $this->getAverageResponseTime($agent),
            ];
        }
        
        return view('lead-analytics.performance', compact('agentPerformance'));
    }
    
    public function customReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'metrics' => 'required|array',
            'metrics.*' => 'in:count,conversion_rate,value,avg_value',
            'group_by' => 'nullable|in:day,week,month,source,status,agent',
        ]);
        
        $leads = Lead::whereBetween('created_at', [$request->start_date, $request->end_date]);
        
        if ($request->group_by) {
            $leads = $this->applyGrouping($leads, $request->group_by);
        }
        
        $data = [];
        foreach ($request->metrics as $metric) {
            switch ($metric) {
                case 'count':
                    $data[$metric] = $leads->count();
                    break;
                case 'conversion_rate':
                    $data[$metric] = $leads->whereNotNull('converted_at')->count() / max($leads->count(), 1) * 100;
                    break;
                case 'value':
                    $data[$metric] = $leads->sum('estimated_value');
                    break;
                case 'avg_value':
                    $data[$metric] = $leads->avg('estimated_value');
                    break;
            }
        }
        
        return view('lead-analytics.custom-report', compact('data', 'request'));
    }
    
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:leads,conversions,activities',
            'format' => 'required|in:csv,xlsx,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);
        
        $data = $this->getExportData($request);
        
        // Implement export logic based on format
        switch ($request->format) {
            case 'csv':
                return $this->exportCSV($data, $request->type);
            case 'xlsx':
                return $this->exportExcel($data, $request->type);
            case 'pdf':
                return $this->exportPDF($data, $request->type);
        }
    }
    
    private function getAverageConversionTime()
    {
        $conversions = Lead::whereNotNull('converted_at')
            ->selectRaw('AVG(DATEDIFF(converted_at, created_at)) as avg_days')
            ->first();
            
        return $conversions->avg_days ?? 0;
    }
    
    private function getConversionFunnel()
    {
        $statuses = LeadStatus::orderBy('order')->get();
        $funnel = [];
        
        foreach ($statuses as $status) {
            $count = Lead::where('status_id', $status->id)->count();
            $funnel[] = [
                'status' => $status->name,
                'count' => $count,
                'percentage' => $count / max(Lead::count(), 1) * 100,
            ];
        }
        
        return $funnel;
    }
    
    private function getAverageResponseTime($agent)
    {
        // Calculate average time between lead assignment and first activity
        $activities = LeadActivity::whereHas('lead', function($query) use ($agent) {
            $query->where('assigned_to', $agent->id);
        })->where('type', 'contact')->get();
        
        $totalTime = 0;
        $count = 0;
        
        foreach ($activities as $activity) {
            $lead = $activity->lead;
            if ($lead->assigned_at) {
                $timeDiff = $activity->created_at->diffInHours($lead->assigned_at);
                $totalTime += $timeDiff;
                $count++;
            }
        }
        
        return $count > 0 ? $totalTime / $count : 0;
    }
    
    private function applyGrouping($query, $groupBy)
    {
        switch ($groupBy) {
            case 'day':
                return $query->selectRaw('DATE(created_at) as group_date, COUNT(*) as count')
                    ->groupBy('group_date');
            case 'week':
                return $query->selectRaw('YEARWEEK(created_at) as week, COUNT(*) as count')
                    ->groupBy('week');
            case 'month':
                return $query->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                    ->groupBy('year', 'month');
            case 'source':
                return $query->join('lead_sources', 'leads.source_id', '=', 'lead_sources.id')
                    ->selectRaw('lead_sources.name as group_name, COUNT(*) as count')
                    ->groupBy('lead_sources.name');
            case 'status':
                return $query->join('lead_statuses', 'leads.status_id', '=', 'lead_statuses.id')
                    ->selectRaw('lead_statuses.name as group_name, COUNT(*) as count')
                    ->groupBy('lead_statuses.name');
            case 'agent':
                return $query->join('users', 'leads.assigned_to', '=', 'users.id')
                    ->selectRaw('users.name as group_name, COUNT(*) as count')
                    ->groupBy('users.name');
        }
        
        return $query;
    }
    
    private function getExportData($request)
    {
        switch ($request->type) {
            case 'leads':
                $query = Lead::with(['source', 'status', 'assignedUser']);
                break;
            case 'conversions':
                $query = LeadConversion::with(['lead.source', 'lead.status', 'convertedBy']);
                break;
            case 'activities':
                $query = LeadActivity::with(['lead', 'user']);
                break;
        }
        
        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }
        
        return $query->get();
    }
    
    private function exportCSV($data, $type)
    {
        // Implement CSV export
        return response()->download('export.csv');
    }
    
    private function exportExcel($data, $type)
    {
        // Implement Excel export
        return response()->download('export.xlsx');
    }
    
    private function exportPDF($data, $type)
    {
        // Implement PDF export
        return response()->download('export.pdf');
    }
}
