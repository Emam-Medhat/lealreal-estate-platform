<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\CompanyInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCompanyInvoice implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $invoiceId;
    protected $companyId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $invoiceId, int $companyId)
    {
        $this->invoiceId = $invoiceId;
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $invoice = CompanyInvoice::with(['company', 'subscription'])->find($this->invoiceId);
            
            if (!$invoice) {
                Log::error('Company invoice not found', ['invoice_id' => $this->invoiceId]);
                return;
            }

            // Generate invoice PDF
            $pdfPath = $this->generateInvoicePdf($invoice);
            
            // Update invoice with generated PDF
            $invoice->update([
                'pdf_path' => $pdfPath,
                'sent_at' => now()
            ]);

            // Send invoice email
            $this->sendInvoiceEmail($invoice, $pdfPath);
            
            // Update subscription status if needed
            $this->updateSubscriptionStatus($invoice);
            
            Log::info('Company invoice sent successfully', [
                'invoice_id' => $this->invoiceId,
                'company_id' => $this->companyId,
                'amount' => $invoice->amount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send company invoice', [
                'invoice_id' => $this->invoiceId,
                'company_id' => $this->companyId,
                'error' => $e->getMessage()
            ]);

            // Update invoice status to failed
            CompanyInvoice::where('id', $this->invoiceId)
                ->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            
            throw $e;
        }
    }

    /**
     * Generate invoice PDF
     */
    private function generateInvoicePdf(CompanyInvoice $invoice): string
    {
        $pdf = new \Dompdf\Dompdf();
        
        $pdf->loadView('invoices.company-invoice', [
            'invoice' => $invoice,
            'company' => $invoice->company,
            'subscription' => $invoice->subscription
        ]);

        $filename = "invoice-{$invoice->id}-" . time() . ".pdf";
        $path = "invoices/{$filename}";
        
        Storage::disk('private')->put($path, $pdf->output());
        
        return $path;
    }

    /**
     * Send invoice email
     */
    private function sendInvoiceEmail(CompanyInvoice $invoice, string $pdfPath): void
    {
        try {
            Mail::to($invoice->company->owner->email)->send(new \App\Mail\CompanyInvoiceMail($invoice, $pdfPath));
            
            Log::info('Company invoice email sent', [
                'invoice_id' => $invoice->id,
                'company_id' => $this->companyId,
                'email' => $invoice->company->owner->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send company invoice email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update subscription status
     */
    private function updateSubscriptionStatus(CompanyInvoice $invoice): void
    {
        if ($invoice->subscription) {
            // Check if this is a subscription renewal invoice
            if ($invoice->type === 'subscription_renewal') {
                $newExpiryDate = now()->addDays($invoice->subscription->duration_days);
                
                $invoice->subscription->update([
                    'status' => 'active',
                    'expires_at' => $newExpiryDate,
                    'renewed_at' => now()
                ]);
            }
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Company invoice job failed', [
            'invoice_id' => $this->invoiceId,
            'company_id' => $this->companyId,
            'error' => $exception->getMessage()
        ]);
    }
}
