<?php

namespace App\Http\Controllers;

use App\Models\TaxFiling;
use App\Models\PropertyTax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaxFilingController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index(Request $request)
    {
        $query = TaxFiling::with(['propertyTax', 'user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tax_year) {
            $query->where('tax_year', $request->tax_year);
        }

        $filings = $query->latest()->paginate(20);

        return view('taxes.filing.index', compact('filings'));
    }

    public function create()
    {
        $propertyTaxes = PropertyTax::where('status', 'pending')->get();

        return view('taxes.filing.create', compact('propertyTaxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_tax_id' => 'required|exists:property_taxes,id',
            'filing_type' => 'required|in:annual,quarterly,amended',
            'tax_year' => 'required|integer',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $filing = TaxFiling::create([
            'property_tax_id' => $request->property_tax_id,
            'user_id' => Auth::id(),
            'filing_type' => $request->filing_type,
            'tax_year' => $request->tax_year,
            'status' => 'submitted',
            'submission_date' => now(),
            'created_by' => Auth::id(),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tax-filings', 'public');
                $filing->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()
            ->route('taxes.filing.show', $filing)
            ->with('success', 'تم تقديم الإقرار الضريبي بنجاح');
    }

    public function show(TaxFiling $taxFiling)
    {
        $taxFiling->load(['propertyTax.property', 'attachments', 'reviewer']);

        return view('taxes.filing.show', compact('taxFiling'));
    }

    public function edit(TaxFiling $taxFiling)
    {
        if ($taxFiling->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل الإقرار بعد تقديمه');
        }

        return view('taxes.filing.edit', compact('taxFiling'));
    }

    public function update(Request $request, TaxFiling $taxFiling)
    {
        if ($taxFiling->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل الإقرار بعد تقديمه');
        }

        $request->validate([
            'filing_type' => 'required|in:annual,quarterly,amended',
            'tax_year' => 'required|integer',
        ]);

        $taxFiling->update([
            'filing_type' => $request->filing_type,
            'tax_year' => $request->tax_year,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.filing.show', $taxFiling)
            ->with('success', 'تم تحديث الإقرار الضريبي بنجاح');
    }

    public function submit(TaxFiling $taxFiling)
    {
        if ($taxFiling->status !== 'draft') {
            return back()->with('error', 'الإقرار بالفعل تم تقديمه');
        }

        $taxFiling->update([
            'status' => 'submitted',
            'submission_date' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تقديم الإقرار الضريبي بنجاح');
    }

    public function approve(Request $request, TaxFiling $taxFiling)
    {
        $request->validate([
            'review_notes' => 'nullable|string',
            'approved_amount' => 'required|numeric|min:0',
        ]);

        $taxFiling->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'review_date' => now(),
            'review_notes' => $request->review_notes,
            'approved_amount' => $request->approved_amount,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم اعتماد الإقرار الضريبي بنجاح');
    }

    public function reject(Request $request, TaxFiling $taxFiling)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $taxFiling->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'review_date' => now(),
            'rejection_reason' => $request->rejection_reason,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم رفض الإقرار الضريبي');
    }

    public function downloadAttachment(TaxFiling $taxFiling, $attachmentId)
    {
        $attachment = $taxFiling->attachments()->findOrFail($attachmentId);

        return Storage::download($attachment->file_path, $attachment->file_name);
    }
}
