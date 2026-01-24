@extends('layouts.app')

@section('title', 'تحليلات الإعلانات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">تحليلات الإعلانات</h1>

            <!-- Date Range Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('ads.analytics.dashboard') }}">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">من تاريخ</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">إلى تاريخ</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="campaign_id" class="form-label">الحملة</label>
                                <select class="form-select" id="campaign_id" name="campaign_id">
                                    <option value="">جميع الحملات</option>
                                    @foreach($campaigns as $campaign)
                                        <option value="{{ $campaign->id }}" {{ request('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                            {{ $campaign->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> تطبيق
                                    </button>
                                    <a href="{{ route('ads.analytics.dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Overview Metrics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ number_format($analytics['total_impressions']) }}</h4>
                                    <p class="card-text">إجمالي الظهور</p>
                                    <small class="text-white-50">
                                        {{ $analytics['impressions_change'] >= 0 ? '+' : '' }}{{ number_format($analytics['impressions_change'], 1) }}%
                                    </small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-eye fa-2x"></i>
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
                                    <h4 class="card-title">{{ number_format($analytics['total_clicks']) }}</h4>
                                    <p class="card-text">إجمالي النقرات</p>
                                    <small class="text-white-50">
                                        {{ $analytics['clicks_change'] >= 0 ? '+' : '' }}{{ number_format($analytics['clicks_change'], 1) }}%
                                    </small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-mouse-pointer fa-2x"></i>
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
                                    <h4 class="card-title">{{ number_format($analytics['total_conversions']) }}</h4>
                                    <p class="card-text">إجمالي التحويلات</p>
                                    <small class="text-white-50">
                                        {{ $analytics['conversions_change'] >= 0 ? '+' : '' }}{{ number_format($analytics['conversions_change'], 1) }}%
                                    </small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
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
                                    <h4 class="card-title">{{ number_format($analytics['total_spent'], 2) }} ريال</h4>
                                    <p class="card-text">إجمالي الإنفاق</p>
                                    <small class="text-white-50">
                                        {{ $analytics['spending_change'] >= 0 ? '+' : '' }}{{ number_format($analytics['spending_change'], 1) }}%
                                    </small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <h5 class="mb-0">اتجاهات الأداء</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">توزيع الأداء</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceDistributionChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Campaigns -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">أفضل الحملات أداءً</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>الحملة</th>
                                            <th>الظهور</th>
                                            <th>النقرات</th>
                                            <th>CTR</th>
                                            <th>التحويلات</th>
                                            <th>التكلفة</th>
                                            <th>ROI</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topCampaigns as $campaign)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $campaign->name }}</div>
                                                    <small class="text-muted">{{ $campaign->objective_label }}</small>
                                                </td>
                                                <td>{{ number_format($campaign->total_impressions) }}</td>
                                                <td>{{ number_format($campaign->total_clicks) }}</td>
                                                <td>{{ number_format($campaign->average_ctr, 2) }}%</td>
                                                <td>{{ number_format($campaign->total_conversions) }}</td>
                                                <td>{{ number_format($campaign->total_spent, 2) }} ريال</td>
                                                <td>
                                                    <span class="badge bg-{{ $campaign->roi > 0 ? 'success' : 'danger' }}">
                                                        {{ number_format($campaign->roi, 1) }}%
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('ads.campaigns.show', $campaign->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">لا توجد بيانات كافية</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device & Location Analytics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">تحليلات الجهاز</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="deviceAnalyticsChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">تحليلات الموقع</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="locationAnalyticsChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hourly Performance -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">الأداء بالساعة</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="hourlyPerformanceChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data from server - passed as global variables
    var performanceData = window.performanceData || [];
    var deviceData = window.deviceData || {};
    var locationData = window.locationData || {};
    var hourlyData = window.hourlyData || [];
    var totalImpressions = window.totalImpressions || 0;
    var totalClicks = window.totalClicks || 0;
    var totalConversions = window.totalConversions || 0;
    
    // Performance Trend Chart
    new Chart(document.getElementById('performanceTrendChart'), {
        type: 'line',
        data: {
            labels: performanceData.labels,
            datasets: [{
                label: 'الظهور',
                data: performanceData.impressions,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                yAxisID: 'y',
            }, {
                label: 'النقرات',
                data: performanceData.clicks,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                yAxisID: 'y1',
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
    
        
    // Performance Distribution Chart
    new Chart(document.getElementById('performanceDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['ظهور', 'نقرات', 'تحويلات'],
            datasets: [{
                data: [
                    totalImpressions,
                    totalClicks,
                    totalConversions
                ],
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(75, 192, 192)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
    
    // Device Analytics Chart
    new Chart(document.getElementById('deviceAnalyticsChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(deviceData),
            datasets: [{
                data: Object.values(deviceData),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
    
    // Location Analytics Chart
    new Chart(document.getElementById('locationAnalyticsChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(locationData),
            datasets: [{
                label: 'الظهور',
                data: Object.values(locationData),
                backgroundColor: 'rgba(54, 162, 235, 0.8)'
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
    
    // Hourly Performance Chart
    new Chart(document.getElementById('hourlyPerformanceChart'), {
        type: 'bar',
        data: {
            labels: hourlyData.labels,
            datasets: [{
                label: 'الظهور',
                data: hourlyData.impressions,
                backgroundColor: 'rgba(54, 162, 235, 0.8)'
            }, {
                label: 'النقرات',
                data: hourlyData.clicks,
                backgroundColor: 'rgba(255, 99, 132, 0.8)'
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

<!-- Pass data from Laravel to JavaScript -->
@php
    $dataScript = '';
    if(isset($performanceData)) {
        $dataScript .= 'window.performanceData = ' . json_encode($performanceData) . ';';
    }
    if(isset($deviceData)) {
        $dataScript .= 'window.deviceData = ' . json_encode($deviceData) . ';';
    }
    if(isset($locationData)) {
        $dataScript .= 'window.locationData = ' . json_encode($locationData) . ';';
    }
    if(isset($hourlyData)) {
        $dataScript .= 'window.hourlyData = ' . json_encode($hourlyData) . ';';
    }
    if(isset($analytics['total_impressions'])) {
        $dataScript .= 'window.totalImpressions = ' . $analytics['total_impressions'] . ';';
    }
    if(isset($analytics['total_clicks'])) {
        $dataScript .= 'window.totalClicks = ' . $analytics['total_clicks'] . ';';
    }
    if(isset($analytics['total_conversions'])) {
        $dataScript .= 'window.totalConversions = ' . $analytics['total_conversions'] . ';';
    }
@endphp
<script>{!! $dataScript !!}</script>
@endsection
