@extends('layouts.app')

@section('title')
    تحليل حملة التسويق
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('marketing.index') }}">التسويق العقاري</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('marketing.property-marketing.show', $campaign) }}">{{ $campaign->title }}</a></li>
                    <li class="breadcrumb-item active">تحليل</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">تحليل حملة التسويق</h1>
            <p class="text-muted mb-0">{{ $campaign->title }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshAnalytics()">
                <i class="fas fa-sync-alt me-1"></i>
                تحديث
            </button>
            <button class="btn btn-outline-success" onclick="downloadReport()">
                <i class="fas fa-download me-1"></i>
                تحميل تقرير
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">الانطباعات</h6>
                            <h3 class="mb-0">{{ number_format($analytics['total_impressions']) }}</h3>
                            <small class="text-white-50">+{{ number_format($analytics['impressions_change']) }}%</small>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">النقرات</h6>
                            <h3 class="mb-0">{{ number_format($analytics['total_clicks']) }}</h3>
                            <small class="text-white-50">+{{ number_format($analytics['clicks_change']) }}%</small>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-mouse-pointer fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">التحويلات</h6>
                            <h3 class="mb-0">{{ number_format($analytics['total_conversions']) }}</h3>
                            <small class="text-white-50">+{{ number_format($analytics['conversions_change']) }}%</small>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">العائد على الإنفاق</h6>
                            <h3 class="mb-0">{{ number_format($analytics['roi'] ?? 0) }}%</h3>
                            <small class="text-white-50">+{{ number_format($analytics['roi_change'] ?? 0) }}%</small>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                    <h5 class="card-title mb-0">أداء الحملة</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">توزيع القنوات</h5>
                </div>
                <div class="card-body">
                    <canvas id="channelChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معدلات الأداء</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <h4>{{ $analytics['click_through_rate'] ?? 0 }}%</h4>
                                <small class="text-muted">معدل النقرات</small>
                                <div class="progress mt-2">
                                    <div class="progress-bar" style="width: {{ min(100, $analytics['click_through_rate'] ?? 0) }}%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <h4>{{ $analytics['conversion_rate'] ?? 0 }}%</h4>
                                <small class="text-muted">معدل التحويل</small>
                                <div class="progress mt-2">
                                    <div class="progress-bar" style="width: {{ min(100, $analytics['conversion_rate'] ?? 0) }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <h4>{{ number_format($analytics['cost_per_click'] ?? 0) }}</h4>
                                <small class="text-muted">تكلفة النقرة</small>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-warning" style="width: {{ min(100, ($analytics['cost_per_click'] ?? 0) * 5) }}%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <h4>{{ number_format($analytics['cost_per_conversion'] ?? 0) }}</h4>
                                <small class="text-muted">تكلفة التحويل</small>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-danger" style="width: {{ min(100, ($analytics['cost_per_conversion'] ?? 0) * 2) }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الأداء حسب الوقت</h5>
                </div>
                <div class="card-body">
                    <canvas id="timeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Channel Performance -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">أداء القنوات</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>القناة</th>
                            <th>الانطباعات</th>
                            <th>النقرات</th>
                            <th>التحويلات</th>
                            <th>معدل النقرات</th>
                            <th>معدل التحويل</th>
                            <th>التكلفة</th>
                            <th>العائد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analytics['channel_performance'] ?? [] as $channel => $data)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $channel }}</span>
                                </td>
                                <td>{{ number_format($data['impressions']) }}</td>
                                <td>{{ number_format($data['clicks']) }}</td>
                                <td>{{ number_format($data['conversions']) }}</td>
                                <td>{{ $data['click_through_rate'] }}%</td>
                                <td>{{ $data['conversion_rate'] }}%</td>
                                <td>{{ number_format($data['cost'], 2) }}</td>
                                <td>{{ $data['roi'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Audience Insights -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">رؤى الجمهور</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6>الفئات العمرية</h6>
                            <canvas id="ageChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>الجنس</h6>
                            <canvas id="genderChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الجغرافيا</h5>
                </div>
                <div class="card-body">
                    <canvas id="locationChart" height="350"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion Funnel -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">مسار التحويل</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <canvas id="funnelChart" height="300"></canvas>
                </div>
                <div class="col-md-4">
                    <h6>تفاصيل المسار</h6>
                    <ul class="list-unstyled">
                        @foreach($analytics['conversion_funnel'] ?? [] as $stage => $data)
                            <li class="mb-2">
                                <strong>{{ $stage }}:</strong>
                                <br>
                                <small>{{ number_format($data['users']) }} مستخدم</small>
                                <br>
                                <small>{{ $data['rate'] }} معدل</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">توصيات التحسين</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($analytics['recommendations'] ?? [] as $recommendation)
                    <div class="col-md-4 mb-3">
                        <div class="alert alert-{{ $recommendation['priority'] === 'high' ? 'danger' : ($recommendation['priority'] === 'medium' ? 'warning' : 'info') }}">
                            <h6>{{ $recommendation['title'] }}</h6>
                            <p class="mb-0">{{ $recommendation['description'] }}</p>
                            <small>الأثر: {{ $recommendation['impact'] }} | الجهد: {{ $recommendation['effort'] }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: @json($analytics['performance_timeline']['labels'] ?? []),
            datasets: [{
                label: 'الانطباعات',
                data: @json($analytics['performance_timeline']['impressions'] ?? []),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
            }, {
                label: 'النقرات',
                data: @json($analytics['performance_timeline']['clicks'] ?? []),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1,
            }, {
                label: 'التحويلات',
                data: @json($analytics['performance_timeline']['conversions'] ?? []),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            }
        }
    });

    // Channel Chart
    const channelCtx = document.getElementById('channelChart').getContext('2d');
    new Chart(channelCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($analytics['channel_performance'] ?? [])),
            datasets: [{
                data: @json(array_column($analytics['channel_performance'] ?? [], 'impressions')),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Time Chart
    const timeCtx = document.getElementById('timeChart').getContext('2d');
    new Chart(timeCtx, {
        type: 'bar',
        data: {
            labels: @json($analytics['time_performance']['labels'] ?? []),
            datasets: [{
                label: 'الانطباعات',
                data: @json($analytics['time_performance']['impressions'] ?? []),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });

    // Age Chart
    const ageCtx = document.getElementById('ageChart').getContext('2d');
    new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($analytics['audience_insights']['age_groups'] ?? [])),
            datasets: [{
                label: 'النسبة المئوية',
                data: @json(array_values($analytics['audience_insights']['age_groups'] ?? [])),
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });

    // Gender Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'pie',
        data: {
            labels: @json(array_keys($analytics['audience_insights']['genders'] ?? [])),
            datasets: [{
                data: @json(array_values($analytics['audience_insights']['genders'] ?? [])),
                backgroundColor: ['rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Location Chart
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    new Chart(locationCtx, {
        type: 'horizontalBar',
        data: {
            labels: @json(array_keys($analytics['audience_insights']['locations'] ?? [])),
            datasets: [{
                label: 'النسبة المئوية',
                data: @json(array_values($analytics['audience_insights']['locations'] ?? [])),
                backgroundColor: 'rgba(255, 206, 86, 0.8)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });

    // Funnel Chart
    const funnelCtx = document.getElementById('funnelChart').getContext('2d');
    new Chart(funnelCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($analytics['conversion_funnel'] ?? [])),
            datasets: [{
                label: 'عدد المستخدمين',
                data: @json(array_column($analytics['conversion_funnel'] ?? [], 'users')),
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                ],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });

    function refreshAnalytics() {
        location.reload();
    }

    function downloadReport() {
        fetch(`/marketing/property-marketing/{{ $campaign->id }}/download`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `campaign-{{ $campaign->id }}-analytics.pdf`;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => {
                document.body.removeChild(a);
            }, 0);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء تحميل التقرير');
        });
    }
</script>
@endpush
