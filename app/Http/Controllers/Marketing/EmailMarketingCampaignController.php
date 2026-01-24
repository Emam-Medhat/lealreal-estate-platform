<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\EmailCampaign;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmailMarketingCampaignController extends Controller
{
    /**
     * Display a listing of the email marketing campaigns.
     */
    public function index(Request $request): Response
    {
        $query = EmailCampaign::with(['property', 'campaignType', 'targetAudience', 'emailTemplates'])
            ->latest('created_at');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->has('campaign_type_id')) {
            $query->where('campaign_type_id', $request->campaign_type_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('start_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->has('sent_status')) {
            $query->where('sent_status', $request->sent_status);
        }

        $campaigns = $query->paginate(15);

        // Get statistics
        $stats = [
            'total_campaigns' => EmailCampaign::count(),
            'sent_campaigns' => EmailCampaign::where('sent_status', 'sent')->count(),
            'draft_campaigns' => EmailCampaign::where('sent_status', 'draft')->count(),
            'total_properties' => Property::count(),
            'targeted_properties' => EmailCampaign::distinct('property_id')->count(),
            'campaign_types' => EmailCampaign::select('campaign_type_id', DB::raw('count(*) as count'))
                ->groupBy('campaign_type_id')
                ->get(),
            'sent_status' => EmailCampaign::select('sent_status', DB::raw('count(*) as count'))
                ->groupBy('sent_status')
                ->get(),
            'monthly_performance' => EmailCampaign::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(sent_count) as sent_count'),
                DB::raw('SUM(open_count) as open_count'),
                DB::raw('SUM(click_count) as click_count'),
                DB::raw('SUM(conversion_count) as conversion_count')
            )
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get(),
        ];

        return Inertia::render('marketing/email-campaigns', [
            'campaigns' => $campaigns,
            'stats' => $stats,
            'filters' => $request->only(['status', 'property_id', 'campaign_type_id', 'start_date', 'end_date', 'sent_status']),
            'properties' => Property::select('id', 'title', 'reference_number', 'status')->get(),
            'campaignTypes' => [
                'newsletter' => 'نشرة بريد إلكتروني',
                'property_alerts' => 'تنبيهات العقارات',
                'market_updates' => 'تحديثات السوق',
                'new_listings' => 'العقارات الجديدة',
                'price_alerts' => 'تنبيهات الأسعار',
                'community_news' => 'أخبار المجتمع',
                'event_invitations' => 'دعوات الفعاليات',
            ],
            'sentStatuses' => [
                'draft' => 'مسودة',
                'scheduled' => 'مجدولة',
                'sent' => 'مُرسلة',
                'failed' => 'فشل',
                'cancelled' => 'ملغاة',
            ],
        ]);
    }

    /**
     * Show the form for creating a new email marketing campaign.
     */
    public function create(): Response
    {
        $properties = Property::select('id', 'title', 'reference_number', 'status')
            ->where('status', 'active')
            ->get();

        return Inertia::render('marketing/email-campaign-create', [
            'properties' => $properties,
            'campaignTypes' => [
                'newsletter' => 'نشرة بريد إلكتروني',
                'property_alerts' => 'تنبيهات العقارات',
                'market_updates' => 'تحديثات السوق',
                'new_listings' => 'العقارات الجديدة',
                'price_alerts' => 'تنبيهات الأسعار',
                'community_news' => 'أخبار المجتمع',
                'event_invitations' => 'دعوات الفعاليات',
            ],
            'sentStatuses' => [
                'draft' => 'مسودة',
                'scheduled' => 'مجدولة',
                'sent' => 'مُرسلة',
                'failed' => 'فشل',
                'cancelled' => 'ملغاة',
            ],
            'targetAudiences' => [
                'first_time_buyers' => 'المشترون لأول مرة',
                'investors' => 'المستثمرون',
                'families' => 'العائلات',
                'professionals' => 'المحترفين',
                'students' => 'الطلاب',
                'retirees' => 'المتقاعدين',
                'expats' => 'الغرباء',
            ],
            'emailTemplates' => [
                'property_alerts' => 'قالب تنبيهات العقارات',
                'price_alerts' => 'قالب تغيير الأسعار',
                'market_updates' => 'قالب تحديثات السوق',
                'new_listings' => 'العقارات الجديدة',
                'community_news' => 'أخبار المجتمع',
                'event_invitations' => 'دعوات الفعاليات',
            ],
        ]);
    }

    /**
     * Store a newly created email marketing campaign in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'campaign_type_id' => 'required|exists:email_campaign_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:draft,scheduled,sent,failed,cancelled',
            'sent_status' => 'nullable|string|in:draft,scheduled,sent,failed,cancelled',
            'send_date' => 'nullable|date',
            'scheduled_at' => 'nullable|date',
            'target_audience' => 'nullable|array',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'personalization' => 'nullable|array',
            'tracking_parameters' => 'nullable|array',
            'success_metrics' => 'nullable|array',
            'budget' => 'required|numeric|min:0',
            'sender_name' => 'nullable|string|max:255',
            'sender_email' => 'nullable|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'cc_emails' => 'nullable|array',
            'bcc_emails' => 'nullable|array',
            'attachments' => 'nullable|array',
            'metadata' => 'nullable|array',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $campaign = EmailCampaign::create([
            'property_id' => $validated['property_id'],
            'campaign_type_id' => $validated['campaign_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'sent_status' => $validated['sent_status'] ?? 'draft',
            'send_date' => $validated['send_date'],
            'scheduled_at' => $validated['scheduled_at'],
            'target_audience' => $validated['target_audience'] ?? [],
            'email_template_id' => $validated['email_template_id'] ?? null,
            'personalization' => $validated['personalization'] ?? [],
            'tracking_parameters' => $validated['tracking_parameters'] ?? [],
            'success_metrics' => $validated['success_metrics'] ?? [],
            'budget' => $validated['budget'],
            'sender_name' => $validated['sender_name'] ?? 'النظام العقارات',
            'sender_email' => $validated['sender_email'] ?? 'marketing@realestate.com',
            'reply_to_email' => $validated['reply_to_email'] ?? null,
            'cc_emails' => $validated['cc_emails'] ?? [],
            'bcc_emails' => $validated['bcc_emails'] ?? [],
            'attachments' => $validated['attachments'] ?? [],
            'metadata' => $validated['metadata'] ?? [],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Schedule campaign if status is scheduled
        if ($campaign->status === 'scheduled') {
            $this->scheduleEmailCampaign($campaign);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء حملة البريد الإلكتروني بنجاح',
            'campaign' => $campaign->load('property', 'campaignType', 'emailTemplate'),
        ]);
    }

    /**
     * Display the specified email marketing campaign.
     */
    public function show(EmailCampaign $emailCampaign): Response
    {
        $emailCampaign->load([
            'property',
            'campaignType',
            'emailTemplate',
            'targetAudience',
            'personalization',
            'trackingParameters',
            'successMetrics',
            'attachments',
        ]);

        // Get campaign performance data
        $performance = [
            'sent_count' => $this->getEmailSentCount($emailCampaign),
            'open_count' => $this->getEmailOpenCount($emailCampaign),
            'click_count' => $this->getEmailClickCount($emailCampaign),
            'conversion_count' => $this->getEmailConversionCount($emailCampaign),
            'open_rate' => $this->getOpenRate($emailCampaign),
            'click_rate' => $this->getClickRate($emailCampaign),
            'conversion_rate' => $this->getConversionRate($emailCampaign),
            'roi' => $this->calculateEmailROI($emailCampaign),
            'cost_per_email' => $this->getCostPerEmail($emailCampaign),
        ];

        return Inertia::render('marketing/email-campaign-show', [
            'campaign' => $emailCampaign,
            'performance' => $performance,
        ]);
    }

    /**
     * Show the form for editing the specified email marketing campaign.
     */
    public function edit(EmailCampaign $emailCampaign): Response
    {
        $properties = Property::select('id', 'title', 'reference_number', 'status')
            ->where('status', 'active')
            ->get();

        return Inertia::render('marketing/email-campaign-edit', [
            'campaign' => $emailCampaign,
            'properties' => $properties,
            'campaignTypes' => [
                'newsletter' => 'نشرة بريد إلكتروني',
                'property_alerts' => 'تنبيهات العقارات',
                'market_updates' => 'تحديثات السوق',
                'new_listings' => 'العقارات الجديدة',
                'price_alerts' => 'تنبيهات الأسعار',
                'community_news' => 'أخبار المجتمع',
                'event_invitations' => 'دعوات الفعاليات',
            ],
            'sentStatuses' => [
                'draft' => 'مسودة',
                'scheduled' => 'مجدولة',
                'sent' => 'مُرسلة',
                'failed' => 'فشل',
                'cancelled' => 'ملغاة',
            ],
            'targetAudiences' => [
                'first_time_buyers' => 'المشترون لأول مرة',
                'investors' => 'المستثمرون',
                'families' => 'العائلات',
                'professionals' => 'المحترفين',
                'students' => 'الطلاب',
                'retirees' => 'المتقاعدين',
                'expats' => 'الغرباء',
            ],
            'emailTemplates' => [
                'property_alerts' => 'قالب تنبيهات العقارات',
                'price_alerts' => 'قالب تغيير الأسعار',
                'market_updates' => 'قالب تحديثات السوق',
                'new_listings' => 'العقارات الجديدة',
                'community_news' => 'أخبار المجتمع',
                'event_invitations' => 'دعوات الفعاليات',
            ],
        ]);
    }

    /**
     * Update the specified email marketing campaign in storage.
     */
    public function update(Request $request, EmailCampaign $emailCampaign): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'campaign_type_id' => 'required|exists:email_campaign_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:draft,scheduled,sent,failed,cancelled',
            'sent_status' => 'nullable|string|in:draft,scheduled,sent,failed,cancelled',
            'send_date' => 'nullable|date',
            'scheduled_at' => 'nullable|date',
            'target_audience' => 'nullable|array',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'personalization' => 'nullable|array',
            'tracking_parameters' => 'nullable|array',
            'success_metrics' => 'nullable|array',
            'budget' => 'required|numeric|min:0',
            'sender_name' => 'nullable|string|max:255',
            'sender_email' => 'nullable|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'cc_emails' => 'nullable|array',
            'bcc_emails' => 'nullable|array',
            'attachments' => 'nullable|array',
            'metadata' => 'nullable|array',
            'updated_by' => auth()->id(),
        ]);

        // Calculate promoted price if discount percentage is provided
        if (isset($validated['discount_percentage']) && isset($validated['original_price'])) {
            $validated['promoted_price'] = $validated['original_price'] - ($validated['original_price'] * $validated['discount_percentage'] / 100);
        }

        $emailCampaign->update([
            'property_id' => $validated['property_id'],
            'campaign_type_id' => $validated['campaign_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'sent_status' => $validated['sent_status'] ?? 'draft',
            'send_date' => $validated['send_date'],
            'scheduled_at' => $validated['scheduled_at'],
            'target_audience' => $validated['target_audience'] ?? [],
            'email_template_id' => $validated['email_template_id'] ?? null,
            'personalization' => $validated['personalization'] ?? [],
            'tracking_parameters' => $validated['tracking_parameters'] ?? [],
            'success_metrics' => $validated['success_metrics'] ?? [],
            'budget' => $validated['budget'],
            'sender_name' => $validated['sender_name'] ?? 'النظام العقارات',
            'sender_email' => $validated['sender_email'] ?? 'marketing@realestate.com',
            'reply_to_email' => $validated['reply_to_email'] ?? null,
            'cc_emails' => $validated['cc_emails'] ?? [],
            'bcc_emails' => $validated['bcc_emails'] ?? [],
            'attachments' => $validated['attachments'] ?? [],
            'metadata' => $validated['metadata'] ?? [],
            'updated_by' => auth()->id(),
        ]);

        // Update campaign status based on sent status
        if ($emailCampaign->sent_status === 'sent') {
            $emailCampaign->update(['status' => 'completed']);
        } elseif ($emailCampaign->status === 'failed' || $emailCampaign->status === 'cancelled') {
            $emailCampaign->update(['status' => 'draft']);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حملة البريد الإلكتروني بنجاح',
            'campaign' => $emailCampaign->load('property', 'campaignType', 'emailTemplate'),
        ]);
    }

    /**
     * Remove the specified email marketing campaign from storage.
     */
    public function destroy(EmailCampaign $emailCampaign): JsonResponse
    {
        $emailCampaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف حملة البريد الإلكتروني بنجاح',
        ]);
    }

    /**
     * Send the specified email marketing campaign.
     */
    public function send(EmailCampaign $emailCampaign): JsonResponse
    {
        if ($emailCampaign->status !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إرسل حملة ليست في حالتها الحالية',
            ]);
        }

        try {
            // Send email campaign
            $this->sendEmailCampaignEmail($emailCampaign);

            // Update campaign status to sent
            $emailCampaign->update([
                'status' => 'sent',
                'sent_date' => now(),
                'sent_count' => DB::raw('sent_count + 1'),
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسل الحملة الإلكتروني بنجاح',
                'campaign' => $emailCampaign->load('property', 'campaignType', 'emailTemplate'),
            ]);
        } catch (\Exception $e) {
            // Update campaign status to failed
            $emailCampaign->update([
                'status' => 'failed',
                'failed_reason' => $e->getMessage(),
                'failed_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسل الحملة الإلكتروني: ' . $e->getMessage(),
                'campaign' => $emailCampaign,
            ]);
        }
    }

    /**
     * Schedule the specified email marketing campaign.
     */
    public function schedule(EmailCampaign $emailCampaign): JsonResponse
    {
        if ($emailCampaign->status !== 'draft') {
            return response()->json([
                'success' => 'false',
                'message' => 'لا يمكن جدولة حملة ليست في حالتها الحالية',
            ]);
        }

        // Schedule the campaign
        $emailCampaign->update([
            'status' => 'scheduled',
            'scheduled_at' => $emailCampaign->scheduled_at ?? now()->addDays(7),
            'updated_by' => auth()->id(),
        ]);

        // Queue the email for sending
        $this->scheduleEmailCampaignEmail($emailCampaign);

        return response()->json([
            'success' => true,
            'message' => 'تم جدولة الحملة الإلكتروني بنجاح',
            'campaign' => $emailCampaign->load('property', 'campaignType', 'emailTemplate'),
        ]);
    }

    /**
     * Schedule email campaign for sending.
     */
    private function scheduleEmailCampaignEmail(EmailCampaign $emailCampaign): void
    {
        // Mock implementation - in real app, this would queue the email for sending
        // In real app, this would integrate with Laravel's queue system
    }

    /**
     * Send email campaign email.
     */
    private function sendEmailEmail(EmailCampaign $emailCampaign): void
    {
        // Mock implementation - in real app, this would send the actual email
        // In real app, this would integrate with Laravel's Mail facade
        // In real app, this would integrate with email service providers
    }

    /**
     * Get email sent count.
     */
    private function getEmailSentCount(EmailCampaign $campaign): int
    {
        // Mock implementation - in real app, this would come from analytics
        return rand(100, 10000);
    }

    /**
     * Get email open count.
     */
    private function getEmailOpenCount(EmailCampaign $campaign): int
    {
        // Mock implementation - in real app, this would come from analytics
        return rand(50, 500);
    }

    /**
     * Get email click count.
     */
    private function getEmailClickCount(EmailCampaign $campaign): int
    {
        // Mock implementation - in real app, this would come from analytics
        return rand(10, 100);
    }

    /**
     * Get email conversion count.
     */
    private function getEmailConversionCount(EmailCampaign $campaign): int
    {
        // Mock implementation - in real app, this would come from analytics
        return rand(1, 50);
    }

    /**
     * Get email open rate.
     */
    private function getOpenRate(EmailCampaign $campaign): float
    {
        $sentCount = $this->getEmailSentCount($campaign);
        $openCount = $this->getEmailOpenCount($campaign);
        
        return $sentCount > 0 ? ($openCount / $sentCount) * 100 : 0;
    }

    /**
     * Get email click rate.
     */
    private function getClickRate(EmailCampaign $campaign): float
    {
        $clickCount = $this->getEmailClickCount($campaign);
        $sentCount = $this->getEmailSentCount($campaign);
        
        return $sentCount > 0 ? ($clickCount / $sentCount) * 100 : 0;
    }

    /**
     * Calculate email ROI.
     */
    private function calculateEmailROI(EmailCampaign $campaign): float
    {
        $cost = $campaign->budget;
        $conversions = $this->getEmailConversionCount($campaign);
        $avgPropertyValue = 500000; // Mock average property value
        
        $revenue = $conversions * $avgPropertyValue;
        
        return $cost > 0 ? (($revenue - $cost) / $cost) * 100 : 0;
    }

    /**
     * Get cost per email.
     */
    private function getCostPerEmail(EmailCampaign $campaign): float
    {
        $cost = $campaign->budget;
        $sentCount = $this->getEmailSentCount($campaign);
        
        return $sentCount > 0 ? $cost / $sentCount : 0;
    }

    /**
     * Get daily performance data.
     */
    private function getDailyPerformance(EmailCampaign $campaign): array
    {
        // Mock implementation - in real app, this would come from analytics
        $data = [];
        for ($i = 0; $i < 30; $i++) {
            $data[] = [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'sent_count' => rand(10, 100),
                'open_count' => rand(5, 50),
                'click_count' => rand(5, 50),
                'conversion_count' => rand(1, 10),
                'cost' => rand(50, 500),
            ];
        }
        return array_reverse($data);
    }

    /**
     * Get audience performance data.
     */
    private function getAudiencePerformance(EmailCampaign $campaign): array
    {
        // Mock implementation - in real app, this would come from analytics
        return [
                'first_time_buyers' => [
                    'sent_count' => rand(10, 100),
                    'open_count' => rand(5, 50),
                    'click_count' => rand(5, 50),
                    'conversion_count' => rand(1, 10),
                    'conversion_rate' => rand(5, 15),
                ],
                'investors' => [
                    'sent_count' => rand(20, 200),
                    'open_count' => rand(10, 100),
                    'click_count' => rand(10, 100),
                    'conversion_count' => rand(5, 20),
                    'conversion_rate' => rand(10, 30),
                ],
                'families' => [
                    'sent_count' => rand(30, 300),
                    'open_count' => rand(20, 150),
                    'click_count' => rand(20, 150),
                    'conversion_count' => rand(10, 30),
                    'conversion_rate' => rand(5, 20),
                ],
                'professionals' => [
                    'sent_count' => rand(15, 100),
                    'open_count' => rand(5, 50),
                    'click_count' => rand(5, 50),
                    'conversion_count' => rand(1, 10),
                    'conversion_rate' => rand(1, 5),
                ],
                'students' => [
                    'sent_count' => rand(5, 50),
                    'open_count' => rand(2, 20),
                    'click_count' => rand(1, 10),
                    'conversion_count' => rand(0, 5),
                    'conversion_rate' => rand(0, 2),
                ],
                'retirees' => [
                    'sent_count' => rand(5, 20),
                    'open_count' => rand(2, 10),
                    'click_count' => rand(1, 10),
                    'conversion_count' => rand(0, 2),
                    'conversion_rate' => rand(0, 2),
                ],
                'expats' => [
                    'sent_count' => rand(1, 10),
                    'open_count' => rand(0, 5),
                    'click_count' => rand(0, 5),
                    'conversion_count' => rand(0, 2),
                    'conversion_rate' => rand(0, 2),
                ],
            ];
    }

    /**
     * Get time series data.
     */
    private function getTimeSeriesData(EmailCampaign $campaign): array
    {
        // Mock implementation - in real app, this would come from analytics
        $data = [];
        for ($i = 0; $i < 90; $i++) {
            $data[] = [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'sent_count' => rand(10, 100),
                'open_count' => rand(5, 50),
                'click_count' => rand(5, 50),
                'conversion_count' => rand(0, 5),
            ];
        }
        return array_reverse($data);
    }

    /**
     * Duplicate the specified email campaign.
     */
    public function duplicate(EmailCampaign $emailCampaign): JsonResponse
    {
        $newCampaign = $emailCampaign->replicate([
            'title' => $emailCampaign->title . ' (نسخة)',
            'status' => 'draft',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم نسخ الحملة البريد الإلكتروني بنجاح',
            'campaign' => $newCampaign->load('property', 'campaignType', 'emailTemplate'),
        ]);
    }

    /**
     * Export campaign data to various formats.
     */
    public function export(Request $request, EmailCampaign $emailCampaign): JsonResponse
    {
        $format = $request->get('format', 'csv');
        
        $data = [
            'campaign' => $emailCampaign->toArray(),
            'property' => $emailCampaign->property->toArray(),
            'analytics' => $this->getEmailCampaignAnalytics($emailCampaign),
            'assets' => $this->getEmailCampaignAssets($emailCampaign),
        ];

        switch ($format) {
            case 'csv':
                $filename = 'email_campaign_' . $emailCampaign->id . '_export.csv';
                $content = $this->exportToCsv($data);
                break;
            case 'xlsx':
                $filename = 'email_campaign_' . $emailCampaign->id . '_export.xlsx';
                $content = $this->exportToXlsx($data);
                break;
            case 'json':
                $filename = 'email_campaign_' . $emailCampaign->id . '_export.json';
                $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'تنسيق التصدير غير مدعوم',
                ]);
        }

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'content' => $content,
        ]);
    }

    /**
     * Export data to CSV format.
     */
    private function exportToCsv(array $data): string
    {
        $csv = '';
        $headers = array_keys($data['campaign']);
        $csv .= implode(',', $headers) . "\n";
        
        $csv .= implode(',', [
            $data['campaign']['id'],
            $data['campaign']['title'],
            $data['campaign']['subject'],
            $data['campaign']['status'],
            $data['campaign']['sent_status'],
            $data['campaign']['budget'],
            $data['campaign']['sent_count'],
            $data['campaign']['open_count'],
            $data['campaign']['click_count'],
            $data['campaign']['conversion_count'],
            $data['campaign']['start_date'],
            $data['campaign']['end_date'],
        ]) . "\n";
        
        return $csv;
    }

    /**
     * Export data to XLSX format.
     */
    private function exportToXlsx(array $data): string
    {
        // Mock implementation - in real app, this would use a library like Laravel Excel
        return 'Mock XLSX content';
    }
}
