@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Market Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تقرير السوق</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportReport('pdf')">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="exportReport('excel')">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="printReport()">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Market Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">متوسط السعر</h5>
                                    <h3>{{ number_format($averageMarketPrice, 2) }} ريال</h3>
                                    <small>{{ $priceChange > 0 ? '+' : '' }}{{ number_format($priceChange, 1) }}% من الشهر الماضي</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">إجمالي العقارات</h5>
                                    <h3>{{ $totalProperties }}</h3>
                                    <small>{{ $propertiesChange > 0 ? '+' : '' }}{{ number_format($propertiesChange, 1) }}% من الشهر الماضي</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">مؤشر الطلب</h5>
                                    <h3>{{ number_format($demandIndex, 1) }}</h3>
                                    <small>{{ $demandChange > 0 ? '+' : '' }}{{ number_format($demandChange, 1) }}% من الشهر الماضي</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">حجم البحث</h5>
                                    <h3>{{ number_format($searchVolume) }}</h3>
                                    <small>{{ $searchChange > 0 ? '+' : '' }}{{ number_format($searchChange, 1) }}% من الشهر الماضي</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Market Charts -->
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

                    <!-- Additional Charts -->
                    <div class="row mb-4">
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
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">اتجاهات البحث</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="searchTrendsChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Demand Analysis -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">تحليل الطلب حسب النوع</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="demandByTypeChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
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
    
    .btn-group .btn {
        margin: 0 1px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data preprocessing - PHP processes this, JavaScript uses it
<?php
$marketTrendsLabels = $marketTrends->pluck('month')->toJson();
$averagePriceData = $marketTrends->pluck('average_price')->toJson();
$propertyCountData = $marketTrends->pluck('property_count')->toJson();
$priceByTypeLabels = $priceByType->pluck('property_type')->toJson();
$priceByTypeData = $priceByType->pluck('average_price')->toJson();
$priceRangesLabels = $priceRanges->pluck('range')->toJson();
$priceRangesData = $priceRanges->pluck('count')->toJson();
$searchTrendsLabels = $searchTrends->pluck('date')->toJson();
$searchTrendsData = $searchTrends->pluck('volume')->toJson();
$demandByTypeLabels = $demandByType->pluck('type')->toJson();
$demandByTypeData = $demandByType->pluck('demand_index')->toJson();
?>

document.addEventListener('DOMContentLoaded', function() {
    // Clean JavaScript variables - no Blade syntax here
    var marketReportData = {
        marketTrendsLabels: <?php echo $marketTrendsLabels; ?>,
        averagePriceData: <?php echo $averagePriceData; ?>,
        propertyCountData: <?php echo $propertyCountData; ?>,
        priceByTypeLabels: <?php echo $priceByTypeLabels; ?>,
        priceByTypeData: <?php echo $priceByTypeData; ?>,
        priceRangesLabels: <?php echo $priceRangesLabels; ?>,
        priceRangesData: <?php echo $priceRangesData; ?>,
        searchTrendsLabels: <?php echo $searchTrendsLabels; ?>,
        searchTrendsData: <?php echo $searchTrendsData; ?>,
        demandByTypeLabels: <?php echo $demandByTypeLabels; ?>,
        demandByTypeData: <?php echo $demandByTypeData; ?>
    };
    
    // Market Trends Chart
    var marketTrendsCtx = document.getElementById('marketTrendsChart').getContext('2d');
    new Chart(marketTrendsCtx, {
        type: 'line',
        data: {
            labels: marketReportData.marketTrendsLabels,
            datasets: [{
                label: 'متوسط السعر',
                data: marketReportData.averagePriceData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'عدد العقارات',
                data: marketReportData.propertyCountData,
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
    var marketDistributionCtx = document.getElementById('marketDistributionChart').getContext('2d');
    new Chart(marketDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: marketReportData.priceByTypeLabels,
            datasets: [{
                data: marketReportData.priceByTypeData,
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
    var priceDistributionCtx = document.getElementById('priceDistributionChart').getContext('2d');
    new Chart(priceDistributionCtx, {
        type: 'bar',
        data: {
            labels: marketReportData.priceRangesLabels,
            datasets: [{
                label: 'عدد العقارات',
                data: marketReportData.priceRangesData,
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

    // Search Trends Chart
    var searchTrendsCtx = document.getElementById('searchTrendsChart').getContext('2d');
    new Chart(searchTrendsCtx, {
        type: 'line',
        data: {
            labels: marketReportData.searchTrendsLabels,
            datasets: [{
                label: 'حجم البحث',
                data: marketReportData.searchTrendsData,
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
    var demandByTypeCtx = document.getElementById('demandByTypeChart').getContext('2d');
    new Chart(demandByTypeCtx, {
        type: 'bar',
        data: {
            labels: marketReportData.demandByTypeLabels,
            datasets: [{
                label: 'مؤشر الطلب',
                data: marketReportData.demandByTypeData,
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

function exportReport(format) {
    window.location.href = '/reports/market/export?format=' + format;
}

function printReport() {
    window.print();
}
</script>
@endpush
