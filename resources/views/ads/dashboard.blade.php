@extends('layouts.app')

@section('title', 'لوحة تحكم الإعلانات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">لوحة تحكم الإعلانات</h1>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $stats['total_ads'] }}</h4>
                                    <p class="card-text">إجمالي الإعلانات</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-ad fa-2x"></i>
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
                                    <h4 class="card-title">{{ number_format($stats['total_impressions']) }}</h4>
                                    <p class="card-text">إجمالي الظهور</p>
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
                                    <h4 class="card-title">{{ number_format($stats['total_clicks']) }}</h4>
                                    <p class="card-text">إجمالي النقرات</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-mouse-pointer fa-2x"></i>
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
                                    <h4 class="card-title">{{ number_format($stats['total_spent'], 2) }} ريال</h4>
                                    <p class="card-text">إجمالي الإنفاق</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Overview -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">نظرة عامة على الأداء</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">مقاييس الأداء الرئيسية</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>متوسط CTR</span>
                                    <span>{{ number_format($stats['avg_ctr'], 2) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: {{ min(100, $stats['avg_ctr'] * 10) }}%;"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>متوسط CPC</span>
                                    <span>{{ number_format($stats['avg_cpc'], 2) }} ريال</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ min(100, ($stats['avg_cpc'] / 10) * 100) }}%;"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>معدل التحويل</span>
                                    <span>{{ number_format($stats['conversion_rate'], 2) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" style="width: {{ min(100, $stats['conversion_rate'] * 10) }}%;"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>ROI</span>
                                    <span>{{ number_format($stats['roi'], 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: {{ min(100, $stats['roi']) }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">آخر الإعلانات</h5>
                        </div>
                        <div class="card-body">
                            @forelse($recentAds as $ad)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        @if($ad->image_url)
                                            <img src="{{ $ad->image_url_full }}" alt="{{ $ad->title }}" 
                                                 class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-ad text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $ad->title }}</div>
                                            <small class="text-muted">{{ $ad->created_at->format('Y-m-d') }}</small>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $ad->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $ad->status == 'active' ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">لا توجد إعلانات حديثة</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">الحملات النشطة</h5>
                        </div>
                        <div class="card-body">
                            @forelse($activeCampaigns as $campaign)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <div class="fw-bold">{{ $campaign->name }}</div>
                                        <small class="text-muted">{{ $campaign->ads->count() }} إعلان</small>
                                    </div>
                                    <div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ number_format($campaign->total_spent, 2) }} ريال</small>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $campaign->budget_utilization }}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">لا توجد حملات نشطة</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Ads -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">أفضل الإعلانات أداءً</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>الإعلان</th>
                                            <th>الظهور</th>
                                            <th>النقرات</th>
                                            <th>CTR</th>
                                            <th>التحويلات</th>
                                            <th>التكلفة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topAds as $ad)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($ad->image_url)
                                                            <img src="{{ $ad->image_url_full }}" alt="{{ $ad->title }}" 
                                                                 class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $ad->title }}</div>
                                                            <small class="text-muted">{{ $ad->type }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($ad->impressions_count) }}</td>
                                                <td>{{ number_format($ad->clicks_count) }}</td>
                                                <td>{{ number_format($ad->impressions_count > 0 ? ($ad->clicks_count / $ad->impressions_count) * 100 : 0, 2) }}%</td>
                                                <td>{{ number_format($ad->conversions_count) }}</td>
                                                <td>{{ number_format($ad->total_spent, 2) }} ريال</td>
                                                <td>
                                                    <a href="{{ route('ads.show', $ad->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">لا توجد بيانات كافية</td>
                                            </tr>
                                        @endforelse
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const performanceData = @json($performanceData);
    
    new Chart(ctx, {
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
            }, {
                label: 'التحويلات',
                data: performanceData.conversions,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                yAxisID: 'y1',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'الظهور'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'النقرات/التحويلات'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                },
            }
        }
    });
});
</script>
@endsection
