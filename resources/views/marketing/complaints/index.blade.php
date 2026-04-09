@extends('admin.layouts.admin')

@section('title', 'إدارة الشكاوى')

@push('styles')
<style>
    .complaints-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stats-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .stats-card.primary {
        background: linear-gradient(180deg, #f8f9ff 0%, #e8eaff 100%);
        border-left: 6px solid #667eea;
    }
    
    .stats-card.danger {
        background: linear-gradient(180deg, #fef2f2 0%, #fee2e2 100%);
        border-left: 6px solid #ef4444;
    }
    
    .stats-card.warning {
        background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%);
        border-left: 6px solid #f59e0b;
    }
    
    .stats-card.success {
        background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%);
        border-left: 6px solid #10b981;
    }
    
    .chart-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .chart-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .chart-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .table-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .complaints-table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .complaints-table thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        color: #495057;
        border: none;
        padding: 1rem;
        font-weight: 600;
    }
    
    .complaints-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .complaints-table tbody tr:hover {
        background-color: #f8f9ff;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-custom.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .badge-custom.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .badge-custom.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .badge-custom.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .severity-box {
        border-radius: 10px;
        padding: 1rem;
        color: white;
        font-weight: 600;
        transition: transform 0.2s ease;
    }
    
    .severity-box:hover {
        transform: scale(1.05);
    }
    
    .severity-box.critical {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    
    .severity-box.high {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        color: white;
    }
    
    .btn-action:hover {
        transform: scale(1.1);
    }
    
    .btn-action.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #495057;
    }
    
    .stats-label {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .icon-circle.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .icon-circle.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .icon-circle.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .icon-circle.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .empty-state {
        padding: 3rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border-radius: 15px;
    }
    
    .empty-state i {
        color: #667eea;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <!-- Page Header -->
    <div class="complaints-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-exclamation-triangle ml-3"></i>
                    إدارة الشكاوى
                </h1>
                <p class="mb-0 opacity-90">مراقبة وإدارة شكاوى العملاء والوكالات</p>
            </div>
            <div class="col-md-4 text-md-left">
                <button class="btn btn-primary-custom btn-lg">
                    <i class="fas fa-download ml-2"></i> تصدير التقرير
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Complaints -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card primary h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="stats-label text-primary mb-1">
                                <i class="fas fa-file-alt ml-2"></i>إجمالي الشكاوى
                            </div>
                            <div class="stats-number">{{ $stats['total_complaints'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle primary">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Complaints -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card danger h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="stats-label text-danger mb-1">
                                <i class="fas fa-exclamation-circle ml-2"></i>شكاوى مفتوحة
                            </div>
                            <div class="stats-number">{{ $stats['open_complaints'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle danger">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investigating -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card warning h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="stats-label text-warning mb-1">
                                <i class="fas fa-search ml-2"></i>قيد التحقيق
                            </div>
                            <div class="stats-number">{{ $stats['investigating_complaints'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle warning">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card success h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="stats-label text-success mb-1">
                                <i class="fas fa-check-circle ml-2"></i>تم الحل
                            </div>
                            <div class="stats-number">{{ $stats['resolved_complaints'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle success">
                                <i class="fas fa-check-circle"></i>
                            </div>
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
            <div class="card chart-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie ml-2"></i>
                        تصنيف الشكاوى
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <i class="fas fa-tag ml-2"></i>التصنيف
                                    </th>
                                    <th>
                                        <i class="fas fa-hashtag ml-2"></i>العدد
                                    </th>
                                    <th>
                                        <i class="fas fa-percentage ml-2"></i>النسبة
                                    </th>
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
            <div class="card chart-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        توزيع الخطورة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="severity-box critical text-center">
                                <h6 class="mb-2">
                                    <i class="fas fa-exclamation-triangle ml-2"></i>خطيرة
                                </h6>
                                <h3>{{ $stats['critical_complaints'] }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="severity-box high text-center">
                                <h6 class="mb-2">
                                    <i class="fas fa-exclamation-circle ml-2"></i>عالية
                                </h6>
                                <h3>{{ $stats['high_complaints'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complaints Table -->
    <div class="card table-card mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list ml-2"></i>
                أحدث الشكاوى
            </h6>
        </div>
        <div class="card-body">
            @if($complaints->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox fa-4x mb-3"></i>
                    <h4 class="text-gray-700 mb-3">لا توجد شكاوى حالياً</h4>
                    <p class="text-gray-600">جميع الشكاوى تمت معالجتها أو لم يتم تسجيل أي شكاوى بعد</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table complaints-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    <i class="fas fa-hashtag ml-2"></i>المعرف
                                </th>
                                <th>
                                    <i class="fas fa-user ml-2"></i>المشتكي
                                </th>
                                <th>
                                    <i class="fas fa-user-tie ml-2"></i>الوكيل
                                </th>
                                <th>
                                    <i class="fas fa-home ml-2"></i>العقار
                                </th>
                                <th>
                                    <i class="fas fa-info-circle ml-2"></i>الحالة
                                </th>
                                <th>
                                    <i class="fas fa-exclamation-triangle ml-2"></i>الخطورة
                                </th>
                                <th>
                                    <i class="fas fa-user-check ml-2"></i>المعين له
                                </th>
                                <th>
                                    <i class="fas fa-calendar ml-2"></i>التاريخ
                                </th>
                                <th>
                                    <i class="fas fa-cogs ml-2"></i>الإجراءات
                                </th>
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
                                        <span class="badge-custom {{ $complaint->status == 'open' ? 'danger' : ($complaint->status == 'resolved' ? 'success' : 'warning') }}">
                                            {{ $complaint->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-custom {{ $complaint->severity == 'critical' ? 'danger' : ($complaint->severity == 'high' ? 'warning' : 'info') }}">
                                            {{ $complaint->severity }}
                                        </span>
                                    </td>
                                    <td>{{ $complaint->assigned_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($complaint->created_at)->format('Y-m-d') }}</td>
                                    <td>
                                        <button class="btn-action info" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </button>
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