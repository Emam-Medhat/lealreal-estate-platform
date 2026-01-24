@extends('layouts.app')

@section('title', 'نظام الصيانة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">نظام الصيانة</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ App\Models\MaintenanceRequest::count() }}</h4>
                            <p class="mb-0">إجمالي طلبات الصيانة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
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
                            <h4 class="mb-0">{{ App\Models\MaintenanceRequest::where('status', 'pending')->count() }}</h4>
                            <p class="mb-0">طلبات معلقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h4 class="mb-0">{{ App\Models\MaintenanceRequest::where('status', 'completed')->count() }}</h4>
                            <p class="mb-0">طلبات مكتملة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ App\Models\EmergencyRepair::where('status', '!=', 'completed')->count() }}</h4>
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

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> طلب صيانة جديد
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.schedules.create') }}" class="btn btn-info btn-block">
                                <i class="fas fa-calendar"></i> جدولة صيانة
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.emergency-repairs.create') }}" class="btn btn-danger btn-block">
                                <i class="fas fa-exclamation-triangle"></i> إصلاح طارئ
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.inventory.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-box"></i> إضافة مخزون
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.work-orders.create') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-clipboard"></i> أمر عمل
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.invoices.create') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-file-invoice"></i> فاتورة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">آخر طلبات الصيانة</h5>
                </div>
                <div class="card-body">
                    @if($recentRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>العنوان</th>
                                        <th>الحالة</th>
                                        <th>الأولوية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRequests as $request)
                                    <tr>
                                        <td>{{ $request->request_number }}</td>
                                        <td>{{ $request->title }}</td>
                                        <td>
                                            <span class="badge badge-{{ $request->status_color }}">
                                                {{ $request->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $request->priority_color }}">
                                                {{ $request->priority_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">لا توجد طلبات صيانة حديثة</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">جدول الصيانة اليوم</h5>
                </div>
                <div class="card-body">
                    @if($todaySchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>الوقت</th>
                                        <th>النشاط</th>
                                        <th>العقار</th>
                                        <th>الفريق</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySchedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->scheduled_date->format('H:i') }}</td>
                                        <td>{{ $schedule->title }}</td>
                                        <td>{{ $schedule->property->title ?? 'N/A' }}</td>
                                        <td>{{ $schedule->maintenanceTeam->name ?? 'غير محدد' }}</td>
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

    <!-- Charts Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">حالة طلبات الصيانة</h5>
                </div>
                <div class="card-body">
                    <canvas id="requestStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أولوية طلبات الصيانة</h5>
                </div>
                <div class="card-body">
                    <canvas id="requestPriorityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Request Status Chart
    const statusCtx = document.getElementById('requestStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['في انتظار', 'مكلف', 'قيد التنفيذ', 'مكتمل', 'ملغي'],
            datasets: [{
                data: [
                    {{ App\Models\MaintenanceRequest::where('status', 'pending')->count() }},
                    {{ App\Models\MaintenanceRequest::where('status', 'assigned')->count() }},
                    {{ App\Models\MaintenanceRequest::where('status', 'in_progress')->count() }},
                    {{ App\Models\MaintenanceRequest::where('status', 'completed')->count() }},
                    {{ App\Models\MaintenanceRequest::where('status', 'cancelled')->count() }}
                ],
                backgroundColor: ['#6c757d', '#007bff', '#ffc107', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Request Priority Chart
    const priorityCtx = document.getElementById('requestPriorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['منخفض', 'متوسط', 'عالي', 'طوارئ'],
            datasets: [{
                label: 'عدد الطلبات',
                data: [
                    {{ App\Models\MaintenanceRequest::where('priority', 'low')->count() }},
                    {{ App\Models\MaintenanceRequest::where('priority', 'medium')->count() }},
                    {{ App\Models\MaintenanceRequest::where('priority', 'high')->count() }},
                    {{ App\Models\MaintenanceRequest::where('priority', 'emergency')->count() }}
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
@endpush
