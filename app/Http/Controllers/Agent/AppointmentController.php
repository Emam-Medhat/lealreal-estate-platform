<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Lead;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Services\AgentAppointmentService;

class AppointmentController extends Controller
{
    protected $agentAppointmentService;

    public function __construct(AgentAppointmentService $agentAppointmentService)
    {
        $this->middleware('auth');
        $this->middleware('agent');
        $this->agentAppointmentService = $agentAppointmentService;
    }

    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $appointments = $this->agentAppointmentService->getAgentAppointments($agent, $request);
        $stats = $this->agentAppointmentService->getAppointmentStats($agent);

        return view('agent.appointments.index', compact('appointments', 'stats'));
    }

    public function calendar()
    {
        $agent = Auth::user();
        
        $appointments = Appointment::where('agent_id', $agent->id)
            ->with(['lead', 'property'])
            ->where('start_datetime', '>=', now()->subMonths(3))
            ->where('start_datetime', '<=', now()->addMonths(3))
            ->get();

        return view('agent.appointments.calendar', compact('appointments'));
    }

    public function create()
    {
        $agent = Auth::user();
        
        $leads = Lead::where('agent_id', $agent->id)->where('lead_status', '!=', 'archived')->get();
        $properties = Property::where('agent_id', $agent->id)->where('status', 'active')->get();

        return view('agent.appointments.create', compact('leads', 'properties'));
    }

    public function store(Request $request)
    {
        $agent = Auth::user();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lead_id' => 'nullable|exists:leads,id',
            'property_id' => 'nullable|exists:properties,id',
            'appointment_type' => 'required|string|in:property_viewing,consultation,follow_up,closing,negotiation,other',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'duration' => 'nullable|integer|min:15',
            'timezone' => 'required|string|max:50',
            'location_type' => 'required|string|in:office,property,virtual,other',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'meeting_link' => 'nullable|url',
            'meeting_password' => 'nullable|string|max:100',
            'meeting_platform' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'agenda' => 'nullable|array',
            'agenda.*' => 'string',
            'preparation_notes' => 'nullable|array',
            'preparation_notes.*' => 'string',
            'priority' => 'required|string|in:low,medium,high',
            'attendees' => 'nullable|array',
            'attendees.*' => 'email',
            'required_attendees' => 'nullable|array',
            'required_attendees.*' => 'email',
            'optional_attendees' => 'nullable|array',
            'optional_attendees.*' => 'email',
            'reminders' => 'nullable|array',
            'reminders.*.minutes_before' => 'required|integer|min:0',
            'reminders.*.method' => 'required|string|in:email,sms,push',
        ]);

        // Calculate duration if not provided
        if (!isset($validated['duration'])) {
            $start = new \DateTime($validated['start_datetime']);
            $end = new \DateTime($validated['end_datetime']);
            $validated['duration'] = $end->getTimestamp() - $start->getTimestamp();
            $validated['duration'] = max(15, $validated['duration'] / 60); // Convert to minutes, minimum 15
        }

        $validated['agent_id'] = $agent->id;
        $validated['status'] = 'pending';
        $validated['confirmation_status'] = 'pending';
        $validated['rescheduled_count'] = 0;
        $validated['created_by'] = $agent->id;

        $appointment = Appointment::create($validated);
        $this->agentAppointmentService->invalidateCache($agent->id);

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment scheduled successfully!');
    }

    public function show(Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $appointment->load(['lead', 'property', 'notes', 'documents', 'reminders']);

        return view('agent.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $agent = Auth::user();
        $leads = Lead::where('agent_id', $agent->id)->where('lead_status', '!=', 'archived')->get();
        $properties = Property::where('agent_id', $agent->id)->where('status', 'active')->get();

        return view('agent.appointments.edit', compact('appointment', 'leads', 'properties'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lead_id' => 'nullable|exists:leads,id',
            'property_id' => 'nullable|exists:properties,id',
            'appointment_type' => 'required|string|in:property_viewing,consultation,follow_up,closing,negotiation,other',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'duration' => 'nullable|integer|min:15',
            'timezone' => 'required|string|max:50',
            'location_type' => 'required|string|in:office,property,virtual,other',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'meeting_link' => 'nullable|url',
            'meeting_password' => 'nullable|string|max:100',
            'meeting_platform' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'agenda' => 'nullable|array',
            'agenda.*' => 'string',
            'preparation_notes' => 'nullable|array',
            'preparation_notes.*' => 'string',
            'priority' => 'required|string|in:low,medium,high',
            'attendees' => 'nullable|array',
            'attendees.*' => 'email',
            'required_attendees' => 'nullable|array',
            'required_attendees.*' => 'email',
            'optional_attendees' => 'nullable|array',
            'optional_attendees.*' => 'email',
            'reminders' => 'nullable|array',
            'reminders.*.minutes_before' => 'required|integer|min:0',
            'reminders.*.method' => 'required|string|in:email,sms,push',
        ]);

        // Check if date/time changed
        $dateChanged = $appointment->start_datetime != $validated['start_datetime'] || 
                      $appointment->end_datetime != $validated['end_datetime'];

        if ($dateChanged) {
            $validated['rescheduled_count'] = $appointment->rescheduled_count + 1;
            $validated['original_start_datetime'] = $appointment->start_datetime;
        }

        // Calculate duration if not provided
        if (!isset($validated['duration'])) {
            $start = new \DateTime($validated['start_datetime']);
            $end = new \DateTime($validated['end_datetime']);
            $validated['duration'] = $end->getTimestamp() - $start->getTimestamp();
            $validated['duration'] = max(15, $validated['duration'] / 60);
        }

        $validated['updated_by'] = Auth::id();

        $appointment->update($validated);
        $this->agentAppointmentService->invalidateCache($appointment->agent_id);

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully!');
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $agentId = $appointment->agent_id;
        $appointment->delete();
        $this->agentAppointmentService->invalidateCache($agentId);

        return redirect()
            ->route('agent.appointments.index')
            ->with('success', 'Appointment deleted successfully!');
    }

    public function confirm(Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $appointment->confirm();
        $this->agentAppointmentService->invalidateCache($appointment->agent_id);

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment confirmed successfully!');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $appointment->cancel($request->reason);
        $this->agentAppointmentService->invalidateCache($appointment->agent_id);

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment cancelled successfully!');
    }

    public function markAsNoShow(Request $request, Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $appointment->markAsNoShow($request->reason);

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment marked as no show!');
    }

    public function complete(Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $appointment->complete();

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment completed successfully!');
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'new_start_datetime' => 'required|date|after:now',
            'new_end_datetime' => 'required|date|after:new_start_datetime',
            'reason' => 'required|string|max:500',
        ]);

        $appointment->reschedule($request->new_start_datetime, $request->new_end_datetime, $request->reason);
        $this->agentAppointmentService->invalidateCache($appointment->agent_id);

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment rescheduled successfully!');
    }

    public function addNote(Request $request, Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'type' => 'nullable|string|max:100',
            'is_private' => 'boolean',
        ]);

        $appointment->addNote($validated['content'], $validated['type'] ?? 'general', $validated['is_private'] ?? false);

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Note added successfully!');
    }

    public function addDocument(Request $request, Appointment $appointment)
    {
        if ($appointment->agent_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'type' => 'nullable|string|max:100',
        ]);

        $filePath = $validated['file']->store('appointments/documents', 'public');

        $appointment->addDocument($validated['title'], $filePath, $validated['type'] ?? 'general');

        return redirect()
            ->route('agent.appointments.show', $appointment)
            ->with('success', 'Document added successfully!');
    }
}
