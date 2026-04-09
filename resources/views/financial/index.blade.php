@extends('layouts.app')

@section('title', 'تحليل مالي عقاري')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تحليل مالي عقاري</h1>
            <p class="text-muted mb-0">تحليل شامل للأداء المالي والعائد على الاستثمار</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAnalysisModal">
                <i class="fas fa-plus"></i> تحليل جديد
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="exportData()">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($totalProperties) }}</h4>
                            <p class="card-text">إجمالي العقارات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($totalValue, 2) }} ريال</h4>
                            <p class="card-text">إجمالي القيمة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($averageROI, 2) }}%</h4>
                            <p class="card-text">متوسط العائد</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($totalCashFlow, 2) }} ريال</h4>
                            <p class="card-text">التدفق النقدي</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="propertyFilter" class="form-label">العقار</label>
                    <select class="form-select" id="propertyFilter" name="property_id">
                        <option value="">جميع العقارات</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">الحالة</label>
                    <select class="form-select" id="statusFilter" name="status">
                        <option value="">جميع الحالات</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                        <option value="completed">مكتمل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="typeFilter" class="form-label">نوع التحليل</label>
                    <select class="form-select" id="typeFilter" name="analysis_type">
                        <option value="">جميع الأنواع</option>
                        <option value="investment">استثماري</option>
                        <option value="valuation">تقييم</option>
                        <option value="portfolio">محفظة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFilter" class="form-label">التاريخ</label>
                    <input type="date" class="form-control" id="dateFilter" name="date">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetFilters()">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Analyses List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">التحليلات المالية</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>العقار</th>
                                    <th>تاريخ التحليل</th>
                                    <th>القيمة الحالية</th>
                                    <th>العائد على الاستثمار</th>
                                    <th>معدل التأجير</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyses as $analysis)
                                    <tr>
                                        <td>{{ $analysis->property->name }}</td>
                                        <td>{{ $analysis->analysis_date->format('Y-m-d') }}</td>
                                        <td>{{ number_format($analysis->current_value, 2) }} ريال</td>
                                        <td>
                                            <span class="badge bg-{{ $analysis->calculateReturnOnInvestment()['roi'] >= 10 ? 'success' : ($analysis->calculateReturnOnInvestment()['roi'] >= 5 ? 'warning' : 'danger') }}">
                                                {{ number_format($analysis->calculateReturnOnInvestment()['roi'], 2) }}%
                                            </span>
                                        </td>
                                        <td>{{ number_format($analysis->calculateCapitalizationRate(), 2) }}%</td>
                                        <td>
                                            <span class="badge bg-{{ $analysis->status == 'active' ? 'success' : ($analysis->status == 'completed' ? 'info' : 'secondary') }}">
                                                {{ $analysis->status == 'active' ? 'نشط' : ($analysis->status == 'completed' ? 'مكتمل' : 'غير نشط') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" onclick="viewAnalysis({{ $analysis->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editAnalysis({{ $analysis->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info" onclick="recalculateAnalysis({{ $analysis->id }})">
                                                    <i class="fas fa-calculator"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted">عرض {{ $analyses->firstItem() }} - {{ $analyses->lastItem() }} من {{ $analyses->total() }} سجل</span>
                        {{ $analyses->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Charts -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="openROICalculator()">
                            <i class="fas fa-percentage"></i> حاسبة العائد على الاستثمار
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="openCashFlowAnalyzer()">
                            <i class="fas fa-chart-line"></i> تحليل التدفق النقدي
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="openCapRateCalculator()">
                            <i class="fas fa-home"></i> حاسبة معدل التأجير
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="openAppreciationCalculator()">
                            <i class="fas fa-chart-area"></i> حاسبة ارتفاع القيمة
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="openPortfolioAnalyzer()">
                            <i class="fas fa-briefcase"></i> تحليل المحفظة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">أداء الاستثمار</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الأنشطة الأخيرة</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $activity->type == 'created' ? 'success' : ($activity->type == 'updated' ? 'info' : 'warning') }}"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ $activity->title }}</h6>
                                    <p class="text-muted small mb-0">{{ $activity->description }}</p>
                                    <small class="text-muted">{{ $activity->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Analysis Modal -->
<div class="modal fade" id="newAnalysisModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحليل مالي جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newAnalysisForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="property_id" class="form-label">العقار *</label>
                            <select class="form-select" id="property_id" name="property_id" required>
                                <option value="">اختر العقار</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="analysis_type" class="form-label">نوع التحليل *</label>
                            <select class="form-select" id="analysis_type" name="analysis_type" required>
                                <option value="">اختر النوع</option>
                                <option value="investment">استثماري</option>
                                <option value="valuation">تقييم</option>
                                <option value="portfolio">محفظة</option>
                                <option value="comprehensive">شامل</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="current_value" class="form-label">القيمة الحالية *</label>
                            <input type="number" class="form-control" id="current_value" name="current_value" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="purchase_price" class="form-label">سعر الشراء *</label>
                            <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="annual_rental_income" class="form-label">الدخل الإيجاري السنوي *</label>
                            <input type="number" class="form-control" id="annual_rental_income" name="annual_rental_income" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="operating_expenses" class="form-label">المصاريف التشغيلية *</label>
                            <input type="number" class="form-control" id="operating_expenses" name="operating_expenses" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label for="vacancy_rate" class="form-label">معدل الشغور (%)</label>
                            <input type="number" class="form-control" id="vacancy_rate" name="vacancy_rate" step="0.1" min="0" max="100" value="5">
                        </div>
                        <div class="col-md-4">
                            <label for="appreciation_rate" class="form-label">معدل ارتفاع القيمة (%)</label>
                            <input type="number" class="form-control" id="appreciation_rate" name="appreciation_rate" step="0.1" value="3">
                        </div>
                        <div class="col-md-4">
                            <label for="holding_period" class="form-label">فترة الاستحواذ (سنوات)</label>
                            <input type="number" class="form-control" id="holding_period" name="holding_period" min="1" value="10">
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التحليل</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}
</style>

<script>
// Performance Chart
const ctx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($performanceLabels),
        datasets: [{
            label: 'العائد على الاستثمار (%)',
            data: @json($performanceData),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Form submissions
document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    window.location.href = '?' + params.toString();
});

document.getElementById('newAnalysisForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/financial/analyses', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
});

// Functions
function viewAnalysis(id) {
    window.location.href = '/financial/analyses/' + id;
}

function editAnalysis(id) {
    window.location.href = '/financial/analyses/' + id + '/edit';
}

function recalculateAnalysis(id) {
    if (confirm('هل أنت متأكد من إعادة حساب هذا التحليل؟')) {
        fetch('/financial/analyses/' + id + '/recalculate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        });
    }
}

function exportData() {
    window.location.href = '/financial/analyses/export';
}

function resetFilters() {
    document.getElementById('filterForm').reset();
    window.location.href = '/financial/analyses';
}

function openROICalculator() {
    window.location.href = '/financial/roi-calculator';
}

function openCashFlowAnalyzer() {
    window.location.href = '/financial/cash-flow-analyzer';
}

function openCapRateCalculator() {
    window.location.href = '/financial/cap-rate-calculator';
}

function openAppreciationCalculator() {
    window.location.href = '/financial/appreciation-calculator';
}

function openPortfolioAnalyzer() {
    window.location.href = '/financial/portfolio-analyzer';
}
</script>
@endsection
