<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadNurturingController extends Controller
{
    public function index()
    {
        $leads = Lead::with(['source', 'status', 'assignedUser'])
            ->where('status_id', '!=', 6) // Not converted
            ->where('created_at', '>', now()->subDays(90))
            ->orderBy('last_contact_at', 'asc')
            ->paginate(20);
            
        return view('lead-nurturing.index', compact('leads'));
    }
    
    public function create()
    {
        return view('lead-nurturing.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'nurturing_type' => 'required|in:email,call,sms,meeting',
            'subject' => 'required_if:nurturing_type,email|string|max:255',
            'message' => 'required|string|max:2000',
            'scheduled_at' => 'nullable|date|after:now',
        ]);
        
        $lead = Lead::findOrFail($request->lead_id);
        
        if ($request->scheduled_at) {
            // Schedule nurturing activity
            $this->scheduleNurturing($lead, $request->all());
            
            return redirect()->back()
                ->with('success', 'تم جدولة نشاط رعاية العميل بنجاح');
        } else {
            // Execute nurturing immediately
            $this->executeNurturing($lead, $request->all());
            
            return redirect()->back()
                ->with('success', 'تم تنفيذ نشاط رعاية العميل بنجاح');
        }
    }
    
    public function show(Lead $lead)
    {
        $lead->load(['activities' => function($query) {
            $query->where('type', 'nurturing')
                  ->with('user')
                  ->orderBy('created_at', 'desc');
        }]);
        
        return view('lead-nurturing.show', compact('lead'));
    }
    
    public function bulkNurturing(Request $request)
    {
        $request->validate([
            'leads' => 'required|array',
            'leads.*' => 'exists:leads,id',
            'nurturing_type' => 'required|in:email,call,sms,meeting',
            'subject' => 'required_if:nurturing_type,email|string|max:255',
            'message' => 'required|string|max:2000',
            'scheduled_at' => 'nullable|date|after:now',
        ]);
        
        $count = 0;
        foreach ($request->leads as $leadId) {
            $lead = Lead::findOrFail($leadId);
            
            if ($request->scheduled_at) {
                $this->scheduleNurturing($lead, $request->all());
            } else {
                $this->executeNurturing($lead, $request->all());
            }
            
            $count++;
        }
        
        return redirect()->back()
            ->with('success', 'تم تنفيذ رعاية ' . $count . ' عميل بنجاح');
    }
    
    public function automation()
    {
        return view('lead-nurturing.automation');
    }
    
    public function createAutomation()
    {
        $statuses = \App\Models\LeadStatus::all();
        $sources = \App\Models\LeadSource::all();
        
        return view('lead-nurturing.create-automation', compact('statuses', 'sources'));
    }
    
    public function storeAutomation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'triggers' => 'required|array',
            'triggers.*.type' => 'required|in:status_change,time_based,lead_score',
            'triggers.*.conditions' => 'required|array',
            'actions' => 'required|array',
            'actions.*.type' => 'required|in:send_email,create_task,update_score,assign_lead',
            'actions.*.parameters' => 'required|array',
            'is_active' => 'boolean',
        ]);
        
        // Store automation rules in database or cache
        $automation = [
            'name' => $request->name,
            'description' => $request->description,
            'triggers' => $request->triggers,
            'actions' => $request->actions,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ];
        
        // This would typically be stored in a database table
        cache()->put('lead_automation_' . time(), $automation, now()->addYear());
        
        return redirect()->route('lead-nurturing.automation')
            ->with('success', 'تم إنشاء أتمتة رعاية العملاء بنجاح');
    }
    
    public function followUpReminders()
    {
        $reminders = Lead::with(['source', 'status', 'assignedUser'])
            ->where(function($query) {
                $query->where('last_contact_at', '<', now()->subDays(7))
                      ->orWhereNull('last_contact_at');
            })
            ->where('status_id', '!=', 6) // Not converted
            ->where('created_at', '>', now()->subDays(60))
            ->orderBy('last_contact_at', 'asc')
            ->get();
            
        return view('lead-nurturing.reminders', compact('reminders'));
    }
    
    private function executeNurturing(Lead $lead, array $data)
    {
        switch ($data['nurturing_type']) {
            case 'email':
                $this->sendEmail($lead, $data);
                break;
            case 'call':
                $this->scheduleCall($lead, $data);
                break;
            case 'sms':
                $this->sendSMS($lead, $data);
                break;
            case 'meeting':
                $this->scheduleMeeting($lead, $data);
                break;
        }
        
        // Update last contact
        $lead->update(['last_contact_at' => now()]);
        
        // Create activity record
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'nurturing',
            'description' => 'تم تنفيذ نشاط رعاية: ' . $data['nurturing_type'],
            'user_id' => auth()->id(),
        ]);
    }
    
    private function scheduleNurturing(Lead $lead, array $data)
    {
        // This would typically store in a scheduled tasks table
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'scheduled_nurturing',
            'description' => 'تم جدولة نشاط رعاية: ' . $data['nurturing_type'],
            'scheduled_at' => $data['scheduled_at'],
            'user_id' => auth()->id(),
        ]);
    }
    
    private function sendEmail(Lead $lead, array $data)
    {
        // Implement email sending logic
        // Mail::to($lead->email)->send(new LeadNurturingEmail($data['subject'], $data['message']));
    }
    
    private function scheduleCall(Lead $lead, array $data)
    {
        // Implement call scheduling logic
    }
    
    private function sendSMS(Lead $lead, array $data)
    {
        // Implement SMS sending logic
    }
    
    private function scheduleMeeting(Lead $lead, array $data)
    {
        // Implement meeting scheduling logic
    }
}
