@extends('layouts.app')

@section('title', 'أداء العقار')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">{{ $performanceData['property']->title ?? 'تقرير أداء العقار' }}</h2>
                    <p class="text-muted">{{ $performanceData['property']->address ?? '' }}</p>
                </div>
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">إجمالي الإيرادات</h6>
                    <h3 class="mb-0">{{ number_format($performanceData['revenue']['total'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">نسبة الإشغال</h6>
                    <h3 class="mb-0">{{ $performanceData['performance_metrics']['occupancy_rate'] ?? 0 }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">عائد الاستثمار (ROI)</h6>
                    <h3 class="mb-0">{{ $performanceData['performance_metrics']['roi'] ?? 0 }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">صافي الدخل</h6>
                    <h3 class="mb-0">{{ number_format($performanceData['revenue']['net_income'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Details -->
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">تحليل الإيرادات والمصروفات</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">تفاصيل الأداء</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            المصروفات التشغيلية
                            <span class="badge bg-danger rounded-pill">{{ number_format($performanceData['revenue']['expenses'] ?? 0, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            إجمالي الإيجارات
                            <span class="badge bg-success rounded-pill">{{ number_format($performanceData['revenue']['rental_income'] ?? 0, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            متوسط مدة الإيجار
                            <span>{{ $performanceData['performance_metrics']['avg_lease_duration'] ?? 0 }} شهر</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['إيرادات', 'مصروفات', 'صافي الدخل'],
            datasets: [{
                label: 'المبلغ',
                data: [
                    {{ $performanceData['revenue']['total'] ?? 0 }},
                    {{ $performanceData['revenue']['expenses'] ?? 0 }},
                    {{ $performanceData['revenue']['net_income'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection
