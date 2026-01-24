@extends('layouts.app')

@section('title', 'المستشاري الاستثماري الذكي')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-line me-2"></i>
            المستشاري الاستثماري الذكي
        </h1>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAnalysisModal">
                <i class="fas fa-plus me-2"></i>تحليل جديد
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
                            <h4 class="card-title">{{ $stats['total_analyses'] ?? 0 }}</h4>
                            <p class="card-text">إجمالي التحليلات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-analytics fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $stats['high_potential_analyses'] ?? 0 }}</h4>
                            <p class="card-text">تحليلات عالية الإمكانات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-trending-up fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $stats['avg_roi'] ?? 0 }}%</h4>
                            <p class="card-text">متوسط العائد</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $stats['active_recommendations'] ?? 0 }}</h4>
                            <p class="card-text">توصيات نشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-lightbulb fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ai.investment-advisor.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن تحليل..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع التحليل</label>
                        <select name="analysis_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="opportunity_analysis" {{ request('analysis_type') == 'opportunity_analysis' ? 'selected' : '' }}>تحليل الفرص</option>
                            <option value="risk_assessment" {{ request('analysis_type') == 'risk_assessment' ? 'selected' : '' }}>تقييم المخاطر</option>
                            <option value="portfolio_optimization" {{ request('analysis_type') == 'portfolio_optimization' ? 'selected' : '' }}>تحسين المحفظة</option>
                            <option value="market_comparison" {{ request('analysis_type') == 'market_comparison' ? 'selected' : '' }}>مقارنة السوق</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">مستوى المخاطرة</label>
                        <select name="risk_tolerance" class="form-select">
                            <option value="">الكل</option>
                            <option value="low" {{ request('risk_tolerance') == 'low' ? 'selected' : '' }}>منخفض</option>
                            <option value="medium" {{ request('risk_tolerance') == 'medium' ? 'selected' : '' }}>متوسط</option>
                            <option value="high" {{ request('risk_tolerance') == 'high' ? 'selected' : '' }}>مرتفع</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الأفق الزمني</label>
                        <select name="time_horizon" class="form-select">
                            <option value="">الكل</option>
                            <option value="1month" {{ request('time_horizon') == '1month' ? 'selected' : '' }}>شهر واحد</option>
                            <option value="3months" {{ request('time_horizon') == '3months' ? 'selected' : '' }}>3 أشهر</option>
                            <option value="6months" {{ request('time_horizon') == '6months' ? 'selected' : '' }}>6 أشهر</option>
                            <option value="1year" {{ request('time_horizon') == '1year' ? 'selected' : '' }}>سنة واحدة</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('ai.investment-advisor.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>مسح
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Analyses Grid -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">سجل التحليلات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse ($analyses ?? [] as $analysis)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">{{ $analysis->analysis_type_label }}</h6>
                                <p class="card-text text-muted small">{{ $analysis->property_id }} - {{ $analysis->created_at->format('Y-m-d') }}</p>
                                
                                <div class="row text-center mb-2">
                                    <div class="col-4">
                                        <small class="d-block text-muted">مستوى المخاطرة</small>
                                        <strong>{{ $analysis->risk_tolerance_label }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="d-block text-muted">الأفق الزمني</small>
                                        <strong>{{ $analysis->time_horizon_label }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="d-block text-muted">التوصية</small>
                                        <strong>{{ $analysis->recommendation_level_label }}</strong>
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $analysis->confidence_score >= 80 ? 'success' : ($analysis->confidence_score >= 60 ? 'warning' : 'danger') }}" 
                                             style="width: {{ $analysis->confidence_score }}%"></div>
                                    </div>
                                    <small class="text-muted">مستوى الثقة: {{ $analysis->confidence_score }}%</small>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-{{ $analysis->investment_recommendation_level == 'strong_buy' ? 'success' : ($analysis->investment_recommendation_level == 'buy' ? 'primary' : 'secondary') }}">
                                            {{ $analysis->investment_recommendation_level }}
                                        </span>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showAnalysisDetails({{ $analysis->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadReport({{ $analysis->id }})">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد تحليلات حالياً</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if (isset($analyses) && $analyses->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $analyses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- New Analysis Modal -->
<div class="modal fade" id="newAnalysisModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحليل استثماري جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newAnalysisForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">رقم العقار *</label>
                            <select name="property_id" class="form-select" required>
                                <option value="">اختر العقار</option>
                                @foreach ($properties ?? [] as $property)
                                    <option value="{{ $property->id }}">{{ $property->id }} - {{ $property->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">مبلغ الاستثمار *</label>
                            <input type="number" name="investment_amount" class="form-control" min="10000" step="1000" required>
                            <small class="text-muted">الحد الأدنى: 10,000 ريال</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نوع الاستثمار *</label>
                            <select name="investment_type" class="form-select" required>
                                <option value="buy">شراء</option>
                                <option value="rent">إيجار</option>
                                <option value="flip">شراء وبيع</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الأفق الزمني *</label>
                            <select name="time_horizon" class="form-select" required>
                                <option value="1month">شهر واحد</option>
                                <option value="3months">3 أشهر</option>
                                <option value="6months">6 أشهر</option>
                                <option value="1year">سنة واحدة</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">مستوى المخاطرة *</label>
                            <select name="risk_tolerance" class="form-select" required>
                                <option value="low">منخفض</option>
                                <option value="medium">متوسط</option>
                                <option value="high">مرتفع</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">عوامل السوق (اختياري)</label>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="number" name="market_factors[growth_rate]" class="form-control" placeholder="نمو النمو %" step="0.1">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="market_factors[inflation_rate]" class="form-control" placeholder="معدل التضخم %" step="0.1">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="market_factors[interest_rate]" class="form-control" placeholder="سعر الفائدة %" step="0.1">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="market_factors[unemployment_rate]" class="form-control" placeholder="معدل البطالة %" step="0.1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">ميزات العقار (اختياري)</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="number" name="property_features[bedrooms]" class="form-control" placeholder="غرف نوم">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="property_features[bathrooms]" class="form-control" placeholder="دورات مياه">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="property_features[area]" class="form-control" placeholder="المساحة (م²)">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="أدخل أي ملاحظات إضافية..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-line me-2"></i>بدء التحليل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Analysis Details Modal -->
<div class="modal fade" id="analysisDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل التحليل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="analysisDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="printReport()">
                    <i class="fas fa-print me-2"></i>طباعة التقرير
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentAnalysisId = null;

// New Analysis Form
document.getElementById('newAnalysisForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري التحليل...';
    
    fetch('{{ route("ai.investment-advisor.analyze") }}', {
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
            text: 'حدث خطأ أثناء التحليل',
            confirmButtonText: 'موافق'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Show Analysis Details
function showAnalysisDetails(analysisId) {
    fetch(`/ai/investment-advisor/${analysisId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentAnalysisId = analysisId;
            displayAnalysisDetails(data.analysis);
            new bootstrap.Modal(document.getElementById('analysisDetailsModal')).show();
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

// Display Analysis Details
function displayAnalysisDetails(analysis) {
    const content = document.getElementById('analysisDetailsContent');
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات أساسية</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>رقم العقار:</strong></td>
                        <td>${analysis.property_id}</td>
                    </tr>
                    <tr>
                        <td><strong>نوع التحليل:</strong></td>
                        <td>${analysis.analysis_type_label}</td>
                    </tr>
                    <tr>
                        <td><strong>مبلغ الاستثمار:</strong></td>
                        <td>${number_format(analysis.investment_amount, 2)} ريال</td>
                    </tr>
                    <tr>
                        <td><strong>نوع الاستثمار:</strong></td>
                        <td>${analysis.investment_type}</td>
                    </tr>
                    <tr>
                        <td><strong>الأفق الزمني:</strong></td>
                        <td>${analysis.time_horizon_label}</td>
                    </tr>
                    <tr>
                        <td><strong>مستوى المخاطرة:</strong></td>
                        <td>${analysis.risk_tolerance_label}</td>
                    </tr>
                    <tr>
                        <td><strong>التاريخ:</strong></td>
                        <td>${analysis.created_at}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>نتائج التحليل</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>التوصية:</strong></td>
                        <td>${analysis.investment_recommendation_level_label}</td>
                    </tr>
                    <tr>
                        <td><strong>مستوى الثقة:</strong></td>
                        <td>${analysis.confidence_score}% (${analysis.confidence_level_text})</td>
                    </tr>
                    <tr>
                        <td><strong>القيمة المتوقعة:</strong></td>
                        <td>${number_format(analysis.projected_value, 2)} ريال</td>
                    </tr>
                    <tr>
                        <td><strong>العائد المتوقع:</strong></td>
                        <td>${analysis.roi_percentage}%</td>
                    </tr>
                    <tr>
                        <td><strong>فترة استرداد رأس المال:</strong></td>
                        <td>${analysis.payback_period} سنة</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6>عوامل التحليل</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>العامل</th>
                                <th>القيمة</th>
                                <th>الأثر</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(analysis.optimization_factors || []).map(factor => `
                                <tr>
                                    <td>${factor.factor}</td>
                                    <td>${factor.value}</td>
                                    <td>${factor.impact}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6>توقعات السوق</h6>
                <div class="alert alert-info">
                    <strong>الاتجاه:</strong> ${analysis.market_outlook.short_term.trend}<br>
                    <strong>المتوسط:</strong> ${analysis.market_outlook.medium_term.trend}<br>
                    <strong>طويل:</strong> ${analysis.market_outlook.long_term.trend}
                </div>
            </div>
            <div class="col-md-6">
                <h6>تقييم المخاطر</h6>
                <div class="alert alert-warning">
                    <strong>مستوى المخاطر:</strong> ${analysis.risk_assessment.risk_level}<br>
                    <strong>عوامل المخاطر:</strong><br>
                    <ul class="list-unstyled small">
                        ${(analysis.risk_assessment.risk_factors || []).map(risk => `<li>• ${risk.factor}: ${risk.severity}/10</li>`).join('')}
                    </ul>
                    <strong>استراتيجيات التخفيف:</strong><br>
                    <ul class="list-unstyled small">
                        ${(analysis.risk_assessment.mitigation_strategies || []).map(strategy => `<li>• ${strategy}</li>`).join('')}
                    </ul>
                </div>
            </div>
        </div>
    `;
}

// Download Report
function downloadReport(analysisId) {
    window.open(`/ai/investment-advisor/${analysisId}/download`, '_blank');
}

// Print Report
function printReport() {
    if (!currentAnalysisId) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه!',
            text: 'يرجى اختيار تحليل أولاً',
            confirmButtonText: 'موافق'
        });
        return;
    }
    
    const modalContent = document.getElementById('analysisDetailsContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>تقرير التحليل الاستثماري</title>
                <style>
                    body { font-family: Arial, sans-serif; direction: rtl; padding: 20px; }
                    .table { width: 100%; border-collapse: collapse; }
                    .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                    .table th { background-color: #f2f2f2; }
                    .alert { padding: 15px; margin-bottom: 15px; border-radius: 5px; }
                    .alert-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
                    .alert-warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
                    .text-success { color: #155724; }
                    .text-warning { color: #856404; }
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
</script>
@endpush
