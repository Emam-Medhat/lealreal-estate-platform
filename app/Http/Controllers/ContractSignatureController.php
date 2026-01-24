<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractSignatureController extends Controller
{
    public function store(Request $request, $contractId)
    {
        $contract = Contract::findOrFail($contractId);

        // Validate signature request
        $request->validate([
            'signature_data' => 'required|string',
            'signature_type' => 'required|in:electronic,digital',
            'agreement' => 'required|accepted'
        ]);

        // Check if user can sign
        if (!$this->canUserSign($contract)) {
            return response()->json(['error' => 'Cannot sign this contract'], 403);
        }

        // Create signature
        $signature = ContractSignature::create([
            'contract_id' => $contractId,
            'user_id' => Auth::id(),
            'signature_data' => $request->signature_data,
            'signature_type' => $request->signature_type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'signed_at' => now()
        ]);

        // Update contract status if all parties signed
        $this->updateContractStatus($contract);

        return response()->json([
            'success' => true,
            'signature' => $signature,
            'contract_status' => $contract->status
        ]);
    }

    public function verify($signatureId)
    {
        $signature = ContractSignature::with(['contract', 'user'])
            ->findOrFail($signatureId);

        $verification = [
            'is_valid' => $this->verifySignature($signature),
            'signature_details' => [
                'signed_at' => $signature->signed_at,
                'ip_address' => $signature->ip_address,
                'user_agent' => $signature->user_agent,
                'signature_type' => $signature->signature_type
            ],
            'contract_details' => [
                'contract_id' => $signature->contract_id,
                'contract_status' => $signature->contract->status,
                'property_title' => $signature->contract->property->title
            ],
            'user_details' => [
                'user_name' => $signature->user->name,
                'user_email' => $signature->user->email
            ]
        ];

        return response()->json($verification);
    }

    public function download($signatureId)
    {
        $signature = ContractSignature::with(['contract', 'user'])
            ->findOrFail($signatureId);

        // Check permission
        if (!in_array(Auth::id(), [
            $signature->user_id,
            $signature->contract->buyer_id,
            $signature->contract->seller_id
        ])) {
            abort(403);
        }

        // Generate signature certificate
        $certificate = $this->generateSignatureCertificate($signature);

        return response()->make($certificate, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="signature-certificate-' . $signatureId . '.pdf"'
        ]);
    }

    public function revoke($signatureId)
    {
        $signature = ContractSignature::where('user_id', Auth::id())
            ->with('contract')
            ->findOrFail($signatureId);

        // Check if signature can be revoked
        if ($signature->contract->status === 'completed') {
            return back()->with('error', 'Cannot revoke signature on completed contract');
        }

        if (now()->diffInHours($signature->signed_at) > 24) {
            return back()->with('error', 'Can only revoke signatures within 24 hours');
        }

        // Revoke signature
        $signature->update([
            'revoked_at' => now(),
            'revocation_reason' => 'User requested revocation'
        ]);

        // Update contract status
        $signature->contract->update(['status' => 'pending']);

        // Notify other party
        $otherParty = $signature->contract->buyer_id === Auth::id() ? 
                   $signature->contract->seller : 
                   $signature->contract->buyer;

        $otherParty->notifications()->create([
            'title' => 'Signature Revoked',
            'message' => 'A signature has been revoked on the contract',
            'type' => 'contract',
            'action_url' => '/contracts/' . $signature->contract->id,
            'action_text' => 'View Contract'
        ]);

        return back()->with('success', 'Signature revoked successfully');
    }

    public function history($contractId)
    {
        $contract = Contract::findOrFail($contractId);

        // Check permission
        if (!in_array(Auth::id(), [$contract->buyer_id, $contract->seller_id])) {
            abort(403);
        }

        $signatures = $contract->signatures()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['signatures' => $signatures]);
    }

    public function auditTrail($signatureId)
    {
        $signature = ContractSignature::findOrFail($signatureId);

        // Check permission
        if (!in_array(Auth::id(), [
            $signature->user_id,
            $signature->contract->buyer_id,
            $signature->contract->seller_id
        ])) {
            abort(403);
        }

        $auditTrail = [
            'created_at' => $signature->created_at,
            'signed_at' => $signature->signed_at,
            'revoked_at' => $signature->revoked_at,
            'ip_address' => $signature->ip_address,
            'user_agent' => $signature->user_agent,
            'verification_attempts' => $signature->verification_attempts ?? 0,
            'last_verified_at' => $signature->last_verified_at
        ];

        return response()->json($auditTrail);
    }

    private function canUserSign(Contract $contract): bool
    {
        $user = Auth::user();

        // Check if user is buyer or seller
        if (!in_array($user->id, [$contract->buyer_id, $contract->seller_id])) {
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

        // Check if signature hasn't been revoked recently
        $recentRevocation = $contract->signatures()
            ->where('user_id', $user->id)
            ->whereNotNull('revoked_at')
            ->where('revoked_at', '>', now()->subHours(24))
            ->exists();

        if ($recentRevocation) {
            return false;
        }

        return true;
    }

    private function updateContractStatus(Contract $contract): void
    {
        $buyerSigned = $contract->signatures()->where('user_id', $contract->buyer_id)->exists();
        $sellerSigned = $contract->signatures()->where('user_id', $contract->seller_id)->exists();

        if ($buyerSigned && $sellerSigned) {
            $contract->update([
                'status' => 'signed',
                'signed_at' => now()
            ]);
        }
    }

    private function verifySignature(ContractSignature $signature): bool
    {
        // Implement signature verification logic
        // This would verify the digital signature or validate electronic signature
        
        // For electronic signatures, verify the data integrity
        if ($signature->signature_type === 'electronic') {
            return !empty($signature->signature_data);
        }

        // For digital signatures, verify cryptographic signature
        if ($signature->signature_type === 'digital') {
            // Implement digital signature verification
            return true; // Placeholder
        }

        return false;
    }

    private function generateSignatureCertificate(ContractSignature $signature): string
    {
        // Generate PDF certificate for the signature
        $certificateData = [
            'signature_id' => $signature->id,
            'contract_id' => $signature->contract_id,
            'user_name' => $signature->user->name,
            'signed_at' => $signature->signed_at->format('Y-m-d H:i:s'),
            'ip_address' => $signature->ip_address,
            'signature_type' => $signature->signature_type,
            'verification_code' => $this->generateVerificationCode($signature)
        ];

        // Generate PDF content (implementation would depend on PDF library)
        return 'Signature Certificate PDF content';
    }

    private function generateVerificationCode(ContractSignature $signature): string
    {
        // Generate unique verification code
        return 'SIG-' . $signature->id . '-' . strtoupper(substr(md5($signature->id . $signature->signed_at), 0, 8));
    }
}
