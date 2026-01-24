@extends('layouts.app')

@section('title', 'تقارير الاستدامة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">تقارير الاستدامة</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('sustainability.reports.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء تقرير جديد
                    </a>
                    <a href="{{ route('sustainability.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> لوحة التحكم
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">الفلاتر</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sustainability.reports.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="property">العقار</label>
                            <select name="property_id" id="property" class="form-control">
                                <option value="">جميع العقارات</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                        {{ $property->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="report_type">نوع التقرير</label>
                            <select name="report_type" id="report_type" class="form-control">
                                <option value="">جميع الأنواع</option>
                                <option value="comprehensive" {{ request('report_type') == 'comprehensive' ? 'selected' : '' }}>شامل</option>
                                <option value="certification" {{ request('report_type') == 'certification' ? 'selected' : '' }}>شهادة</option>
                                <option value="carbon_footprint" {{ request('report_type') == 'carbon_footprint' ? 'selected' : '' }}>بصمة كربونية</option>
                                <option value="energy_efficiency" {{ request('report_type') == 'energy_efficiency' ? 'selected' : '' }}>كفاءة الطاقة</option>
                                <option value="water_conservation" {{ request('report_type') == 'water_conservation' ? 'selected' : '' }}>حفظ المياه</option>
                                <option value="materials_assessment" {{ request('report_type') == 'materials_assessment' ? 'selected' : '' }}>تقييم المواد</option>
                                <option value="climate_impact" {{ request('report_type') == 'climate_impact' ? 'selected' : '' }}>التأثير المناخي</option>
                                <option value="performance" {{ request('report_type') == 'performance' ? 'selected' : '' }}>الأداء</option>
                                <option value="compliance" {{ request('report_type') == 'compliance' ? 'selected' : '' }}>الامتثال</option>
                                <option value="benchmarking" {{ request('report_type') == 'benchmarking' ? 'selected' : '' }}>المقارنة المعيارية</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">الحالة</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="generated" {{ request('status') == 'generated' ? 'selected' : '' }}>تم إنشاؤه</option>
                                <option value="generating" {{ request('status') == 'generating' ? 'selected' : '' }}>قيد الإنشاء</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل الإنشاء</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_range">نطاق التاريخ</label>
                            <select name="date_range" id="date_range" class="form-control">
                                <option value="">جميع الفترات</option>
                                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>اليوم</option>
                                <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                                <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>هذا الشهر</option>
                                <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>هذا الربع</option>
                                <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>هذا العام</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date_from">من تاريخ</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date_to">إلى تاريخ</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> تطبيق الفلاتر
                                </button>
                                <a href="{{ route('sustainability.reports.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> مسح الفلاتر
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
                                إجمالي التقارير
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_reports'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                                التقارير المنشأة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['generated_reports'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                إجمالي التنزيلات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_downloads'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-download fa-2x text-gray-300"></i>
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
                                متوسط حجم الملف
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['avg_file_size'], 1) }} MB
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">قائمة التقارير</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="{{ route('sustainability.reports.export', 'excel') }}">
                        <i class="fas fa-file-excel"></i> تصدير Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('sustainability.reports.export', 'csv') }}">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                    <a class="dropdown-item" href="#" onclick="bulkDownload()">
                        <i class="fas fa-download"></i> تنزيل جماعي
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>العنوان</th>
                            <th>العقار</th>
                            <th>نوع التقرير</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>حجم الملف</th>
                            <th>التنزيلات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($reports->count() > 0)
                            @foreach($reports as $report)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_reports[]" value="{{ $report->id }}" 
                                               class="report-checkbox">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $report->title }}</strong>
                                            @if($report->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($report->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($report->propertySustainability)
                                            <a href="{{ route('properties.show', $report->propertySustainability->property_id) }}" 
                                               class="text-primary">
                                                {{ $report->propertySustainability->property->title ?? 'عقار غير معروف' }}
                                            </a>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $report->report_type_text }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $report->status == 'generated' ? 'success' : ($report->status == 'generating' ? 'warning' : ($report->status == 'failed' ? 'danger' : 'secondary')) }}">
                                            {{ $report->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $report->generated_at->format('Y-m-d') }}
                                            <br>
                                            <small class="text-muted">{{ $report->generated_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ $report->file_size_text }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $report->download_count }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('sustainability.reports.show', $report) }}" 
                                               class="btn btn-outline-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($report->status == 'generated' && $report->file_path)
                                                <a href="{{ route('sustainability.reports.download', $report) }}" 
                                                   class="btn btn-outline-success" title="تنزيل">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            @if($report->status == 'generated')
                                                <a href="{{ route('sustainability.reports.regenerate', $report) }}" 
                                                   class="btn btn-outline-warning" title="إعادة إنشاء">
                                                    <i class="fas fa-sync"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('sustainability.reports.preview', $report) }}" 
                                               class="btn btn-outline-info" title="معاينة">
                                                <i class="fas fa-search"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete({{ $report->id }})" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                    <div>لا توجد تقارير</div>
                                    <a href="{{ route('sustainability.reports.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus"></i> إنشاء تقرير جديد
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($reports->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        عرض {{ $reports->firstItem() }} - {{ $reports->lastItem() }} 
                        من {{ $reports->total() }} تقرير
                    </div>
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.report-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا التقرير؟ لا يمكن التراجع عن هذا الإجراء.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    function bulkDownload() {
        const selected = document.querySelectorAll('.report-checkbox:checked');
        if (selected.length === 0) {
            alert('يرجى اختيار تقارير للتنزيل');
            return;
        }
        
        const ids = Array.from(selected).map(cb => cb.value);
        window.location.href = '{{ route("sustainability.reports.bulk-download") }}?ids=' + ids.join(',');
    }

    // Update select all checkbox state
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('report-checkbox')) {
            const allCheckboxes = document.querySelectorAll('.report-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.report-checkbox:checked');
            const selectAll = document.getElementById('selectAll');
            
            selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
            selectAll.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        }
    });

    // Auto-refresh for generating reports
    setInterval(function() {
        const generatingReports = document.querySelectorAll('[data-status="generating"]');
        if (generatingReports.length > 0) {
            window.location.reload();
        }
    }, 30000); // Refresh every 30 seconds
</script>
@endpush
