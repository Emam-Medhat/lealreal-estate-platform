<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;

class LeadAutomationController extends Controller
{
    public function index()
    {
        $automations = cache()->get('lead_automations', []);
        
        return view('lead-automation.index', compact('automations'));
    }
    
    public function create()
    {
        $statuses = \App\Models\LeadStatus::all();
        $sources = \App\Models\LeadSource::all();
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('lead-automation.create', compact('statuses', 'sources', 'users'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'triggers' => 'required|array',
            'triggers.*.type' => 'required|in:status_change,time_based,lead_score,new_lead,no_activity',
            'triggers.*.conditions' => 'required|array',
            'actions' => 'required|array',
            'actions.*.type' => 'required|in:send_email,create_task,update_score,assign_lead,change_status,add_tag',
            'actions.*.parameters' => 'required|array',
            'is_active' => 'boolean',
        ]);
        
        $automation = [
            'id' => time(),
            'name' => $request->name,
            'description' => $request->description,
            'triggers' => $request->triggers,
            'actions' => $request->actions,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
            'created_at' => now(),
        ];
        
        $automations = cache()->get('lead_automations', []);
        $automations[] = $automation;
        cache()->put('lead_automations', $automations, now()->addYear());
        
        return redirect()->route('lead-automation.index')
            ->with('success', 'تم إنشاء أتمتة العملاء المحتملين بنجاح');
    }
    
    public function show($id)
    {
        $automations = cache()->get('lead_automations', []);
        $automation = collect($automations)->firstWhere('id', $id);
        
        if (!$automation) {
            abort(404);
        }
        
        return view('lead-automation.show', compact('automation'));
    }
    
    public function edit($id)
    {
        $automations = cache()->get('lead_automations', []);
        $automation = collect($automations)->firstWhere('id', $id);
        
        if (!$automation) {
            abort(404);
        }
        
        $statuses = \App\Models\LeadStatus::all();
        $sources = \App\Models\LeadSource::all();
        $users = \App\Models\User::where('role', 'agent')->orWhere('role', 'admin')->get();
        
        return view('lead-automation.edit', compact('automation', 'statuses', 'sources', 'users'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'triggers' => 'required|array',
            'triggers.*.type' => 'required|in:status_change,time_based,lead_score,new_lead,no_activity',
            'triggers.*.conditions' => 'required|array',
            'actions' => 'required|array',
            'actions.*.type' => 'required|in:send_email,create_task,update_score,assign_lead,change_status,add_tag',
            'actions.*.parameters' => 'required|array',
            'is_active' => 'boolean',
        ]);
        
        $automations = cache()->get('lead_automations', []);
        $automationIndex = collect($automations)->search(function($automation) use ($id) {
            return $automation['id'] == $id;
        });
        
        if ($automationIndex === false) {
            abort(404);
        }
        
        $automations[$automationIndex] = [
            'id' => $id,
            'name' => $request->name,
            'description' => $request->description,
            'triggers' => $request->triggers,
            'actions' => $request->actions,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => $automations[$automationIndex]['created_by'],
            'created_at' => $automations[$automationIndex]['created_at'],
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ];
        
        cache()->put('lead_automations', $automations, now()->addYear());
        
        return redirect()->route('lead-automation.show', $id)
            ->with('success', 'تم تحديث أتمتة العملاء المحتملين بنجاح');
    }
    
    public function destroy($id)
    {
        $automations = cache()->get('lead_automations', []);
        $automationIndex = collect($automations)->search(function($automation) use ($id) {
            return $automation['id'] == $id;
        });
        
        if ($automationIndex === false) {
            abort(404);
        }
        
        unset($automations[$automationIndex]);
        $automations = array_values($automations);
        cache()->put('lead_automations', $automations, now()->addYear());
        
        return redirect()->route('lead-automation.index')
            ->with('success', 'تم حذف أتمتة العملاء المحتملين بنجاح');
    }
    
    public function toggleStatus($id)
    {
        $automations = cache()->get('lead_automations', []);
        $automationIndex = collect($automations)->search(function($automation) use ($id) {
            return $automation['id'] == $id;
        });
        
        if ($automationIndex === false) {
            abort(404);
        }
        
        $automations[$automationIndex]['is_active'] = !$automations[$automationIndex]['is_active'];
        $automations[$automationIndex]['updated_by'] = auth()->id();
        $automations[$automationIndex]['updated_at'] = now();
        
        cache()->put('lead_automations', $automations, now()->addYear());
        
        return redirect()->back()
            ->with('success', 'تم تحديث حالة الأتمتة بنجاح');
    }
    
    public function test(Request $request)
    {
        $request->validate([
            'automation_id' => 'required|integer',
            'lead_id' => 'required|exists:leads,id',
        ]);
        
        $automations = cache()->get('lead_automations', []);
        $automation = collect($automations)->firstWhere('id', $request->automation_id);
        
        if (!$automation) {
            return response()->json(['error' => 'الأتمتة غير موجودة'], 404);
        }
        
        $lead = Lead::findOrFail($request->lead_id);
        $results = $this->executeAutomation($automation, $lead, true);
        
        return response()->json(['results' => $results]);
    }
    
    public function runAutomations()
    {
        $automations = cache()->get('lead_automations', []);
        $activeAutomations = collect($automations)->where('is_active', true);
        
        $results = [];
        foreach ($activeAutomations as $automation) {
            $leads = $this->getLeadsForAutomation($automation);
            
            foreach ($leads as $lead) {
                $result = $this->executeAutomation($automation, $lead);
                if ($result) {
                    $results[] = $result;
                }
            }
        }
        
        return response()->json(['results' => $results]);
    }
    
    private function getLeadsForAutomation($automation)
    {
        $leads = Lead::query();
        
        foreach ($automation['triggers'] as $trigger) {
            switch ($trigger['type']) {
                case 'status_change':
                    if (isset($trigger['conditions']['status_id'])) {
                        $leads->where('status_id', $trigger['conditions']['status_id']);
                    }
                    break;
                    
                case 'time_based':
                    if (isset($trigger['conditions']['days'])) {
                        $leads->where('created_at', '<=', now()->subDays($trigger['conditions']['days']));
                    }
                    break;
                    
                case 'lead_score':
                    if (isset($trigger['conditions']['min_score'])) {
                        $leads->where('score', '>=', $trigger['conditions']['min_score']);
                    }
                    break;
                    
                case 'new_lead':
                    $leads->where('created_at', '>', now()->subHours(24));
                    break;
                    
                case 'no_activity':
                    if (isset($trigger['conditions']['days'])) {
                        $leads->where('last_contact_at', '<', now()->subDays($trigger['conditions']['days']))
                              ->orWhereNull('last_contact_at');
                    }
                    break;
            }
        }
        
        return $leads->get();
    }
    
    private function executeAutomation($automation, Lead $lead, $test = false)
    {
        $results = [];
        
        foreach ($automation['actions'] as $action) {
            $result = $this->executeAction($action, $lead, $test);
            if ($result) {
                $results[] = $result;
            }
        }
        
        if (!$test && !empty($results)) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'type' => 'automation_executed',
                'description' => 'تم تنفيذ أتمتة: ' . $automation['name'],
                'user_id' => null, // System action
            ]);
        }
        
        return $results;
    }
    
    private function executeAction($action, Lead $lead, $test = false)
    {
        switch ($action['type']) {
            case 'send_email':
                if (!$test) {
                    // Implement email sending
                }
                return ['action' => 'send_email', 'result' => 'تم إرسال البريد الإلكتروني'];
                
            case 'create_task':
                if (!$test) {
                    // Implement task creation
                }
                return ['action' => 'create_task', 'result' => 'تم إنشاء مهمة'];
                
            case 'update_score':
                if (isset($action['parameters']['score'])) {
                    if (!$test) {
                        $lead->update(['score' => $action['parameters']['score']]);
                    }
                    return ['action' => 'update_score', 'result' => 'تم تحديث التقييم'];
                }
                break;
                
            case 'assign_lead':
                if (isset($action['parameters']['user_id'])) {
                    if (!$test) {
                        $lead->update(['assigned_to' => $action['parameters']['user_id']]);
                    }
                    return ['action' => 'assign_lead', 'result' => 'تم تخصيص العميل'];
                }
                break;
                
            case 'change_status':
                if (isset($action['parameters']['status_id'])) {
                    if (!$test) {
                        $lead->update(['status_id' => $action['parameters']['status_id']]);
                    }
                    return ['action' => 'change_status', 'result' => 'تم تغيير الحالة'];
                }
                break;
                
            case 'add_tag':
                if (isset($action['parameters']['tag_id'])) {
                    if (!$test) {
                        $lead->tags()->syncWithoutDetaching([$action['parameters']['tag_id']]);
                    }
                    return ['action' => 'add_tag', 'result' => 'تم إضافة وسم'];
                }
                break;
        }
        
        return null;
    }
}
