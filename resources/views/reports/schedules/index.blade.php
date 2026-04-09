@extends('layouts.app')

@section('title', 'جدولة التقارير')

@push('styles')
<style>
    .schedules-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .schedules-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .schedules-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .schedules-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .schedules-table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .schedules-table thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        color: #495057;
        border: none;
        padding: 1rem;
        font-weight: 600;
        text-align: right;
    }
    
    .schedules-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .schedules-table tbody tr:hover {
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
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-custom.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .badge-custom.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .badge-custom.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .badge-custom.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .badge-custom.secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
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
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <!-- Header -->
    <div class="schedules-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-calendar-alt ml-3"></i>
                    جدولة التقارير
                </h1>
                <p class="mb-0 opacity-90">إدارة الجداول الزمنية لإرسال التقارير تلقائياً</p>
            </div>
            <div class="col-md-4 text-md-left">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary-custom">
                    <i class="fas fa-arrow-left ml-2"></i>
                    العودة للتقارير
                </a>
                <a href="{{ route('reports.schedules.create') }}" class="btn btn-primary-custom">
                    <i class="fas fa-plus ml-2"></i>
                    جدولة جديدة
                </a>
            </div>
        </div>
    </div>

    <!-- Schedules List -->
    <div class="schedules-card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list ml-2"></i>
                قائمة الجداول الزمنية
            </h6>
        </div>
        <div class="card-body">
            <table class="table schedules-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>
                            <i class="fas fa-tag ml-2"></i>
                            اسم التقرير
                        </th>
                        <th>
                            <i class="fas fa-sync ml-2"></i>
                            التكرار
                        </th>
                        <th>
                            <i class="fas fa-clock ml-2"></i>
                            وقت الإرسال
                        </th>
                        <th>
                            <i class="fas fa-users ml-2"></i>
                            المستلمون
                        </th>
                        <th>
                            <i class="fas fa-info-circle ml-2"></i>
                            الحالة
                        </th>
                        <th>
                            <i class="fas fa-cogs ml-2"></i>
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Empty State -->
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center">
                            <div class="empty-state">
                                <i class="fas fa-calendar-alt fa-4x mb-3"></i>
                                <h4 class="text-gray-700 mb-3">لا توجد تقارير مجدولة حالياً</h4>
                                <p class="text-gray-600">قم بإنشاء جدولة جديدة لتبدأ باستلام التقارير تلقائياً</p>
                                <a href="{{ route('reports.schedules.create') }}" class="btn btn-primary-custom">
                                    <i class="fas fa-plus ml-2"></i>
                                    جدولة جديدة
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
