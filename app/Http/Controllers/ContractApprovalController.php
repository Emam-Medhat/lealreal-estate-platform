<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractApproval;
use App\Models\ContractParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContractApprovalController extends Controller
{
    public function index()
    {
        $approvals = ContractApproval::with(['contract', 'approver', 'contract.parties'])
            ->filter(request(['status', 'approver', 'date_range']))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('contracts.approvals.index', compact('approvals'));
    }
    
    public function create(Contract $contract)
    {
        $contract->load(['parties', 'terms']);
        
        // Check if contract needs approval
        if (!in_array($contract->status, ['ready_for_approval', 'negotiation'])) {
            return back()->with('error', 'العقد ليس في حالة تسمح بطلب الموافقة');
        }
        
        $users = \App\Models\User::where('role', 'manager')
            ->orWhere('role', 'admin')
            ->orWhere('role', 'legal')
            ->get();
            
        return view('contracts.approvals.create', compact('contract', 'users'));
    }
    
    public function store(Request $request, Contract $contract)
    {
        $request->validate([
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'exists:users,id',
            'approval_notes' => 'required|string',
            'approval_type' => 'required|in:sequential,parallel',
            'deadline' => 'nullable|date|after:today',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update contract status
            $contract->update([
                'status' => 'pending_approval',
                'approval_started_at' => now(),
                'approval_deadline' => $request->deadline,
            ]);
            
            // Create approval requests
            $approvalOrder = 1;
            foreach ($request->approvers as $approverId) {
                $approval = ContractApproval::create([
                    'contract_id' => $contract->id,
                    'approver_id' => $approverId,
                    'approval_type' => $request->approval_type,
                    'approval_order' => $approvalOrder++,
                    'status' => 'pending',
                    'requested_by' => auth()->id(),
                    'requested_at' => now(),
                    'deadline' => $request->deadline,
                    'notes' => $request->approval_notes,
                ]);
                
                // Send notification to approver
                $this->sendApprovalNotification($approval);
            }
            
            DB::commit();
            
            return redirect()->route('contracts.approvals.show', $contract)
                ->with('success', 'تم إرسال طلب الموافقة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إرسال طلب الموافقة: ' . $e->getMessage());
        }
    }
    
    public function show(Contract $contract)
    {
        $contract->load([
            'approvals' => function($query) {
                $query->with(['approver'])->orderBy('approval_order');
            },
            'parties',
            'terms'
        ]);
        
        return view('contracts.approvals.show', compact('contract'));
    }
    
    public function approve(Request $request, ContractApproval $approval)
    {
        // Check if user can approve
        if ($approval->approver_id !== auth()->id()) {
            return back()->with('error', 'غير مصرح لك بالموافقة على هذا العقد');
        }
        
        // Check if approval is pending
        if ($approval->status !== 'pending') {
            return back()->with('error', 'هذا الطلب تمت معالجته بالفعل');
        }
        
        $request->validate([
            'decision' => 'required|in:approve,reject,request_changes',
            'comments' => 'required|string',
            'requested_changes' => 'nullable|array',
            'requested_changes.*.term_id' => 'required|exists:contract_terms,id',
            'requested_changes.*.change' => 'required|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $approval->update([
                'status' => $request->decision === 'approve' ? 'approved' : 
                           ($request->decision === 'reject' ? 'rejected' : 'changes_requested'),
                'decision_at' => now(),
                'comments' => $request->comments,
                'requested_changes' => $request->requested_changes ?? [],
            ]);
            
            // Handle different decisions
            switch ($request->decision) {
                case 'approve':
                    $this->handleApproval($approval->contract);
                    break;
                    
                case 'reject':
                    $this->handleRejection($approval->contract, $approval);
                    break;
                    
                case 'request_changes':
                    $this->handleChangesRequest($approval->contract, $approval);
                    break;
            }
            
            // Notify relevant parties
            $this->notifyApprovalResult($approval);
            
            DB::commit();
            
            return redirect()->route('contracts.approvals.show', $approval->contract)
                ->with('success', 'تم تسجيل قرارك بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تسجيل القرار: ' . $e->getMessage());
        }
    }
    
    public function delegate(Request $request, ContractApproval $approval)
    {
        // Check if user can delegate
        if ($approval->approver_id !== auth()->id()) {
            return back()->with('error', 'غير مصرح لك بتفويض هذا الطلب');
        }
        
        $request->validate([
            'delegate_to' => 'required|exists:users,id',
            'delegation_reason' => 'required|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $approval->update([
                'delegated_to' => $request->delegate_to,
                'delegated_by' => auth()->id(),
                'delegated_at' => now(),
                'delegation_reason' => $request->delegation_reason,
            ]);
            
            // Create new approval for delegate
            $newApproval = ContractApproval::create([
                'contract_id' => $approval->contract_id,
                'approver_id' => $request->delegate_to,
                'approval_type' => $approval->approval_type,
                'approval_order' => $approval->approval_order,
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'requested_at' => now(),
                'deadline' => $approval->deadline,
                'notes' => 'تفويض من: ' . auth()->user()->name . '. السبب: ' . $request->delegation_reason,
                'parent_approval_id' => $approval->id,
            ]);
            
            // Send notification to delegate
            $this->sendApprovalNotification($newApproval);
            
            DB::commit();
            
            return back()->with('success', 'تم تفويض الطلب بنجاح');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء التفويض: ' . $e->getMessage());
        }
    }
    
    public function remind(ContractApproval $approval)
    {
        if ($approval->status !== 'pending') {
            return back()->with('error', 'هذا الطلب تمت معالجته بالفعل');
        }
        
        // Send reminder notification
        $this->sendReminderNotification($approval);
        
        return back()->with('success', 'تم إرسال تذكير بنجاح');
    }
    
    public function escalate(Request $request, ContractApproval $approval)
    {
        $request->validate([
            'escalation_reason' => 'required|string|max:500',
            'escalate_to' => 'required|exists:users,id',
        ]);
        
        DB::beginTransaction();
        
        try {
            $approval->update([
                'escalated_to' => $request->escalate_to,
                'escalated_by' => auth()->id(),
                'escalated_at' => now(),
                'escalation_reason' => $request->escalation_reason,
            ]);
            
            // Create escalation approval
            $escalationApproval = ContractApproval::create([
                'contract_id' => $approval->contract_id,
                'approver_id' => $request->escalate_to,
                'approval_type' => 'escalation',
                'approval_order' => 999, // High priority
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'requested_at' => now(),
                'notes' => 'تصعيد للمراجعة. السبب: ' . $request->escalation_reason,
                'parent_approval_id' => $approval->id,
            ]);
            
            // Send escalation notification
            $this->sendEscalationNotification($escalationApproval);
            
            DB::commit();
            
            return back()->with('success', 'تم تصعيد الطلب بنجاح');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء التصعيد: ' . $e->getMessage());
        }
    }
    
    public function history(Contract $contract)
    {
        $approvals = $contract->approvals()
            ->with(['approver', 'delegatedTo'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('contracts.approvals.history', compact('contract', 'approvals'));
    }
    
    public function dashboard()
    {
        $stats = [
            'pending_approvals' => ContractApproval::where('status', 'pending')
                ->where('approver_id', auth()->id())
                ->count(),
            'overdue_approvals' => ContractApproval::where('status', 'pending')
                ->where('approver_id', auth()->id())
                ->where('deadline', '<', now())
                ->count(),
            'approved_today' => ContractApproval::where('status', 'approved')
                ->where('approver_id', auth()->id())
                ->whereDate('decision_at', today())
                ->count(),
        ];
        
        $myApprovals = ContractApproval::with(['contract', 'contract.parties'])
            ->where('approver_id', auth()->id())
            ->where('status', 'pending')
            ->orderBy('deadline', 'asc')
            ->take(10)
            ->get();
            
        $recentApprovals = ContractApproval::with(['contract', 'approver'])
            ->where('approver_id', auth()->id())
            ->orderBy('decision_at', 'desc')
            ->take(10)
            ->get();
            
        return view('contracts.approvals.dashboard', compact('stats', 'myApprovals', 'recentApprovals'));
    }
    
    private function handleApproval(Contract $contract)
    {
        $approvals = $contract->approvals;
        $approvalType = $approvals->first()->approval_type;
        
        if ($approvalType === 'sequential') {
            // Check if this was the last approval
            $currentOrder = $approvals->where('status', 'approved')->max('approval_order');
            $nextApproval = $approvals->where('approval_order', $currentOrder + 1)->first();
            
            if ($nextApproval) {
                // Activate next approval
                $nextApproval->update(['status' => 'pending']);
                $this->sendApprovalNotification($nextApproval);
            } else {
                // All approvals completed
                $contract->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);
            }
        } else {
            // Parallel approval - check if all are approved
            $pendingApprovals = $approvals->where('status', 'pending')->count();
            
            if ($pendingApprovals === 0) {
                $contract->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);
            }
        }
    }
    
    private function handleRejection(Contract $contract, ContractApproval $approval)
    {
        $contract->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $approval->comments,
        ]);
    }
    
    private function handleChangesRequest(Contract $contract, ContractApproval $approval)
    {
        $contract->update([
            'status' => 'changes_requested',
            'changes_requested_at' => now(),
        ]);
        
        // Apply requested changes to terms
        foreach ($approval->requested_changes as $change) {
            $term = \App\Models\ContractTerm::findOrFail($change['term_id']);
            $term->update([
                'proposed_content' => $change['change'],
                'change_status' => 'approval_requested',
                'change_requested_by' => $approval->approver_id,
                'change_requested_at' => now(),
            ]);
        }
    }
    
    private function sendApprovalNotification(ContractApproval $approval)
    {
        // Implement notification system
        // $approval->approver->notify(new ContractApprovalRequest($approval));
    }
    
    private function sendReminderNotification(ContractApproval $approval)
    {
        // Implement reminder notification
        // $approval->approver->notify(new ContractApprovalReminder($approval));
    }
    
    private function sendEscalationNotification(ContractApproval $approval)
    {
        // Implement escalation notification
        // $approval->approver->notify(new ContractApprovalEscalation($approval));
    }
    
    private function notifyApprovalResult(ContractApproval $approval)
    {
        // Notify contract creator and other relevant parties
        // $approval->contract->createdBy->notify(new ContractApprovalResult($approval));
    }
}
