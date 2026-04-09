<?php

namespace App\Http\Controllers;

use App\Models\Esignature;
use App\Models\Document;
use Illuminate\Http\Request;

class EsignatureController extends Controller
{
    public function index()
    {
        $signatures = Esignature::with(['document', 'signer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('esignatures.index', compact('signatures'));
    }
    
    public function create(Document $document)
    {
        return view('esignatures.create', compact('document'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'signer_email' => 'required|email',
            'signer_name' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
            'expires_at' => 'nullable|date|after:today',
        ]);
        
        $signature = Esignature::create([
            'document_id' => $request->document_id,
            'signer_email' => $request->signer_email,
            'signer_name' => $request->signer_name,
            'message' => $request->message,
            'expires_at' => $request->expires_at,
            'status' => 'pending',
            'token' => \Str::random(60),
            'requested_by' => auth()->id(),
        ]);
        
        // Send signature request email
        // Mail::to($request->signer_email)->send(new SignatureRequestMail($signature));
        
        return redirect()->route('esignatures.show', $signature)
            ->with('success', 'تم إرسال طلب التوقيع بنجاح');
    }
    
    public function show(Esignature $signature)
    {
        $signature->load(['document', 'signer', 'requestedBy']);
        
        return view('esignatures.show', compact('signature'));
    }
    
    public function sign($token)
    {
        $signature = Esignature::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();
            
        if ($signature->expires_at && $signature->expires_at < now()) {
            abort(403, 'انتهت صلاحية رابط التوقيع');
        }
        
        return view('esignatures.sign', compact('signature'));
    }
    
    public function submitSignature(Request $request, $token)
    {
        $signature = Esignature::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();
            
        if ($signature->expires_at && $signature->expires_at < now()) {
            abort(403, 'انتهت صلاحية رابط التوقيع');
        }
        
        $request->validate([
            'signature_data' => 'required|string',
            'signature_type' => 'required|in:draw,type,upload',
            'acceptance' => 'required|accepted',
        ]);
        
        // Create signer user if not exists
        $signer = \App\Models\User::firstOrCreate(
            ['email' => $signature->signer_email],
            ['name' => $signature->signer_name, 'role' => 'client']
        );
        
        $signature->update([
            'signature_data' => $request->signature_data,
            'signature_type' => $request->signature_type,
            'signed_at' => now(),
            'signed_by' => $signer->id,
            'status' => 'signed',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        // Update document status
        $document = $signature->document;
        $document->update(['status' => 'signed']);
        
        // Send confirmation email
        // Mail::to($signature->signer_email)->send(new SignatureConfirmationMail($signature));
        
        return view('esignatures.confirmation', compact('signature'));
    }
    
    public function remind(Esignature $signature)
    {
        if ($signature->status !== 'pending') {
            return back()->with('error', 'لا يمكن إرسال تذكير لهذا التوقيع');
        }
        
        // Send reminder email
        // Mail::to($signature->signer_email)->send(new SignatureReminderMail($signature));
        
        return back()->with('success', 'تم إرسال التذكير بنجاح');
    }
    
    public function cancel(Esignature $signature)
    {
        if ($signature->status === 'signed') {
            return back()->with('error', 'لا يمكن إلغاء التوقيع المكتمل');
        }
        
        $signature->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'تم إلغاء طلب التوقيع بنجاح');
    }
    
    public function verify($token)
    {
        $signature = Esignature::where('verification_token', $token)
            ->where('status', 'signed')
            ->firstOrFail();
            
        return view('esignatures.verify', compact('signature'));
    }
    
    public function download(Esignature $signature)
    {
        if ($signature->status !== 'signed') {
            abort(403, 'التوقيع غير مكتمل');
        }
        
        // Generate signed document PDF
        $filename = 'signed_document_' . $signature->id . '.pdf';
        $filepath = storage_path('app/temp/' . $filename);
        
        // This would generate PDF with signature overlay
        // For now, return original document
        
        return response()->download($filepath, $filename)
            ->deleteFileAfterSend(true);
    }
}
