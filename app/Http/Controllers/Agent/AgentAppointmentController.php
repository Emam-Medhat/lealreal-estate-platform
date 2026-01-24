<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreAppointmentRequest;
use App\Http\Requests\Agent\UpdateAppointmentRequest;
use App\Models\Agent;
use App\Models\AgentAppointment;
use App\Models\AgentLead;
use App\Models\Property;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $appointments = $agent->appointments()
            ->with(['lead', 'property'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('lead', function ($leadQuery) use ($search) {
                        $leadQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('appointment_date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('appointment_date', '<=', $date);
            })
            ->orderBy('appointment_date')
            ->paginate(20);

        return view('agent.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $agent = Auth::user()->agent;
        $leads = $agent->leads()->where('status', '!=', 'closed')->get(['id', 'name']);
        $properties = $agent->properties()->where('status', 'active')->get(['id', 'title']);
        
        return view('agent.appointments.create', compact('leads', 'properties'));
    }

    public function store(StoreAppointmentRequest $request)
    {
        $agent = Auth::user()->agent;
        
        $appointment = AgentAppointment::create([
            'agent_id' => $agent->id,
            'lead_id' => $request->lead_id,
            'property_id' => $request->property_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $request->status ?? 'scheduled',
            'appointment_date' => $request->appointment_date,
            'appointment_end_date' => $request->appointment_end_date,
            'location' => $request->location,
            'meeting_link' => $request->meeting_link,
            'phone_number' => $request->phone_number,
            'notes' => $request->notes,
            'reminder_sent' => false,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_appointment',
            'details' => "Created appointment: {$appointment->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment created successfully.');
    }

    public function show(AgentAppointment $appointment)
    {
        $this->authorize('view', $appointment);
        
        $appointment->load(['lead', 'property', 'notes']);
        
        return view('agent.appointments.show', compact('appointment'));
    }

    public function edit(AgentAppointment $appointment)
    {
        $this->authorize('update', $appointment);
        
        $agent = Auth::user()->agent;
        $leads = $agent->leads()->where('status', '!=', 'closed')->get(['id', 'name']);
        $properties = $agent->properties()->where('status', 'active')->get(['id', 'title']);
        $appointment->load(['lead', 'property']);
        
        return view('agent.appointments.edit', compact('appointment', 'leads', 'properties'));
    }

    public function update(UpdateAppointmentRequest $request, AgentAppointment $appointment)
    {
        $this->authorize('update', $appointment);
        
        $appointment->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $request->status,
            'appointment_date' => $request->appointment_date,
            'appointment_end_date' => $request->appointment_end_date,
            'location' => $request->location,
            'meeting_link' => $request->meeting_link,
            'phone_number' => $request->phone_number,
            'notes' => $request->notes,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_appointment',
            'details' => "Updated appointment: {$appointment->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(AgentAppointment $appointment)
    {
        $this->authorize('delete', $appointment);
        
        $appointmentTitle = $appointment->title;
        $appointment->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_appointment',
            'details' => "Deleted appointment: {$appointmentTitle}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    public function updateStatus(Request $request, AgentAppointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);
        
        $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,rescheduled,no_show',
        ]);

        $appointment->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_appointment_status',
            'details' => "Updated appointment {$appointment->title} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Appointment status updated successfully'
        ]);
    }

    public function reschedule(Request $request, AgentAppointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);
        
        $request->validate([
            'new_date' => 'required|date|after:now',
            'new_end_date' => 'required|date|after:new_date',
            'reason' => 'required|string|max:500',
        ]);

        $oldDate = $appointment->appointment_date;
        
        $appointment->update([
            'appointment_date' => $request->new_date,
            'appointment_end_date' => $request->new_end_date,
            'status' => 'rescheduled',
            'reschedule_reason' => $request->reason,
            'rescheduled_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'rescheduled_appointment',
            'details' => "Rescheduled appointment {$appointment->title} from {$oldDate} to {$request->new_date}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully'
        ]);
    }

    public function addNote(Request $request, AgentAppointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);
        
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $appointment->notes()->create([
            'agent_id' => Auth::user()->agent->id,
            'content' => $request->note,
            'type' => 'note',
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'added_appointment_note',
            'details' => "Added note to appointment: {$appointment->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully'
        ]);
    }

    public function completeAppointment(Request $request, AgentAppointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);
        
        $request->validate([
            'outcome' => 'required|string|max:1000',
            'next_steps' => 'nullable|string|max:1000',
        ]);

        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
            'outcome' => $request->outcome,
            'next_steps' => $request->next_steps,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'completed_appointment',
            'details' => "Completed appointment: {$appointment->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment completed successfully'
        ]);
    }

    public function getCalendarEvents(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $start = $request->start;
        $end = $request->end;

        $appointments = $agent->appointments()
            ->whereBetween('appointment_date', [$start, $end])
            ->with(['lead', 'property'])
            ->get(['id', 'appointment_date as start', 'appointment_end_date as end', 'title', 'type', 'status', 'lead_id', 'property_id'])
            ->map(function ($appointment) {
                $backgroundColor = match($appointment->status) {
                    'scheduled' => '#007bff',
                    'completed' => '#28a745',
                    'cancelled' => '#dc3545',
                    'rescheduled' => '#ffc107',
                    'no_show' => '#6c757d',
                    default => '#007bff'
                };

                $appointment['backgroundColor'] = $backgroundColor;
                $appointment['borderColor'] = $backgroundColor;
                $appointment['textColor'] = '#ffffff';
                $appointment['url'] = route('agent.appointments.show', $appointment->id);
                $appointment['extendedProps'] = [
                    'lead_name' => $appointment->lead?->name,
                    'property_title' => $appointment->property?->title,
                    'type' => $appointment->type,
                    'status' => $appointment->status,
                ];
                
                return $appointment;
            });

        return response()->json($appointments);
    }

    public function getTodayAppointments(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $appointments = $agent->appointments()
            ->whereDate('appointment_date', today())
            ->where('status', 'scheduled')
            ->with(['lead', 'property'])
            ->orderBy('appointment_date')
            ->get();

        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    public function getUpcomingAppointments(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $appointments = $agent->appointments()
            ->where('appointment_date', '>=', now())
            ->where('appointment_date', '<=', now()->addDays(7))
            ->where('status', 'scheduled')
            ->with(['lead', 'property'])
            ->orderBy('appointment_date')
            ->get();

        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    public function getAppointmentStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'total_appointments' => $agent->appointments()->count(),
            'scheduled_appointments' => $agent->appointments()->where('status', 'scheduled')->count(),
            'completed_appointments' => $agent->appointments()->where('status', 'completed')->count(),
            'cancelled_appointments' => $agent->appointments()->where('status', 'cancelled')->count(),
            'today_appointments' => $agent->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->count(),
            'this_week_appointments' => $agent->appointments()
                ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->where('status', 'scheduled')
                ->count(),
            'this_month_appointments' => $agent->appointments()
                ->whereMonth('appointment_date', now()->month)
                ->whereYear('appointment_date', now()->year)
                ->count(),
            'completion_rate' => 0,
        ];

        $totalAppointments = $stats['total_appointments'];
        if ($totalAppointments > 0) {
            $stats['completion_rate'] = round(($stats['completed_appointments'] / $totalAppointments) * 100, 2);
        }

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportAppointments(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->appointments()->with(['lead', 'property']);

        if ($request->date_from) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }

        $appointments = $query->get();

        $filename = "agent_appointments_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $appointments,
            'filename' => $filename,
            'message' => 'Appointments exported successfully'
        ]);
    }
}
