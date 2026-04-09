<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentAppointment;
use App\Models\AgentTask;
use App\Models\AgentClient;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentCalendarController extends Controller
{
    public function index()
    {
        $agent = Auth::user()->agent;
        
        // Get today's events
        $todayEvents = $this->getTodayEvents($agent);
        
        // Get upcoming events
        $upcomingEvents = $this->getUpcomingEvents($agent);
        
        // Get tasks
        $tasks = $agent->tasks()
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        return view('agent.calendar.index', compact('todayEvents', 'upcomingEvents', 'tasks'));
    }

    public function getCalendarEvents(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $start = $request->start;
        $end = $request->end;

        // Get appointments
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

                return [
                    'id' => $appointment->id,
                    'title' => $appointment->title,
                    'start' => $appointment->start,
                    'end' => $appointment->end,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $backgroundColor,
                    'textColor' => '#ffffff',
                    'url' => route('agent.appointments.show', $appointment->id),
                    'extendedProps' => [
                        'type' => 'appointment',
                        'lead_name' => $appointment->lead?->name,
                        'property_title' => $appointment->property?->title,
                        'appointment_type' => $appointment->type,
                        'status' => $appointment->status,
                    ],
                ];
            });

        // Get tasks
        $tasks = $agent->tasks()
            ->whereBetween('due_date', [$start, $end])
            ->get(['id', 'title', 'due_date', 'priority', 'status'])
            ->map(function ($task) {
                $backgroundColor = match($task->priority) {
                    'urgent' => '#dc3545',
                    'high' => '#fd7e14',
                    'medium' => '#ffc107',
                    'low' => '#28a745',
                    default => '#6c757d'
                };

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->format('Y-m-d'),
                    'allDay' => true,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $backgroundColor,
                    'textColor' => '#ffffff',
                    'url' => route('agent.tasks.show', $task->id),
                    'extendedProps' => [
                        'type' => 'task',
                        'priority' => $task->priority,
                        'status' => $task->status,
                    ],
                ];
            });

        // Get client follow-ups
        $followUps = $agent->clients()
            ->whereNotNull('next_follow_up')
            ->whereBetween('next_follow_up', [$start, $end])
            ->get(['id', 'name', 'next_follow_up', 'follow_up_type'])
            ->map(function ($client) {
                return [
                    'id' => 'followup_' . $client->id,
                    'title' => "Follow-up: {$client->name}",
                    'start' => $client->next_follow_up->format('Y-m-d H:i:s'),
                    'allDay' => false,
                    'backgroundColor' => '#17a2b8',
                    'borderColor' => '#17a2b8',
                    'textColor' => '#ffffff',
                    'url' => route('agent.clients.show', $client->id),
                    'extendedProps' => [
                        'type' => 'followup',
                        'client_name' => $client->name,
                        'follow_up_type' => $client->follow_up_type,
                    ],
                ];
            });

        $events = $appointments->merge($tasks)->merge($followUps);

        return response()->json($events);
    }

    public function getTodayEvents(Agent $agent): array
    {
        $today = now()->startOfDay();
        $tomorrow = now()->endOfDay();

        return [
            'appointments' => $agent->appointments()
                ->whereBetween('appointment_date', [$today, $tomorrow])
                ->with(['lead', 'property'])
                ->orderBy('appointment_date')
                ->get(),
            
            'tasks' => $agent->tasks()
                ->whereDate('due_date', today())
                ->orderBy('priority', 'desc')
                ->get(),
            
            'follow_ups' => $agent->clients()
                ->whereDate('next_follow_up', today())
                ->get(['id', 'name', 'next_follow_up', 'follow_up_type']),
        ];
    }

    public function getUpcomingEvents(Agent $agent): array
    {
        $nextWeek = now()->addWeek();
        
        return [
            'appointments' => $agent->appointments()
                ->where('appointment_date', '>', now())
                ->where('appointment_date', '<=', $nextWeek)
                ->where('status', 'scheduled')
                ->with(['lead', 'property'])
                ->orderBy('appointment_date')
                ->limit(5)
                ->get(),
            
            'tasks' => $agent->tasks()
                ->where('due_date', '>', now())
                ->where('due_date', '<=', $nextWeek)
                ->where('status', 'pending')
                ->orderBy('due_date')
                ->limit(5)
                ->get(),
            
            'follow_ups' => $agent->clients()
                ->where('next_follow_up', '>', now())
                ->where('next_follow_up', '<=', $nextWeek)
                ->where('status', 'active')
                ->limit(5)
                ->get(['id', 'name', 'next_follow_up', 'follow_up_type']),
        ];
    }

    public function getWeekView(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $weekStart = $request->week_start 
            ? Carbon::parse($request->week_start)->startOfWeek()
            : now()->startOfWeek();
        
        $weekEnd = $weekStart->copy()->endOfWeek();

        $events = [
            'appointments' => $agent->appointments()
                ->whereBetween('appointment_date', [$weekStart, $weekEnd])
                ->with(['lead', 'property'])
                ->orderBy('appointment_date')
                ->get(),
            
            'tasks' => $agent->tasks()
                ->whereBetween('due_date', [$weekStart, $weekEnd])
                ->orderBy('due_date')
                ->get(),
            
            'follow_ups' => $agent->clients()
                ->whereBetween('next_follow_up', [$weekStart, $weekEnd])
                ->get(['id', 'name', 'next_follow_up', 'follow_up_type']),
        ];

        return response()->json([
            'success' => true,
            'week_start' => $weekStart->format('Y-m-d'),
            'week_end' => $weekEnd->format('Y-m-d'),
            'events' => $events,
        ]);
    }

    public function getMonthView(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $events = [
            'appointments' => $agent->appointments()
                ->whereBetween('appointment_date', [$monthStart, $monthEnd])
                ->with(['lead', 'property'])
                ->orderBy('appointment_date')
                ->get(),
            
            'tasks' => $agent->tasks()
                ->whereBetween('due_date', [$monthStart, $monthEnd])
                ->orderBy('due_date')
                ->get(),
            
            'follow_ups' => $agent->clients()
                ->whereBetween('next_follow_up', [$monthStart, $monthEnd])
                ->get(['id', 'name', 'next_follow_up', 'follow_up_type']),
        ];

        return response()->json([
            'success' => true,
            'month' => $month,
            'year' => $year,
            'events' => $events,
        ]);
    }

    public function getCalendarStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
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
                ->where('status', 'scheduled')
                ->count(),
            
            'overdue_tasks' => $agent->tasks()
                ->where('due_date', '<', now())
                ->where('status', 'pending')
                ->count(),
            
            'due_today_tasks' => $agent->tasks()
                ->whereDate('due_date', today())
                ->where('status', 'pending')
                ->count(),
            
            'pending_follow_ups' => $agent->clients()
                ->whereNotNull('next_follow_up')
                ->where('next_follow_up', '<=', now()->addDays(3))
                ->where('status', 'active')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function createQuickEvent(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'type' => 'required|in:appointment,task,followup',
            'date' => 'required|date|after:now',
            'duration' => 'nullable|integer|min:15|max:480',
            'notes' => 'nullable|string|max:1000',
        ]);

        $agent = Auth::user()->agent;

        switch ($request->type) {
            case 'appointment':
                $appointment = $agent->appointments()->create([
                    'title' => $request->title,
                    'type' => 'general',
                    'status' => 'scheduled',
                    'appointment_date' => $request->date,
                    'appointment_end_date' => $request->duration 
                        ? Carbon::parse($request->date)->addMinutes($request->duration)
                        : Carbon::parse($request->date)->addHour(),
                    'notes' => $request->notes,
                ]);

                $event = [
                    'type' => 'appointment',
                    'id' => $appointment->id,
                    'url' => route('agent.appointments.show', $appointment->id),
                ];
                break;

            case 'task':
                $task = $agent->tasks()->create([
                    'title' => $request->title,
                    'description' => $request->notes,
                    'priority' => 'medium',
                    'status' => 'pending',
                    'due_date' => $request->date,
                ]);

                $event = [
                    'type' => 'task',
                    'id' => $task->id,
                    'url' => route('agent.tasks.show', $task->id),
                ];
                break;

            case 'followup':
                // For follow-ups, we need a client ID
                if (!$request->client_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Client ID is required for follow-up events'
                    ]);
                }

                $client = $agent->clients()->findOrFail($request->client_id);
                $client->update([
                    'next_follow_up' => $request->date,
                    'follow_up_type' => 'general',
                    'follow_up_notes' => $request->notes,
                ]);

                $event = [
                    'type' => 'followup',
                    'id' => $client->id,
                    'url' => route('agent.clients.show', $client->id),
                ];
                break;
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_quick_calendar_event',
            'details' => "Created quick {$request->type}: {$request->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'event' => $event,
            'message' => ucfirst($request->type) . ' created successfully'
        ]);
    }

    public function exportCalendar(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,ical',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'event_types' => 'required|array',
            'event_types.*' => 'in:appointments,tasks,followups',
        ]);

        $agent = Auth::user()->agent;
        $events = [];

        if (in_array('appointments', $request->event_types)) {
            $events['appointments'] = $agent->appointments()
                ->whereBetween('appointment_date', [$request->date_from, $request->date_to])
                ->with(['lead', 'property'])
                ->get();
        }

        if (in_array('tasks', $request->event_types)) {
            $events['tasks'] = $agent->tasks()
                ->whereBetween('due_date', [$request->date_from, $request->date_to])
                ->get();
        }

        if (in_array('followups', $request->event_types)) {
            $events['followups'] = $agent->clients()
                ->whereNotNull('next_follow_up')
                ->whereBetween('next_follow_up', [$request->date_from, $request->date_to])
                ->get();
        }

        $filename = "agent_calendar_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $events,
            'filename' => $filename,
            'format' => $request->format,
            'message' => 'Calendar data exported successfully'
        ]);
    }
}
