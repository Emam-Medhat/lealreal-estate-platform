<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;

class LeadAssignmentController extends Controller
{
    public function index()
    {
        $unassignedLeads = Lead::whereNull('assigned_to')
            ->with(['source', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('lead-assignment.index', compact('unassignedLeads', 'users'));
    }
    
    public function assign(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $lead = Lead::findOrFail($request->lead_id);
        $oldAssignee = $lead->assigned_to;
        
        $lead->update([
            'assigned_to' => $request->assigned_to,
            'assigned_at' => now(),
        ]);
        
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'assigned',
            'description' => 'تم تخصيص العميل المحتمل لـ ' . $lead->assignedUser->name,
            'user_id' => auth()->id(),
            'notes' => $request->notes,
        ]);
        
        return redirect()->back()
            ->with('success', 'تم تخصيص العميل المحتمل بنجاح');
    }
    
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'leads' => 'required|array',
            'leads.*' => 'exists:leads,id',
            'assigned_to' => 'required|exists:users,id',
            'assignment_type' => 'required|in:manual,round_robin,balanced',
        ]);
        
        $count = 0;
        
        switch ($request->assignment_type) {
            case 'manual':
                $count = $this->manualAssignment($request->leads, $request->assigned_to);
                break;
            case 'round_robin':
                $count = $this->roundRobinAssignment($request->leads);
                break;
            case 'balanced':
                $count = $this->balancedAssignment($request->leads);
                break;
        }
        
        return redirect()->back()
            ->with('success', 'تم تخصيص ' . $count . ' عميل بنجاح');
    }
    
    public function reassign(Request $request, Lead $lead)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'reason' => 'required|string|max:1000',
        ]);
        
        $oldAssignee = $lead->assigned_to;
        
        $lead->update([
            'assigned_to' => $request->assigned_to,
            'reassigned_at' => now(),
        ]);
        
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'reassigned',
            'description' => 'تم إعادة تخصيص العميل المحتمل من ' . ($oldAssignee ? $lead->assignedUser->name : 'غير محدد') . ' إلى ' . $lead->assignedUser->name,
            'user_id' => auth()->id(),
            'notes' => $request->reason,
        ]);
        
        return redirect()->back()
            ->with('success', 'تم إعادة تخصيص العميل المحتمل بنجاح');
    }
    
    public function unassign(Lead $lead)
    {
        $oldAssignee = $lead->assigned_to;
        
        $lead->update([
            'assigned_to' => null,
            'unassigned_at' => now(),
        ]);
        
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'unassigned',
            'description' => 'تم إلغاء تخصيص العميل المحتمل',
            'user_id' => auth()->id(),
        ]);
        
        return redirect()->back()
            ->with('success', 'تم إلغاء تخصيص العميل المحتمل بنجاح');
    }
    
    public function assignmentRules()
    {
        $rules = cache()->get('lead_assignment_rules', []);
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('lead-assignment.rules', compact('rules', 'users'));
    }
    
    public function storeAssignmentRules(Request $request)
    {
        $request->validate([
            'rules' => 'required|array',
            'rules.*.name' => 'required|string|max:255',
            'rules.*.conditions' => 'required|array',
            'rules.*.assignment_type' => 'required|in:manual,round_robin,balanced',
            'rules.*.assigned_to' => 'required_if:rules.*.assignment_type,manual|exists:users,id',
            'rules.*.priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        cache()->put('lead_assignment_rules', $request->rules, now()->addDays(30));
        
        return redirect()->route('lead-assignment.rules')
            ->with('success', 'تم حفظ قواعد التخصيص بنجاح');
    }
    
    public function workload()
    {
        $users = \App\Models\User::with(['assignedLeads' => function($query) {
            $query->where('assigned_at', '>', now()->subDays(30));
        }])->where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('lead-assignment.workload', compact('users'));
    }
    
    private function manualAssignment(array $leadIds, $userId)
    {
        $count = 0;
        foreach ($leadIds as $leadId) {
            $lead = Lead::findOrFail($leadId);
            if (!$lead->assigned_to) {
                $lead->update([
                    'assigned_to' => $userId,
                    'assigned_at' => now(),
                ]);
                
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'type' => 'assigned',
                    'description' => 'تم تخصيص العميل المحتمل',
                    'user_id' => auth()->id(),
                ]);
                
                $count++;
            }
        }
        
        return $count;
    }
    
    private function roundRobinAssignment(array $leadIds)
    {
        $agents = \App\Models\User::where('role', 'agent')->get();
        if ($agents->isEmpty()) {
            return 0;
        }
        
        $count = 0;
        $agentIndex = 0;
        
        foreach ($leadIds as $leadId) {
            $lead = Lead::findOrFail($leadId);
            if (!$lead->assigned_to) {
                $agent = $agents[$agentIndex % $agents->count()];
                
                $lead->update([
                    'assigned_to' => $agent->id,
                    'assigned_at' => now(),
                ]);
                
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'type' => 'assigned',
                    'description' => 'تم تخصيص العميل المحتمل (توزيع دوروري)',
                    'user_id' => auth()->id(),
                ]);
                
                $count++;
                $agentIndex++;
            }
        }
        
        return $count;
    }
    
    private function balancedAssignment(array $leadIds)
    {
        $agents = \App\Models\User::withCount(['assignedLeads' => function($query) {
            $query->where('assigned_at', '>', now()->subDays(30));
        }])->where('role', 'agent')->orderBy('assigned_leads_count', 'asc')->get();
        
        if ($agents->isEmpty()) {
            return 0;
        }
        
        $count = 0;
        $agentIndex = 0;
        
        foreach ($leadIds as $leadId) {
            $lead = Lead::findOrFail($leadId);
            if (!$lead->assigned_to) {
                $agent = $agents[$agentIndex % $agents->count()];
                
                $lead->update([
                    'assigned_to' => $agent->id,
                    'assigned_at' => now(),
                ]);
                
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'type' => 'assigned',
                    'description' => 'تم تخصيص العميل المحتمل (توزيع متوازن)',
                    'user_id' => auth()->id(),
                ]);
                
                $count++;
                $agentIndex++;
                
                // Refresh agent counts every 10 assignments
                if ($count % 10 == 0) {
                    $agents = \App\Models\User::withCount(['assignedLeads' => function($query) {
                        $query->where('assigned_at', '>', now()->subDays(30));
                    }])->where('role', 'agent')->orderBy('assigned_leads_count', 'asc')->get();
                }
            }
        }
        
        return $count;
    }
}
