@extends('layouts.app')

@section('title', 'التحليلات الجغرافية')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">التحليلات الجغرافية</h1>
            <p class="text-muted mb-0">إدارة وتحليل البيانات الجغرافية والمكانية للعقارات</p>
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

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">العقار</label>
                    <select name="property_id" class="form-select">
                        <option value="">كل العقارات</option>
                        @foreach ($properties ?? [] as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">نوع التحليل</label>
                    <select name="analysis_type" class="form-select">
                        <option value="">كل الأنواع</option>
                        <option value="market" {{ request('analysis_type') == 'market' ? 'selected' : '' }}>تحليل السوق</option>
                        <option value="location" {{ request('analysis_type') == 'location' ? 'selected' : '' }}>تحليل الموقع</option>
                        <option value="demographic" {{ request('analysis_type') == 'demographic' ? 'selected' : '' }}>تحليل ديموغرافي</option>
                        <option value="risk" {{ request('analysis_type') == 'risk' ? 'selected' : '' }}>تحليل المخاطر</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter me-1"></i>تصفية
                        </button>
                        <a href="/geospatial/analytics" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>مسح
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
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
                            <div class="text-muted small mb-1">مكتملة</div>
                            <div class="h4 mb-0">{{ $stats['completed_analyses'] ?? 0 }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check text-success"></i>
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
                            <div class="text-muted small mb-1">متوسط الثقة</div>
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
                            <div class="text-muted small mb-1">قيد المعالجة</div>
                            <div class="h4 mb-0">{{ $stats['processing_analyses'] ?? 0 }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analyses Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">التحليلات</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="batchAnalyze()">
                        <i class="fas fa-play me-1"></i>تحليل مجمع
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSelected()">
                        <i class="fas fa-trash me-1"></i>حذف محدد
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>العقار</th>
                            <th>نوع التحليل</th>
                            <th>نصف القطر</th>
                            <th>درجة الثقة</th>
                            <th>جودة البيانات</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($analyses ?? [] as $analysis)
                        <tr>
                            <td>
                                <input type="checkbox" class="analysis-checkbox" value="{{ $analysis->id }}">
                            </td>
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
                                <span class="badge bg-light text-dark">{{ $analysis->analysis_type }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $analysis->analysis_radius ?? 'N/A' }} كم</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $analysis->confidence_score ?? 0 }}%"></div>
                                    </div>
                                    <small>{{ number_format($analysis->confidence_score ?? 0, 1) }}%</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-info" style="width: {{ $analysis->data_quality_score ?? 0 }}%"></div>
                                    </div>
                                    <small>{{ number_format($analysis->data_quality_score ?? 0, 1) }}%</small>
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
                                    <button class="btn btn-outline-danger" onclick="deleteAnalysis({{ $analysis->id }})" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
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
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.analysis-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.analysis-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function viewAnalysis(id) {
    window.location.href = `/geospatial/analytics/${id}`;
}

function downloadAnalysis(id) {
    window.location.href = `/geospatial/analytics/${id}/download`;
}

function editAnalysis(id) {
    window.location.href = `/geospatial/analytics/${id}/edit`;
}

function deleteAnalysis(id) {
    if (confirm('هل أنت متأكد من حذف هذا التحليل؟')) {
        fetch(`/geospatial/analytics/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء الحذف');
            }
        });
    }
}

function batchAnalyze() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('يرجى اختيار تحليل واحد على الأقل');
        return;
    }
    
    if (confirm(`هل تريد تحليل ${ids.length} تحليل؟`)) {
        fetch('/geospatial/analytics/batch-analyze', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ ids: ids })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء التحليل');
            }
        });
    }
}

function deleteSelected() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('يرجى اختيار تحليل واحد على الأقل');
        return;
    }
    
    if (confirm(`هل أنت متأكد من حذف ${ids.length} تحليل؟`)) {
        fetch('/geospatial/analytics/batch-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ ids: ids })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء الحذف');
            }
        });
    }
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/geospatial/analytics/export?${params.toString()}`;
}
</script>
@endsection
