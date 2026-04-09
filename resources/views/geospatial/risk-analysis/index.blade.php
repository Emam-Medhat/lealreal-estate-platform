@extends('layouts.app')

@section('title', 'تحليل المخاطر')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تحليل المخاطر</h1>
            <p class="text-muted mb-0">تحليل شامل للمخاطر الطبيعية والبيئية للعقارات</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="window.location.href='/geospatial/risk-analysis/create'">
                <i class="fas fa-plus me-2"></i>تحليل جديد
            </button>
            <button class="btn btn-outline-secondary" onclick="exportData()">
                <i class="fas fa-download me-2"></i>تصدير
            </button>
        </div>
    </div>

    <!-- Risk Overview Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">مخاطر الفيضانات</div>
                            <div class="h4 mb-0">{{ $stats['flood_risks'] ?? 0 }}</div>
                            <div class="text-success small">
                                <i class="fas fa-arrow-down"></i> {{ $stats['flood_risk_change'] ?? 0 }}%
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-water text-info"></i>
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
                            <div class="text-muted small mb-1">مخاطر الزلازل</div>
                            <div class="h4 mb-0">{{ $stats['earthquake_risks'] ?? 0 }}</div>
                            <div class="text-warning small">
                                <i class="fas fa-minus"></i> {{ $stats['earthquake_risk_change'] ?? 0 }}%
                            </div>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-house-damage text-warning"></i>
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
                            <div class="text-muted small mb-1">مستوى الأمان</div>
                            <div class="h4 mb-0">{{ number_format($stats['safety_level'] ?? 0, 1) }}%</div>
                            <div class="text-success small">
                                <i class="fas fa-arrow-up"></i> {{ $stats['safety_improvement'] ?? 0 }}%
                            </div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-shield-alt text-success"></i>
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
                            <div class="text-info small">
                                <i class="fas fa-clock"></i> {{ $stats['processing_analyses'] ?? 0 }} قيد المعالجة
                            </div>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-chart-line text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Analysis Types -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <h5 class="mb-3">أنواع تحليل المخاطر</h5>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/flood-risks'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-water fa-2x text-info"></i>
                    </div>
                    <h5 class="card-title">مخاطر الفيضانات</h5>
                    <p class="card-text text-muted">تحليل مخاطر الفيضانات ومستويات المياه</p>
                    <div class="text-muted small">{{ $riskTypes['flood'] ?? 0 }} تحليل</div>
                    <div class="mt-2">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $riskStats['flood_percentage'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/earthquake-risks'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-house-damage fa-2x text-warning"></i>
                    </div>
                    <h5 class="card-title">مخاطر الزلازل</h5>
                    <p class="card-text text-muted">تحليل المخاطر الزلزالية والمناطق الزلزالية</p>
                    <div class="text-muted small">{{ $riskTypes['earthquake'] ?? 0 }} تحليل</div>
                    <div class="mt-2">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ $riskStats['earthquake_percentage'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100" onclick="window.location.href='/geospatial/crime-maps'" style="cursor: pointer;">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-shield-alt fa-2x text-danger"></i>
                    </div>
                    <h5 class="card-title">خرائط الجريمة</h5>
                    <p class="card-text text-muted">تحليل مستويات الأمان والجريمة</p>
                    <div class="text-muted small">{{ $riskTypes['crime'] ?? 0 }} تحليل</div>
                    <div class="mt-2">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: {{ $riskStats['crime_percentage'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Risk Analyses -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">تحليلات المخاطر الأخيرة</h5>
                <div class="d-flex gap-2">
                    <select id="riskFilter" class="form-select form-select-sm" style="width: auto;">
                        <option value="all">كل المخاطر</option>
                        <option value="flood">فيضانات</option>
                        <option value="earthquake">زلازل</option>
                        <option value="crime">جريمة</option>
                    </select>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshRiskData()">
                        <i class="fas fa-sync-alt me-1"></i>تحديث
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>العقار</th>
                            <th>نوع المخاطر</th>
                            <th>مستوى المخاطر</th>
                            <th>درجة المخاطر</th>
                            <th>التوصيات</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riskAnalyses ?? [] as $analysis)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="fas fa-building text-primary small"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $analysis->property?->name ?? 'غير معروف' }}</div>
                                        <div class="text-muted small">{{ $analysis->property_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $analysis->risk_type ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @switch($analysis->risk_level)
                                    @case('low')
                                        <span class="badge bg-success">منخفض</span>
                                        @break
                                    @case('moderate')
                                        <span class="badge bg-warning">متوسط</span>
                                        @break
                                    @case('high')
                                        <span class="badge bg-danger">عالي</span>
                                        @break
                                    @case('very_high')
                                        <span class="badge bg-dark">عالي جداً</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">غير محدد</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar {{ $analysis->risk_score >= 7 ? 'bg-danger' : ($analysis->risk_score >= 4 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $analysis->risk_score ?? 0 }}%"></div>
                                    </div>
                                    <small>{{ number_format($analysis->risk_score ?? 0, 1) }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    @if (isset($analysis->recommendations))
                                        {{ implode(', ', array_slice($analysis->recommendations, 0, 2)) }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                @switch($analysis->status)
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
                                <div class="text-muted small">{{ $analysis->created_at->format('Y-m-d H:i') }}</div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewAnalysis({{ $analysis->id }})" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="downloadAnalysis({{ $analysis->id }})" title="تحميل">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" onclick="editAnalysis({{ $analysis->id }})" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                <div>لا توجد تحليلات مخاطر حالياً</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Risk Map Visualization -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">خريطة المخاطر</h5>
                <div class="d-flex gap-2">
                    <select id="riskMapType" class="form-select form-select-sm" style="width: auto;">
                        <option value="flood">مخاطر الفيضانات</option>
                        <option value="earthquake">مخاطر الزلازل</option>
                        <option value="crime">مخاطر الجريمة</option>
                        <option value="combined">مخاطر مجمعة</option>
                    </select>
                    <button class="btn btn-sm btn-outline-success" onclick="toggleRiskFullscreen()">
                        <i class="fas fa-expand me-1"></i>ملء الشاشة
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="riskMap" style="height: 400px; background: #f8f9fa; position: relative;">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center text-muted">
                        <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                        <h5>خريطة المخاطر</h5>
                        <p>اختر نوع المخاطر لعرض الخريطة التفاعلية</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewAnalysis(id) {
    window.location.href = `/geospatial/risk-analysis/${id}`;
}

function downloadAnalysis(id) {
    window.location.href = `/geospatial/risk-analysis/${id}/download`;
}

function editAnalysis(id) {
    window.location.href = `/geospatial/risk-analysis/${id}/edit`;
}

function refreshRiskData() {
    const filter = document.getElementById('riskFilter').value;
    console.log('Refreshing risk data with filter:', filter);
    // Implement risk data refresh logic
}

function toggleRiskFullscreen() {
    const mapElement = document.getElementById('riskMap');
    if (!document.fullscreenElement) {
        mapElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/geospatial/risk-analysis/export?${params.toString()}`;
}

// Initialize risk map when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing risk analysis dashboard...');
    
    // Update risk map when type changes
    document.getElementById('riskMapType').addEventListener('change', function() {
        const mapType = this.value;
        console.log('Changing risk map type to:', mapType);
        // Implement map type change logic
    });
    
    // Update filter when changes
    document.getElementById('riskFilter').addEventListener('change', function() {
        const filter = this.value;
        console.log('Changing risk filter to:', filter);
        // Implement filter change logic
    });
});
</script>
@endsection
