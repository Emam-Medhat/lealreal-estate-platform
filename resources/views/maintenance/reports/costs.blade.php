@extends('admin.layouts.admin')

@section('title', 'تقارير التكاليف')

@section('content')
<div class="container-fluid" dir="rtl">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تقارير التكاليف</h1>
            <p class="text-muted mb-0">تحليل التكاليف والميزانيات</p>
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

    <!-- Cost Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ number_format($monthlyCosts->sum('total_cost') ?? 0, 2) }}</h2>
                    <p class="mb-0">إجمالي التكاليف</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ number_format($monthlyCosts->sum('estimated_cost') ?? 0, 2) }}</h2>
                    <p class="mb-0">التكاليف المقدرة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ number_format($monthlyCosts->sum('actual_cost') ?? 0, 2) }}</h2>
                    <p class="mb-0">التكاليف الفعلية</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ number_format($monthlyCosts->avg('avg_cost_per_order') ?? 0, 2) }}</h2>
                    <p class="mb-0">متوسط التكلفة للأمر</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Cost Trends -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">اتجاهات التكاليف الشهرية</h5>
        </div>
        <div class="card-body">
            <canvas id="monthlyCostChart" width="400" height="150"></canvas>
        </div>
    </div>

    <!-- Cost Analysis -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تحليل التكاليف حسب الفرق</h5>
                </div>
                <div class="card-body">
                    <canvas id="teamCostChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أغلى الأوامر</h5>
                </div>
                <div class="card-body">
                    @php
                        $expensiveOrders = App\Models\WorkOrder::with(['property', 'assignedTeam'])
                            ->where('actual_cost', '>', 0)
                            ->orderBy('actual_cost', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @if($expensiveOrders->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($expensiveOrders as $order)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $order->title }}</h6>
                                        <small class="text-muted">{{ $order->assignedTeam->name ?? 'N/A' }}</small>
                                    </div>
                                    <div class="text-left">
                                        <span class="badge bg-danger">{{ number_format($order->actual_cost, 2) }} ريال</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-dollar-sign fa-3x mb-3"></i>
                            <p>لا توجد بيانات تكاليف</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Breakdown Table -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">تفاصيل التكاليف الشهرية</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>الشهر</th>
                            <th>عدد الأوامر</th>
                            <th>التكلفة المقدرة</th>
                            <th>التكلفة الفعلية</th>
                            <th>فرق التكلفة</th>
                            <th>متوسط التكلفة</th>
                            <th>نسبة التوفير</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($monthlyCosts) && $monthlyCosts->count() > 0)
                            @foreach($monthlyCosts as $cost)
                                <tr>
                                    <td>{{ $cost->month }}</td>
                                    <td>{{ $cost->order_count }}</td>
                                    <td>{{ number_format($cost->estimated_cost, 2) }} ريال</td>
                                    <td>{{ number_format($cost->actual_cost, 2) }} ريال</td>
                                    <td>
                                        <span class="badge bg-{{ $cost->cost_variance >= 0 ? 'success' : 'danger' }}">
                                            {{ number_format($cost->cost_variance, 2) }} ريال
                                        </span>
                                    </td>
                                    <td>{{ number_format($cost->avg_cost_per_order, 2) }} ريال</td>
                                    <td>
                                        @if($cost->estimated_cost > 0)
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $cost->savings_rate >= 0 ? 'success' : 'danger' }}" style="width: {{ abs($cost->savings_rate) }}%"></div>
                                                </div>
                                                <span class="badge bg-{{ $cost->savings_rate >= 0 ? 'success' : 'danger' }}">
                                                    {{ number_format($cost->savings_rate, 1) }}%
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    لا توجد بيانات تكاليف حالياً
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Budget Overview -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">نظرة عامة على الميزانية</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>توزيع التكاليف حسب النوع</h6>
                    <canvas id="costTypeChart" width="400" height="200"></canvas>
                </div>
                <div class="col-md-6">
                    <h6>مقارنة التكاليف</h6>
                    <canvas id="costComparisonChart" width="400" height="200"></canvas>
                </div>
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
    // Monthly Cost Chart
    const monthlyCostCtx = document.getElementById('monthlyCostChart').getContext('2d');
    const monthlyCostChart = new Chart(monthlyCostCtx, {
        type: 'line',
        data: {
            labels: @json(isset($monthlyCosts) ? $monthlyCosts->pluck('month')->toArray() : []),
            datasets: [{
                label: 'التكلفة المقدرة',
                data: @json(isset($monthlyCosts) ? $monthlyCosts->pluck('estimated_cost')->toArray() : []),
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4
            }, {
                label: 'التكلفة الفعلية',
                data: @json(isset($monthlyCosts) ? $monthlyCosts->pluck('actual_cost')->toArray() : []),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ريال';
                        }
                    }
                }
            }
        }
    });

    // Team Cost Chart
    const teamCostCtx = document.getElementById('teamCostChart').getContext('2d');
    const teamCostChart = new Chart(teamCostCtx, {
        type: 'bar',
        data: {
            labels: @json(isset($monthlyCosts) ? $monthlyCosts->pluck('month')->toArray() : []),
            datasets: [{
                label: 'متوسط التكلفة للأمر',
                data: @json(isset($monthlyCosts) ? $monthlyCosts->pluck('avg_cost_per_order')->toArray() : []),
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ريال';
                        }
                    }
                }
            }
        }
    });

    // Cost Type Chart (Pie Chart)
    const costTypeCtx = document.getElementById('costTypeChart').getContext('2d');
    const costTypeChart = new Chart(costTypeCtx, {
        type: 'doughnut',
        data: {
            labels: ['تكاليف العمالة', 'تكاليف المواد', 'تكاليف المعدات', 'تكاليف أخرى'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Cost Comparison Chart
    const costComparisonCtx = document.getElementById('costComparisonChart').getContext('2d');
    const costComparisonChart = new Chart(costComparisonCtx, {
        type: 'bar',
        data: {
            labels: ['هذا الشهر', 'الشهر الماضي', 'متوسط 3 أشهر', 'متوسط 6 أشهر'],
            datasets: [{
                label: 'التكلفة الفعلية',
                data: [{{ $monthlyCosts->last()->actual_cost ?? 0 }}, {{ $monthlyCosts->slice(-2, 1)->first()->actual_cost ?? 0 }}, {{ $monthlyCosts->slice(-3)->avg('actual_cost') ?? 0 }}, {{ $monthlyCosts->slice(-6)->avg('actual_cost') ?? 0 }}],
                backgroundColor: '#dc3545'
            }, {
                label: 'التكلفة المقدرة',
                data: [{{ $monthlyCosts->last()->estimated_cost ?? 0 }}, {{ $monthlyCosts->slice(-2, 1)->first()->estimated_cost ?? 0 }}, {{ $monthlyCosts->slice(-3)->avg('estimated_cost') ?? 0 }}, {{ $monthlyCosts->slice(-6)->avg('estimated_cost') ?? 0 }}],
                backgroundColor: '#ffc107'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ريال';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
