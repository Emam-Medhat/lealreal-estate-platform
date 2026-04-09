<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\StoreInvestorRequest;
use App\Http\Requests\Investor\UpdateInvestorRequest;
use App\Models\Investor;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        $investors = Investor::with(['user'])
            ->when($request->search, function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->investment_range, function ($query, $range) {
                $ranges = [
                    'small' => [0, 10000],
                    'medium' => [10001, 50000],
                    'large' => [50001, 100000],
                    'enterprise' => [100001, 999999999]
                ];
                if (isset($ranges[$range])) {
                    $query->whereBetween('total_invested', $ranges[$range]);
                }
            })
            ->latest()
            ->paginate(20);

        return view('investor.index', compact('investors'));
    }

    public function create()
    {
        return view('investor.create');
    }

    public function store(StoreInvestorRequest $request)
    {
        try {
            $investor = Investor::create([
                'user_id' => Auth::id(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'investor_type' => $request->investor_type,
                'status' => $request->status ?? 'active',
                'total_invested' => (float) ($request->total_invested ?? 0),
                'total_returns' => (float) ($request->total_returns ?? 0),
                'risk_tolerance' => $request->risk_tolerance,
                'investment_goals' => $request->investment_goals ?? [],
                'preferred_sectors' => $request->preferred_sectors ?? [],
                'experience_years' => $request->experience_years,
                'accredited_investor' => $request->accredited_investor ?? false,
                'verification_status' => $request->verification_status ?? 'pending',
                'address' => $request->address ?? [],
                'social_links' => $request->social_links ?? [],
                'bio' => $request->bio,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Refresh the model to get the correct relationships
            $investor->refresh();

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $picturePath = $request->file('profile_picture')->store('investor-pictures', 'public');
                $investor->update(['profile_picture' => $picturePath]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_investor',
                'description' => "Created investor: {$investor->first_name} {$investor->last_name}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('investors.show', $investor)
                ->with('success', 'Investor created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create investor: ' . $e->getMessage());
        }
    }

    public function show(Investor $investor)
    {
        $this->authorize('view', $investor);
        
        $investor->load(['user', 'portfolios', 'transactions']);
        
        return view('investor.show', compact('investor'));
    }

    public function edit(Investor $investor)
    {
        $this->authorize('update', $investor);
        
        return view('investor.edit', compact('investor'));
    }

    public function update(UpdateInvestorRequest $request, Investor $investor)
    {
        $this->authorize('update', $investor);
        
        $investor->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'investor_type' => $request->investor_type,
            'status' => $request->status,
            'total_invested' => $request->total_invested,
            'total_returns' => $request->total_returns,
            'risk_tolerance' => $request->risk_tolerance,
            'investment_goals' => $request->investment_goals ?? [],
            'preferred_sectors' => $request->preferred_sectors ?? [],
            'experience_years' => $request->experience_years,
            'accredited_investor' => $request->accredited_investor,
            'verification_status' => $request->verification_status,
            'address' => $request->address ?? [],
            'social_links' => $request->social_links ?? [],
            'bio' => $request->bio,
            'updated_by' => Auth::id(),
        ]);

        // Handle profile picture update
        if ($request->hasFile('profile_picture')) {
            if ($investor->profile_picture) {
                Storage::disk('public')->delete($investor->profile_picture);
            }
            $picturePath = $request->file('profile_picture')->store('investor-pictures', 'public');
            $investor->update(['profile_picture' => $picturePath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investor',
            'details' => "Updated investor: {$investor->first_name} {$investor->last_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investors.show', $investor)
            ->with('success', 'Investor updated successfully.');
    }

    public function destroy(Investor $investor)
    {
        $this->authorize('delete', $investor);
        
        $investorName = $investor->first_name . ' ' . $investor->last_name;
        
        // Delete profile picture
        if ($investor->profile_picture) {
            Storage::disk('public')->delete($investor->profile_picture);
        }
        
        $investor->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_investor',
            'details' => "Deleted investor: {$investorName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('investors.index')
            ->with('success', 'Investor deleted successfully.');
    }

    public function updateStatus(Request $request, Investor $investor): JsonResponse
    {
        $this->authorize('update', $investor);
        
        $request->validate([
            'status' => 'required|in:active,inactive,suspended,verified',
        ]);

        $investor->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investor_status',
            'description' => "Updated investor '{$investor->first_name} {$investor->last_name}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Investor status updated successfully'
        ]);
    }

    public function updateVerification(Request $request, Investor $investor): JsonResponse
    {
        $this->authorize('update', $investor);
        
        $request->validate([
            'verification_status' => 'required|in:pending,verified,rejected',
        ]);

        $investor->update(['verification_status' => $request->verification_status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investor_verification',
            'description' => "Updated investor '{$investor->first_name} {$investor->last_name}' verification to {$request->verification_status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'verification_status' => $request->verification_status,
            'message' => 'Investor verification status updated successfully'
        ]);
    }

    public function getInvestorStats()
    {
        $stats = [
            'total_investors' => Investor::count(),
            'active_investors' => Investor::where('status', 'active')->count(),
            'verified_investors' => Investor::where('verification_status', 'verified')->count(),
            'by_type' => Investor::groupBy('investor_type')
                ->selectRaw('investor_type, count(*) as count')
                ->pluck('count', 'investor_type'),
            'by_risk_tolerance' => Investor::groupBy('risk_tolerance')
                ->selectRaw('risk_tolerance, count(*) as count')
                ->pluck('count', 'risk_tolerance'),
            'average_investment' => Investor::avg('total_invested'),
            'total_invested' => Investor::sum('total_invested'),
            'total_returns' => Investor::sum('total_returns'),
        ];

        return view('investor.stats', compact('stats'));
    }

    public function getInvestorStatsApi(): JsonResponse
    {
        $stats = [
            'total_investors' => Investor::count(),
            'active_investors' => Investor::where('status', 'active')->count(),
            'verified_investors' => Investor::where('verification_status', 'verified')->count(),
            'by_type' => Investor::groupBy('investor_type')
                ->selectRaw('investor_type, count(*) as count')
                ->pluck('count', 'investor_type'),
            'by_risk_tolerance' => Investor::groupBy('risk_tolerance')
                ->selectRaw('risk_tolerance, count(*) as count')
                ->pluck('count', 'risk_tolerance'),
            'average_investment' => Investor::avg('total_invested'),
            'total_invested' => Investor::sum('total_invested'),
            'total_returns' => Investor::sum('total_returns'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getInvestmentOpportunities()
    {
        // Get real opportunities from database
        $opportunities = \App\Models\InvestmentOpportunity::where('status', 'active')
            ->orderBy('featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();

        return view('investor.opportunities', compact('opportunities'));
    }

    public function getInvestmentFunds()
    {
        // Get real funds from database
        $funds = \App\Models\InvestmentFund::where('status', 'active')
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('investor.funds', compact('funds'));
    }

    public function getCrowdfundingProjects()
    {
        // For now, create sample data for crowdfunding projects
        $projects = [
            [
                'id' => 1,
                'title' => 'مشروع تطوير العقارات السكنية',
                'description' => 'مشروع طموح لتطوير 100 وحدة سكنية في منطقة ناشئة بتكلفة إجمالية 5 مليون ريال',
                'category' => 'real_estate',
                'target_amount' => 5000000,
                'current_amount' => 3200000,
                'min_investment' => 10000,
                'max_investment' => 500000,
                'expected_return' => 12.5,
                'duration' => '24 شهر',
                'risk_level' => 'medium',
                'investors_count' => 67,
                'days_left' => 45,
                'location' => 'الرياض، السعودية',
                'featured' => true,
                'status' => 'active',
                'created_at' => now()->subMonths(2),
                'image_url' => '/images/crowdfunding/real-estate.jpg'
            ],
            [
                'id' => 2,
                'title' => 'منصة تعليمية متطورة',
                'description' => 'تطوير منصة تعليمية تستخدم الذكاء الاصطناعي لتخصيص المحتوى التعليمي للطلاب',
                'category' => 'technology',
                'target_amount' => 2000000,
                'current_amount' => 1450000,
                'min_investment' => 5000,
                'max_investment' => 100000,
                'expected_return' => 18.0,
                'duration' => '36 شهر',
                'risk_level' => 'high',
                'investors_count' => 124,
                'days_left' => 30,
                'location' => 'دبي، الإمارات',
                'featured' => true,
                'status' => 'active',
                'created_at' => now()->subMonth(),
                'image_url' => '/images/crowdfunding/education.jpg'
            ],
            [
                'id' => 3,
                'title' => 'مزرعة عضوية حديثة',
                'description' => 'إنشاء مزرعة عضوية حديثة لإنتاج الخضروات العضوية بمعايير عالمية',
                'category' => 'agriculture',
                'target_amount' => 1500000,
                'current_amount' => 890000,
                'min_investment' => 2500,
                'max_investment' => 75000,
                'expected_return' => 15.5,
                'duration' => '18 شهر',
                'risk_level' => 'medium',
                'investors_count' => 89,
                'days_left' => 60,
                'location' => 'القصيم، السعودية',
                'featured' => false,
                'status' => 'active',
                'created_at' => now()->subWeeks(3),
                'image_url' => '/images/crowdfunding/agriculture.jpg'
            ],
            [
                'id' => 4,
                'title' => 'مقهى specialty متخصص',
                'description' => 'تأسيس سلسلة مقاهي specialty متخصصة في القهوة والوجبات الخفيفة الفاخرة',
                'category' => 'food_beverage',
                'target_amount' => 800000,
                'current_amount' => 620000,
                'min_investment' => 1000,
                'max_investment' => 50000,
                'expected_return' => 22.0,
                'duration' => '12 شهر',
                'risk_level' => 'high',
                'investors_count' => 156,
                'days_left' => 20,
                'location' => 'جدة، السعودية',
                'featured' => false,
                'status' => 'active',
                'created_at' => now()->subWeeks(),
                'image_url' => '/images/crowdfunding/cafe.jpg'
            ],
            [
                'id' => 5,
                'title' => 'تطبيق صحي موبايل',
                'description' => 'تطبيق صحي شامل يربط المرضى بالأطباء ويقدم خدمات طبية عن بعد',
                'category' => 'healthcare',
                'target_amount' => 3000000,
                'current_amount' => 2100000,
                'min_investment' => 15000,
                'max_investment' => 200000,
                'expected_return' => 25.0,
                'duration' => '30 شهر',
                'risk_level' => 'high',
                'investors_count' => 203,
                'days_left' => 15,
                'location' => 'المنامة، عمان',
                'featured' => true,
                'status' => 'active',
                'created_at' => now()->subDays(10),
                'image_url' => '/images/crowdfunding/healthcare.jpg'
            ],
            [
                'id' => 6,
                'title' => 'ورشة تصنيع أثاث مخصص',
                'description' => 'إنشاء ورشة متخصصة في تصنيع الأثاث المخصص والتصاميم الفريدة',
                'category' => 'manufacturing',
                'target_amount' => 1200000,
                'current_amount' => 450000,
                'min_investment' => 3000,
                'max_investment' => 60000,
                'expected_return' => 20.0,
                'duration' => '15 شهر',
                'risk_level' => 'medium',
                'investors_count' => 45,
                'days_left' => 75,
                'location' => 'الدمام، السعودية',
                'featured' => false,
                'status' => 'active',
                'created_at' => now()->subDays(5),
                'image_url' => '/images/crowdfunding/furniture.jpg'
            ]
        ];

        // Convert to collection for dynamic behavior
        $projects = collect($projects);

        return view('investor.crowdfunding', compact('projects'));
    }

    public function getCrowdfundingProjectDetails($projectId)
    {
        // Find the project from our sample data
        $projects = [
            [
                'id' => 1,
                'title' => 'مشروع تطوير العقارات السكنية',
                'description' => 'مشروع طموح لتطوير 100 وحدة سكنية في منطقة ناشئة بتكلفة إجمالية 5 مليون ريال. المشروع يهدف إلى توفير وحدات سكنية عالية الجودة بأسعار تنافسية للمستثمرين. يتضمن المشروع تصميمات حديثة ومرافق حديثة، مع تركيز على الاستدامة وكفاءة الطاقة.',
                'category' => 'real_estate',
                'target_amount' => 5000000,
                'current_amount' => 3200000,
                'min_investment' => 10000,
                'max_investment' => 500000,
                'expected_return' => 12.5,
                'duration' => '24 شهر',
                'risk_level' => 'medium',
                'investors_count' => 67,
                'days_left' => 45,
                'location' => 'الرياض، السعودية',
                'featured' => true,
                'status' => 'active',
                'created_at' => now()->subMonths(2),
                'image_url' => '/images/crowdfunding/real-estate.jpg',
                'team' => [
                    ['name' => 'أحمد محمد', 'role' => 'مدير المشروع', 'experience' => '15 عام في تطوير العقارات'],
                    ['name' => 'فاطمة العلي', 'role' => 'مديرة مالية', 'experience' => '10 سنوات في التمويل العقاري'],
                    ['name' => 'محمد خالد', 'role' => 'مهندس معماري', 'experience' => '12 عام في التصميم']
                ],
                'timeline' => [
                    ['date' => '2024-01-15', 'title' => 'إطلاق المشروع', 'description' => 'بدء التخطيط والإعداد للمشروع'],
                    ['date' => '2024-03-01', 'title' => 'بدء التمويل', 'description' => 'فتح باب التمويل للمستثمرين'],
                    ['date' => '2024-06-01', 'title' => 'بدء التنفيذ', 'description' => 'بدء أعمال البناء والتطوير'],
                    ['date' => '2024-12-01', 'title' => 'التسليم', 'description' => 'التسليم المتوقع للوحدات السكنية']
                ],
                'documents' => [
                    ['name' => 'دراسة جدوى فنية', 'type' => 'PDF', 'size' => '2.5 MB'],
                    ['name' => 'خطة عمل المشروع', 'type' => 'PDF', 'size' => '1.8 MB'],
                    ['name' => 'رخصم مالي', 'type' => 'Excel', 'size' => '1.2 MB']
                ],
                'faqs' => [
                    ['question' => 'ما هو الحد الأدنى للاستثمار؟', 'answer' => 'الحد الأدنى للاستثمار هو 10,000 ريال سعودي.'],
                    ['question' => 'ما هو العائد المتوقع؟', 'answer' => 'العائد المتوقع هو 12.5% سنويا.'],
                    ['question' => 'كم مدة الاستثمار؟', 'answer' => 'مدة الاستثمار هي 24 شهر.'],
                    ['question' => 'ما هو مستوى المخاطرة؟', 'answer' => 'مستوى المخاطرة متوسط.']
                ]
            ],
            [
                'id' => 2,
                'title' => 'منصة تعليمية متطورة',
                'description' => 'تطوير منصة تعليمية تستخدم الذكاء الاصطناعي لتخصيص المحتوى التعليمي للطلاب من المرحلة الابتدائية حتى الجامعية. المنصة تهدف إلى ثورة التعليم من خلال توفير تجربة تعلم مخصصة وتفاعلية تتكيف مع احتياجات كل طالب.',
                'category' => 'technology',
                'target_amount' => 2000000,
                'current_amount' => 1450000,
                'min_investment' => 5000,
                'max_investment' => 100000,
                'expected_return' => 18.0,
                'duration' => '36 شهر',
                'risk_level' => 'high',
                'investors_count' => 124,
                'days_left' => 30,
                'location' => 'دبي، الإمارات',
                'featured' => true,
                'status' => 'active',
                'created_at' => now()->subMonth(),
                'image_url' => '/images/crowdfunding/education.jpg',
                'team' => [
                    ['name' => 'سارة أحمد', 'role' => 'مؤسسة ومديرة تنفيذية', 'experience' => '20 عام في التعليم والتكنولوجيا'],
                    ['name' => 'خالد سالم', 'role' => 'CTO التقني', 'experience' => '15 عام في تطوير البرمجيات'],
                    ['name' => 'نورة علي', 'role' => 'مديرة منتجات', 'experience' => '8 سنوات في التسويق']
                ],
                'timeline' => [
                    ['date' => '2024-02-01', 'title' => 'تطوير النموذج الأولي', 'description' => 'إطلاق تطوير النموذج الأولي للمنصة'],
                    ['date' => '2024-04-01', 'title' => 'اختبار تجريبي', 'description' => 'إجراء اختبارات تجريبية مع المدارس والطلاب'],
                    ['date' => '2024-06-01', 'title' => 'إطلاق عام', 'description' => 'إطلاق المنصة بشكل رسمي'],
                    ['date' => '2025-06-01', 'title' => 'التوسع الكامل', 'description' => 'التوسع ليشمل جميع المراحل التعليمية']
                ],
                'documents' => [
                    ['name' => 'خطة العمل', 'type' => 'PDF', 'size' => '3.2 MB'],
                    ['name' => 'دراسة السوق', 'type' => 'PDF', 'size' => '2.8 MB'],
                    ['name' => 'عرض تقني', 'type' => 'PPT', 'size' => '5.1 MB']
                ],
                'faqs' => [
                    ['question' => 'ما هي المنصة؟', 'answer' => 'منصة تعليمية تستخدم الذكاء الاصطناعي.'],
                    ['question' => 'من هي الفئة المستهدفة؟', 'answer' => 'الطلاب من المرحلة الابتدائية حتى الجامعية.'],
                    ['question' => 'ما هي التكنولوجيا المستخدمة؟', 'answer' => 'الذكاء الاصطناعي وتعلم الآلة.'],
                    ['question' => 'ما هو العائد المتوقع؟', 'answer' => 'العائد المتوقع هو 18% سنويا.']
                ]
            ],
            [
                'id' => 3,
                'title' => 'مزرعة عضوية حديثة',
                'description' => 'إنشاء مزرعة عضوية حديثة لإنتاج الخضروات العضوية بمعايير عالمية. المشروع يهدف إلى تلبية الطلب المتزايد على المنتجات العضوية في السوق المحلي والخليجي، مع التركيز على الجودة والاستدامة.',
                'category' => 'agriculture',
                'target_amount' => 1500000,
                'current_amount' => 890000,
                'min_investment' => 2500,
                'max_investment' => 75000,
                'expected_return' => 15.5,
                'duration' => '18 شهر',
                'risk_level' => 'medium',
                'investors_count' => 89,
                'days_left' => 60,
                'location' => 'القصيم، السعودية',
                'featured' => false,
                'status' => 'active',
                'created_at' => now()->subWeeks(3),
                'image_url' => '/images/crowdfunding/agriculture.jpg',
                'team' => [
                    ['name' => 'عبدالله محمد', 'role' => 'مدير المزرعة', 'experience' => '12 عام في الزراعة العضوية'],
                    ['name' => 'مريم أحمد', 'role' => 'خبيرة في الزراعة', 'experience' => '8 سنوات في الزراعة العضوية'],
                    ['name' => 'سالم عبدالعزيز', 'role' => 'خبير تسويق', 'experience' => '10 سنوات في تسويق المزارع']
                ],
                'timeline' => [
                    ['date' => '2024-01-15', 'title' => 'تجهيز الأرض', 'description' => 'تجهيز الأرض وتجهيز البنية التحتية'],
                    ['date' => '2024-03-01', 'title' => 'الزراعة الأولى', 'description' => 'بدء الزراعة الأولى للمحصول العضوي'],
                    ['date' => '2024-06-01', 'title' => 'التوسع', 'description' => 'توسيع مساحة الزراعة وزيادة المحاصيل'],
                    ['date' => '2024-09-01', 'title' => 'الحصاد الأول', 'description' => 'الحصاد الأول للمنتجات العضوية']
                ],
                'documents' => [
                    ['name' => 'خطة المزرعة', 'type' => 'PDF', 'size' => '2.1 MB'],
                    ['name' => 'شهادة عضوية', 'type' => 'PDF', 'size' => '1.5 MB'],
                    ['name' => 'تحليل التربة', 'type' => 'PDF', 'size' => '3.3 MB']
                ],
                'faqs' => [
                    ['question' => 'ما هي المزرعة؟', 'answer' => 'مزرعة عضوية لإنتاج الخضروات العضوية.'],
                    ['question' => 'ما هي المنتجات؟', 'answer' => 'خضروات عضوية معتمدة.'],
                    ['question' => 'ما هي الشهادات؟', 'answer' => 'شهادات عضوية معتمدة.'],
                    ['question' => 'ما هو العائد؟', 'answer' => 'العائد المتوقع هو 15.5% سنويا.']
                ]
            ],
            [
                'id' => 4,
                'title' => 'مقهى specialty متخصص',
                'description' => 'تأسيس سلسلة مقاهي specialty متخصصة في القهوة والوجبات الخفيفة الفاخرة. المشروع يهدف إلى إنشاء 3 فروع في المواقع الاستراتيجية خلال السنة الأولى، مع التركيز على الجودة العالية والخدمة المميزة.',
                'category' => 'food_beverage',
                'target_amount' => 800000,
                'current_amount' => 620000,
                'min_investment' => 1000,
                'max_investment' => 50000,
                'expected_return' => 22.0,
                'duration' => '12 شهر',
                'risk_level' => 'high',
                'investors_count' => 156,
                'days_left' => 20,
                'location' => 'جدة، السعودية',
                'featured' => false,
                'status' => 'active',
                'created_at' => now()->subWeeks(),
                'image_url' => '/images/crowdfunding/cafe.jpg',
                'team' => [
                    ['name' => 'خالد عبدالله', 'role' => 'مؤسسس ومدير عام', 'experience' => '10 سنوات في إدارة المطاعم'],
                    ['name' => 'سارة محمد', 'role' => 'مديرة عمليات', 'experience' => '8 سنوات في إدارة المطاعم'],
                    ['name' => 'أحمد علي', 'role' => 'رئيس الطهاة', 'experience' => '12 عام في الطهي']
                ],
                'timeline' => [
                    ['date' => '2024-03-01', 'title' => 'تأسيس الفرع الأول', 'description' => 'افتتاح أول مقهى specialty'],
                    ['date' => '2024-05-01', 'title' => 'توسع النشاط', 'description' => 'توسع نطاق العمل وزيادة الموظفين'],
                    ['date' => '2024-08-01', 'title' => 'الفرع الثاني', 'description' => 'افتتاح الفرع الثاني في موقع استراتيجي'],
                    ['date' => '2024-11-01', 'title' => 'الفرع الثالث', 'description' => 'افتتاح الفرع الثالث']
                ],
                'documents' => [
                    ['name' => 'خطة العمل', 'type' => 'PDF', 'size' => '1.8 MB'],
                    ['name' => 'قائمة الطعام', 'type' => 'PDF', 'size' => '1.2 MB'],
                    ['name' => 'رخصم العلامة التجارية', 'type' => 'PDF', 'size' => '2.5 MB']
                ],
                'faqs' => [
                    ['question' => 'ما هو المقهى؟', 'answer' => 'مقهى specialty متخصص في القهوة.'],
                    ['question' => 'ما هي القائمة؟', 'answer' => 'قائمة منتجات specialty عالية الجودة.'],
                    ['question' => 'ما هو العائد؟', 'answer' => 'العائد المتوقع هو 22% سنويا.'],
                    ['question' => 'كم عدد الفروع؟', 'answer' => '3 فروع في السنة الأولى.']
                ]
            ],
            [
                'id' => 5,
                'title' => 'تطبيق صحي موبايل',
                'description' => 'تطبيق صحي شامل يربط المرضى بالأطباء ويقدم خدمات طبية عن بعد. التطبيق يشمل استشارات طبية، وصفات إلكترونية، ومتابعة الحالات الصحية، مع التركيز على سهولة الاستخدام والخصوصية.',
                'category' => 'healthcare',
                'target_amount' => 3000000,
                'current_amount' => 2100000,
                'min_investment' => 15000,
                'max_investment' => 200000,
                'expected_return' => 25.0,
                'duration' => '30 شهر',
                'risk_level' => 'high',
                'investors_count' => 203,
                'days_left' => 15,
                'location' => 'المنامة، عمان',
                'featured' => true,
                'status' => 'active',
                'created_at' => now()->subDays(10),
                'image_url' => '/images/crowdfunding/healthcare.jpg',
                'team' => [
                    ['name' => 'د. محمد أحمد', 'role' => 'مدير طبي ومؤسسس', 'experience' => '15 عام في الطب الرقمي'],
                    ['name' => 'نورة سالم', 'role' => 'مديرة تقنية', 'experience' => '10 سنوات في تطوير التطبيقات الصحية'],
                    ['name' => 'خالد عمر', 'role' => 'مدير مالي', 'experience' => '12 عام في التمويل الصحي']
                ],
                'timeline' => [
                    ['date' => '2024-01-01', 'title' => 'تطوير التطبيق', 'description' => 'بدء تطوير التطبيق الأساسي'],
                    ['date' => '2024-04-01', 'title' => 'اختبار سريري', 'description' => 'إجراء اختبارات سريرية مع الأطباء'],
                    ['date' => '2024-07-01', 'title' => 'إطلاق تجريبي', 'description' => 'إطلاق تجريبي مع مجموعة محدودة من المستخدمين'],
                    ['date' => '2024-10-01', 'title' => 'الإطلاق الرسمي', 'description' => 'الإطلاق الرسمي للتطبيق']
                ],
                'documents' => [
                    ['name' => 'ملف تقني', 'type' => 'PDF', 'size' => '4.2 MB'],
                    ['name' => 'شهادة التسجيل', 'type' => 'PDF', 'size' => '1.8 MB'],
                    ['name' => 'موافقات الأطباء', 'type' => 'PDF', 'size' => '2.1 MB']
                ],
                'faqs' => [
                    ['question' => 'ما هو التطبيق؟', 'answer' => 'تطبيق صحي يربط المرضى بالأطباء.'],
                    ['question' => 'ما هي الخدمات؟', 'answer' => 'استشارات طبية وخدمات عن بعد.'],
                    ['question' => 'هل هو آمن؟', 'answer' => 'نعم، يتبع معايير الأمان والخصوصية.'],
                    ['question' => 'ما هو العائد؟', 'answer' => 'العائد المتوقع هو 25% سنويا.']
                ]
            ],
            [
                'id' => 6,
                'title' => 'ورشة تصنيع أثاث مخصص',
                'description' => 'إنشاء ورشة متخصصة في تصنيع الأثاث المخصص والتصاميمات الفريدة. الورشة تهدف إلى تلبية طلب السوق المتزايد على الأثاث الفريد والمصممات المبتكرة، مع التركيز على الجودة العالية والصناعة الحرفية.',
                'category' => 'manufacturing',
                'target_amount' => 1200000,
                'current_amount' => 450000,
                'min_investment' => 3000,
                'max_investment' => 60000,
                'expected_return' => 20.0,
                'duration' => '15 شهر',
                'risk_level' => 'medium',
                'investors_count' => 45,
                'days_left' => 75,
                'location' => 'الدمام، السعودية',
                'featured' => false,
                'status' => 'active',
                'created_at' => now()->subDays(5),
                'image_url' => '/images/crowdfunding/furniture.jpg',
                'team' => [
                    ['name' => 'عبدالله سالم', 'role' => 'مدير الورشة', 'experience' => '18 عام في تصنيع الأثاث'],
                    ['name' => 'فاطمة علي', 'role' => 'مصممة أثاث', 'experience' => '10 سنوات في التصميم'],
                    ['name' => 'محمد خالد', 'role' => 'رئيس الحرفيين', 'experience' => '15 عام في النجارة']
                ],
                'timeline' => [
                    ['date' => '2024-02-01', 'title' => 'تجهيز الورشة', 'description' => 'تجهيز الورشة بالمعدات اللازمة'],
                    ['date' => '2024-04-01', 'title' => 'التصاميم الأول', 'description' => 'بدء تصميم أول مجموعة من الأثاث'],
                    ['date' => '2024-06-01', 'title' => 'التصنيع', 'description' => 'بدء تصنيع الأثاث المخصصة'],
                    ['date' => '2024-09-01', 'title' => 'التسويق الأول', 'description' => 'تسويق وبيع أول مجموعة من الأثاث']
                ],
                'documents' => [
                    ['name' => 'خطة الورشة', 'type' => 'PDF', 'size' => '2.8 MB'],
                    ['name' => 'كتيالogue التصاميم', 'type' => 'PDF', 'size' => '4.1 MB'],
                    ['name' => 'شهادة الجودة', 'type' => 'PDF', 'size' => '1.9 MB']
                ],
                'faqs' => [
                    ['question' => 'ما هي الورشة؟', 'answer' => 'ورشة متخصصة في تصنيع الأثاث المخصص.'],
                    ['question' => 'ما هي المنتجات؟', 'answer' => 'أثاث مخصص وتصاميمات فريدة.'],
                    ['question' => 'ما هي المواد؟', 'answer' => 'مواد عالية الجودة ومستدامة.'],
                    ['question' => 'ما هو العائد؟', 'answer' => 'العائد المتوقع هو 20% سنويا.']
                ]
            ]
        ];

        // Find the project by ID
        $project = collect($projects)->firstWhere('id', $projectId);
        
        if (!$project) {
            abort(404, 'المشروع غير موجود');
        }

        return view('investor.crowdfunding.show', compact('project'));
    }

    public function exportInvestors(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,inactive,suspended,verified',
            'investor_type' => 'nullable|string|max:50',
        ]);

        $query = Investor::with(['user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->investor_type) {
            $query->where('investor_type', $request->investor_type);
        }

        $investors = $query->get();

        $filename = "investors_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $investors,
            'filename' => $filename,
            'message' => 'Investors exported successfully'
        ]);
    }
}
