@extends('layouts.app')

@section('title', 'تقييم العقارات بالذكاء الاصطناعي')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-brain me-2"></i>
            تقييم العقارات بالذكاء الاصطناعي
        </h1>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newValuationModal">
                <i class="fas fa-plus me-2"></i>تقييم جديد
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
        <div class="col-md-3">
            <div class="card bg-success text-white">
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
        <div class="col-md-3">
            <div class="card bg-info text-white">
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
        <div class="col-md-3">
            <div class="card bg-warning text-white">
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
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ai.property-valuation.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن عقار..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">طريقة التقييم</label>
                        <select name="valuation_method" class="form-select">
                            <option value="">الكل</option>
                            <option value="comparable_sales" {{ request('valuation_method') == 'comparable_sales' ? 'selected' : '' }}>مقارنة المبيعات</option>
                            <option value="income_approach" {{ request('valuation_method') == 'income_approach' ? 'selected' : '' }}>طريقة الدخل</option>
                            <option value="cost_approach" {{ request('valuation_method') == 'cost_approach' ? 'selected' : '' }}>طريقة التكلفة</option>
                            <option value="ai_hybrid" {{ request('valuation_method') == 'ai_hybrid' ? 'selected' : '' }}>ذكاء اصطناعي مركب</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">مستوى الثقة</label>
                        <select name="confidence_level" class="form-select">
                            <option value="">الكل</option>
                            <option value="high" {{ request('confidence_level') == 'high' ? 'selected' : '' }}>عالي</option>
                            <option value="medium" {{ request('confidence_level') == 'medium' ? 'selected' : '' }}>متوسط</option>
                            <option value="low" {{ request('confidence_level') == 'low' ? 'selected' : '' }}>منخفض</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('ai.property-valuation.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>مسح
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Valuations Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">سجل التقييمات</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
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
                                <td>{{ $valuation->property_id }}</td>
                                <td>{{ $valuation->valuation_date->format('Y-m-d') }}</td>
                                <td>{{ number_format($valuation->estimated_value, 2) }} ريال</td>
                                <td>
                                    <span class="badge bg-{{ $valuation->confidence_score >= 80 ? 'success' : ($valuation->confidence_score >= 60 ? 'warning' : 'danger') }}">
                                        {{ $valuation->confidence_score }}%
                                    </span>
                                </td>
                                <td>{{ $valuation->valuation_method_label }}</td>
                                <td>
                                    <span class="badge bg-{{ $valuation->status == 'completed' ? 'success' : ($valuation->status == 'processing' ? 'warning' : 'secondary') }}">
                                        {{ $valuation->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showValuationDetails({{ $valuation->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if ($valuation->status == 'completed')
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadReport({{ $valuation->id }})">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد تقييمات حالياً</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if (isset($valuations) && $valuations->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $valuations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- New Valuation Modal -->
<div class="modal fade" id="newValuationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تقييم عقار جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newValuationForm">
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
                            <label class="form-label">طريقة التقييم *</label>
                            <select name="valuation_method" class="form-select" required>
                                <option value="comparable_sales">مقارنة المبيعات</option>
                                <option value="income_approach">طريقة الدخل</option>
                                <option value="cost_approach">طريقة التكلفة</option>
                                <option value="ai_hybrid">ذكاء اصطناعي مركب</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="أدخل أي ملاحظات إضافية..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-brain me-2"></i>بدء التقييم
                    </button>
                </div>
            </form>
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
// New Valuation Form
document.getElementById('newValuationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري التقييم...';
    
    fetch('{{ route("ai.property-valuation.store") }}', {
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
            text: 'حدث خطأ أثناء التقييم',
            confirmButtonText: 'موافق'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

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
