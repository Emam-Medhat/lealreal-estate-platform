<?php

namespace App\Http\Controllers;

use App\Models\TaxDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaxDocumentController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index(Request $request)
    {
        $query = TaxDocument::with(['user']);

        if ($request->document_type) {
            $query->where('document_type', $request->document_type);
        }

        $documents = $query->latest()->paginate(20);

        return view('taxes.documents.index', compact('documents'));
    }

    public function create()
    {
        $documentTypes = ['tax_certificate', 'assessment_report', 'exemption_certificate', 'payment_receipt', 'filing_confirmation'];

        return view('taxes.documents.create', compact('documentTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $path = $request->file('file')->store('tax-documents', 'public');

        $document = TaxDocument::create([
            'document_type' => $request->document_type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_name' => $request->file->getClientOriginalName(),
            'file_size' => $request->file->getSize(),
            'file_type' => $request->file->getMimeType(),
            'expires_at' => $request->expires_at,
            'user_id' => Auth::id(),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.documents.show', $document)
            ->with('success', 'تم رفع المستند الضريبي بنجاح');
    }

    public function show(TaxDocument $taxDocument)
    {
        return view('taxes.documents.show', compact('taxDocument'));
    }

    public function download(TaxDocument $taxDocument)
    {
        return Storage::download($taxDocument->file_path, $taxDocument->file_name);
    }

    public function destroy(TaxDocument $taxDocument)
    {
        Storage::delete($taxDocument->file_path);
        $taxDocument->delete();

        return redirect()
            ->route('taxes.documents.index')
            ->with('success', 'تم حذف المستند الضريبي بنجاح');
    }
}
