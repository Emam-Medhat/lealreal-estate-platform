@extends('layouts.app')

@section('title', 'Sales Reports - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">Sales Reports</h1>
                    <p class="text-blue-100 mt-2">Analyze sales performance and trends</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reports.sales.create') }}" class="px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        New Sales Report
                    </a>
                    <a href="{{ route('reports.dashboard') }}" class="px-4 py-2 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="container mx-auto px-4 -mt-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($stats['total_sales'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Properties Sold</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['properties_sold']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-home text-blue-600"></i>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>إيرادات الشهر القادم:</span>
                                <strong>{{ number_format($nextMonthRevenue, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>معاملات الربع القادم:</span>
                                <strong>{{ $nextQuarterTransactions }}</strong>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>اتجاه الأسعار:</span>
                                <strong class="text-{{ $priceTrend === 'increasing' ? 'success' : ($priceTrend === 'decreasing' ? 'danger' : 'warning') }}">
                                    {{ $priceTrend === 'increasing' ? 'مرتفع' : ($priceTrend === 'decreasing' ? 'منخفض' : 'مستقر') }}
                                </strong>
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
document.addEventListener('DOMContentLoaded', function() {
    // Sales Report Data - Processed by Blade
    var salesReportData = {
        salesTrendLabels: {!! $monthlySales->map(function($item) {
            return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
        })->toJson() !!},
        salesTrendData: {!! $monthlySales->pluck('total')->toJson() !!},
        salesCountData: {!! $monthlySales->pluck('count')->toJson() !!},
        salesByTypeLabels: {!! $salesByType->pluck('property_type')->toJson() !!},
        salesByTypeData: {!! $salesByType->pluck('total')->toJson() !!}
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
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                },
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
</script>
@endpush
