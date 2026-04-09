@extends('layouts.app')

@section('title', 'تقييم العقارات بالذكاء الاصطناعي')

@push('styles')
<style>
/* Custom Styles for AI Property Valuation Page */
.valuation-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.valuation-header h1 {
    margin: 0;
    font-weight: 700;
    font-size: 2rem;
}

.stat-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.stat-card.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.stat-card.bg-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
}

.stat-card.bg-info {
    background: linear-gradient(135deg, #667eea 0%, #4facfe 100%) !important;
}

.stat-card.bg-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
}

.stat-card .card-body {
    padding: 1.5rem;
}

.stat-card .card-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-card .card-text {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0;
}

.search-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    background: white;
}

.search-card .card-body {
    padding: 1.5rem;
}

.table-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    background: white;
    overflow: hidden;
}

.table-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.table-card .card-header h5 {
    margin: 0;
    font-weight: 600;
}

.table-custom {
    border-radius: 0;
}

.table-custom thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 1rem;
}

.table-custom tbody tr {
    transition: all 0.2s ease;
}

.table-custom tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
}

.table-custom tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.badge-custom {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    border-radius: 10px;
    font-weight: 500;
}

.btn-group-custom .btn {
    border-radius: 8px;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.btn-group-custom .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 15px 15px 0 0;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
}

.form-control, .form-select {
    border-radius: 10px;
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.empty-state {
    padding: 3rem;
    text-align: center;
}

.empty-state i {
    color: #dee2e6;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .valuation-header {
        padding: 1.5rem;
    }
    
    .valuation-header h1 {
        font-size: 1.5rem;
    }
    
    .stat-card .card-title {
        font-size: 2rem;
    }
    
    .table-custom {
        font-size: 0.9rem;
    }
    
    .table-custom thead th,
    .table-custom tbody td {
        padding: 0.75rem 0.5rem;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Loading spinner custom */
.spinner-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="valuation-header d-flex justify-content-between align-items-center fade-in-up">
        <h1 class="mb-0">
            <i class="fas fa-brain me-3"></i>
            تقييم العقارات بالذكاء الاصطناعي
        </h1>
        <div>
            <a href="{{ route('ai.valuation.create') }}" class="btn btn-light btn-lg">
                <i class="fas fa-plus me-2"></i>تقييم جديد
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary text-white fade-in-up">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_valuations'] ?? 0 }}</h4>
                            <p class="card-text">إجمالي التقييمات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success text-white fade-in-up">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['high_confidence'] ?? 0 }}</h4>
                            <p class="card-text">تقييمات عالية الثقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-info text-white fade-in-up">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['avg_confidence'] ?? 0 }}%</h4>
                            <p class="card-text">متوسط الثقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning text-white fade-in-up">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['recent_valuations'] ?? 0 }}</h4>
                            <p class="card-text">التقييمات الأخيرة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card search-card mb-4 fade-in-up">
        <div class="card-body">
            <form method="GET" action="{{ route('ai.valuation.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن عقار..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">طريقة التقييم</label>
                        <select name="valuation_method" class="form-select">
                            <option value="">الكل</option>
                            <option value="comparable_sales" {{ request('valuation_method') == 'comparable_sales' ? 'selected' : '' }}>مقارنة المبيعات</option>
                            <option value="income_approach" {{ request('valuation_method') == 'income_approach' ? 'selected' : '' }}>طريقة الدخل</option>
                            <option value="cost_approach" {{ request('valuation_method') == 'cost_approach' ? 'selected' : '' }}>طريقة التكلفة</option>
                            <option value="ai_hybrid" {{ request('valuation_method') == 'ai_hybrid' ? 'selected' : '' }}>ذكاء اصطناعي مركب</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">مستوى الثقة</label>
                        <select name="confidence_level" class="form-select">
                            <option value="">الكل</option>
                            <option value="high" {{ request('confidence_level') == 'high' ? 'selected' : '' }}>عالي</option>
                            <option value="medium" {{ request('confidence_level') == 'medium' ? 'selected' : '' }}>متوسط</option>
                            <option value="low" {{ request('confidence_level') == 'low' ? 'selected' : '' }}>منخفض</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('ai.valuation.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>مسح
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Valuations Table -->
    <div class="card table-card fade-in-up">
        <div class="card-header">
            <h5 class="card-title mb-0">سجل التقييمات</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>رقم العقار</th>
                            <th>التاريخ</th>
                            <th>القيمة المقدرة</th>
                            <th>مستوى الثقة</th>
                            <th>طريقة التقييم</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($valuations ?? [] as $valuation)
                            <tr>
                                <td><strong>{{ $valuation->property_id }}</strong></td>
                                <td>{{ $valuation->valuation_date->format('Y-m-d') }}</td>
                                <td><strong>{{ number_format($valuation->estimated_value, 2) }} ريال</strong></td>
                                <td>
                                    <span class="badge badge-custom bg-{{ $valuation->confidence_score >= 80 ? 'success' : ($valuation->confidence_score >= 60 ? 'warning' : 'danger') }}">
                                        {{ $valuation->confidence_score }}%
                                    </span>
                                </td>
                                <td>{{ $valuation->valuation_method_label }}</td>
                                <td>
                                    <span class="badge badge-custom bg-{{ $valuation->status == 'completed' ? 'success' : ($valuation->status == 'processing' ? 'warning' : 'secondary') }}">
                                        {{ $valuation->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-custom">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showValuationDetails({{ $valuation->id }})" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if ($valuation->status == 'completed')
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadReport({{ $valuation->id }})" title="تحميل التقرير">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                        <p class="text-muted h5">لا توجد تقييمات حالياً</p>
                                        <p class="text-muted">ابدأ بإضافة تقييم جديد لرؤية النتائج هنا</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if (isset($valuations) && $valuations->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $valuations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>


<!-- Valuation Details Modal -->
<div class="modal fade" id="valuationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل التقييم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="valuationDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary-custom" onclick="printReport()">
                    <i class="fas fa-print me-2"></i>طباعة التقرير
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show Valuation Details
function showValuationDetails(valuationId) {
    fetch(`/ai/property-valuation/${valuationId}`)
    .then(response => response.text())
    .then(html => {
        document.getElementById('valuationDetailsContent').innerHTML = html;
        new bootstrap.Modal(document.getElementById('valuationDetailsModal')).show();
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

// Download Report
function downloadReport(valuationId) {
    window.open(`/ai/property-valuation/${valuationId}/download`, '_blank');
}

// Print Report
function printReport() {
    const modalContent = document.getElementById('valuationDetailsContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>تقرير التقييم</title>
                <style>
                    body { font-family: Arial, sans-serif; direction: rtl; padding: 20px; }
                    .table { width: 100%; border-collapse: collapse; }
                    .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                    .table th { background-color: #f2f2f2; }
                    .badge { padding: 4px 8px; border-radius: 4px; color: white; font-size: 12px; }
                    .bg-success { background-color: #28a745; }
                    .bg-warning { background-color: #ffc107; }
                    .bg-danger { background-color: #dc3545; }
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
