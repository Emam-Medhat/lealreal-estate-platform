<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\NotaryVerification;
use App\Models\DocumentSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotaryController extends Controller
{
    public function index()
    {
        $verifications = NotaryVerification::with(['contract', 'notary', 'requestedBy'])
            ->filter(request(['status', 'notary', 'date_range']))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('notary.index', compact('verifications'));
    }
    
    public function create(Contract $contract)
    {
        $contract->load(['parties', 'terms', 'signatures']);
        
        // Check if contract can be notarized
        if (!in_array($contract->status, ['signed', 'approved'])) {
            return back()->with('error', 'العقد ليس في حالة تسمح بالتوثيق');
        }
        
        // Check if all parties have signed
        $requiredSignatures = $contract->required_signatures ?? count($contract->parties);
        $currentSignatures = $contract->signatures()->where('status', 'signed')->count();
        
        if ($currentSignatures < $requiredSignatures) {
            return back()->with('error', 'يجب أن يتم توقيع العقد من جميع الأطراف قبل التوثيق');
        }
        
        $notaries = \App\Models\User::where('role', 'notary')
            ->orWhere('role', 'legal')
            ->get();
            
        return view('notary.create', compact('contract', 'notaries'));
    }
    
    public function store(Request $request, Contract $contract)
    {
        $request->validate([
            'notary_id' => 'required|exists:users,id',
            'verification_type' => 'required|in:standard,expedited,priority',
            'witnesses' => 'required|array|min:2',
            'witnesses.*.name' => 'required|string|max:255',
            'witnesses.*.id_number' => 'required|string|max:50',
            'witnesses.*.address' => 'required|string|max:500',
            'witnesses.*.phone' => 'required|string|max:20',
            'verification_notes' => 'nullable|string',
            'documents' => 'required|array',
            'documents.*.type' => 'required|string|max:100',
            'documents.*.number' => 'required|string|max:100',
            'documents.*.issue_date' => 'required|date',
            'documents.*.expiry_date' => 'nullable|date|after:documents.*.issue_date',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create notary verification
            $verification = NotaryVerification::create([
                'contract_id' => $contract->id,
                'notary_id' => $request->notary_id,
                'verification_type' => $request->verification_type,
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'requested_at' => now(),
                'witnesses' => $request->witnesses,
                'documents' => $request->documents,
                'notes' => $request->verification_notes,
                'verification_code' => $this->generateVerificationCode(),
                'estimated_completion' => $this->getEstimatedCompletion($request->verification_type),
            ]);
            
            // Update contract status
            $contract->update([
                'status' => 'notarization_pending',
                'notarization_requested_at' => now(),
            ]);
            
            // Send notification to notary
            $this->notifyNotary($verification);
            
            DB::commit();
            
            return redirect()->route('notary.show', $verification)
                ->with('success', 'تم إرسال طلب التوثيق بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إرسال طلب التوثيق: ' . $e->getMessage());
        }
    }
    
    public function show(NotaryVerification $verification)
    {
        $verification->load(['contract', 'notary', 'requestedBy', 'contract.parties', 'contract.signatures']);
        
        return view('notary.show', compact('verification'));
    }
    
    public function verify(Request $request, NotaryVerification $verification)
    {
        // Check if user is the assigned notary
        if ($verification->notary_id !== auth()->id()) {
            return back()->with('error', 'غير مصرح لك بالتحقق من هذا الطلب');
        }
        
        // Check if verification is pending
        if ($verification->status !== 'pending') {
            return back()->with('error', 'هذا الطلب تمت معالجته بالفعل');
        }
        
        $request->validate([
            'decision' => 'required|in:verified,rejected,requires_additional_info',
            'verification_notes' => 'required|string',
            'verification_details' => 'required|array',
            'verification_details.party_identities_verified' => 'required|boolean',
            'verification_details.signatures_verified' => 'required|boolean',
            'verification_details.documents_verified' => 'required|boolean',
            'verification_details.witnesses_verified' => 'required|boolean',
            'verification_details.legal_compliance' => 'required|boolean',
            'additional_requirements' => 'nullable|array',
            'notary_seal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        DB::beginTransaction();
        
        try {
            $verification->update([
                'status' => $request->decision === 'verified' ? 'verified' : 
                           ($request->decision === 'rejected' ? 'rejected' : 'requires_info'),
                'verified_at' => now(),
                'verification_details' => $request->verification_details,
                'verification_notes' => $request->verification_notes,
                'additional_requirements' => $request->additional_requirements ?? [],
            ]);
            
            // Handle notary seal upload
            if ($request->hasFile('notary_seal')) {
                $path = $request->file('notary_seal')
                    ->store('notary_seals', 'public');
                    
                $verification->update(['notary_seal_path' => $path]);
            }
            
            // Update contract status based on decision
            $contract = $verification->contract;
            
            switch ($request->decision) {
                case 'verified':
                    $contract->update([
                        'status' => 'notarized',
                        'notarized_at' => now(),
                        'notarization_number' => $this->generateNotarizationNumber(),
                    ]);
                    
                    // Generate notarized document
                    $this->generateNotarizedDocument($contract, $verification);
                    break;
                    
                case 'rejected':
                    $contract->update([
                        'status' => 'notarization_rejected',
                        'notarization_rejected_at' => now(),
                        'rejection_reason' => $request->verification_notes,
                    ]);
                    break;
                    
                case 'requires_additional_info':
                    $contract->update([
                        'status' => 'notarization_info_required',
                    ]);
                    break;
            }
            
            // Notify relevant parties
            $this->notifyVerificationResult($verification);
            
            DB::commit();
            
            return redirect()->route('notary.show', $verification)
                ->with('success', 'تم تسجيل نتيجة التحقق بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تسجيل نتيجة التحقق: ' . $e->getMessage());
        }
    }
    
    public function provideAdditionalInfo(Request $request, NotaryVerification $verification)
    {
        $request->validate([
            'additional_documents' => 'required|array',
            'additional_documents.*.type' => 'required|string|max:100',
            'additional_documents.*.file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'additional_notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $documents = [];
            
            foreach ($request->additional_documents as $doc) {
                $path = $doc['file']->store('notary_documents', 'public');
                
                $documents[] = [
                    'type' => $doc['type'],
                    'file_path' => $path,
                    'original_name' => $doc['file']->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            
            $verification->update([
                'status' => 'pending',
                'additional_documents' => $documents,
                'additional_notes' => $request->additional_notes,
                'info_provided_at' => now(),
            ]);
            
            // Notify notary
            $this->notifyNotary($verification, 'additional_info_provided');
            
            DB::commit();
            
            return back()->with('success', 'تم تقديم المعلومات الإضافية بنجاح');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تقديم المعلومات الإضافية');
        }
    }
    
    public function certificate(NotaryVerification $verification)
    {
        if ($verification->status !== 'verified') {
            return back()->with('error', 'شهادة التوثيق غير متوفرة');
        }
        
        return view('notary.certificate', compact('verification'));
    }
    
    public function downloadCertificate(NotaryVerification $verification)
    {
        if ($verification->status !== 'verified') {
            return back()->with('error', 'شهادة التوثيق غير متوفرة');
        }
        
        $pdf = \PDF::loadView('notary.certificate-pdf', compact('verification'));
        
        return $pdf->download('notary-certificate-' . $verification->verification_code . '.pdf');
    }
    
    public function verifyCertificate(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);
        
        $verification = NotaryVerification::where('verification_code', $request->verification_code)
            ->where('status', 'verified')
            ->first();
            
        if (!$verification) {
            return response()->json([
                'valid' => false,
                'message' => 'رمز التحقق غير صالح'
            ]);
        }
        
        return response()->json([
            'valid' => true,
            'verification' => [
                'contract_title' => $verification->contract->title,
                'contract_number' => $verification->contract->contract_number,
                'notarization_date' => $verification->verified_at->format('Y-m-d'),
                'notary_name' => $verification->notary->name,
                'verification_code' => $verification->verification_code,
            ]
        ]);
    }
    
    public function dashboard()
    {
        $stats = [
            'pending_verifications' => NotaryVerification::where('status', 'pending')
                ->where('notary_id', auth()->id())
                ->count(),
            'verified_today' => NotaryVerification::where('status', 'verified')
                ->where('notary_id', auth()->id())
                ->whereDate('verified_at', today())
                ->count(),
            'total_verified' => NotaryVerification::where('status', 'verified')
                ->where('notary_id', auth()->id())
                ->count(),
        ];
        
        $myVerifications = NotaryVerification::with(['contract', 'contract.parties'])
            ->where('notary_id', auth()->id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();
            
        $recentVerifications = NotaryVerification::with(['contract', 'notary'])
            ->where('notary_id', auth()->id())
            ->orderBy('verified_at', 'desc')
            ->take(10)
            ->get();
            
        return view('notary.dashboard', compact('stats', 'myVerifications', 'recentVerifications'));
    }
    
    private function generateVerificationCode()
    {
        return 'NV-' . date('Y') . '-' . Str::upper(Str::random(8));
    }
    
    private function generateNotarizationNumber()
    {
        return 'NT-' . date('Y') . '-' . str_pad(NotaryVerification::where('status', 'verified')->count() + 1, 6, '0', STR_PAD_LEFT);
    }
    
    private function getEstimatedCompletion(string $type): Carbon
    {
        $hours = match($type) {
            'standard' => 48,
            'expedited' => 24,
            'priority' => 8,
            default => 48,
        };
        
        return now()->addHours($hours);
    }
    
    private function generateNotarizedDocument(Contract $contract, NotaryVerification $verification)
    {
        // Generate notarized PDF document
        $pdf = \PDF::loadView('contracts.notarized', compact('contract', 'verification'));
        
        $filename = 'contracts/notarized/' . Str::slug($contract->title) . '-notarized.pdf';
        
        \Storage::put($filename, $pdf->output());
        
        $contract->update(['notarized_document_path' => $filename]);
    }
    
    private function notifyNotary(NotaryVerification $verification, string $event = 'verification_requested')
    {
        // Implement notification system
        // $verification->notary->notify(new NotaryVerificationRequest($verification, $event));
    }
    
    private function notifyVerificationResult(NotaryVerification $verification)
    {
        // Notify contract creator and parties
        // $verification->contract->createdBy->notify(new NotaryVerificationResult($verification));
    }
}
