<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadScore;
use Illuminate\Http\Request;

class LeadScoringController extends Controller
{
    public function index()
    {
        $scores = LeadScore::with(['lead', 'calculatedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('lead-scoring.index', compact('scores'));
    }
    
    public function create()
    {
        $leads = Lead::whereDoesntHave('scores')
            ->orWhereHas('scores', function($query) {
                $query->where('created_at', '<', now()->subDays(7));
            })
            ->get();
            
        return view('lead-scoring.create', compact('leads'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'score' => 'required|integer|min:0|max:100',
            'factors' => 'nullable|array',
            'factors.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $lead = Lead::findOrFail($request->lead_id);
        
        LeadScore::create([
            'lead_id' => $request->lead_id,
            'score' => $request->score,
            'factors' => $request->factors,
            'notes' => $request->notes,
            'calculated_by' => auth()->id(),
        ]);
        
        $lead->update(['score' => $request->score]);
        
        return redirect()->route('lead-scoring.index')
            ->with('success', 'تم تقييم العميل المحتمل بنجاح');
    }
    
    public function show(LeadScore $leadScore)
    {
        $leadScore->load(['lead.source', 'lead.status', 'lead.assignedUser', 'calculatedBy']);
        
        return view('lead-scoring.show', compact('leadScore'));
    }
    
    public function edit(LeadScore $leadScore)
    {
        return view('lead-scoring.edit', compact('leadScore'));
    }
    
    public function update(Request $request, LeadScore $leadScore)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'factors' => 'nullable|array',
            'factors.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $leadScore->update([
            'score' => $request->score,
            'factors' => $request->factors,
            'notes' => $request->notes,
        ]);
        
        $leadScore->lead->update(['score' => $request->score]);
        
        return redirect()->route('lead-scoring.show', $leadScore)
            ->with('success', 'تم تحديث تقييم العميل المحتمل بنجاح');
    }
    
    public function destroy(LeadScore $leadScore)
    {
        $lead = $leadScore->lead;
        $leadScore->delete();
        
        $latestScore = $lead->scores()->latest()->first();
        if ($latestScore) {
            $lead->update(['score' => $latestScore->score]);
        } else {
            $lead->update(['score' => 0]);
        }
        
        return redirect()->route('lead-scoring.index')
            ->with('success', 'تم حذف تقييم العميل المحتمل بنجاح');
    }
    
    public function bulkScore(Request $request)
    {
        $request->validate([
            'leads' => 'required|array',
            'leads.*' => 'exists:leads,id',
            'scoring_rules' => 'required|array',
        ]);
        
        $scores = [];
        foreach ($request->leads as $leadId) {
            $lead = Lead::findOrFail($leadId);
            $score = $this->calculateScore($lead, $request->scoring_rules);
            
            LeadScore::create([
                'lead_id' => $leadId,
                'score' => $score['total'],
                'factors' => $score['factors'],
                'calculated_by' => auth()->id(),
            ]);
            
            $lead->update(['score' => $score['total']]);
            $scores[] = $score['total'];
        }
        
        return redirect()->back()
            ->with('success', 'تم تقييم ' . count($request->leads) . ' عميل محتمل بنجاح');
    }
    
    public function scoringRules()
    {
        return view('lead-scoring.rules');
    }
    
    public function updateScoringRules(Request $request)
    {
        $request->validate([
            'rules' => 'required|array',
            'rules.*.factor' => 'required|string|max:255',
            'rules.*.weight' => 'required|integer|min:0|max:100',
            'rules.*.conditions' => 'nullable|array',
        ]);
        
        cache()->put('lead_scoring_rules', $request->rules, now()->addDays(30));
        
        return redirect()->route('lead-scoring.rules')
            ->with('success', 'تم تحديث قواعد التقييم بنجاح');
    }
    
    private function calculateScore(Lead $lead, array $rules)
    {
        $score = 0;
        $factors = [];
        
        foreach ($rules as $rule) {
            $ruleScore = 0;
            $conditions = $rule['conditions'] ?? [];
            
            if ($this->matchesConditions($lead, $conditions)) {
                $ruleScore = $rule['weight'];
                $factors[] = $rule['factor'] . ' (+' . $rule['weight'] . ')';
            }
            
            $score += $ruleScore;
        }
        
        return [
            'total' => min($score, 100),
            'factors' => $factors,
        ];
    }
    
    private function matchesConditions(Lead $lead, array $conditions)
    {
        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? 'equals';
            $value = $condition['value'] ?? null;
            
            if (!$field || !$lead->{$field}) {
                continue;
            }
            
            switch ($operator) {
                case 'equals':
                    if ($lead->{$field} != $value) {
                        return false;
                    }
                    break;
                case 'contains':
                    if (strpos($lead->{$field}, $value) === false) {
                        return false;
                    }
                    break;
                case 'greater_than':
                    if ($lead->{$field} <= $value) {
                        return false;
                    }
                    break;
                case 'less_than':
                    if ($lead->{$field} >= $value) {
                        return false;
                    }
                    break;
            }
        }
        
        return true;
    }
}
