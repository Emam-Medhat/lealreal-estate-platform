@extends('layouts.app')

@section('title', 'التحليلات الجغرافية')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">التحليلات الجغرافية</h1>
            <p class="text-muted mb-0">نظام متقدم للتحليلات الجغرافية والمكانية للعقارات في الميتافيرس</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="window.location.href='/geospatial/analytics/create'">
                <i class="fas fa-plus me-2"></i>تحليل جديد
            </button>
            <button class="btn btn-outline-secondary" onclick="exportData()">
                <i class="fas fa-download me-2"></i>تصدير
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">إجمالي التحليلات</div>
                            <div class="h4 mb-0">{{ $stats['total_analyses'] ?? 0 }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-chart-line text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">تحليلات عالية الجودة</div>
                            <div class="h4 mb-0">{{ $stats['high_quality_analyses'] ?? 0 }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-star text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">متوسط درجة الثقة</div>
                            <div class="h4 mb-0">{{ number_format($stats['average_confidence'] ?? 0, 1) }}%</div>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-percentage text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">التحليلات النشطة</div>
                            <div class="h4 mb-0">{{ $stats['active_analyses'] ?? 0 }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analysis Types Grid -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <h5 class="mb-3">أنواع التحليلات</h5>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/analytics'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-globe-americas fa-2x text-primary"></i>
                    </div>
                    <h5 class="card-title">التحليلات الجغرافية</h5>
                    <p class="card-text text-muted">تحليلات شاملة للبيانات الجغرافية والمكانية</p>
                    <div class="text-muted small">{{ $analysisTypes['geospatial'] ?? 0 }} تحليل</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/heatmaps'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-fire fa-2x text-danger"></i>
                    </div>
                    <h5 class="card-title">خرائط الحرارة</h5>
                    <p class="card-text text-muted">خرائط حرارة تفاعلية للأسعار والكثافة</p>
                    <div class="text-muted small">{{ $analysisTypes['heatmaps'] ?? 0 }} خريطة</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/location-intelligence'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-brain fa-2x text-success"></i>
                    </div>
                    <h5 class="card-title">ذكاء الموقع</h5>
                    <p class="card-text text-muted">تحليلات ذكية للمواقع والاستثمار</p>
                    <div class="text-muted small">{{ $analysisTypes['location_intelligence'] ?? 0 }} تحليل</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/proximity-analysis'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-map-marked-alt fa-2x text-info"></i>
                    </div>
                    <h5 class="card-title">تحليل القرب</h5>
                    <p class="card-text text-muted">تحليلات القرب والوصولية للمرافق</p>
                    <div class="text-muted small">{{ $analysisTypes['proximity'] ?? 0 }} تحليل</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/demographic-analysis'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-users fa-2x text-warning"></i>
                    </div>
                    <h5 class="card-title">التحليل الديموغرافي</h5>
                    <p class="card-text text-muted">تحليلات سكانية وديموغرافية متقدمة</p>
                    <div class="text-muted small">{{ $analysisTypes['demographic'] ?? 0 }} تحليل</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/property-density'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-secondary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-th fa-2x text-secondary"></i>
                    </div>
                    <h5 class="card-title">كثافة العقارات</h5>
                    <p class="card-text text-muted">تحليلات كثافة العقارات والتطور</p>
                    <div class="text-muted small">{{ $analysisTypes['density'] ?? 0 }} تحليل</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Analyses -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">التحليلات الأخيرة</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='/geospatial/analytics'">
                    عرض الكل
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>العقار</th>
                            <th>نوع التحليل</th>
                            <th>درجة الثقة</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentAnalyses ?? [] as $analysis)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="fas fa-building text-primary small"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $analysis['property_name'] ?? 'غير معروف' }}</div>
                                        <div class="text-muted small">{{ $analysis['property_id'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $analysis['analysis_type'] }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar" style="width: {{ $analysis['confidence_score'] ?? 0 }}%"></div>
                                    </div>
                                    <small>{{ number_format($analysis['confidence_score'] ?? 0, 1) }}%</small>
                                </div>
                            </td>
                            <td>
                                @switch($analysis['status'])
                                    @case('completed')
                                        <span class="badge bg-success">مكتمل</span>
                                        @break
                                    @case('processing')
                                        <span class="badge bg-warning">قيد المعالجة</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-secondary">في الانتظار</span>
                                        @break
                                    @default
                                        <span class="badge bg-danger">فشل</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="text-muted small">{{ $analysis['created_at'] }}</div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewAnalysis({{ $analysis['id'] }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="downloadAnalysis({{ $analysis['id'] }})">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <div>لا توجد تحليلات حالياً</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function viewAnalysis(id) {
    window.location.href = `/geospatial/analytics/${id}`;
}

function downloadAnalysis(id) {
    window.location.href = `/geospatial/analytics/${id}/download`;
}

function exportData() {
    window.location.href = '/geospatial/analytics/export';
}
</script>
@endsection
