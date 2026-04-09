@extends('layouts.app')

@section('title', 'لوحة تحكم الاستدامة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">لوحة تحكم الاستدامة</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('sustainability.reports.create') }}" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i> إنشاء تقرير
                    </a>
                    <a href="{{ route('sustainability.calculator') }}" class="btn btn-info">
                        <i class="fas fa-calculator"></i> حاسبة الاستدامة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                متوسط درجة الاستدامة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['avg_eco_score'], 1) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                العقارات المعتمدة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['certified_properties'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                متوسط البصمة الكربونية
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['avg_carbon_footprint'], 1) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cloud fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                إجمالي العقارات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_properties'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">اتجاهات الاستدامة</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">آخر 7 أيام</a>
                            <a class="dropdown-item" href="#">آخر 30 يوم</a>
                            <a class="dropdown-item" href="#">آخر 90 يوم</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="sustainabilityTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع شهادات الاستدامة</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="certificationPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخر التقييمات</h6>
                </div>
                <div class="card-body">
                    @if($recentAssessments->count() > 0)
                        @foreach($recentAssessments as $assessment)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $assessment->property->title ?? 'عقار غير معروف' }}</h6>
                                    <div class="small text-gray-500">
                                        {{ $assessment->created_at->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                                <div class="ml-auto">
                                    <span class="badge badge-{{ $assessment->eco_score >= 80 ? 'success' : ($assessment->eco_score >= 60 ? 'warning' : 'danger') }}">
                                        {{ number_format($assessment->eco_score, 1) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-500 py-3">
                            لا توجد تقييمات حديثة
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">التقارير الأخيرة</h6>
                </div>
                <div class="card-body">
                    @if($recentReports->count() > 0)
                        @foreach($recentReports as $report)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $report->title }}</h6>
                                    <div class="small text-gray-500">
                                        {{ $report->report_type_text }} - {{ $report->generated_at->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                                <div class="ml-auto">
                                    <a href="{{ route('sustainability.reports.download', $report) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-500 py-3">
                            لا توجد تقارير حديثة
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">نظرة عامة على الأداء</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-success">
                                    {{ $performanceOverview['excellent'] }}
                                </div>
                                <div class="small text-gray-500">ممتاز (80+)</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-info">
                                    {{ $performanceOverview['good'] }}
                                </div>
                                <div class="small text-gray-500">جيد (60-79)</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-warning">
                                    {{ $performanceOverview['average'] }}
                                </div>
                                <div class="small text-gray-500">متوسط (40-59)</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-danger">
                                    {{ $performanceOverview['poor'] }}
                                </div>
                                <div class="small text-gray-500">ضعيف (<40)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sustainability Trend Chart
    const trendCtx = document.getElementById('sustainabilityTrendChart').getContext('2d');
    const sustainabilityTrendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'متوسط درجة الاستدامة',
                data: @json($chartData),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Certification Pie Chart
    const pieCtx = document.getElementById('certificationPieChart').getContext('2d');
    const certificationPieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: @json($certificationLabels),
            datasets: [{
                data: @json($certificationData),
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
