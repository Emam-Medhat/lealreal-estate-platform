<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractTerm;
use App\Models\ContractParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContractNegotiationController extends Controller
{
    public function index()
    {
        $negotiations = Contract::with(['parties', 'terms'])
            ->where('status', 'negotiation')
            ->filter(request(['search', 'party', 'date_range']))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('contracts.negotiations.index', compact('negotiations'));
    }
    
    public function create(Contract $contract)
    {
        $contract->load(['parties', 'terms' => function($query) {
            $query->orderBy('order');
        }]);
        
        // Check if contract can be negotiated
        if (!in_array($contract->status, ['draft', 'sent', 'negotiation'])) {
            return back()->with('error', 'العقد ليس في حالة تسمح بالتفاوض');
        }
        
        return view('contracts.negotiations.create', compact('contract'));
    }
    
    public function store(Request $request, Contract $contract)
    {
        $request->validate([
            'negotiation_notes' => 'required|string',
            'proposed_changes' => 'required|array',
            'proposed_changes.*.term_id' => 'required|exists:contract_terms,id',
            'proposed_changes.*.type' => 'required|in:modify,add,remove',
            'proposed_changes.*.content' => 'required_if:proposed_changes.*.type,modify,add|string',
            'proposed_changes.*.reason' => 'required|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update contract status
            $contract->update([
                'status' => 'negotiation',
                'negotiation_started_at' => now(),
            ]);
            
            // Store negotiation data
            $negotiationData = [
                'notes' => $request->negotiation_notes,
                'proposed_changes' => $request->proposed_changes,
                'negotiated_by' => auth()->id(),
                'negotiated_at' => now(),
            ];
            
            // Add to contract metadata
            $metadata = $contract->metadata ?? [];
            $metadata['negotiations'] = $metadata['negotiations'] ?? [];
            $metadata['negotiations'][] = $negotiationData;
            
            $contract->update(['metadata' => $metadata]);
            
            // Process proposed changes
            foreach ($request->proposed_changes as $change) {
                $term = ContractTerm::findOrFail($change['term_id']);
                
                switch ($change['type']) {
                    case 'modify':
                        // Store original content for history
                        $term->update([
                            'original_content' => $term->content,
                            'proposed_content' => $change['content'],
                            'change_reason' => $change['reason'],
                            'change_status' => 'proposed',
                            'changed_by' => auth()->id(),
                            'changed_at' => now(),
                        ]);
                        break;
                        
                    case 'add':
                        // Create new term as proposal
                        ContractTerm::create([
                            'contract_id' => $contract->id,
                            'title' => $change['title'] ?? 'بند جديد',
                            'content' => $change['content'],
                            'order' => $term->order + 1,
                            'is_proposed' => true,
                            'proposed_by' => auth()->id(),
                            'proposed_at' => now(),
                        ]);
                        break;
                        
                    case 'remove':
                        // Mark term for removal
                        $term->update([
                            'proposed_removal' => true,
                            'removal_reason' => $change['reason'],
                            'proposed_by' => auth()->id(),
                            'proposed_at' => now(),
                        ]);
                        break;
                }
            }
            
            // Notify other parties
            $this->notifyParties($contract, 'negotiation_started');
            
            DB::commit();
            
            return redirect()->route('contracts.negotiations.show', $contract)
                ->with('success', 'تم بدء التفاوض على العقد بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء بدء التفاوض: ' . $e->getMessage());
        }
    }
    
    public function show(Contract $contract)
    {
        $contract->load([
            'parties',
            'terms' => function($query) {
                $query->orderBy('order');
            }
        ]);
        
        // Get negotiation history
        $negotiations = $contract->metadata['negotiations'] ?? [];
        
        return view('contracts.negotiations.show', compact('contract', 'negotiations'));
    }
    
    public function respond(Request $request, Contract $contract)
    {
        $request->validate([
            'response_type' => 'required|in:accept,reject,counter',
            'response_notes' => 'required|string',
            'term_responses' => 'required|array',
            'term_responses.*.term_id' => 'required|exists:contract_terms,id',
            'term_responses.*.action' => 'required|in:accept,reject,modify',
            'term_responses.*.content' => 'required_if:term_responses.*.action,modify|string',
            'term_responses.*.notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Process term responses
            foreach ($request->term_responses as $response) {
                $term = ContractTerm::findOrFail($response['term_id']);
                
                switch ($response['action']) {
                    case 'accept':
                        if ($term->proposed_content) {
                            $term->update([
                                'content' => $term->proposed_content,
                                'proposed_content' => null,
                                'change_status' => 'accepted',
                                'accepted_by' => auth()->id(),
                                'accepted_at' => now(),
                            ]);
                        }
                        break;
                        
                    case 'reject':
                        $term->update([
                            'proposed_content' => null,
                            'change_status' => 'rejected',
                            'rejected_by' => auth()->id(),
                            'rejected_at' => now(),
                            'rejection_reason' => $response['notes'],
                        ]);
                        break;
                        
                    case 'modify':
                        $term->update([
                            'proposed_content' => $response['content'],
                            'change_status' => 'counter_proposed',
                            'counter_proposed_by' => auth()->id(),
                            'counter_proposed_at' => now(),
                            'counter_notes' => $response['notes'],
                        ]);
                        break;
                }
            }
            
            // Store response in metadata
            $responseData = [
                'type' => $request->response_type,
                'notes' => $request->response_notes,
                'term_responses' => $request->term_responses,
                'responded_by' => auth()->id(),
                'responded_at' => now(),
            ];
            
            $metadata = $contract->metadata ?? [];
            $metadata['negotiations'] = $metadata['negotiations'] ?? [];
            $metadata['negotiations'][] = $responseData;
            
            $contract->update(['metadata' => $metadata]);
            
            // Update contract status based on response
            if ($request->response_type === 'accept') {
                // Check if all changes are accepted
                $pendingChanges = $contract->terms()
                    ->whereIn('change_status', ['proposed', 'counter_proposed'])
                    ->count();
                    
                if ($pendingChanges === 0) {
                    $contract->update([
                        'status' => 'ready_for_signature',
                        'negotiation_completed_at' => now(),
                    ]);
                    
                    // Apply all accepted changes
                    $this->applyAcceptedChanges($contract);
                }
            } elseif ($request->response_type === 'reject') {
                $contract->update([
                    'status' => 'rejected',
                    'negotiation_completed_at' => now(),
                ]);
            }
            
            // Notify parties
            $this->notifyParties($contract, 'negotiation_response');
            
            DB::commit();
            
            return redirect()->route('contracts.negotiations.show', $contract)
                ->with('success', 'تم إرسال الرد بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إرسال الرد: ' . $e->getMessage());
        }
    }
    
    public function finalize(Request $request, Contract $contract)
    {
        $request->validate([
            'final_notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Apply all accepted changes
            $this->applyAcceptedChanges($contract);
            
            // Update contract status
            $contract->update([
                'status' => 'ready_for_signature',
                'negotiation_completed_at' => now(),
                'finalization_notes' => $request->final_notes,
                'finalized_by' => auth()->id(),
            ]);
            
            // Clear negotiation data
            $metadata = $contract->metadata ?? [];
            unset($metadata['negotiations']);
            $contract->update(['metadata' => $metadata]);
            
            // Reset all term change statuses
            $contract->terms()->update([
                'change_status' => null,
                'proposed_content' => null,
                'original_content' => null,
                'change_reason' => null,
                'is_proposed' => false,
                'proposed_removal' => false,
                'removal_reason' => null,
            ]);
            
            // Notify parties
            $this->notifyParties($contract, 'negotiation_completed');
            
            DB::commit();
            
            return redirect()->route('contracts.show', $contract)
                ->with('success', 'تم إنهاء التفاوض على العقد بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنهاء التفاوض: ' . $e->getMessage());
        }
    }
    
    public function cancel(Contract $contract)
    {
        DB::beginTransaction();
        
        try {
            // Reset contract status
            $contract->update([
                'status' => 'draft',
                'negotiation_started_at' => null,
            ]);
            
            // Reset all term changes
            $contract->terms()->update([
                'change_status' => null,
                'proposed_content' => null,
                'original_content' => null,
                'change_reason' => null,
                'is_proposed' => false,
                'proposed_removal' => false,
                'removal_reason' => null,
            ]);
            
            // Remove proposed terms
            $contract->terms()->where('is_proposed', true)->delete();
            
            // Clear negotiation metadata
            $metadata = $contract->metadata ?? [];
            unset($metadata['negotiations']);
            $contract->update(['metadata' => $metadata]);
            
            DB::commit();
            
            return redirect()->route('contracts.show', $contract)
                ->with('success', 'تم إلغاء التفاوض بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->with('error', 'حدث خطأ أثناء إلغاء التفاوض');
        }
    }
    
    public function history(Contract $contract)
    {
        $negotiations = $contract->metadata['negotiations'] ?? [];
        
        return view('contracts.negotiations.history', compact('contract', 'negotiations'));
    }
    
    public function compare(Contract $contract)
    {
        $originalTerms = $contract->terms()
            ->whereNull('change_status')
            ->orWhere('change_status', '!=', 'proposed')
            ->get();
            
        $proposedTerms = $contract->terms()
            ->whereNotNull('change_status')
            ->get();
            
        return view('contracts.negotiations.compare', compact('contract', 'originalTerms', 'proposedTerms'));
    }
    
    private function applyAcceptedChanges(Contract $contract)
    {
        $terms = $contract->terms;
        
        foreach ($terms as $term) {
            if ($term->proposed_content && $term->change_status === 'accepted') {
                $term->update([
                    'content' => $term->proposed_content,
                    'proposed_content' => null,
                    'change_status' => null,
                ]);
            }
            
            if ($term->proposed_removal && $term->change_status === 'accepted') {
                $term->delete();
            }
            
            if ($term->is_proposed && $term->change_status === 'accepted') {
                $term->update([
                    'is_proposed' => false,
                    'change_status' => null,
                ]);
            }
        }
        
        // Reorder terms
        $this->reorderTerms($contract);
    }
    
    private function reorderTerms(Contract $contract)
    {
        $terms = $contract->terms()->orderBy('order')->get();
        
        foreach ($terms as $index => $term) {
            $term->update(['order' => $index + 1]);
        }
    }
    
    private function notifyParties(Contract $contract, string $event)
    {
        $parties = $contract->parties;
        
        foreach ($parties as $party) {
            // Send notification based on event
            $message = $this->getNotificationMessage($event, $contract);
            
            // Here you would implement your notification system
            // Notification::send($party->user, new ContractNegotiationNotification($contract, $message));
        }
    }
    
    private function getNotificationMessage(string $event, Contract $contract): string
    {
        $messages = [
            'negotiation_started' => 'تم بدء التفاوض على العقد: ' . $contract->title,
            'negotiation_response' => 'تم تلقي رود على التفاوض للعقد: ' . $contract->title,
            'negotiation_completed' => 'تم إنهاء التفاوض على العقد: ' . $contract->title,
        ];
        
        return $messages[$event] ?? 'حدث تحديث على العقد: ' . $contract->title;
    }
}
