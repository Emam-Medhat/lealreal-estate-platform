@extends('layouts.app')

@section('title', 'رؤى السوق العقاري')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-line me-2"></i>
            رؤى السوق العقاري
        </h1>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newInsightModal">
                <i class="fas fa-plus me-2"></i>رؤية جديدة
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_insights'] ?? 0 }}</h4>
                            <p class="card-text">إجمالي الرؤى</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-lightbulb fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['high_priority_insights'] ?? 0 }}</h4>
                            <p class="card-text">رؤى عالية الأولوية</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['avg_reliability'] ?? 0 }}%</h4>
                            <p class="card-text">متوسط الموثوقية</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shield-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_views'] ?? 0 }}</h4>
                            <p class="card-text">إجمالي المشاهدات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ai.market-insights.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن رؤية..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع الرؤية</label>
                        <select name="insight_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="price_analysis" {{ request('insight_type') == 'price_analysis' ? 'selected' : '' }}>تحليل الأسعار</option>
                            <option value="demand_forecast" {{ request('insight_type') == 'demand_forecast' ? 'selected' : '' }}>توقعات الطلب</option>
                            <option value="investment_opportunity" {{ request('insight_type') == 'investment_opportunity' ? 'selected' : '' }}>فرصة استثمارية</option>
                            <option value="market_trend" {{ request('insight_type') == 'market_trend' ? 'selected' : '' }}>اتجاه السوق</option>
                            <option value="risk_assessment" {{ request('insight_type') == 'risk_assessment' ? 'selected' : '' }}>تقييم المخاطر</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">منطقة السوق</label>
                        <select name="market_area" class="form-select">
                            <option value="">الكل</option>
                            <option value="Riyadh" {{ request('market_area') == 'Riyadh' ? 'selected' : '' }}>الرياض</option>
                            <option value="Jeddah" {{ request('market_area') == 'Jeddah' ? 'selected' : '' }}>جدة</option>
                            <option value="Dammam" {{ request('market_area') == 'Dammam' ? 'selected' : '' }}>الدمام</option>
                            <option value="Mecca" {{ request('market_area') == 'Mecca' ? 'selected' : '' }}>مكة</option>
                            <option value="Medina" {{ request('market_area') == 'Medina' ? 'selected' : '' }}>المدينة</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع العقار</label>
                        <select name="property_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="residential" {{ request('property_type') == 'residential' ? 'selected' : '' }}>سكني</option>
                            <option value="commercial" {{ request('property_type') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                            <option value="industrial" {{ request('property_type') == 'industrial' ? 'selected' : '' }}>صناعي</option>
                            <option value="land" {{ request('property_type') == 'land' ? 'selected' : '' }}>أرض</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('ai.market-insights.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>مسح
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Insights Grid -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">معرض الرؤى</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse ($insights ?? [] as $insight)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title">{{ $insight->insight_type_label }}</h6>
                                        <p class="card-text text-muted small">{{ $insight->market_area }} - {{ $insight->property_type }}</p>
                                    </div>
                                    <div class="ms-2">
                                        @if ($insight->is_high_priority)
                                            <span class="badge bg-danger">عالي الأولوية</span>
                                        @else
                                            <span class="badge bg-{{ $insight->insight_score >= 8 ? 'success' : ($insight->insight_score >= 6 ? 'warning' : 'secondary') }}">
                                                {{ $insight->insight_level }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="d-block text-muted">الجودة</small>
                                            <strong>{{ $insight->insight_score }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="d-block text-muted">الموثوقية</small>
                                            <strong>{{ $insight->reliability_score }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="d-block text-muted">الثقة</small>
                                            <strong>{{ $insight->confidence_level_text }}</strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="card-text">{{ Str::limit($insight->market_trends['description'] ?? 'لا يوجد وصف', 100) }}</p>
                                </div>
                                
                                @if ($insight->investment_opportunities && count($insight->investment_opportunities) > 0)
                                    <div class="mb-3">
                                        <h6 class="text-success">فرص استثمارية:</h6>
                                        <ul class="list-unstyled small">
                                            @foreach ($insight->investment_opportunities as $opportunity)
                                                <li>• {{ $opportunity['type'] }} ({{ $opportunity['expected_roi'] * 100 }}% عائد)</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if ($insight->risk_factors && count($insight->risk_factors) > 0)
                                    <div class="mb-3">
                                        <h6 class="text-warning">عوامل الخطر:</h6>
                                        <ul class="list-unstyled small">
                                            @foreach ($insight->risk_factors as $risk)
                                                <li>• {{ $risk['description'] }} (شدة: {{ $risk['severity'] }}/10)</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $insight->created_at->format('Y-m-d H:i') }}</small>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showInsightDetails({{ $insight->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadInsight({{ $insight->id }})">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد رؤى حالياً</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if (isset($insights) && $insights->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $insights->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- New Insight Modal -->
<div class="modal fade" id="newInsightModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رؤية سوق جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newInsightForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نوع الرؤية *</label>
                            <select name="insight_type" class="form-select" required>
                                <option value="price_analysis">تحليل الأسعار</option>
                                <option value="demand_forecast">توقعات الطلب</option>
                                <option value="investment_opportunity">فرصة استثمارية</option>
                                <option value="market_trend">اتجاه السوق</option>
                                <option value="risk_assessment">تقييم المخاطر</option>
                                <option value="competitor_intelligence">معلومات المنافسين</option>
                                <option value="development_impact">تأثير التطوير</option>
                                <option value="regulatory_impact">تأثير تنظيمي</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">منطقة السوق *</label>
                            <select name="market_area" class="form-select" required>
                                <option value="Riyadh">الرياض</option>
                                <option value="Jeddah">جدة</option>
                                <option value="Dammam">الدمام</option>
                                <option value="Mecca">مكة</option>
                                <option value="Medina">المدينة</option>
                                <option value="Khobar">الخبر</option>
                                <option value="Tabuk">تبوك</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نوع العقار *</label>
                            <select name="property_type" class="form-select" required>
                                <option value="residential">سكني</option>
                                <option value="commercial">تجاري</option>
                                <option value="industrial">صناعي</option>
                                <option value="land">أرض</option>
                                <option value="mixed_use">استخدام مختلط</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نطاق السعر</label>
                            <input type="text" name="price_range" class="form-control" placeholder="مثال: 500000-1000000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الأفق الزمني *</label>
                            <select name="time_horizon" class="form-select" required>
                                <option value="short_term">قصير الأجل (3-6 أشهر)</option>
                                <option value="medium_term">متوسط الأجل (6-12 شهر)</option>
                                <option value="long_term">طويل الأجل (1-3 سنوات)</option>
                                <option value="very_long_term">طويل الأجل جداً (3+ سنوات)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم العقار</label>
                            <select name="property_id" class="form-select">
                                <option value="">اختر العقار (اختياري)</option>
                                @foreach ($properties ?? [] as $property)
                                    <option value="{{ $property->id }}">{{ $property->id }} - {{ $property->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ملاحظات إضافية</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="أدخل أي ملاحظات إضافية..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-lightbulb me-2"></i>إنشاء الرؤية
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Insight Details Modal -->
<div class="modal fade" id="insightDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل الرؤية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="insightDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="printInsight()">
                    <i class="fas fa-print me-2"></i>طباعة التقرير
                </button>
                <button type="button" class="btn btn-success" onclick="shareInsight()">
                    <i class="fas fa-share me-2"></i>مشاركة الرؤية
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentInsightId = null;

// New Insight Form
document.getElementById('newInsightForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإنشاء...';
    
    fetch('{{ route("ai.market-insights.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: data.message,
                confirmButtonText: 'موافق'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: data.message,
                confirmButtonText: 'موافق'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'حدث خطأ أثناء إنشاء الرؤية',
            confirmButtonText: 'موافق'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Show Insight Details
function showInsightDetails(insightId) {
    fetch(`/ai/market-insights/${insightId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentInsightId = insightId;
            displayInsightDetails(data.insight);
            new bootstrap.Modal(document.getElementById('insightDetailsModal')).show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'حدث خطأ أثناء تحميل التفاصيل',
            confirmButtonText: 'موافق'
        });
    });
}

// Display Insight Details
function displayInsightDetails(insight) {
    const content = document.getElementById('insightDetailsContent');
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات أساسية</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>النوع:</strong></td>
                        <td>${insight.insight_type_label}</td>
                    </tr>
                    <tr>
                        <td><strong>منطقة السوق:</strong></td>
                        <td>${insight.market_area}</td>
                    </tr>
                    <tr>
                        <td><strong>نوع العقار:</strong></td>
                        <td>${insight.property_type}</td>
                    </tr>
                    <tr>
                        <td><strong>الأفق الزمني:</strong></td>
                        <td>${insight.time_horizon_label}</td>
                    </tr>
                    <tr>
                        <td><strong>تاريخ الإنشاء:</strong></td>
                        <td>${insight.created_at}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>تقييم الجودة</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>جودة الرؤية:</strong></td>
                        <td>${insight.insight_score} (${insight.insight_level})</td>
                    </tr>
                    <tr>
                        <td><strong>الموثوقية:</strong></td>
                        <td>${insight.reliability_score} (${insight.reliability_level})</td>
                    </tr>
                    <tr>
                        <td><strong>مستوى الثقة:</strong></td>
                        <td>${insight.confidence_level_text}</td>
                    </tr>
                    <tr>
                        <td><strong>الحالة:</strong></td>
                        <td>${insight.status_label}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6>اتجاهات السوق</h6>
                <div class="alert alert-info">
                    <strong>الاتجاه:</strong> ${insight.market_trend_direction}<br>
                    <strong>القوة:</strong> ${insight.market_trends?.strength || 'غير محدد'}<br>
                    <strong>المدة:</strong> ${insight.market_trends?.duration || 'غير محدد'}
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6>تحليل الطلب</h6>
                <div class="alert alert-success">
                    <strong>مستوى الطلب:</strong> ${insight.demand_level}<br>
                    <strong>معدل النمو:</strong> ${insight.demand_analysis?.growth_rate || 'غير محدد'}%<br>
                    <strong>التقلب الموسمي:</strong> ${insight.demand_analysis?.seasonal_variation || 'غير محدد'}%
                </div>
            </div>
            <div class="col-md-6">
                <h6>تحليل العرض</h6>
                <div class="alert alert-warning">
                    <strong>مستوى العرض:</strong> ${insight.supply_level}<br>
                    <strong>معدل الدوران:</strong> ${insight.supply_analysis?.inventory_turnover || 'غير محدد'} يوم<br>
                    <strong>معدل الشواغر:</strong> ${insight.supply_analysis?.vacancy_rate || 'غير محدد'}%
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6>فرص استثمارية</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>الموقع</th>
                                <th>العائد المتوقع</th>
                                <th>الأفق الزمني</th>
                                <th>مستوى الخطر</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(insight.investment_opportunities || []).map(opp => `
                                <tr>
                                    <td>${opp.type}</td>
                                    <td>${opp.location}</td>
                                    <td>${(opp.expected_roi * 100).toFixed(1)}%</td>
                                    <td>${opp.time_horizon}</td>
                                    <td>${opp.risk_level}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6>عوامل الخطر</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>نوع الخطر</th>
                                <th>الوصف</th>
                                <th>الشدة</th>
                                <th>استراتيجية التخفيف</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(insight.risk_factors || []).map(risk => `
                                <tr>
                                    <td>${risk.type}</td>
                                    <td>${risk.description}</td>
                                    <td>${risk.severity}/10</td>
                                    <td>${risk.mitigation}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
}

// Download Insight
function downloadInsight(insightId) {
    window.open(`/ai/market-insights/${insightId}/download`, '_blank');
}

// Print Insight
function printInsight() {
    if (!currentInsightId) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه!',
            text: 'يرجى اختيار رؤية أولاً',
            confirmButtonText: 'موافق'
        });
        return;
    }
    
    const modalContent = document.getElementById('insightDetailsContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>تقرير الرؤية</title>
                <style>
                    body { font-family: Arial, sans-serif; direction: rtl; padding: 20px; }
                    .table { width: 100%; border-collapse: collapse; }
                    .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                    .table th { background-color: #f2f2f2; }
                    .alert { padding: 15px; margin-bottom: 15px; border-radius: 5px; }
                    .alert-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
                    .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
                    .alert-warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
                    .text-center { text-align: center; }
                    .mb-3 { margin-bottom: 15px; }
                    .mt-3 { margin-top: 15px; }
                </style>
            </head>
            <body>
                ${modalContent}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Share Insight
function shareInsight() {
    if (!currentInsightId) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه!',
            text: 'يرجى اختيار رؤية أولاً',
            confirmButtonText: 'موافق'
        });
        return;
    }
    
    // Copy link to clipboard
    const url = `${window.location.origin}/ai/market-insights/${currentInsightId}`;
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'نجح!',
            text: 'تم نسخ الرابط إلى الحافظة',
            confirmButtonText: 'موافق'
        });
    }).catch(err => {
        console.error('Failed to copy: ', err);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'فشل في نسخ الرابط',
            confirmButtonText: 'موافق'
        });
    });
}
</script>
@endpush
