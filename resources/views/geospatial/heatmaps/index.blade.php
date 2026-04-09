@extends('layouts.app')

@section('title', 'خرائط الحرارة')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">خرائط الحرارة</h1>
            <p class="text-muted mb-0">خرائط حرارة تفاعلية للأسعار والكثافة والنشاط</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="window.location.href='/geospatial/heatmaps/create'">
                <i class="fas fa-plus me-2"></i>خريطة جديدة
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
                    <label class="form-label">نوع الخريطة</label>
                    <select name="heatmap_type" class="form-select">
                        <option value="">كل الأنواع</option>
                        <option value="price_density" {{ request('heatmap_type') == 'price_density' ? 'selected' : '' }}>كثافة الأسعار</option>
                        <option value="price_appreciation" {{ request('heatmap_type') == 'price_appreciation' ? 'selected' : '' }}>ارتفاع الأسعار</option>
                        <option value="investment_hotspot" {{ request('heatmap_type') == 'investment_hotspot' ? 'selected' : '' }}>نقاط الاستثمار</option>
                        <option value="market_activity" {{ request('heatmap_type') == 'market_activity' ? 'selected' : '' }}>نشاط السوق</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">مخطط الألوان</label>
                    <select name="color_scheme" class="form-select">
                        <option value="">كل المخططات</option>
                        <option value="viridis" {{ request('color_scheme') == 'viridis' ? 'selected' : '' }}>Viridis</option>
                        <option value="plasma" {{ request('color_scheme') == 'plasma' ? 'selected' : '' }}>Plasma</option>
                        <option value="inferno" {{ request('color_scheme') == 'inferno' ? 'selected' : '' }}>Inferno</option>
                        <option value="magma" {{ request('color_scheme') == 'magma' ? 'selected' : '' }}>Magma</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter me-1"></i>تصفية
                        </button>
                        <a href="/geospatial/heatmaps" class="btn btn-outline-secondary">
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
                            <div class="text-muted small mb-1">إجمالي الخرائط</div>
                            <div class="h4 mb-0">{{ $stats['total_heatmaps'] ?? 0 }}</div>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-fire text-danger"></i>
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
                            <div class="text-muted small mb-1">خرائط الأسعار</div>
                            <div class="h4 mb-0">{{ $stats['price_heatmaps'] ?? 0 }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-dollar-sign text-success"></i>
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
                            <div class="text-muted small mb-1">مستوى التكبير</div>
                            <div class="h4 mb-0">{{ $stats['average_zoom'] ?? 0 }}</div>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-search-plus text-info"></i>
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
                            <div class="text-muted small mb-1">حجم الشبكة</div>
                            <div class="h4 mb-0">{{ $stats['average_grid_size'] ?? 0 }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-th text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Heatmaps Grid -->
    <div class="row g-4 mb-4">
        @forelse ($heatmaps ?? [] as $heatmap)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $heatmap->heatmap_type }}</h5>
                            <p class="text-muted small mb-0">{{ $heatmap->property?->name ?? 'غير معروف' }}</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/geospatial/heatmaps/{{ $heatmap->id }}">عرض</a></li>
                                <li><a class="dropdown-item" href="/geospatial/heatmaps/{{ $heatmap->id }}/edit">تعديل</a></li>
                                <li><a class="dropdown-item" href="/geospatial/heatmaps/{{ $heatmap->id }}/download">تحميل</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteHeatmap({{ $heatmap->id }})">حذف</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Heatmap Preview -->
                    <div class="heatmap-preview mb-3" style="height: 200px; background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%, transparent 75%, #f8f9fa 75%, #f8f9fa), linear-gradient(45deg, #f8f9fa 25%, transparent 25%, transparent 75%, #f8f9fa 75%, #f8f9fa); background-size: 20px 20px; background-position: 0 0, 10px 10px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <div class="text-center text-muted">
                            <i class="fas fa-map fa-2x mb-2"></i>
                            <div>معاينة الخريطة</div>
                            <small>{{ count($heatmap->data_points ?? []) } نقطة بيانات</small>
                        </div>
                    </div>
                    
                    <!-- Heatmap Info -->
                    <div class="row g-2 text-sm">
                        <div class="col-6">
                            <div class="text-muted small">مخطط الألوان</div>
                            <div class="fw-medium">{{ $heatmap->color_scheme }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">مستوى التكبير</div>
                            <div class="fw-medium">{{ $heatmap->zoom_level }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">حجم الشبكة</div>
                            <div class="fw-medium">{{ $heatmap->grid_size }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">الحالة</div>
                            <div>
                                @switch($heatmap->status)
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-fire fa-3x text-muted mb-3"></i>
                    <h5>لا توجد خرائط حرارة</h5>
                    <p class="text-muted">ابدأ بإنشاء خريطة حرارة جديدة لعرض البيانات الجغرافية</p>
                    <button class="btn btn-primary" onclick="window.location.href='/geospatial/heatmaps/create'">
                        <i class="fas fa-plus me-2"></i>إنشاء خريطة جديدة
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Interactive Map Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">الخريطة التفاعلية</h5>
                <div class="d-flex gap-2">
                    <select id="heatmapTypeSelect" class="form-select form-select-sm" style="width: auto;">
                        <option value="price_density">كثافة الأسعار</option>
                        <option value="price_appreciation">ارتفاع الأسعار</option>
                        <option value="investment_hotspot">نقاط الاستثمار</option>
                        <option value="market_activity">نشاط السوق</option>
                    </select>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshMap()">
                        <i class="fas fa-sync-alt me-1"></i>تحديث
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="toggleFullscreen()">
                        <i class="fas fa-expand me-1"></i>ملء الشاشة
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="interactiveMap" style="height: 500px; background: #f8f9fa; position: relative;">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center text-muted">
                        <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                        <h5>الخريطة التفاعلية</h5>
                        <p>اختر نوع الخريطة لعرض البيانات الجغرافية</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteHeatmap(id) {
    if (confirm('هل أنت متأكد من حذف هذه الخريطة؟')) {
        fetch(`/geospatial/heatmaps/${id}`, {
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

function refreshMap() {
    const mapType = document.getElementById('heatmapTypeSelect').value;
    // Implement map refresh logic
    console.log('Refreshing map with type:', mapType);
}

function toggleFullscreen() {
    const mapElement = document.getElementById('interactiveMap');
    if (!document.fullscreenElement) {
        mapElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/geospatial/heatmaps/export?${params.toString()}`;
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize interactive map
    console.log('Initializing interactive map...');
});
</script>
@endsection
