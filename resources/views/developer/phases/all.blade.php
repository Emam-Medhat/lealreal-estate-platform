@extends('layouts.app')

@section('title', 'مراحل المشروع')

@push('styles')
<style>
    .phases-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .filter-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .filter-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .filter-card .card-header {
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
    
    .phases-table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .phases-table thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        color: #495057;
        border: none;
        padding: 1rem;
        font-weight: 600;
    }
    
    .phases-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .phases-table tbody tr:hover {
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
    
    .badge-custom.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .badge-custom.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .badge-custom.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
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
    
    .badge-custom.secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
    }
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        height: 8px;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    
    .progress-bar.bg-success {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }
    
    .progress-bar.bg-info {
        background: linear-gradient(90deg, #06b6d4 0%, #0891b2 100%);
    }
    
    .progress-bar.bg-warning {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    }
    
    .progress-bar.bg-danger {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
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
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <!-- Page Header -->
    <div class="phases-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-tasks ml-3"></i>
                    جميع المراحل
                </h1>
                <p class="mb-0 opacity-90">إدارة ومراقبة جميع مراحل المشاريع</p>
            </div>
            <div class="col-md-4 text-md-left">
                <a href="{{ route('developer.projects.index') }}" class="btn btn-secondary-custom btn-lg">
                    <i class="fas fa-arrow-left ml-2"></i> العودة للمشاريع
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter ml-2"></i>
                البحث والتصفية
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('developer.phases.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search" class="form-label">البحث</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="ابحث عن المراحل...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>مخطط</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>متأخر</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary-custom btn-block">
                                <i class="fas fa-search ml-2"></i> بحث
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Phases Table -->
    <div class="table-card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list ml-2"></i>
                قائمة المراحل
            </h6>
        </div>
        <div class="card-body">
            @if(isset($error))
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle fa-4x mb-3"></i>
                    <h4 class="text-gray-700 mb-3">مشكلة في الوصول</h4>
                    <p class="text-gray-600">{{ $error }}</p>
                    <a href="{{ route('developer.dashboard.index') }}" class="btn btn-primary-custom">
                        <i class="fas fa-tachometer-alt ml-2"></i>لوحة التحكم
                    </a>
                </div>
            @elseif($phases->count() > 0)
                <div class="table-responsive">
                    <table class="table phases-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    <i class="fas fa-heading ml-2"></i>اسم المرحلة
                                </th>
                                <th>
                                    <i class="fas fa-building ml-2"></i>المشروع
                                </th>
                                <th>
                                    <i class="fas fa-tag ml-2"></i>نوع المرحلة
                                </th>
                                <th>
                                    <i class="fas fa-info-circle ml-2"></i>الحالة
                                </th>
                                <th>
                                    <i class="fas fa-calendar-alt ml-2"></i>تاريخ البدء
                                </th>
                                <th>
                                    <i class="fas fa-calendar-check ml-2"></i>تاريخ الانتهاء
                                </th>
                                <th>
                                    <i class="fas fa-chart-line ml-2"></i>التقدم
                                </th>
                                <th>
                                    <i class="fas fa-cogs ml-2"></i>الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($phases as $phase)
                                <tr>
                                    <td>
                                        <strong>{{ $phase->name }}</strong>
                                        @if($phase->description)
                                            <br><small class="text-muted">{{ Str::limit($phase->description, 100) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($phase->project_title)
                                            <a href="{{ route('developer.projects.show', $phase->project_id) }}" class="text-primary">
                                                {{ $phase->project_title }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                        $phaseTypeClass = 'primary';
                                        if ($phase->phase_type == 'structure') $phaseTypeClass = 'success';
                                        elseif ($phase->phase_type == 'finishing') $phaseTypeClass = 'info';
                                        elseif ($phase->phase_type == 'electrical') $phaseTypeClass = 'warning';
                                        @endphp
                                        <span class="badge-custom {{ $phaseTypeClass }}">
                                            {{ $phase->phase_type }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                        $statusClass = 'secondary';
                                        if ($phase->status == 'completed') $statusClass = 'success';
                                        elseif ($phase->status == 'in_progress') $statusClass = 'info';
                                        elseif ($phase->status == 'delayed') $statusClass = 'warning';
                                        elseif ($phase->status == 'cancelled') $statusClass = 'danger';
                                        @endphp
                                        <span class="badge-custom {{ $statusClass }}">
                                            {{ $phase->status }}
                                        </span>
                                    </td>
                                    <td>{{ $phase->start_date ? \Carbon\Carbon::parse($phase->start_date)->format('Y-m-d') : '-' }}</td>
                                    <td>{{ $phase->end_date ? \Carbon\Carbon::parse($phase->end_date)->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        @if($phase->progress_percentage)
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $phase->progress_percentage >= 80 ? 'success' : ($phase->progress_percentage >= 50 ? 'info' : ($phase->progress_percentage >= 25 ? 'warning' : 'danger')) }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $phase->progress_percentage }}%">
                                                    {{ $phase->progress_percentage }}%
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('developer.phases.show', $phase->id) }}" class="btn btn-sm btn-primary-custom">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('developer.phases.edit', $phase->id) }}" class="btn btn-sm btn-secondary-custom">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $phases->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-tasks fa-4x mb-3"></i>
                    <h4 class="text-gray-700 mb-3">لا توجد مراحل حالياً</h4>
                    <p class="text-gray-600">لم يتم تسجيل أي مراحل بعد. يمكنك البدء بإضافة مرحلة جديدة.</p>
                    <a href="{{ route('developer.projects.index') }}" class="btn btn-primary-custom">
                        <i class="fas fa-plus ml-2"></i>عرض المشاريع
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
