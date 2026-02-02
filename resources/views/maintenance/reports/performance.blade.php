@extends('admin.layouts.admin')

@section('title', 'تقارير الأداء')

@section('content')
<div class="container-fluid" dir="rtl">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تقارير الأداء</h1>
            <p class="text-muted mb-0">تحليل الأداء الشهري ومقارنات الفرق</p>
        </div>
        <div>
            <a href="{{ route('maintenance.reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right ms-2"></i>
                العودة للتقارير
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print ms-2"></i>
                طباعة
            </button>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ $monthlyStats->sum('total_orders') ?? 0 }}</h2>
                    <p class="mb-0">إجمالي الأوامر</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ $monthlyStats->sum('completed_orders') ?? 0 }}</h2>
                    <p class="mb-0">أوامر مكتملة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ $monthlyStats->avg('completion_rate') ?? 0 }}%</h2>
                    <p class="mb-0">معدل الإنجاز</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ $monthlyStats->avg('avg_completion_time') ?? 0 }} يوم</h2>
                    <p class="mb-0">متوسط وقت الإنجاز</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">الأداء الشهري</h5>
        </div>
        <div class="card-body">
            <canvas id="monthlyPerformanceChart" width="400" height="150"></canvas>
        </div>
    </div>

    <!-- Team Performance Comparison -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">مقارنة أداء الفرق</h5>
                </div>
                <div class="card-body">
                    <canvas id="teamComparisonChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أفضل الفرق</h5>
                </div>
                <div class="card-body">
                    @php
                        $topTeams = $teamPerformance->sortByDesc('completion_rate')->take(5);
                    @endphp
                    @if($topTeams->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topTeams as $team)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $team->team_name }}</h6>
                                        <small class="text-muted">{{ $team->total_orders }} أمر</small>
                                    </div>
                                    <div class="text-left">
                                        <span class="badge bg-success">{{ number_format($team->completion_rate, 1) }}%</span>
                                        <br>
                                        <small class="text-muted">{{ number_format($team->avg_completion_time, 1) } يوم</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-trophy fa-3x mb-3"></i>
                            <p>لا توجد بيانات كافية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics Table -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">تفاصيل الأداء الشهري</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>الشهر</th>
                            <th>إجمالي الأوامر</th>
                            <th>الأوامر المكتملة</th>
                            <th>معدل الإنجاز</th>
                            <th>متوسط وقت الإنجاز</th>
                            <th>متوسط التكلفة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($monthlyStats) && $monthlyStats->count() > 0)
                            @foreach($monthlyStats as $stat)
                                <tr>
                                    <td>{{ $stat->month }}</td>
                                    <td>{{ $stat->total_orders }}</td>
                                    <td>{{ $stat->completed_orders }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: {{ $stat->completion_rate }}%"></div>
                                            </div>
                                            <span class="badge bg-success">{{ number_format($stat->completion_rate, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($stat->avg_completion_time, 1) }} يوم</td>
                                    <td>{{ number_format($stat->avg_cost, 2) }} ريال</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    لا توجد بيانات أداء حالياً
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .badge {
        font-size: 0.75rem;
    }
    .progress {
        background-color: #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Performance Chart
    const monthlyCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json(isset($monthlyStats) ? $monthlyStats->pluck('month')->toArray() : []),
            datasets: [{
                label: 'إجمالي الأوامر',
                data: @json(isset($monthlyStats) ? $monthlyStats->pluck('total_orders')->toArray() : []),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }, {
                label: 'الأوامر المكتملة',
                data: @json(isset($monthlyStats) ? $monthlyStats->pluck('completed_orders')->toArray() : []),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
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

    // Team Comparison Chart
    const teamCtx = document.getElementById('teamComparisonChart').getContext('2d');
    const teamChart = new Chart(teamCtx, {
        type: 'radar',
        data: {
            labels: ['معدل الإنجاز', 'سرعة التنفيذ', 'جودة العمل', 'التكلفة', 'الالتزام بالمواعيد'],
            datasets: @json(isset($teamPerformance) ? $teamPerformance->take(5)->map(function($team, $index) {
                return [
                    'label' => $team->team_name,
                    'data' => [
                        $team->completion_rate,
                        min(100, 100 - ($team->avg_completion_time * 5)),
                        $team->completion_rate,
                        min(100, 100 - ($team->avg_cost / 10)),
                        $team->completion_rate
                    ],
                    'borderColor' => ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'][$index],
                    'backgroundColor' => ['rgba(0, 123, 255, 0.2)', 'rgba(40, 167, 69, 0.2)', 'rgba(255, 193, 7, 0.2)', 'rgba(220, 53, 69, 0.2)', 'rgba(111, 66, 193, 0.2)'][$index]
                ];
            })->toArray() : [])
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endpush
