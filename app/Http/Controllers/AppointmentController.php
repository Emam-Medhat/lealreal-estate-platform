<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Appointment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('participant_id', $user->id);
        });
        
        // Filter
        if ($request->filter === 'today') {
            $query->whereDate('start_datetime', today());
        } elseif ($request->filter === 'week') {
            $query->whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->filter === 'month') {
            $query->whereMonth('start_datetime', now()->month)
                  ->whereYear('start_datetime', now()->year);
        }
        
        // Sort
        if ($request->sort === 'date') {
            $query->orderBy('start_datetime', 'asc');
        } elseif ($request->sort === 'name') {
            $query->join('users', function ($join) {
                $join->on('appointments.participant_id', '=', 'users.id')
                     ->orOn('appointments.user_id', '=', 'users.id');
            })->orderBy('users.first_name', 'asc')->orderBy('users.last_name', 'asc');
        } elseif ($request->sort === 'status') {
            $query->orderBy('status', 'asc');
        } else {
            $query->orderBy('start_datetime', 'asc');
        }
        
        $appointments = $query->with(['user', 'participant'])
            ->paginate(15);
        
        $stats = [
            'total' => $this->getAppointmentsCount($user),
            'confirmed' => $this->getAppointmentsCount($user, 'confirmed'),
            'pending' => $this->getAppointmentsCount($user, 'pending'),
            'today' => $this->getAppointmentsCount($user, null, today())
        ];
        
        $todayEvents = Appointment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('participant_id', $user->id);
        })->whereDate('start_datetime', today())
          ->where('status', '!=', 'cancelled')
          ->with(['user', 'participant'])
          ->orderBy('start_datetime', 'asc')
          ->get();
        
        $weekEvents = Appointment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('participant_id', $user->id);
        })->whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()])
          ->where('status', '!=', 'cancelled')
          ->with(['user', 'participant'])
          ->orderBy('start_datetime', 'asc')
          ->get();
        
        // Get users for appointment creation form
        $users = User::where('id', '!=', Auth::id())->get();
        
        return view('messages.appointments', compact('appointments', 'stats', 'todayEvents', 'weekEvents', 'users'));
    }
    
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('messages.appointments-create', compact('users'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'participant_id' => 'required|exists:users,id',
            'start_datetime' => 'required|date|after_or_equal:today',
            'duration' => 'required|integer|min:15|max:480',
            'type' => 'required|in:video,voice,in-person,phone',
            'notes' => 'nullable|string|max:1000',
            'reminder_minutes' => 'nullable|integer|min:0|max:10080'
        ]);
        
        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'participant_id' => $request->participant_id,
            'title' => $request->title,
            'start_datetime' => $request->start_datetime,
            'duration' => $request->duration,
            'appointment_type' => $request->type,
            'notes' => $request->notes,
            'status' => 'pending',
            'reminder_minutes' => $request->reminder_minutes ?? 30
        ]);
        
        // Send notification to participant
        UserNotification::create([
            'user_id' => $request->participant_id,
            'title' => 'New Appointment',
            'message' => Auth::user()->name . ' scheduled an appointment: ' . $request->title,
            'type' => 'appointment',
            'data' => json_encode(['appointment_id' => $appointment->id]),
            'action_url' => '/messages/appointments/' . $appointment->id,
            'action_text' => 'View Appointment'
        ]);
        
        return response()->json([
            'success' => true,
            'appointment_id' => $appointment->id
        ]);
    }
    
    public function edit($id)
    {
        $appointment = Appointment::where(function ($query) {
            $query->where('user_id', Auth::id())
                  ->orWhere('participant_id', Auth::id());
        })->findOrFail($id);
        
        $users = User::where('id', '!=', Auth::id())->get();
        
        return view('messages.appointments-edit', compact('appointment', 'users'));
    }
    
    public function update(Request $request, $id)
    {
        $appointment = Appointment::where(function ($query) {
            $query->where('user_id', Auth::id())
                  ->orWhere('participant_id', Auth::id());
        })->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'start_datetime' => 'required|date|after_or_equal:today',
            'duration' => 'required|integer|min:15|max:480',
            'type' => 'required|in:video,voice,in-person,phone',
            'notes' => 'nullable|string|max:1000',
            'reminder_minutes' => 'nullable|integer|min:0|max:10080'
        ]);
        
        $appointment->update($request->all());
        
        // Notify participant if not the user who updated
        if ($appointment->participant_id !== Auth::id()) {
            UserNotification::create([
                'user_id' => $appointment->participant_id,
                'title' => 'Appointment Updated',
                'message' => Auth::user()->name . ' updated the appointment: ' . $appointment->title,
                'type' => 'appointment',
                'data' => json_encode(['appointment_id' => $appointment->id]),
                'action_url' => '/messages/appointments/' . $appointment->id,
                'action_text' => 'View Appointment'
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function destroy($id)
    {
        $appointment = Appointment::where(function ($query) {
            $query->where('user_id', Auth::id())
                  ->orWhere('participant_id', Auth::id());
        })->findOrFail($id);
        
        $appointment->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function confirm($id)
    {
        $appointment = Appointment::where('participant_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $appointment->update(['status' => 'confirmed']);
        
        // Notify creator
        UserNotification::create([
            'user_id' => $appointment->user_id,
            'title' => 'Appointment Confirmed',
            'message' => Auth::user()->name . ' confirmed the appointment: ' . $appointment->title,
            'type' => 'appointment',
            'data' => json_encode(['appointment_id' => $appointment->id]),
            'action_url' => '/messages/appointments/' . $appointment->id,
            'action_text' => 'View Appointment'
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function cancel($id)
    {
        $appointment = Appointment::where(function ($query) {
            $query->where('user_id', Auth::id())
                  ->orWhere('participant_id', Auth::id());
        })->findOrFail($id);
        
        $appointment->update(['status' => 'cancelled']);
        
        // Notify other party
        $otherUserId = $appointment->user_id === Auth::id() ? $appointment->participant_id : $appointment->user_id;
        
        UserNotification::create([
            'user_id' => $otherUserId,
            'title' => 'Appointment Cancelled',
            'message' => Auth::user()->name . ' cancelled the appointment: ' . $appointment->title,
            'type' => 'appointment',
            'data' => json_encode(['appointment_id' => $appointment->id]),
            'action_url' => '/messages/appointments/' . $appointment->id,
            'action_text' => 'View Appointment'
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function reschedule(Request $request, $id)
    {
        $appointment = Appointment::where(function ($query) {
            $query->where('user_id', Auth::id())
                  ->orWhere('participant_id', Auth::id());
        })->findOrFail($id);
        
        $request->validate([
            'start_datetime' => 'required|date|after_or_equal:today'
        ]);
        
        $appointment->update([
            'start_datetime' => $request->start_datetime,
            'status' => 'pending' // Reset to pending when rescheduled
        ]);
        
        // Notify other party
        $otherUserId = $appointment->user_id === Auth::id() ? $appointment->participant_id : $appointment->user_id;
        
        UserNotification::create([
            'user_id' => $otherUserId,
            'title' => 'Appointment Rescheduled',
            'message' => Auth::user()->name . ' rescheduled the appointment: ' . $appointment->title,
            'type' => 'appointment',
            'data' => json_encode(['appointment_id' => $appointment->id]),
            'action_url' => '/messages/appointments/' . $appointment->id,
            'action_text' => 'View Appointment'
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function sendReminder($id)
    {
        $appointment = Appointment::where(function ($query) {
            $query->where('user_id', Auth::id())
                  ->orWhere('participant_id', Auth::id());
        })->findOrFail($id);
        
        // Send reminder to participant
        if ($appointment->participant_id !== Auth::id()) {
            UserNotification::create([
                'user_id' => $appointment->participant_id,
                'title' => 'Appointment Reminder',
                'message' => 'Reminder: You have an appointment "' . $appointment->title . '" on ' . $appointment->start_datetime->format('M j, Y') . ' at ' . $appointment->start_datetime->format('h:i A'),
                'type' => 'appointment',
                'data' => json_encode(['appointment_id' => $appointment->id]),
                'action_url' => '/messages/appointments/' . $appointment->id,
                'action_text' => 'View Appointment'
            ]);
        }
        
        // Send reminder to creator
        if ($appointment->user_id !== Auth::id()) {
            UserNotification::create([
                'user_id' => $appointment->user_id,
                'title' => 'Appointment Reminder',
                'message' => 'Reminder: You have an appointment "' . $appointment->title . '" on ' . $appointment->start_datetime->format('M j, Y') . ' at ' . $appointment->start_datetime->format('h:i A'),
                'type' => 'appointment',
                'data' => json_encode(['appointment_id' => $appointment->id]),
                'action_url' => '/messages/appointments/' . $appointment->id,
                'action_text' => 'View Appointment'
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function calendar(Request $request)
    {
        $user = Auth::user();
        
        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', now()->year);
        
        $appointments = Appointment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('participant_id', $user->id);
        })->whereMonth('start_datetime', $currentMonth)
          ->whereYear('start_datetime', $currentYear)
          ->where('status', '!=', 'cancelled')
          ->with(['user', 'participant'])
          ->orderBy('start_datetime', 'asc')
          ->get();
        
        $todayEvents = $appointments->filter(function ($appointment) {
            return $appointment->start_datetime->isToday();
        });
        
        $weekEvents = $appointments->filter(function ($appointment) {
            return $appointment->start_datetime->between(now()->startOfWeek(), now()->endOfWeek());
        });
        
        $stats = [
            'appointments' => $appointments->where('type', 'appointment')->count(),
            'meetings' => $appointments->where('type', 'video')->count() + $appointments->where('type', 'in-person')->count(),
            'calls' => $appointments->where('type', 'voice')->count() + $appointments->where('type', 'phone')->count()
        ];
        
        return view('messages.calendar', compact('appointments', 'todayEvents', 'weekEvents', 'stats', 'currentMonth', 'currentYear'));
    }
    
    public function export(Request $request)
    {
        $user = Auth::user();
        
        $appointments = Appointment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('participant_id', $user->id());
        })->with(['user', 'participant'])
          ->orderBy('start_datetime', 'asc')
          ->get();
        
        // Generate calendar file (ICS format)
        $content = "BEGIN:VCALENDAR\r\n";
        $content .= "VERSION:2.0\r\n";
        $content .= "PRODID:-//Real Estate Platform//Appointment Calendar//EN\r\n";
        
        foreach ($appointments as $appointment) {
            $start = $appointment->start_datetime;
            $end = $start->copy()->addMinutes($appointment->duration);
            
            $content .= "BEGIN:VEVENT\r\n";
            $content .= "UID:" . $appointment->id . "@realestate.com\r\n";
            $content .= "DTSTART:" . $start->format('Ymd\THis') . "\r\n";
            $content .= "DTEND:" . $end->format('Ymd\THis') . "\r\n";
            $content .= "SUMMARY:" . $appointment->title . "\r\n";
            $content .= "DESCRIPTION:" . $appointment->notes . "\r\n";
            $content .= "STATUS:" . strtoupper($appointment->status) . "\r\n";
            $content .= "END:VEVENT\r\n";
        }
        
        $content .= "END:VCALENDAR\r\n";
        
        return response($content)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="appointments.ics"');
    }
    
    private function getAppointmentsCount($user, $status = null, $date = null)
    {
        $query = Appointment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('participant_id', $user->id);
        });
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($date) {
            $query->whereDate('start_datetime', $date);
        }
        
        return $query->count();
    }
}
