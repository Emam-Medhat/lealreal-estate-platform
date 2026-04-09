<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentSignature;
use App\Models\DocumentVersion;
use App\Http\Requests\SignDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentSignatureController extends Controller
{
    public function index()
    {
        $signatures = DocumentSignature::with(['document', 'signer', 'version'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('documents.signatures.index', compact('signatures'));
    }
    
    public function create(Document $document)
    {
        $document->load(['versions', 'signatures']);
        
        // Check if document can be signed
        if ($document->status !== 'ready_for_signature') {
            return back()->with('error', 'الوثيقة غير جاهزة للتوقيع');
        }
        
        // Check if user has already signed
        $existingSignature = $document->signatures()
            ->where('signer_id', auth()->id())
            ->first();
            
        if ($existingSignature) {
            return back()->with('error', 'لقد قمت بالتوقيع على هذه الوثيقة بالفعل');
        }
        
        return view('documents.signatures.create', compact('document'));
    }
    
    public function store(SignDocumentRequest $request, Document $document)
    {
        DB::beginTransaction();
        
        try {
            // Get the latest version
            $version = $document->versions()->latest()->first();
            
            if (!$version) {
                return back()->with('error', 'لا توجد نسخة من الوثيقة للتوقيع عليها');
            }
            
            // Create signature
            $signature = DocumentSignature::create([
                'document_id' => $document->id,
                'version_id' => $version->id,
                'signer_id' => auth()->id(),
                'signer_name' => $request->signer_name,
                'signer_email' => $request->signer_email,
                'signature_type' => $request->signature_type,
                'signature_data' => $request->signature_data,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'signed_at' => now(),
                'status' => 'signed',
            ]);
            
            // Save signature image if provided
            if ($request->hasFile('signature_image')) {
                $path = $request->file('signature_image')
                    ->store('signatures', 'public');
                    
                $signature->update(['signature_image_path' => $path]);
            }
            
            // Check if all required signatures are collected
            $this->checkDocumentCompletion($document);
            
            // Log activity
            $this->logSignatureActivity($document, $signature);
            
            DB::commit();
            
            return redirect()->route('documents.show', $document)
                ->with('success', 'تم التوقيع على الوثيقة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء التوقيع: ' . $e->getMessage());
        }
    }
    
    public function show(DocumentSignature $signature)
    {
        $signature->load(['document', 'version', 'signer']);
        
        return view('documents.signatures.show', compact('signature'));
    }
    
    public function verify(Request $request)
    {
        $request->validate([
            'signature_id' => 'required|exists:document_signatures,id',
            'verification_code' => 'required|string',
        ]);
        
        $signature = DocumentSignature::findOrFail($request->signature_id);
        
        // Verify signature
        $isValid = $this->verifySignature($signature, $request->verification_code);
        
        if ($isValid) {
            $signature->update([
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'verification_status' => 'verified',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم التحقق من التوقيع بنجاح'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'فشل التحقق من التوقيع'
        ], 400);
    }
    
    public function bulkSign(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
            'signature_data' => 'required|string',
            'signer_name' => 'required|string|max:255',
            'signer_email' => 'required|email',
        ]);
        
        $signatures = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($request->document_ids as $documentId) {
                $document = Document::findOrFail($documentId);
                
                // Check if user can sign
                if (!$this->canUserSignDocument($document, auth()->user())) {
                    continue;
                }
                
                // Check if already signed
                $existingSignature = $document->signatures()
                    ->where('signer_id', auth()->id())
                    ->first();
                    
                if ($existingSignature) {
                    continue;
                }
                
                $version = $document->versions()->latest()->first();
                
                if (!$version) {
                    continue;
                }
                
                $signature = DocumentSignature::create([
                    'document_id' => $document->id,
                    'version_id' => $version->id,
                    'signer_id' => auth()->id(),
                    'signer_name' => $request->signer_name,
                    'signer_email' => $request->signer_email,
                    'signature_type' => 'digital',
                    'signature_data' => $request->signature_data,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'signed_at' => now(),
                    'status' => 'signed',
                ]);
                
                $signatures[] = $signature;
                
                // Check document completion
                $this->checkDocumentCompletion($document);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'تم التوقيع على ' . count($signatures) . ' وثيقة بنجاح',
                'signatures' => $signatures
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التوقيع المجمع: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function revoke(DocumentSignature $signature)
    {
        // Check if user can revoke
        if ($signature->signer_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return back()->with('error', 'غير مصرح لك بإلغاء هذا التوقيع');
        }
        
        // Check if signature can be revoked
        if ($signature->created_at->diffInHours(now()) > 24) {
            return back()->with('error', 'لا يمكن إلغاء التوقيع بعد مرور 24 ساعة');
        }
        
        DB::beginTransaction();
        
        try {
            $signature->update([
                'status' => 'revoked',
                'revoked_at' => now(),
                'revoked_by' => auth()->id(),
                'revocation_reason' => request('reason', 'إلغاء من قبل المستخدم'),
            ]);
            
            // Update document status
            $document = $signature->document;
            $document->update(['status' => 'signature_revoked']);
            
            DB::commit();
            
            return back()->with('success', 'تم إلغاء التوقيع بنجاح');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->with('error', 'حدث خطأ أثناء إلغاء التوقيع');
        }
    }
    
    public function downloadSignature(DocumentSignature $signature)
    {
        if (!$signature->signature_image_path) {
            return back()->with('error', 'لا توجد صورة للتوقيع');
        }
        
        return Storage::download($signature->signature_image_path);
    }
    
    public function signatureHistory(Document $document)
    {
        $signatures = $document->signatures()
            ->with(['signer', 'version'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('documents.signatures.history', compact('document', 'signatures'));
    }
    
    private function checkDocumentCompletion(Document $document)
    {
        $requiredSignatures = $document->required_signatures ?? 1;
        $currentSignatures = $document->signatures()
            ->where('status', 'signed')
            ->count();
            
        if ($currentSignatures >= $requiredSignatures) {
            $document->update([
                'status' => 'signed',
                'signed_at' => now(),
            ]);
        } else {
            $document->update(['status' => 'partially_signed']);
        }
    }
    
    private function logSignatureActivity(Document $document, DocumentSignature $signature)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($document)
            ->withProperties([
                'signature_id' => $signature->id,
                'signature_type' => $signature->signature_type,
                'signed_at' => $signature->signed_at,
            ])
            ->log('قام بالتوقيع على الوثيقة: ' . $document->title);
    }
    
    private function verifySignature(DocumentSignature $signature, string $code)
    {
        // Simple verification logic - in production, use proper digital signature verification
        return hash('sha256', $signature->signature_data . $signature->document_id) === $code;
    }
    
    private function canUserSignDocument(Document $document, $user)
    {
        // Check if user has permission to sign
        if ($user->isAdmin()) {
            return true;
        }
        
        // Check if user is in the allowed signers list
        if ($document->allowed_signers) {
            return in_array($user->id, $document->allowed_signers);
        }
        
        // Check if user is the document creator
        if ($document->created_by === $user->id) {
            return true;
        }
        
        return false;
    }
}
