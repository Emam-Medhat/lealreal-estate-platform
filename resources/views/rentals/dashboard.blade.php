@extends('layouts.app')

@section('title', 'لوحة تحكم الإيجارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">لوحة تحكم الإيجارات</h1>
                <div class="btn-group">
                    <a href="{{ route('rentals.properties.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة عقار
                    </a>
                    <a href="{{ route('rentals.tenants.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> إضافة مستأجر
                    </a>
                    <a href="{{ route('rentals.leases.create') }}" class="btn btn-info">
                        <i class="fas fa-file-contract"></i> إنشاء عقد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي العقارات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_properties'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                العقارات المؤجرة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['occupied_properties'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                العقارات الشاغرة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['vacant_properties'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                إجمالي المستأجرين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_tenants'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                العقود النشطة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_leases'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                الإيرادات الشهرية
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['monthly_revenue'], 2) }} ريال</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                المدفوعات المعلقة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_payments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                المدفوعات المتأخرة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue_payments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Lease Expirations -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">انتهاء العقود القادمة</h6>
                    <a href="{{ route('rentals.leases.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($upcomingLeaseExpirations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>العقار</th>
                                        <th>المستأجر</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>المتبقي</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingLeaseExpirations as $lease)
                                        <tr>
                                            <td>{{ $lease->property->title }}</td>
                                            <td>{{ $lease->tenant->name }}</td>
                                            <td>{{ $lease->end_date->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge {{ $lease->days_remaining <= 7 ? 'badge-danger' : ($lease->days_remaining <= 15 ? 'badge-warning' : 'badge-info') }}">
                                                    {{ $lease->days_remaining }} يوم
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('rentals.leases.show', $lease) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($lease->renewal_option)
                                                        <a href="{{ route('rentals.renewals.create', $lease) }}" class="btn btn-outline-success">
                                                            <i class="fas fa-redo"></i>
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
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">لا توجد عقود تنتهي قريباً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إجراءات سريعة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-home fa-2x text-primary mb-2"></i>
                                    <h6 class="font-weight-bold">إدارة العقارات</h6>
                                    <p class="text-muted small">إضافة وتعديل العقارات المؤجرة</p>
                                    <a href="{{ route('rentals.properties') }}" class="btn btn-primary btn-sm">إدارة</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                                    <h6 class="font-weight-bold">إدارة المستأجرين</h6>
                                    <p class="text-muted small">عرض وإدارة بيانات المستأجرين</p>
                                    <a href="{{ route('rentals.tenants.index') }}" class="btn btn-success btn-sm">إدارة</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-contract fa-2x text-info mb-2"></i>
                                    <h6 class="font-weight-bold">إدارة العقود</h6>
                                    <p class="text-muted small">عرض وتعديل عقود الإيجار</p>
                                    <a href="{{ route('rentals.leases.index') }}" class="btn btn-info btn-sm">إدارة</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-warning h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
                                    <h6 class="font-weight-bold">المدفوعات</h6>
                                    <p class="text-muted small">تتبع المدفوعات والإيرادات</p>
                                    <a href="{{ route('rentals.payments.index') }}" class="btn btn-warning btn-sm">إدارة</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الأنشطة الحديثة</h6>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="timeline">
                            @foreach($recentActivities as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $activity['color'] }}"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ $activity['title'] }}</h6>
                                        <p class="timeline-text">{{ $activity['description'] }}</p>
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد أنشطة حديثة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-weight: 600;
}

.timeline-text {
    margin: 0 0 5px 0;
    color: #6c757d;
}
</style>
@endpush
