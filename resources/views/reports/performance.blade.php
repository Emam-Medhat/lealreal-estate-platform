@extends('layouts.app')

@section('title', 'تقارير الأداء')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">تقارير الأداء</h2>
                    <p class="text-muted">تحليل شامل لأداء العقارات والتسويق</p>
                </div>
                <div>
                    <a href="{{ route('reports.performance.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> تقرير أداء جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalProperties }}</h4>
                            <p class="mb-0">إجمالي العقارات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-home fa-2x"></i>
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
                            <h4 class="mb-0">{{ $activeListings }}</h4>
                            <p class="mb-0">الإعلانات النشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x"></i>
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
                            <h4 class="mb-0">{{ $soldProperties }}</h4>
                            <p class="mb-0">العقارات المباعة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($averageDaysOnMarket, 1) }}</h4>
                            <p class="mb-0">متوسط الأيام في السوق</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">مؤشرات الأداء الرئيسية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ number_format($averageViews, 0) }}</h4>
                                <p class="text-muted mb-0">متوسط المشاهدات</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ number_format($averageInquiries, 1) }}</h4>
                                <p class="text-muted mb-0">متوسط الاستفسارات</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h4 class="text-info">{{ number_format($viewToInquiryRate, 1) }}%</h4>
                                <p class="text-muted mb-0">معدل التحويل (مشاهدات لاستفسارات)</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ number_format($inquiryToSaleRate, 1) }}%</h4>
                                <p class="text-muted mb-0">معدل التحويل (استفسارات لمبيعات)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">نقاط الأداء</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>نقطة الأداء الإجمالية</span>
                            <span>{{ number_format($overallScore, 1) }}/100</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: {{ min($overallScore, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>نقطة الإعلانات</span>
                            <span>{{ number_format($listingScore, 1) }}/100</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ min($listingScore, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>نقطة التسويق</span>
                            <span>{{ number_format($marketingScore, 1) }}/100</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: {{ min($marketingScore, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>نقطة التحويل</span>
                            <span>{{ number_format($conversionScore, 1) }}/100</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: {{ min($conversionScore, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">اتجاه الأداء الشهري</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الأداء حسب النوع</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceByTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Properties -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">العقارات الأكثر مشاهدة</h5>
                </div>
                <div class="card-body">
                    @if($topViewedProperties->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topViewedProperties as $property)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $property->title }}</h6>
                                        <small class="text-muted">{{ $property->location }}</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary">{{ number_format($property->views_count) }}</span>
                                        <small class="text-muted">مشاهدة</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-eye fa-2x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد بيانات متاحة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">العقارات الأكثر استفساراً</h5>
                </div>
                <div class="card-body">
                    @if($topInquiredProperties->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topInquiredProperties as $property)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $property->title }}</h6>
                                        <small class="text-muted">{{ $property->location }}</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-success">{{ number_format($property->inquiries_count) }}</span>
                                        <small class="text-muted">استفسار</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-question fa-2x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد بيانات متاحة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance by Type -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الأداء حسب نوع العقار</h5>
                </div>
                <div class="card-body">
                    @if($performanceByType->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>نوع العقار</th>
                                        <th>العدد</th>
                                        <th>متوسط المشاهدات</th>
                                        <th>متوسط الاستفسارات</th>
                                        <th>معدل التحويل</th>
                                        <th>الأداء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($performanceByType as $type)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $type->type }}</span>
                                            </td>
                                            <td>{{ $type->count }}</td>
                                            <td>{{ number_format($type->avg_views, 1) }}</td>
                                            <td>{{ number_format($type->avg_inquiries, 1) }}</td>
                                            <td>{{ number_format($type->avg_inquiries > 0 ? ($type->avg_inquiries / $type->avg_views) * 100 : 0, 1) }}%</td>
                                            <td>
                                                @php
                                                    $performanceScore = min(($type->avg_views / 100) * 30 + ($type->avg_inquiries / 10) * 40, 100);
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-info" style="width: {{ $performanceScore }}%">
                                                        {{ number_format($performanceScore, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-2x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد بيانات أداء متاحة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .progress {
        height: 8px;
    }
    
    .list-group-item {
        border: none;
        border-bottom: 1px solid #dee2e6;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Report Data - Processed by Blade
    var performanceReportData = {
        performanceTrendLabels: {!! $monthlyPerformance->map(function($item) {
            return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
        })->toJson() !!},
        propertiesCountData: {!! $monthlyPerformance->pluck('properties_count')->toJson() !!},
        totalViewsData: {!! $monthlyPerformance->pluck('total_views')->toJson() !!},
        avgViewTimeData: {!! $monthlyPerformance->pluck('avg_view_time')->toJson() !!},
        performanceByTypeLabels: {!! $performanceByType->pluck('property_type')->toJson() !!},
        performanceByTypeData: {!! $performanceByType->pluck('performance_score')->toJson() !!},
        totalInquiriesData: {!! $monthlyPerformance->pluck('total_inquiries')->toJson() !!},
        avgViewsData: {!! $performanceByType->pluck('avg_views')->toJson() !!},
        avgInquiriesData: {!! $performanceByType->pluck('avg_inquiries')->toJson() !!}
    };
    
    // Performance Trend Chart
    var performanceTrendCtx = document.getElementById('performanceTrendChart').getContext('2d');
    new Chart(performanceTrendCtx, {
        type: 'line',
        data: {
            labels: performanceReportData.performanceTrendLabels,
            datasets: [{
                label: '新しい物件',
                data: performanceReportData.propertiesCountData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: '総閲覧数',
                data: performanceReportData.totalViewsData,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }, {
                label: 'إجمالي الاستفسارات',
                data: performanceReportData.totalInquiriesData,
                borderColor: 'rgb(255, 205, 86)',
                backgroundColor: 'rgba(255, 205, 86, 0.2)',
                tension: 0.1,
                yAxisID: 'y2'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'العقارات'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'المشاهدات'
                    }
                },
                y2: {
                    type: 'linear',
                    display: false,
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });

    // Performance by Type Chart
    const performanceByTypeCtx = document.getElementById('performanceByTypeChart').getContext('2d');
    new Chart(performanceByTypeCtx, {
        type: 'radar',
        data: {
            labels: window.performanceReportData.performanceByTypeLabels,
            datasets: [{
                label: 'متوسط المشاهدات',
                data: window.performanceReportData.avgViewsData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
            }, {
                label: 'متوسط الاستفسارات',
                data: window.performanceReportData.avgInquiriesData,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
