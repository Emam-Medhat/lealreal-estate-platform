@extends('layouts.app')

@section('title', 'تحليل المحفظة العقارية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تحليل المحفظة العقارية</h1>
            <p class="text-muted mb-0">تحليل شامل لأداء محفظتك العقارية</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="saveAnalysis()">
                <i class="fas fa-save"></i> حفظ التحليل
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="exportResults()">
                <i class="fas fa-download"></i> تصدير النتائج
            </button>
        </div>
    </div>

    <!-- Portfolio Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($portfolio->total_properties_count) }}</h4>
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
                            <h4 class="card-title">{{ number_format($portfolio->total_property_value, 2) }}</h4>
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
                            <h4 class="card-title">{{ number_format($portfolio->portfolio_roi, 2) }}%</h4>
                            <p class="card-text">العائد على المحفظة</p>
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
                            <h4 class="card-title">{{ number_format($portfolio->portfolio_cash_flow, 2) }}</h4>
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

    <!-- Risk and Diversification Metrics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">مؤشرات المخاطرة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $portfolio->risk_score <= 0.3 ? 'success' : ($portfolio->risk_score <= 0.6 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $portfolio->risk_score * 100 }}%">
                                        {{ number_format($portfolio->risk_score * 100, 1) }}%
                                    </div>
                                </div>
                                <small class="text-muted">مستوى المخاطرة</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-primary">{{ number_format($portfolio->volatility_index * 100, 2) }}%</h5>
                                <small class="text-muted">مؤشر التقلب</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-info">{{ number_format($portfolio->sharpe_ratio, 2) }}</h5>
                                <small class="text-muted">نسبة شارب</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-warning">{{ number_format($portfolio->maximum_drawdown * 100, 2) }}%</h5>
                                <small class="text-muted">أقصى انخفاض</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">التنويع</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $portfolio->diversification_score >= 0.8 ? 'success' : ($portfolio->diversification_score >= 0.6 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $portfolio->diversification_score * 100 }}%">
                                        {{ number_format($portfolio->diversification_score * 100, 1) }}%
                                    </div>
                                </div>
                                <small class="text-muted">مجموع التنويع</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-primary">{{ number_format($portfolio->geographic_diversification * 100, 1) }}%</h5>
                                <small class="text-muted">التنويع الجغرافي</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-info">{{ number_format($portfolio->property_type_diversification * 100, 1) }}%</h5>
                                <small class="text-muted">تنويع نوع العقار</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-warning">{{ number_format($portfolio->price_range_diversification * 100, 1) }}%</h5>
                                <small class="text-muted">تنويع نطاق السعر</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Performance Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أداء المحفظة</h5>
                </div>
                <div class="card-body">
                    <canvas id="portfolioPerformanceChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Allocation -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">توزيع الأصول حسب النوع</h5>
                </div>
                <div class="card-body">
                    <canvas id="assetAllocationChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">التوزيع الجغرافي</h5>
                </div>
                <div class="card-body">
                    <canvas id="geographicAllocationChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Details Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">تفاصيل العقارات</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>العقار</th>
                            <th>القيمة</th>
                            <th>العائد</th>
                            <th>التدفق النقدي</th>
                            <th>معدل التأجير</th>
                            <th>نوع العقار</th>
                            <th>الموقع</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                            <tr>
                                <td>{{ $property->name }}</td>
                                <td>{{ number_format($property->current_value, 2) }} ريال</td>
                                <td>
                                    <span class="badge bg-{{ $property->roi >= 10 ? 'success' : ($property->roi >= 5 ? 'warning' : 'danger') }}">
                                        {{ number_format($property->roi, 2) }}%
                                    </span>
                                </td>
                                <td>{{ number_format($property->cash_flow, 2) }} ريال</td>
                                <td>{{ number_format($property->cap_rate, 2) }}%</td>
                                <td>{{ $property->type }}</td>
                                <td>{{ $property->location }}</td>
                                <td>
                                    <span class="badge bg-{{ $property->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ $property->status == 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Optimization Recommendations -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">توصيات التحسين</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>تحسين المحفظة</h6>
                    <ul class="list-unstyled">
                        @if($portfolio->diversification_score < 0.7)
                            <li class="mb-2">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                زيادة التنويع في المحفظة لتقليل المخاطرة
                            </li>
                        @endif
                        @if($portfolio->risk_score > 0.6)
                            <li class="mb-2">
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                مستوى مخاطرة مرتفع - النظر في إعادة التوازن
                            </li>
                        @endif
                        @if($portfolio->sharpe_ratio < 1)
                            <li class="mb-2">
                                <i class="fas fa-info-circle text-info"></i>
                                تحسين العائد المعدل للمخاطرة
                            </li>
                        @endif
                        @if($portfolio->geographic_diversification < 0.6)
                            <li class="mb-2">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                توسيع المحفظة جغرافياً
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>فرص الاستثمار</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-chart-line text-success"></i>
                            العقارات ذات العائد المرتفع (>12%)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-home text-primary"></i>
                            فرص في المناطق النامية
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-percentage text-warning"></i>
                            تحسين معدلات التأجير
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-tools text-info"></i>
                            فرص التحسين والتجديد
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Portfolio Performance Chart
const performanceCtx = document.getElementById('portfolioPerformanceChart').getContext('2d');
const performanceChart = new Chart(performanceCtx, {
    type: 'line',
    data: {
        labels: @json($performanceLabels),
        datasets: [{
            label: 'قيمة المحفظة',
            data: @json($portfolioValueData),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'المعيار',
            data: @json($benchmarkData),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderDash: [5, 5]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: false,
                title: {
                    display: true,
                    text: 'القيمة (ريال)'
                }
            }
        }
    }
});

// Asset Allocation Chart
const assetCtx = document.getElementById('assetAllocationChart').getContext('2d');
const assetChart = new Chart(assetCtx, {
    type: 'doughnut',
    data: {
        labels: @json($assetLabels),
        datasets: [{
            data: @json($assetData),
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Geographic Allocation Chart
const geoCtx = document.getElementById('geographicAllocationChart').getContext('2d');
const geoChart = new Chart(geoCtx, {
    type: 'pie',
    data: {
        labels: @json($geoLabels),
        datasets: [{
            data: @json($geoData),
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function saveAnalysis() {
    // Implementation for saving analysis
    alert('تم حفظ التحليل بنجاح');
}

function exportResults() {
    // Implementation for exporting results
    alert('جاري تصدير النتائج...');
}
</script>
@endsection
