@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Sales Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تقرير المبيعات</h5>
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
                    <!-- Sales Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">إجمالي المبيعات</h5>
                                    <h3>{{ number_format($totalSales, 2) }} ريال</h3>
                                    <small>{{ $salesGrowth > 0 ? '+' : '' }}{{ number_format($salesGrowth, 1) }}% من الشهر الماضي</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">عدد المعاملات</h5>
                                    <h3>{{ $totalTransactions }}</h3>
                                    <small>{{ $transactionsGrowth > 0 ? '+' : '' }}{{ number_format($transactionsGrowth, 1) }}% من الشهر الماضي</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">متوسط سعر العقار</h5>
                                    <h3>{{ number_format($averagePrice, 2) }} ريال</h3>
                                    <small>{{ $priceGrowth > 0 ? '+' : '' }}{{ number_format($priceGrowth, 1) }}% من الشهر الماضي</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">معدل التحويل</h5>
                                    <h3>{{ number_format($conversionRate, 1) }}%</h3>
                                    <small>{{ $conversionGrowth > 0 ? '+' : '' }}{{ number_format($conversionGrowth, 1) }}% من الشهر الماضي</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Charts -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">اتجاه المبيعات الشهري</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesTrendChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">المبيعات حسب النوع</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesByTypeChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Sales Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">آخر المبيعات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>العقار</th>
                                                    <th>النوع</th>
                                                    <th>السعر</th>
                                                    <th>التاريخ</th>
                                                    <th>الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($recentSales->count() > 0)
                                                    @foreach($recentSales as $sale)
                                                        <tr>
                                                            <td>{{ $sale->property_title }}</td>
                                                            <td>
                                                                <span class="badge bg-secondary">{{ $sale->property_type }}</span>
                                                            </td>
                                                            <td>{{ number_format($sale->price, 2) }} ريال</td>
                                                            <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                                                            <td>
                                                                <span class="badge bg-success">{{ $sale->status }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            لا توجد مبيعات حديثة
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
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
$salesTrendLabels = $monthlySales->map(function($item) {
    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
})->toJson();
$salesTrendData = $monthlySales->pluck('total')->toJson();
$salesCountData = $monthlySales->pluck('count')->toJson();
$salesByTypeLabels = $salesByType->pluck('property_type')->toJson();
$salesByTypeData = $salesByType->pluck('total')->toJson();
?>

document.addEventListener('DOMContentLoaded', function() {
    // Clean JavaScript variables - no Blade syntax here
    var salesReportData = {
        salesTrendLabels: <?php echo $salesTrendLabels; ?>,
        salesTrendData: <?php echo $salesTrendData; ?>,
        salesCountData: <?php echo $salesCountData; ?>,
        salesByTypeLabels: <?php echo $salesByTypeLabels; ?>,
        salesByTypeData: <?php echo $salesByTypeData; ?>
    };
    
    // Sales Trend Chart
    var salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: salesReportData.salesTrendLabels,
            datasets: [{
                label: 'إجمالي المبيعات',
                data: salesReportData.salesTrendData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'عدد المعاملات',
                data: salesReportData.salesCountData,
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
                        text: 'المبيعات (ريال)'
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
                        text: 'عدد المعاملات'
                    }
                }
            }
        }
    });

    // Sales by Type Chart
    var salesByTypeCtx = document.getElementById('salesByTypeChart').getContext('2d');
    new Chart(salesByTypeCtx, {
        type: 'doughnut',
        data: {
            labels: salesReportData.salesByTypeLabels,
            datasets: [{
                data: salesReportData.salesByTypeData,
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
});

function exportReport(format) {
    window.location.href = '/reports/sales/export?format=' + format;
}

function printReport() {
    window.print();
}
</script>
@endpush
