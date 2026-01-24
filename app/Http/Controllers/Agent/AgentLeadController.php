<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreLeadRequest;
use App\Http\Requests\Agent\UpdateLeadRequest;
use App\Models\Agent;
use App\Models\AgentLead;
use App\Models\Property;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AgentLeadController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $leads = $agent->leads()
            ->with(['property', 'source'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->source_id, function ($query, $sourceId) {
                $query->where('source_id', $sourceId);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->paginate(20);

        return view('agent.leads.index', compact('leads'));
    }

    public function create()
    {
        $agent = Auth::user()->agent;
        $properties = $agent->properties()->where('status', 'active')->get(['id', 'title']);
        
        return view('agent.leads.create', compact('properties'));
    }

    public function store(StoreLeadRequest $request)
    {
        $agent = Auth::user()->agent;
        
        $lead = AgentLead::create([
            'agent_id' => $agent->id,
            'property_id' => $request->property_id,
            'source_id' => $request->source_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'status' => $request->status ?? 'new',
            'priority' => $request->priority ?? 'medium',
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'preferred_areas' => $request->preferred_areas ?? [],
            'preferred_property_types' => $request->preferred_property_types ?? [],
            'message' => $request->message,
            'notes' => $request->notes,
            'next_follow_up' => $request->next_follow_up,
            'assigned_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_lead',
            'details' => "Created lead: {$lead->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.leads.show', $lead)
            ->with('success', 'Lead created successfully.');
    }

    public function show(AgentLead $lead)
    {
        $this->authorize('view', $lead);
        
        $lead->load(['property', 'source', 'appointments', 'notes']);
        
        return view('agent.leads.show', compact('lead'));
    }

    public function edit(AgentLead $lead)
    {
        $this->authorize('update', $lead);
        
        $agent = Auth::user()->agent;
        $properties = $agent->properties()->where('status', 'active')->get(['id', 'title']);
        $lead->load(['property', 'source']);
        
        return view('agent.leads.edit', compact('lead', 'properties'));
    }

    public function update(UpdateLeadRequest $request, AgentLead $lead)
    {
        $this->authorize('update', $lead);
        
        $lead->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'status' => $request->status,
            'priority' => $request->priority,
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'preferred_areas' => $request->preferred_areas ?? [],
            'preferred_property_types' => $request->preferred_property_types ?? [],
            'message' => $request->message,
            'notes' => $request->notes,
            'next_follow_up' => $request->next_follow_up,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_lead',
            'details' => "Updated lead: {$lead->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(AgentLead $lead)
    {
        $this->authorize('delete', $lead);
        
        $leadName = $lead->name;
        $lead->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_lead',
            'details' => "Deleted lead: {$leadName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    public function updateStatus(Request $request, AgentLead $lead): JsonResponse
    {
        $this->authorize('update', $lead);
        
        $request->validate([
            'status' => 'required|in:new,contacted,qualified,converted,closed,lost',
        ]);

        $lead->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_lead_status',
            'details' => "Updated lead {$lead->name} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Lead status updated successfully'
        ]);
    }

    public function updatePriority(Request $request, AgentLead $lead): JsonResponse
    {
        $this->authorize('update', $lead);
        
        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $lead->update(['priority' => $request->priority]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_lead_priority',
            'details' => "Updated lead {$lead->name} priority to {$request->priority}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'priority' => $request->priority,
            'message' => 'Lead priority updated successfully'
        ]);
    }

    public function addNote(Request $request, AgentLead $lead): JsonResponse
    {
        $this->authorize('update', $lead);
        
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $lead->notes()->create([
            'agent_id' => Auth::user()->agent->id,
            'content' => $request->note,
            'type' => 'note',
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'added_lead_note',
            'details' => "Added note to lead: {$lead->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully'
        ]);
    }

    public function scheduleFollowUp(Request $request, AgentLead $lead): JsonResponse
    {
        $this->authorize('update', $lead);
        
        $request->validate([
            'follow_up_date' => 'required|date|after:now',
            'follow_up_type' => 'required|in:call,email,meeting,whatsapp',
            'follow_up_notes' => 'nullable|string|max:500',
        ]);

        $lead->update([
            'next_follow_up' => $request->follow_up_date,
            'follow_up_type' => $request->follow_up_type,
            'follow_up_notes' => $request->follow_up_notes,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'scheduled_follow_up',
            'details' => "Scheduled follow-up for lead: {$lead->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up scheduled successfully'
        ]);
    }

    public function convertToClient(Request $request, AgentLead $lead): JsonResponse
    {
        $this->authorize('update', $lead);
        
        $request->validate([
            'conversion_notes' => 'required|string|max:1000',
        ]);

        $lead->update([
            'status' => 'converted',
            'converted_at' => now(),
            'conversion_notes' => $request->conversion_notes,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'converted_lead',
            'details' => "Converted lead to client: {$lead->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead converted to client successfully'
        ]);
    }

    public function getLeadStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'total_leads' => $agent->leads()->count(),
            'new_leads' => $agent->leads()->where('status', 'new')->count(),
            'contacted_leads' => $agent->leads()->where('status', 'contacted')->count(),
            'qualified_leads' => $agent->leads()->where('status', 'qualified')->count(),
            'converted_leads' => $agent->leads()->where('status', 'converted')->count(),
            'lost_leads' => $agent->leads()->where('status', 'lost')->count(),
            'conversion_rate' => 0,
            'this_month_leads' => $agent->leads()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $totalLeads = $stats['total_leads'];
        if ($totalLeads > 0) {
            $stats['conversion_rate'] = round(($stats['converted_leads'] / $totalLeads) * 100, 2);
        }

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getUpcomingFollowUps(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $followUps = $agent->leads()
            ->whereNotNull('next_follow_up')
            ->where('next_follow_up', '<=', now()->addDays(7))
            ->where('status', '!=', 'converted')
            ->orderBy('next_follow_up')
            ->limit(10)
            ->get(['id', 'name', 'next_follow_up', 'follow_up_type', 'status']);

        return response()->json([
            'success' => true,
            'follow_ups' => $followUps
        ]);
    }

    public function exportLeads(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->leads()->with(['property', 'source']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->get();

        $filename = "agent_leads_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $leads,
            'filename' => $filename,
            'message' => 'Leads exported successfully'
        ]);
    }
}
