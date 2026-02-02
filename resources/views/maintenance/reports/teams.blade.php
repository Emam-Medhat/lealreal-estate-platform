@extends('admin.layouts.admin')

@section('title', 'تقارير فرق الصيانة')

@section('content')
<div class="container-fluid" dir="rtl">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تقارير فرق الصيانة</h1>
            <p class="text-muted mb-0">تحليل أداء الفرق والإحصائيات</p>
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

    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ $stats['total_teams'] ?? 0 }}</h2>
                    <p class="mb-0">إجمالي الفرق</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ $stats['active_teams'] ?? 0 }}</h2>
                    <p class="mb-0">فرق نشطة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ $stats['total_members'] ?? 0 }}</h2>
                    <p class="mb-0">إجمالي الأعضاء</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h2 class="mb-1">{{ $stats['total_workorders'] ?? 0 }}</h2>
                    <p class="mb-0">أوامر العمل</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Teams Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">قائمة الفرق</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>اسم الفريق</th>
                            <th>قائد الفريق</th>
                            <th>عدد الأعضاء</th>
                            <th>أوامر العمل</th>
                            <th>الحالة</th>
                            <th>متوسط التكلفة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($teams) && $teams->count() > 0)
                            @foreach($teams as $team)
                                <tr>
                                    <td>
                                        <strong>{{ $team->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $team->code }}</small>
                                    </td>
                                    <td>
                                        @if($team->teamLeader)
                                            {{ $team->teamLeader->name }}
                                            <br>
                                            <small class="text-muted">{{ $team->teamLeader->email }}</small>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $team->members->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $team->workOrders->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $team->is_active ? 'success' : 'secondary' }}">
                                            {{ $team->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $avgCost = $team->workOrders->where('actual_cost', '>', 0)->avg('actual_cost');
                                        @endphp
                                        {{ $avgCost ? number_format($avgCost, 2) : '0.00' }} ريال
                                    </td>
                                    <td>
                                        <a href="{{ route('maintenance.teams.show', $team) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('maintenance.teams.workload', $team) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    لا توجد فرق حالياً
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أداء الفرق</h5>
                </div>
                <div class="card-body">
                    <canvas id="teamPerformanceChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">توزيع الأعضاء</h5>
                </div>
                <div class="card-body">
                    <canvas id="membersChart" width="400" height="200"></canvas>
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Team Performance Chart
    const performanceCtx = document.getElementById('teamPerformanceChart').getContext('2d');
    const performanceChart = new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: @json(isset($teams) ? $teams->pluck('name')->toArray() : []),
            datasets: [{
                label: 'أوامر العمل المكتملة',
                data: @json(isset($teams) ? $teams->map(function($team) {
                    return $team->workOrders->where('status', 'completed')->count();
                })->toArray() : []),
                backgroundColor: '#28a745'
            }, {
                label: 'أوامر العمل قيد التنفيذ',
                data: @json(isset($teams) ? $teams->map(function($team) {
                    return $team->workOrders->where('status', 'in_progress')->count();
                })->toArray() : []),
                backgroundColor: '#ffc107'
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

    // Members Distribution Chart
    const membersCtx = document.getElementById('membersChart').getContext('2d');
    const membersChart = new Chart(membersCtx, {
        type: 'doughnut',
        data: {
            labels: @json(isset($teams) ? $teams->pluck('name')->toArray() : []),
            datasets: [{
                data: @json(isset($teams) ? $teams->pluck('members')->map->count()->toArray() : []),
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                    '#17a2b8', '#e83e8c', '#fd7e14', '#20c997', '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush
