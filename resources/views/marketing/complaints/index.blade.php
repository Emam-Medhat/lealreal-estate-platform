@extends('admin.layouts.admin')

@section('title', 'إدارة الشكاوى')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الشكاوى</h1>
        <div>
            <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> تصدير التقرير
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Complaints -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي الشكاوى</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_complaints'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Complaints -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                شكاوى مفتوحة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['open_complaints'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investigating -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                قيد التحقيق</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['investigating_complaints'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                تم الحل</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['resolved_complaints'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Categories Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">تصنيف الشكاوى</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>التصنيف</th>
                                    <th>العدد</th>
                                    <th>النسبة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaintCategories as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->count }}</td>
                                        <td>
                                            @if($stats['total_complaints'] > 0)
                                                {{ round(($category->count / $stats['total_complaints']) * 100, 1) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Severity Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع الخطورة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="p-3 bg-danger text-white rounded">
                                <h6 class="mb-1">خطيرة</h6>
                                <h3>{{ $stats['critical_complaints'] }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-3 bg-warning text-white rounded">
                                <h6 class="mb-1">عالية</h6>
                                <h3>{{ $stats['high_complaints'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complaints Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">أحدث الشكاوى</h6>
        </div>
        <div class="card-body">
            @if($complaints->isEmpty())
                <div class="text-center py-4">
                    <p class="text-gray-500">لا توجد شكاوى حالياً.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>المعرف</th>
                                <th>المشتكي</th>
                                <th>الوكيل</th>
                                <th>العقار</th>
                                <th>الحالة</th>
                                <th>الخطورة</th>
                                <th>المعين له</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($complaints as $complaint)
                                <tr>
                                    <td>#{{ $complaint->id }}</td>
                                    <td>{{ $complaint->complainant_name }}</td>
                                    <td>{{ $complaint->agent_name }}</td>
                                    <td>{{ $complaint->property_title ?? 'عام' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $complaint->status == 'open' ? 'danger' : ($complaint->status == 'resolved' ? 'success' : 'warning') }}">
                                            {{ $complaint->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $complaint->severity == 'critical' ? 'danger' : ($complaint->severity == 'high' ? 'warning' : 'info') }}">
                                            {{ $complaint->severity }}
                                        </span>
                                    </td>
                                    <td>{{ $complaint->assigned_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($complaint->created_at)->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info btn-circle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection