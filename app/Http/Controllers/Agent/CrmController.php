<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadNote;
use App\Models\LeadTask;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Services\AgentCrmService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CrmController extends Controller
{
    protected $agentCrmService;

    public function __construct(AgentCrmService $agentCrmService)
    {
        $this->middleware('auth');
        $this->middleware('agent');
        $this->agentCrmService = $agentCrmService;
    }

    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $leads = $this->agentCrmService->getAgentLeads($agent, $request);
        $stats = $this->agentCrmService->getCrmStats($agent);

        return view('agent.crm.index', compact('leads', 'stats'));
    }

    public function leads(Request $request)
    {
        $request->merge(['only_leads' => true]);

        return $this->index($request);
    }

    public function create()
    {
        return view('agent.crm.create');
    }

    public function store(Request $request)
    {
        $agent = Auth::user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'lead_source' => 'required|string|max:100',
            'lead_type' => 'required|string|in:buyer,seller,renter,investor',
            'priority' => 'required|string|in:low,medium,high',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'currency' => 'required|string|in:SAR,USD,EUR',
            'preferred_property_types' => 'nullable|array',
            'preferred_property_types.*' => 'string',
            'preferred_locations' => 'nullable|array',
            'preferred_locations.*' => 'string',
            'preferred_bedrooms' => 'nullable|integer|min:0',
            'preferred_bathrooms' => 'nullable|integer|min:0',
            'preferred_area_min' => 'nullable|numeric|min:0',
            'preferred_area_max' => 'nullable|numeric|min:0',
            'timeline' => 'nullable|string|max:255',
            'financing_status' => 'nullable|string|max:255',
            'pre_approved' => 'boolean',
            'property_purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ]);

        $validated['agent_id'] = $agent->id;
        $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['lead_status'] = 'new';
        $validated['temperature'] = 50; // Default temperature
        $validated['score'] = 50; // Default score
        $validated['stage'] = 'initial';
        $validated['pipeline_position'] = 1;
        $validated['follow_up_count'] = 0;
        $validated['contact_attempts'] = 0;
        $validated['conversion_probability'] = 25.0;
        $validated['first_contact_at'] = now();
        $validated['last_activity_at'] = now();
        $validated['created_by'] = $agent->id;

        $lead = Lead::create($validated);

        $this->agentCrmService->invalidateCache($agent->id);

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Lead created successfully!');
    }

    public function show(Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $lead->load(['activities', 'notes', 'tasks', 'appointments', 'documents', 'properties']);

        return view('agent.crm.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        return view('agent.crm.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'lead_source' => 'required|string|max:100',
            'lead_type' => 'required|string|in:buyer,seller,renter,investor',
            'priority' => 'required|string|in:low,medium,high',
            'lead_status' => 'required|string|in:new,contacted,qualified,proposals,negotiation,closed_won,closed_lost,archived',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'currency' => 'required|string|in:SAR,USD,EUR',
            'preferred_property_types' => 'nullable|array',
            'preferred_property_types.*' => 'string',
            'preferred_locations' => 'nullable|array',
            'preferred_locations.*' => 'string',
            'preferred_bedrooms' => 'nullable|integer|min:0',
            'preferred_bathrooms' => 'nullable|integer|min:0',
            'preferred_area_min' => 'nullable|numeric|min:0',
            'preferred_area_max' => 'nullable|numeric|min:0',
            'timeline' => 'nullable|string|max:255',
            'financing_status' => 'nullable|string|max:255',
            'pre_approved' => 'boolean',
            'property_purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'temperature' => 'required|integer|min:0|max:100',
            'score' => 'required|integer|min:0|max:100',
            'stage' => 'required|string',
            'pipeline_position' => 'required|integer|min:1',
            'conversion_probability' => 'required|numeric|min:0|max:100',
            'estimated_value' => 'nullable|numeric|min:0',
        ]);

        $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['updated_by'] = Auth::id();

        $lead->update($validated);

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Lead updated successfully!');
    }

    public function destroy(Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $lead->delete();

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.index')
            ->with('success', 'Lead deleted successfully!');
    }

    public function convert(Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $lead->convert();

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Lead converted successfully!');
    }

    public function lose(Lead $lead, Request $request)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'lost_reason' => 'required|string|max:500',
        ]);

        $lead->lose($request->lost_reason);

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Lead marked as lost!');
    }

    public function archive(Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $lead->archive();

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Lead archived successfully!');
    }

    public function addActivity(Request $request, Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'details' => 'nullable|array',
        ]);

        $lead->addActivity($validated['type'], $validated['description'], $validated['details'] ?? []);

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Activity added successfully!');
    }

    public function addNote(Request $request, Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'nullable|string|max:100',
            'is_private' => 'boolean',
        ]);

        LeadNote::create([
            'lead_id' => $lead->id,
            'agent_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'general',
            'is_private' => $validated['is_private'] ?? false,
            'created_by' => Auth::id(),
        ]);

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Note added successfully!');
    }

    public function addTask(Request $request, Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'priority' => 'required|string|in:low,medium,high',
            'due_date' => 'nullable|date|after:now',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        LeadTask::create([
            'lead_id' => $lead->id,
            'agent_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'type' => $validated['type'] ?? 'general',
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? Auth::id(),
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Task added successfully!');
    }

    public function scheduleFollowUp(Request $request, Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'follow_up_date' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $lead->scheduleFollowUp($request->follow_up_date, $request->notes);

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Follow-up scheduled successfully!');
    }

    public function recordContact(Request $request, Lead $lead)
    {
        if ($lead->agent_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'contact_type' => 'required|string|in:call,email,meeting,other',
            'outcome' => 'required|string|max:500',
            'notes' => 'nullable|string',
        ]);

        $lead->recordContact($request->contact_type, $request->outcome, $request->notes);

        $this->agentCrmService->invalidateCache(Auth::id());

        return redirect()
            ->route('agent.crm.show', $lead)
            ->with('success', 'Contact recorded successfully!');
    }
}
