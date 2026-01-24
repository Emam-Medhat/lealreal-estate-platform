@extends('layouts.app')

@section('title', 'تقارير السوق')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">تقارير السوق</h2>
                    <p class="text-muted">تحليل شامل لاتجاهات السوق والمنافسة</p>
                </div>
                <div>
                    <a href="{{ route('reports.market.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> تقرير سوق جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalMarketValue, 2) }}</h4>
                            <p class="mb-0">إجمالي قيمة السوق</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <i class="fas fa-home fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($averageMarketPrice, 2) }}</h4>
                            <p class="mb-0">متوسط سعر السوق</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($marketGrowth, 1) }}%</h4>
                            <p class="mb-0">نمو السوق</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Trends -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">اتجاهات السوق</h5>
                </div>
                <div class="card-body">
                    <canvas id="marketTrendsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">توزيع السوق</h5>
                </div>
                <div class="card-body">
                    <canvas id="marketDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Analysis -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">تحليل الأسعار</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ number_format($averagePrice, 2) }}</h4>
                                <p class="text-muted mb-0">متوسط السعر</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ number_format($medianPrice, 2) }}</h4>
                                <p class="text-muted mb-0">الوسيط السعر</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h4 class="text-info">{{ number_format($minPrice, 2) }}</h4>
                                <p class="text-muted mb-0">أدنى سعر</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ number_format($maxPrice, 2) }}</h4>
                                <p class="text-muted mb-0">أعلى سعر</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">توزيع الأسعار</h5>
                </div>
                <div class="card-body">
                    <canvas id="priceDistributionChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">تحليل المخزون</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center mb-3">
                                <h4 class="text-primary">{{ $totalInventory }}</h4>
                                <p class="text-muted mb-0">إجمالي المخزون</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center mb-3">
                                <h4 class="text-success">{{ $newListings }}</h4>
                                <p class="text-muted mb-0">إعلانات جديدة (30 يوم)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center mb-3">
                                <h4 class="text-warning">{{ $soldProperties }}</h4>
                                <p class="text-muted mb-0">عقارات مباعة (30 يوم)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center mb-3">
                                <h4 class="text-info">{{ number_format($daysOnMarket, 1) }}</h4>
                                <p class="text-muted mb-0">متوسط الأيام في السوق</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>المخزون حسب النوع</h6>
                            <canvas id="inventoryByTypeChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6>المخزون حسب الموقع</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>الموقع</th>
                                            <th>العدد</th>
                                            <th>النسبة المئوية</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventoryByLocation as $location)
                                            <tr>
                                                <td>{{ $location->location }}</td>
                                                <td>{{ $location->count }}</td>
                                                <td>
                                                    <div class="progress" style="height: 15px;">
                                                        <div class="progress-bar bg-info" style="width: {{ $location->percentage }}%">
                                                            {{ number_format($location->percentage, 1) }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demand Indicators -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">مؤشرات الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h4 class="text-primary">{{ number_format($searchVolume, 0) }}</h4>
                                <p class="text-muted mb-0">حجم البحث (شهري)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h4 class="text-success">{{ number_format($viewToInquiryRate, 1) }}%</h4>
                                <p class="text-muted mb-0">معدل التحويل (مشاهدات لاستفسارات)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h4 class="text-warning">{{ number_format($inquiryToViewRatio, 1) }}</h4>
                                <p class="text-muted mb-0">نسبة الاستفسارات للمشاهدات</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>اتجاهات البحث</h6>
                            <canvas id="searchTrendsChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6>الطلب حسب النوع</h6>
                            <canvas id="demandByTypeChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Competitive Analysis -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">التحليل التنافسي</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المنافس</th>
                                    <th>حصة السوق</th>
                                    <th>متوسط السعر</th>
                                    <th>عدد العقارات</th>
                                    <th>معدل البيع</th>
                                    <th>الأداء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($competitors as $competitor)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $competitor->logo ?? asset('images/default-logo.png') }}" 
                                                     alt="{{ $competitor->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="30" height="30">
                                                {{ $competitor->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $competitor->market_share }}%">
                                                    {{ number_format($competitor->market_share, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($competitor->average_price, 2) }}</td>
                                        <td>{{ $competitor->property_count }}</td>
                                        <td>{{ number_format($competitor->sale_rate, 1) }}%</td>
                                        <td>
                                            <span class="badge bg-{{ $competitor->performance >= 80 ? 'success' : ($competitor->performance >= 60 ? 'warning' : 'danger') }}">
                                                {{ number_format($competitor->performance, 1) }}
                                            </span>
                                        </tdedata
                                    </tr>
                                @endforeach
                            </tbody
                        </tbody>
                    </重复
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125arth 0.25rem rgba((0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .progress {
        height: 8px;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data prepared by Blade
    window.marketReportData = {
        marketTrendsLabels: {{ $marketTrends->pluck('month')->toJson() }},
        averagePriceData: {{ $marketTrends->pluck('average_price')->toJson() }},
        propertyCountData: {{ $marketTrends->pluck('property_count')->toJson() }},
        priceByTypeLabels: {{ $priceByType->pluck('property_type')->toJson() }},
        priceByTypeData: {{ $priceByType->pluck('average_price')->toJson() }},
        inventoryLabels: {{ $inventoryAnalysis->pluck('property_type')->toJson() }},
        inventoryData: {{ $inventoryAnalysis->pluck('total_count')->toJson() }},
        priceRangesLabels: {{ $priceRanges->pluck('range')->toJson() }},
        priceRangesData: {{ $priceRanges->pluck('count')->toJson() }},
        searchTrendsLabels: {{ $searchTrends->pluck('date')->toJson()就看
            searchambi: {{ $<|code_suffix|>    .     Labels: {{ureau->.toJson() }},
        demandkel->.toJsonirenko->toJson() coro->ih->toJson() Across->toJson() Developer->toJson()estone->toJson jquery->toJsonVirgin->toJson涌->toJson() }},
        demand mutation->toJson_vp->toJson()uke->toJson() Kemp->toJson失落->toJsonuw->toJson/read->toJson()atel->toJsoneral->toJson Velocity->toJson Fleming->toJson hatch->toJson |
    };

    // Market Trends Chart
    const marketTrendsCtx = document.getElementById('marketTrendsChart').getContext('2d');
    new Chart(marketTrendsCtx, {
        type: 'line',
        data: {
            labels: window.marketReportData.marketTrendsLabels,
            datasets: [{
                label: 'متوسط السعر',
                data: window.marketReportData.averagePriceData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'عدد العقارات',
                data: window.marketReportData.propertyCountData,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
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
                        text: 'السعر'
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
                        text: 'العدد'
                    }
                }
            }
        }
    });

    // Market Distribution Chart
    const marketDistributionCtx = document.getElementById('marketDistributionChart').getContext('2d');
    new Chart(marketDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: priceByTypeLabels,
            datasets: [{
                data: priceByTypeData,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Price Distribution Chart
    const priceDistributionCtx = document.getElementById('priceDistributionChart').getContext('2d');
    new Chart(priceDistributionCtx, {
        type: 'bar',
        data: {
            labels: {{ $priceRanges->pluck('range')->toJson() }},
            datasets: [{
                label: 'عدد العقارات',
                data: {{ $priceRanges->pluck('count')->toJson() }},
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
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

    // Inventory by Type Chart
    const inventoryByTypeCtx = document.getElementById('inventoryByTypeChart').getContext('2d');
    new Chart(inventoryByTypeCtx, {
        type: 'pie',
        data: {
            labels: @json($inventoryByType->pluck('type')),
            datasets: [{
                data: @json($inventoryByType->pluck('count')),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Search Trends Chart
    const searchTrendsCtx = document.getElementById('searchTrendsChart').getContext('2d');
    new Chart(searchTrendsCtx, {
        type: 'line',
        data: {
            labels: {{ $searchTrends->pluck('date')->toJson() }},
            datasets: [{
                label: 'حجم البحث',
                data: {{ $searchTrends->pluck('volume')->toJson() }},
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

    // Demand by Type Chart
    const demandByTypeCtx = document.getElementById('demandByTypeChart').getContext('2d');
    new Chart(demandByTypeCtx, {
        type: 'bar',
        data: {
            labels: {{ $demandByType->pluck('type')->toJson() }},
            datasets: [{
                label: 'مؤشر الطلب',
                data: {{ $demandByType->pluck('demand_index')->toJson() }},
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
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
});
</script>
@endpush
