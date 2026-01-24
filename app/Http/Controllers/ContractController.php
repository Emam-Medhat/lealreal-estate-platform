<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Property;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Contract::with(['property.images', 'buyer', 'seller', 'signatures']);

        if ($request->type === 'buyer') {
            $query->where('buyer_id', $user->id);
        } elseif ($request->type === 'seller') {
            $query->where('seller_id', $user->id);
        }

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total_contracts' => Contract::count(),
            'pending_contracts' => Contract::where('status', 'pending')->count(),
            'signed_contracts' => Contract::where('status', 'signed')->count(),
            'completed_contracts' => Contract::where('status', 'completed')->count(),
            'my_contracts' => Contract::where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })->count()
        ];

        return view('contracts.index', compact('contracts', 'stats'));
    }

    public function show($id)
    {
        $contract = Contract::with([
            'property.images',
            'property.features',
            'buyer',
            'seller',
            'signatures.user',
            'amendments.user',
            'offer',
            'negotiation'
        ])->findOrFail($id);

        // Check if user has permission
        if ($contract->buyer_id !== Auth::id() && $contract->seller_id !== Auth::id()) {
            abort(403);
        }

        $canSign = $this->canSignContract($contract);
        $canAmend = $this->canAmendContract($contract);
        $isFullySigned = $this->isFullySigned($contract);

        return view('contracts.show', compact(
            'contract',
            'canSign',
            'canAmend',
            'isFullySigned'
        ));
    }

    public function create(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'buyer_id' => 'required|exists:users,id',
            'seller_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'terms' => 'required|array',
            'terms.*' => 'string'
        ]);

        $property = Property::findOrFail($request->property_id);

        // Check permissions
        if ($property->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $contract = Contract::create([
            'property_id' => $request->property_id,
            'buyer_id' => $request->buyer_id,
            'seller_id' => $request->seller_id,
            'amount' => $request->amount,
            'terms' => $request->terms,
            'status' => 'pending',
            'created_by' => Auth::id(),
            'expires_at' => now()->addDays(30)
        ]);

        // Notify both parties
        $buyer = User::find($request->buyer_id);
        $seller = User::find($request->seller_id);

        $buyer->notifications()->create([
            'title' => 'Contract Created',
            'message' => 'A new contract has been created for you to review and sign',
            'type' => 'contract',
            'action_url' => '/contracts/' . $contract->id,
            'action_text' => 'View Contract'
        ]);

        $seller->notifications()->create([
            'title' => 'Contract Created',
            'message' => 'A new contract has been created for you to review and sign',
            'type' => 'contract',
            'action_url' => '/contracts/' . $contract->id,
            'action_text' => 'View Contract'
        ]);

        return redirect()->route('contracts.show', $contract->id)
            ->with('success', 'Contract created successfully');
    }

    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        // Check if user can update
        if ($contract->created_by !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        if ($contract->status === 'signed') {
            return back()->with('error', 'Cannot update signed contract');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'terms' => 'required|array',
            'terms.*' => 'string'
        ]);

        $contract->update($request->all());

        return back()->with('success', 'Contract updated successfully');
    }

    public function sign(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        // Check if user can sign
        if (!$this->canSignContract($contract)) {
            return back()->with('error', 'Cannot sign this contract');
        }

        $request->validate([
            'signature_type' => 'required|in:electronic,digital',
            'agreement' => 'required|accepted'
        ]);

        // Create signature
        $signature = $contract->signatures()->create([
            'user_id' => Auth::id(),
            'signature_type' => $request->signature_type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'signed_at' => now()
        ]);

        // Update contract status if all parties have signed
        if ($this->isFullySigned($contract)) {
            $contract->update([
                'status' => 'signed',
                'signed_at' => now()
            ]);

            // Notify all parties
            $contract->buyer->notifications()->create([
                'title' => 'Contract Fully Signed',
                'message' => 'The contract has been fully signed by all parties',
                'type' => 'contract',
                'action_url' => '/contracts/' . $contract->id,
                'action_text' => 'View Contract'
            ]);

            $contract->seller->notifications()->create([
                'title' => 'Contract Fully Signed',
                'message' => 'The contract has been fully signed by all parties',
                'type' => 'contract',
                'action_url' => '/contracts/' . $contract->id,
                'action_text' => 'View Contract'
            ]);
        }

        return back()->with('success', 'Contract signed successfully');
    }

    public function amend(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        // Check if user can amend
        if (!$this->canAmendContract($contract)) {
            return back()->with('error', 'Cannot amend this contract');
        }

        $request->validate([
            'amendments' => 'required|array',
            'amendments.*' => 'string',
            'reason' => 'required|string|max:1000'
        ]);

        $amendment = $contract->amendments()->create([
            'user_id' => Auth::id(),
            'amendments' => $request->amendments,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        // Update contract status
        $contract->update(['status' => 'amended']);

        // Reset signatures
        $contract->signatures()->delete();

        // Notify other party
        $otherParty = $contract->buyer_id === Auth::id() ? $contract->seller : $contract->buyer;
        
        $otherParty->notifications()->create([
            'title' => 'Contract Amendment Proposed',
            'message' => 'An amendment has been proposed to the contract',
            'type' => 'contract',
            'action_url' => '/contracts/' . $contract->id,
            'action_text' => 'View Amendment'
        ]);

        return back()->with('success', 'Amendment proposed successfully');
    }

    public function acceptAmendment(Request $request, $id, $amendmentId)
    {
        $contract = Contract::findOrFail($id);
        $amendment = $contract->amendments()->findOrFail($amendmentId);

        // Check permission
        if ($amendment->user_id === Auth::id()) {
            return back()->with('error', 'Cannot accept your own amendment');
        }

        // Accept amendment
        $amendment->update([
            'status' => 'accepted',
            'accepted_at' => now()
        ]);

        // Update contract terms
        $contract->update([
            'terms' => array_merge($contract->terms, $amendment->amendments),
            'status' => 'pending'
        ]);

        // Notify amendment creator
        $amendment->user->notifications()->create([
            'title' => 'Amendment Accepted',
            'message' => 'Your amendment has been accepted',
            'type' => 'contract',
            'action_url' => '/contracts/' . $contract->id,
            'action_text' => 'View Contract'
        ]);

        return back()->with('success', 'Amendment accepted successfully');
    }

    public function rejectAmendment(Request $request, $id, $amendmentId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $contract = Contract::findOrFail($id);
        $amendment = $contract->amendments()->findOrFail($amendmentId);

        // Check permission
        if ($amendment->user_id === Auth::id()) {
            return back()->with('error', 'Cannot reject your own amendment');
        }

        $amendment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason
        ]);

        // Restore original contract status
        $contract->update(['status' => 'signed']);

        // Notify amendment creator
        $amendment->user->notifications()->create([
            'title' => 'Amendment Rejected',
            'message' => 'Your amendment has been rejected',
            'type' => 'contract',
            'action_url' => '/contracts/' . $contract->id,
            'action_text' => 'View Contract'
        ]);

        return back()->with('success', 'Amendment rejected successfully');
    }

    public function terminate(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $contract = Contract::findOrFail($id);

        // Check permission
        if ($contract->buyer_id !== Auth::id() && $contract->seller_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        if ($contract->status === 'completed') {
            return back()->with('error', 'Cannot terminate completed contract');
        }

        $contract->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'termination_reason' => $request->reason
        ]);

        // Update property status
        $contract->property->update(['status' => 'available']);

        // Notify other party
        $otherParty = $contract->buyer_id === Auth::id() ? $contract->seller : $contract->buyer;
        
        $otherParty->notifications()->create([
            'title' => 'Contract Terminated',
            'message' => 'The contract has been terminated',
            'type' => 'contract',
            'action_url' => '/contracts/' . $contract->id,
            'action_text' => 'View Contract'
        ]);

        return back()->with('success', 'Contract terminated successfully');
    }

    public function complete($id)
    {
        $contract = Contract::findOrFail($id);

        // Check permission
        if ($contract->buyer_id !== Auth::id() && $contract->seller_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        if ($contract->status !== 'signed') {
            return back()->with('error', 'Contract must be signed to complete');
        }

        $contract->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Update property status
        $contract->property->update(['status' => 'sold']);

        // Notify both parties
        $contract->buyer->notifications()->create([
            'title' => 'Contract Completed',
            'message' => 'The contract has been completed successfully',
            'type' => 'contract',
            'action_url' => '/contracts/' . $contract->id,
            'action_text' => 'View Contract'
        ]);

        $contract->seller->notifications()->create([
            'title' => 'Contract Completed',
            'message' => 'The contract has been completed successfully',
            'type' => 'contract',
            'action_url' => '/contracts/' . $contract->id,
            'action_text' => 'View Contract'
        ]);

        return back()->with('success', 'Contract completed successfully');
    }

    public function download($id)
    {
        $contract = Contract::with(['property', 'buyer', 'seller'])->findOrFail($id);

        // Check permission
        if ($contract->buyer_id !== Auth::id() && $contract->seller_id !== Auth::id()) {
            abort(403);
        }

        // Generate PDF
        $pdf = $this->generateContractPDF($contract);

        return $pdf->download('contract-' . $contract->id . '.pdf');
    }

    private function canSignContract(Contract $contract): bool
    {
        $user = Auth::user();

        // Check if user is buyer or seller
        if ($contract->buyer_id !== $user->id && $contract->seller_id !== $user->id) {
            return false;
        }

        // Check if contract is in signable state
        if (!in_array($contract->status, ['pending', 'amended'])) {
            return false;
        }

        // Check if user hasn't already signed
        if ($contract->signatures()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    private function canAmendContract(Contract $contract): bool
    {
        $user = Auth::user();

        // Check if user is buyer or seller
        if ($contract->buyer_id !== $user->id && $contract->seller_id !== $user->id) {
            return false;
        }

        // Check if contract is signed
        if ($contract->status !== 'signed') {
            return false;
        }

        // Check if there's no pending amendment
        $pendingAmendment = $contract->amendments()
            ->where('status', 'pending')
            ->first();

        if ($pendingAmendment) {
            return false;
        }

        return true;
    }

    private function isFullySigned(Contract $contract): bool
    {
        $buyerSigned = $contract->signatures()->where('user_id', $contract->buyer_id)->exists();
        $sellerSigned = $contract->signatures()->where('user_id', $contract->seller_id)->exists();

        return $buyerSigned && $sellerSigned;
    }

    private function generateContractPDF(Contract $contract)
    {
        // Generate PDF contract (implementation would depend on PDF library)
        // This is a placeholder for PDF generation
        return response()->make('PDF contract content', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="contract.pdf"'
        ]);
    }
}
