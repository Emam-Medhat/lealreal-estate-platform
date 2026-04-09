<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreClientRequest;
use App\Http\Requests\Agent\UpdateClientRequest;
use App\Models\Agent;
use App\Models\AgentClient;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentClientController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $clients = $agent->clients()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('client_type', $type);
            })
            ->when($request->source, function ($query, $source) {
                $query->where('source', $source);
            })
            ->latest()
            ->paginate(20);

        return view('agent.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('agent.clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        $agent = Auth::user()->agent;
        
        $client = AgentClient::create([
            'agent_id' => $agent->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'company' => $request->company,
            'client_type' => $request->client_type,
            'status' => $request->status ?? 'active',
            'source' => $request->source,
            'referral_source' => $request->referral_source,
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'preferred_areas' => $request->preferred_areas ?? [],
            'preferred_property_types' => $request->preferred_property_types ?? [],
            'requirements' => $request->requirements ?? [],
            'timeline' => $request->timeline,
            'financing_status' => $request->financing_status,
            'pre_approved_amount' => $request->pre_approved_amount,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'notes' => $request->notes,
            'tags' => $request->tags ?? [],
            'custom_fields' => $request->custom_fields ?? [],
            'last_contact_date' => now(),
            'next_follow_up' => $request->next_follow_up,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_client',
            'details' => "Created client: {$client->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.clients.show', $client)
            ->with('success', 'Client created successfully.');
    }

    public function show(AgentClient $client)
    {
        $this->authorize('view', $client);
        
        $client->load(['properties', 'transactions', 'appointments', 'communications']);
        
        return view('agent.clients.show', compact('client'));
    }

    public function edit(AgentClient $client)
    {
        $this->authorize('update', $client);
        
        return view('agent.clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, AgentClient $client)
    {
        $this->authorize('update', $client);
        
        $client->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'company' => $request->company,
            'client_type' => $request->client_type,
            'status' => $request->status,
            'source' => $request->source,
            'referral_source' => $request->referral_source,
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'preferred_areas' => $request->preferred_areas ?? [],
            'preferred_property_types' => $request->preferred_property_types ?? [],
            'requirements' => $request->requirements ?? [],
            'timeline' => $request->timeline,
            'financing_status' => $request->financing_status,
            'pre_approved_amount' => $request->pre_approved_amount,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'notes' => $request->notes,
            'tags' => $request->tags ?? [],
            'custom_fields' => $request->custom_fields ?? [],
            'next_follow_up' => $request->next_follow_up,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_client',
            'details' => "Updated client: {$client->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(AgentClient $client)
    {
        $this->authorize('delete', $client);
        
        $clientName = $client->name;
        $client->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_client',
            'details' => "Deleted client: {$clientName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    public function updateStatus(Request $request, AgentClient $client): JsonResponse
    {
        $this->authorize('update', $client);
        
        $request->validate([
            'status' => 'required|in:active,inactive,prospect,closed,lost',
        ]);

        $client->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_client_status',
            'details' => "Updated client {$client->name} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Client status updated successfully'
        ]);
    }

    public function addNote(Request $request, AgentClient $client): JsonResponse
    {
        $this->authorize('update', $client);
        
        $request->validate([
            'note' => 'required|string|max:1000',
            'type' => 'nullable|in:note,call,email,meeting,general',
        ]);

        $client->communications()->create([
            'type' => $request->type ?? 'note',
            'content' => $request->note,
            'direction' => 'internal',
            'agent_id' => Auth::user()->agent->id,
        ]);

        $client->update(['last_contact_date' => now()]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'added_client_note',
            'details' => "Added note to client: {$client->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully'
        ]);
    }

    public function logCommunication(Request $request, AgentClient $client): JsonResponse
    {
        $this->authorize('update', $client);
        
        $request->validate([
            'type' => 'required|in:call,email,meeting,text,note',
            'direction' => 'required|in:inbound,outbound,internal',
            'content' => 'required|string|max:2000',
            'subject' => 'nullable|string|max:200',
            'duration_minutes' => 'nullable|integer|min:0',
            'next_follow_up' => 'nullable|date|after:now',
        ]);

        $communication = $client->communications()->create([
            'type' => $request->type,
            'direction' => $request->direction,
            'content' => $request->content,
            'subject' => $request->subject,
            'duration_minutes' => $request->duration_minutes,
            'agent_id' => Auth::user()->agent->id,
        ]);

        $client->update([
            'last_contact_date' => now(),
            'next_follow_up' => $request->next_follow_up,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'logged_client_communication',
            'details' => "Logged {$request->type} communication with client: {$client->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'communication' => $communication,
            'message' => 'Communication logged successfully'
        ]);
    }

    public function scheduleFollowUp(Request $request, AgentClient $client): JsonResponse
    {
        $this->authorize('update', $client);
        
        $request->validate([
            'follow_up_date' => 'required|date|after:now',
            'follow_up_type' => 'required|in:call,email,meeting,property_visit',
            'follow_up_notes' => 'nullable|string|max:500',
        ]);

        $client->update([
            'next_follow_up' => $request->follow_up_date,
            'follow_up_type' => $request->follow_up_type,
            'follow_up_notes' => $request->follow_up_notes,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'scheduled_client_follow_up',
            'details' => "Scheduled follow-up for client: {$client->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up scheduled successfully'
        ]);
    }

    public function getClientStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'total_clients' => $agent->clients()->count(),
            'active_clients' => $agent->clients()->where('status', 'active')->count(),
            'prospect_clients' => $agent->clients()->where('status', 'prospect')->count(),
            'closed_clients' => $agent->clients()->where('status', 'closed')->count(),
            'lost_clients' => $agent->clients()->where('status', 'lost')->count(),
            'total_budget' => $agent->clients()->sum('budget_max'),
            'average_budget' => $agent->clients()->avg('budget_max'),
            'clients_needing_follow_up' => $agent->clients()
                ->whereNotNull('next_follow_up')
                ->where('next_follow_up', '<=', now()->addDays(7))
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getUpcomingFollowUps(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $followUps = $agent->clients()
            ->whereNotNull('next_follow_up')
            ->where('next_follow_up', '<=', now()->addDays(7))
            ->where('status', '!=', 'closed')
            ->orderBy('next_follow_up')
            ->limit(10)
            ->get(['id', 'name', 'next_follow_up', 'follow_up_type', 'status']);

        return response()->json([
            'success' => true,
            'follow_ups' => $followUps
        ]);
    }

    public function getClientTimeline(AgentClient $client): JsonResponse
    {
        $this->authorize('view', $client);
        
        $timeline = $client->communications()
            ->with(['agent.profile'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($communication) {
                return [
                    'id' => $communication->id,
                    'type' => $communication->type,
                    'direction' => $communication->direction,
                    'content' => $communication->content,
                    'subject' => $communication->subject,
                    'created_at' => $communication->created_at->toISOString(),
                    'agent_name' => $communication->agent?->profile?->full_name ?? 'System',
                ];
            });

        return response()->json([
            'success' => true,
            'timeline' => $timeline
        ]);
    }

    public function exportClients(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,inactive,prospect,closed,lost',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->clients();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $clients = $query->get();

        $filename = "agent_clients_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $clients,
            'filename' => $filename,
            'message' => 'Clients exported successfully'
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:agent_clients,id',
            'action' => 'required|in:activate,inactivate,prospect,close,lose',
        ]);

        $agent = Auth::user()->agent;
        $clientIds = $request->client_ids;
        $action = $request->action;

        $statusMap = [
            'activate' => 'active',
            'inactivate' => 'inactive',
            'prospect' => 'prospect',
            'close' => 'closed',
            'lose' => 'lost',
        ];

        $newStatus = $statusMap[$action];

        $agent->clients()->whereIn('id', $clientIds)->update([
            'status' => $newStatus,
            'status_updated_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'bulk_updated_clients',
            'details' => "Bulk {$action} on " . count($clientIds) . " clients",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Clients {$action}d successfully"
        ]);
    }
}
