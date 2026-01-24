@extends('layouts.app')

@section('title', 'التقارير الضريبية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">التقارير الضريبية</h1>
                <a href="{{ route('taxes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Report Types -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">تقرير التحصيل</h5>
                    <p class="card-text">عرض إجمالي الضرائب المحصولة خلال فترة زمنية</p>
                    <a href="{{ route('taxes.reports.collection') }}" class="btn btn-primary mt-auto">
                        <i class="fas fa-eye"></i> عرض التقرير
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">تقرير المتأخرات</h5>
                    <p class="card-text">عرض الضرائب غير المدفوعة والمتأخرة</p>
                    <a href="{{ route('taxes.reports.outstanding') }}" class="btn btn-warning mt-auto">
                        <i class="fas fa-eye"></i> عرض التقرير
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-shield-alt fa-3x text-info mb-3"></i>
                    <h5 class="card-title">تقرير الإعفاءات</h5>
                    <p class="card-text">عرض جميع طلبات الإعفاء الضريبي</p>
                    <a href="{{ route('taxes.reports.exemptions') }}" class="btn btn-info mt-auto">
                        <i class="fas fa-eye"></i> عرض التقرير
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                    <h5 class="card-title">التحليلات</h5>
                    <p class="card-text">عرض رسوم بيانية وتحليلات متقدمة</p>
                    <a href="{{ route('taxes.reports.analytics') }}" class="btn btn-success mt-auto">
                        <i class="fas fa-eye"></i> عرض التحليلات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إحصائيات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <h3 class="text-primary">{{ number_format($totalCollected, 2) }}</h3>
                            <p class="text-muted">إجمالي التحصيل</p>
                        </div>
                        <div class="col-md-2">
                            <h3 class="text-warning">{{ number_format($totalOutstanding, 2) }}</h3>
                            <p class="text-muted">الضرائب المعلقة</p>
                        </div>
                        <div class="col-md-2">
                            <h3 class="text-info">{{ number_format($totalExemptions, 2) }}</h3>
                            <p class="text-muted">إجمالي الإعفاءات</p>
                        </div>
                        <div class="col-md-2">
                            <h3 class="text-success">{{ $completedFilings }}</h3>
                            <p class="text-muted">الإقرارات المكتملة</p>
                        </div>
                        <div class="col-md-2">
                            <h3 class="text-danger">{{ $overdueCount }}</h3>
                            <p class="text-muted">المتأخرات</p>
                        </div>
                        <div class="col-md-2">
                            <h3 class="text-secondary">{{ $pendingExemptions }}</h3>
                            <p class="text-muted">الإعفاءات المعلقة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">التقارير الأخيرة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recentReports as $report)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $report->name }}</h6>
                                <small class="text-muted">{{ $report->description }}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">{{ $report->generated_at->format('Y-m-d H:i') }}</small>
                                <a href="{{ $report->file_path }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> تحميل
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تصدير التقارير</h5>
                </div>
                <div class="card-body">
                    <form id="exportForm">
                        <div class="mb-3">
                            <label for="report_type" class="form-label">نوع التقرير</label>
                            <select class="form-select" id="report_type" name="type" required>
                                <option value="">اختر نوع التقرير</option>
                                <option value="collection">تقرير التحصيل</option>
                                <option value="outstanding">تقرير المتأخرات</option>
                                <option value="exemptions">تقرير الإعفاءات</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="export_format" class="form-label">صيغة التصدير</label>
                            <select class="form-select" id="export_format" name="format">
                                <option value="excel">Excel</option>
                                <option value="pdf">PDF</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="mb-3">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-download"></i> تصدير التقرير
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const url = '{{ route("taxes.reports.export") }}?' + formData;
        
        // Open in new window to download
        window.open(url, '_blank');
    });
    
    // Auto-populate date ranges
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    $('#date_from').val(firstDay.toISOString().split('T')[0]);
    $('#date_to').val(today.toISOString().split('T')[0]);
});
</script>
@endpush
