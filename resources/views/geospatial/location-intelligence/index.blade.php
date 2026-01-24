@extends('layouts.app')

@section('title', 'ذكاء الموقع')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">ذكاء الموقع</h1>
            <p class="text-muted mb-0">تحليلات ذكية للمواقع والاستثمار العقاري</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="window.location.href='/geospatial/location-intelligence/create'">
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
                    <label class="form-label">نوع الذكاء</label>
                    <select name="intelligence_type" class="form-select">
                        <option value="">كل الأنواع</option>
                        <option value="market" {{ request('intelligence_type') == 'market' ? 'selected' : '' }}>ذكاء السوق</option>
                        <option value="competitive" {{ request('intelligence_type') == 'competitive' ? 'selected' : '' }}>ذكاء تنافسي</option>
                        <option value="location" {{ request('intelligence_type') == 'location' ? 'selected' : '' }}>ذكاء الموقع</option>
                        <option value="investment" {{ request('intelligence_type') == 'investment' ? 'selected' : '' }}>ذكاء استثماري</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الإمكانات الاستثمارية</label>
                    <select name="investment_potential" class="form-select">
                        <option value="">كل المستويات</option>
                        <option value="high" {{ request('investment_potential') == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="medium" {{ request('investment_potential') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="low" {{ request('investment_potential') == 'low' ? 'selected' : '' }}>منخفضة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter me-1"></i>تصفية
                        </button>
                        <a href="/geospatial/location-intelligence" class="btn btn-outline-secondary">
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
                            <div class="h4 mb-0">{{ $stats['total_intelligence'] ?? 0 }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-brain text-success"></i>
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
                            <div class="text-muted small mb-1">إمكانات عالية</div>
                            <div class="h4 mb-0">{{ $stats['high_potential'] ?? 0 }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-star text-warning"></i>
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
                            <div class="text-muted small mb-1">متوسط التقييم</div>
                            <div class="h4 mb-0">{{ number_format($stats['average_score'] ?? 0, 1) }}</div>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-chart-line text-info"></i>
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
                            <div class="text-muted small mb-1">تحليلات نشطة</div>
                            <div class="h4 mb-0">{{ $stats['active_intelligence'] ?? 0 }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-rocket text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Intelligence Cards -->
    <div class="row g-4 mb-4">
        @forelse ($intelligenceReports ?? [] as $report)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2">
                                    <i class="fas fa-brain text-success"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">{{ $report->intelligence_type }}</h5>
                                    <p class="text-muted small mb-0">{{ $report->property?->name ?? 'غير معروف' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/geospatial/location-intelligence/{{ $report->id }}">عرض</a></li>
                                <li><a class="dropdown-item" href="/geospatial/location-intelligence/{{ $report->id }}/edit">تعديل</a></li>
                                <li><a class="dropdown-item" href="/geospatial/location-intelligence/{{ $report->id }}/download">تحميل</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteReport({{ $report->id }})">حذف</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Scores -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">تقييم الموقع</div>
                                <div class="h4 mb-0 text-primary">{{ number_format($report->location_score ?? 0, 1) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">الإمكانات الاستثمارية</div>
                                <div class="h4 mb-0 text-success">{{ number_format($report->investment_potential ?? 0, 1) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Key Insights -->
                    <div class="mb-3">
                        <h6 class="text-muted small mb-2">رؤى رئيسية</h6>
                        <div class="row g-2">
                            @if (isset($report->market_analysis['key_insights']))
                                @foreach (array_slice($report->market_analysis['key_insights'], 0, 2) as $insight)
                                <div class="col-12">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-lightbulb text-warning me-2 mt-1"></i>
                                        <small class="text-muted">{{ $insight }}</small>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    <!-- Risk Factors -->
                    <div class="mb-3">
                        <h6 class="text-muted small mb-2">عوامل المخاطر</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @if (isset($report->risk_factors))
                                @foreach (array_slice($report->risk_factors, 0, 3) as $risk)
                                <span class="badge bg-danger bg-opacity-10 text-danger">{{ $risk }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @switch($report->status)
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
                        <div class="text-muted small">{{ $report->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-brain fa-3x text-muted mb-3"></i>
                    <h5>لا توجد تحليلات ذكاء موقع</h5>
                                            <p class="text-muted">ابدأ بإنشاء تحليل ذكاء موقع جديد للحصول على رؤى استثمارية</p>
                                            <button class="btn btn-primary" onclick="window.location.href='/geospatial/location-intelligence/create'">
                                                <i class="fas fa-plus me-2"></i>إنشاء تحليل جديد
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>

                            <!-- Market Overview -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">نظرة عامة على السوق</h5>
                                        <div class="d-flex gap-2">
                                            <select id="marketFilter" class="form-select form-select-sm" style="width: auto;">
                                                <option value="all">كل المناطق</option>
                                                <option value="urban">مناطق حضرية</option>
                                                <option value="suburban">مناطق ضواحي</option>
                                                <option value="rural">مناطق ريفية</option>
                                            </select>
                                            <button class="btn btn-sm btn-outline-primary" onclick="refreshMarketData()">
                                                <i class="fas fa-sync-alt me-1"></i>تحديث
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <h6 class="text-muted small mb-3">اتجاهات السوق</h6>
                                            <div class="space-y-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>اتجاه الأسعار</span>
                                                    <span class="badge bg-success">صاعد</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>الطلب الحالي</span>
                                                    <span class="badge bg-warning">مرتفع</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>المعروض</span>
                                                    <span class="badge bg-info">متوسط</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>السيولة</span>
                                                    <span class="badge bg-success">جيدة</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted small mb-3">التوقعات المستقبلية</h6>
                                            <div class="space-y-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>نمو 6 أشهر</span>
                                                    <span class="text-success">+12.5%</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>نمو سنة</span>
                                                    <span class="text-success">+18.3%</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>نمو 3 سنوات</span>
                                                    <span class="text-success">+45.7%</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>مستوى المخاطرة</span>
                                                    <span class="badge bg-warning">متوسط</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                        function deleteReport(id) {
                            if (confirm('هل أنت متأكد من حذف هذا التقرير؟')) {
                                fetch(`/geospatial/location-intelligence/${id}`, {
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

                        function refreshMarketData() {
                            const filter = document.getElementById('marketFilter').value;
                            console.log('Refreshing market data with filter:', filter);
                            // Implement market data refresh logic
                        }

                        function exportData() {
                            const params = new URLSearchParams(window.location.search);
                            window.location.href = `/geospatial/location-intelligence/export?${params.toString()}`;
                        }

                        // Initialize market data when page loads
                        document.addEventListener('DOMContentLoaded', function() {
                            console.log('Initializing location intelligence dashboard...');
                        });
                        </script>
                        @endsection
