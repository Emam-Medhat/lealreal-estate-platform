@extends('layouts.app')

@section('title', 'لوحة تحكم الصيانة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">لوحة تحكم الصيانة</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_requests'] }}</h4>
                            <p class="mb-0">إجمالي الطلبات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending_requests'] }}</h4>
                            <p class="mb-0">طلبات معلقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['completed_requests'] }}</h4>
                            <p class="mb-0">طلبات مكتملة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['emergency_repairs'] }}</h4>
                            <p class="mb-0">إصلاحات طارئة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">حالة الطلبات</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أولوية الطلبات</h5>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">آخر النشاطات</h5>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="timeline">
                            @foreach($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $activity['color'] }}"></div>
                                <div class="timeline-content">
                                    <h6>{{ $activity['title'] }}</h6>
                                    <p class="text-muted">{{ $activity['description'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">لا توجد نشاطات حديثة</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الفرق النشطة</h5>
                </div>
                <div class="card-body">
                    @if($activeTeams->count() > 0)
                        @foreach($activeTeams as $team)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">{{ $team->name }}</h6>
                                <small class="text-muted">{{ $team->specialization_label }}</small>
                            </div>
                            <span class="badge badge-success">نشط</span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">لا توجد فرق نشطة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">جدول اليوم</h5>
                </div>
                <div class="card-body">
                    @if($todaySchedule->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>الوقت</th>
                                        <th>النشاط</th>
                                        <th>العقار</th>
                                        <th>الفريق</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySchedule as $schedule)
                                    <tr>
                                        <td>{{ $schedule->scheduled_date->format('H:i') }}</td>
                                        <td>{{ $schedule->title }}</td>
                                        <td>{{ $schedule->property->title ?? 'N/A' }}</td>
                                        <td>{{ $schedule->maintenanceTeam->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $schedule->status_color }}">
                                                {{ $schedule->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('maintenance.schedules.show', $schedule) }}" 
                                                   class="btn btn-sm btn-info" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($schedule->canBeStarted())
                                                    <a href="{{ route('maintenance.schedules.start', $schedule) }}" 
                                                       class="btn btn-sm btn-success" title="بدء">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">لا توجد جداول صيانة اليوم</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معدل الإنجاز</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-primary">{{ $stats['completion_rate'] }}%</h2>
                        <p class="text-muted">معدل إكمال الطلبات</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">متوسط وقت الاستجابة</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-info">{{ $stats['avg_response_time'] }} ساعة</h2>
                        <p class="text-muted">متوسط وقت الاستجابة للطلبات</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">التكلفة الإجمالية</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-success">{{ number_format($stats['total_cost'], 2) }} ريال</h2>
                        <p class="text-muted">إجمالي تكاليف الصيانة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['في انتظار', 'مكلف', 'قيد التنفيذ', 'مكتمل', 'ملغي'],
            datasets: [{
                data: [
                    {{ $stats['by_status']['pending'] ?? 0 }},
                    {{ $stats['by_status']['assigned'] ?? 0 }},
                    {{ $stats['by_status']['in_progress'] ?? 0 }},
                    {{ $stats['by_status']['completed'] ?? 0 }},
                    {{ $stats['by_status']['cancelled'] ?? 0 }}
                ],
                backgroundColor: ['#6c757d', '#007bff', '#ffc107', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Priority Chart
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['منخفض', 'متوسط', 'عالي', 'طوارئ'],
            datasets: [{
                label: 'عدد الطلبات',
                data: [
                    {{ $stats['by_priority']['low'] ?? 0 }},
                    {{ $stats['by_priority']['medium'] ?? 0 }},
                    {{ $stats['by_priority']['high'] ?? 0 }},
                    {{ $stats['by_priority']['emergency'] ?? 0 }}
                ],
                backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545']
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
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 10px;
}
</style>
@endpush
