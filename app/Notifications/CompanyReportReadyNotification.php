<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\CompanyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanyReportReadyNotification extends Notification
{
    use Queueable;

    protected $company;
    protected $report;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company, CompanyReport $report)
    {
        $this->company = $company;
        $this->report = $report;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'تقرير الشركة جاهز',
            'message' => "تم إنشاء تقرير {$this->report->type} بنجاح. يمكنك الآن تحميله.",
            'type' => 'report_ready',
            'icon' => 'document-text',
            'color' => 'success',
            'data' => [
                'company_id' => $this->company->id,
                'company_name' => $this->company->name,
                'report_id' => $this->report->id,
                'report_type' => $this->report->type,
                'file_size' => $this->report->file_size,
                'file_format' => $this->report->file_format,
                'download_url' => route('companies.reports.download', $this->report->id),
                'preview_url' => route('companies.reports.preview', $this->report->id),
                'generated_at' => $this->report->generated_at->toDateTimeString(),
                'expires_at' => $this->report->generated_at->addDays(7)->toDateTimeString()
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('تقرير الشركة جاهز')
            ->view('emails.company-report-ready', [
                'company' => $this->company,
                'report' => $this->report,
                'downloadUrl' => route('companies.reports.download', $this->report->id),
                'previewUrl' => route('companies.reports.preview', $this->report->id)
            ]);
    }
}
