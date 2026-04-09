@extends('layouts.app')

@section('title', 'نظام الصيانة')

@push('styles')
<style>
    .maintenance-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.10);
    }

    .card-shell {
        border: none;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        background: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-shell:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .card-shell .card-header {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border: none;
        padding: 1rem 1.25rem;
        font-weight: 700;
    }

    .metric {
        border-radius: 16px;
        color: #fff;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .metric:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .metric.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .metric.yellow { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .metric.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .metric.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .action-btn {
        border-radius: 999px;
        padding: 0.7rem 1.1rem;
        font-weight: 700;
        transition: all 0.25s ease;
        border: none;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.10);
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border: none;
        padding: 0.9rem 1rem;
        font-weight: 800;
        color: #374151;
        white-space: nowrap;
    }

    .table-modern tbody tr {
        transition: background-color 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9ff;
    }

    .fade-in {
        animation: fadeIn 0.45s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <div class="maintenance-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-tools ml-2"></i>
                    نظام الصيانة
                </h1>
                <p class="mb-0 opacity-90">إدارة طلبات الصيانة والجداول والأوامر</p>
            </div>
            <div class="col-md-4 text-md-left mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                    <a href="{{ route('maintenance.create') }}" class="btn btn-light action-btn">
                        <i class="fas fa-plus ml-2"></i>
                        طلب جديد
                    </a>
                    <a href="{{ route('maintenance.calendar') }}" class="btn btn-light action-btn">
                        <i class="fas fa-calendar ml-2"></i>
                        التقويم
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="metric blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">إجمالي الطلبات</div>
                        <div class="h2 mb-0 font-weight-bold">{{ App\Models\MaintenanceRequest::count() }}</div>
                        <div class="text-white-50 small mt-2">طلبات الصيانة</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-tools fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="metric yellow">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">معلّقة</div>
                        <div class="h2 mb-0 font-weight-bold">{{ App\Models\MaintenanceRequest::where('status', 'pending')->count() }}</div>
                        <div class="text-white-50 small mt-2">قيد المعالجة</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="metric green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">مكتملة</div>
                        <div class="h2 mb-0 font-weight-bold">{{ App\Models\MaintenanceRequest::where('status', 'completed')->count() }}</div>
                        <div class="text-white-50 small mt-2">تم الإنهاء</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">طارئة</div>
                        <div class="h2 mb-0 font-weight-bold">{{ App\Models\EmergencyRepair::where('status', '!=', 'completed')->count() }}</div>
                        <div class="text-white-50 small mt-2">إصلاحات</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-shell">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="font-weight-bold text-gray-900">
                            <i class="fas fa-bolt ml-2 text-indigo-600"></i>
                            إجراءات سريعة
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.create') }}" class="btn btn-primary action-btn w-100">
                                <i class="fas fa-plus ml-1"></i> طلب جديد
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.schedule.create') }}" class="btn btn-info action-btn w-100">
                                <i class="fas fa-calendar ml-1"></i> جدولة
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.workorders.create') }}" class="btn btn-danger action-btn w-100">
                                <i class="fas fa-exclamation-triangle ml-1"></i> أمر عمل
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            @if(\Illuminate\Support\Facades\Route::has('inventory.items.create'))
                                <a href="{{ route('inventory.items.create') }}" class="btn btn-success action-btn w-100">
                                    <i class="fas fa-box ml-1"></i> مخزون
                                </a>
                            @endif
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('maintenance.workorders.create') }}" class="btn btn-warning action-btn w-100">
                                <i class="fas fa-clipboard ml-1"></i> متابعة
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            @if(\Illuminate\Support\Facades\Route::has('payments.invoices.create'))
                                <a href="{{ route('payments.invoices.create') }}" class="btn btn-secondary action-btn w-100">
                                    <i class="fas fa-file-invoice ml-1"></i> فاتورة
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-6">
            <div class="card-shell">
                <div class="card-header">
                    <h5 class="card-title mb-0">آخر طلبات الصيانة</h5>
                </div>
                <div class="card-body">
                    @if($recentRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-modern">
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
            <div class="card-shell">
                <div class="card-header">
                    <h5 class="card-title mb-0">جدول الصيانة اليوم</h5>
                </div>
                <div class="card-body">
                    @if($todaySchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-modern">
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
            <div class="card-shell">
                <div class="card-header">
                    <h5 class="card-title mb-0">حالة طلبات الصيانة</h5>
                </div>
                <div class="card-body">
                    <canvas id="requestStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-shell">
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
