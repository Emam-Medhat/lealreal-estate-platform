@extends('layouts.app')

@section('title', 'التقارير المالية - Real Estate Pro')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-8">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-3 fw-bold">التقارير المالية</h1>
                        <p class="text-blue-100">تحليل شامل للأداء المالي والعائدات</p>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('reports.financial.create') }}" class="btn btn-outline-light">
                            <i class="fas fa-plus ms-2"></i>
                            تقرير مالي جديد
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fas fa-tachometer-alt ms-2"></i>
                            لوحة التحكم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <!-- Quick Stats -->
    <div class="row mb-4">
        <!-- Total Revenue -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي الإيرادات</h6>
                            <h3 class="fw-bold text-success">{{ number_format($totalRevenue ?? 0) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +15.3% من الشهر الماضي
                            </small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-coins text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إيرادات الشهر</h6>
                            <h3 class="fw-bold text-primary">{{ number_format($monthlyRevenue ?? 0) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +8.7% من الشهر الماضي
                            </small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-calendar text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي المصروفات</h6>
                            <h3 class="fw-bold text-danger">{{ number_format($totalExpenses ?? 0) }}</h3>
                            <small class="text-danger">
                                <i class="fas fa-arrow-up"></i> +5.2% من الشهر الماضي
                            </small>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-receipt text-danger fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">صافي الربح</h6>
                            <h3 class="fw-bold text-info">{{ number_format(($totalRevenue ?? 0) - ($totalExpenses ?? 0)) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> +22.1% من الشهر الماضي
                            </small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-chart-line text-info fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('reports.financial.dashboard') }}" class="card shadow-lg border-0 h-100 text-decoration-none hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-chart-pie text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">لوحة مالية</h5>
                            <p class="card-text text-muted small mb-0">نظرة عامة على الأداء</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('reports.financial.income-statement') }}" class="card shadow-lg border-0 h-100 text-decoration-none hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-file-invoice-dollar text-success fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">بيان الدخل</h5>
                            <p class="card-text text-muted small mb-0">الإيرادات والمصروفات</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('reports.financial.balance-sheet') }}" class="card shadow-lg border-0 h-100 text-decoration-none hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-balance-scale text-info fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">الميزانية العمومية</h5>
                            <p class="card-text text-muted small mb-0">الأصول والخصوم</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('reports.financial.cash-flow') }}" class="card shadow-lg border-0 h-100 text-decoration-none hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-water text-warning fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">التدفق النقدي</h5>
                            <p class="card-text text-muted small mb-0">تحليل التدفقات</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-white border-0 pt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    التقارير الأخيرة
                </h5>
                <a href="{{ route('reports.financial.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus ms-2"></i>
                    إنشاء تقرير جديد
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(isset($reports) && $reports->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-end">التقرير</th>
                            <th class="text-end">الفترة</th>
                            <th class="text-end">الإيرادات</th>
                            <th class="text-end">المصروفات</th>
                            <th class="text-end">صافي الربح</th>
                            <th class="text-end">الحالة</th>
                            <th class="text-end">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td>
                                <div>
                                    <h6 class="mb-1">{{ $report->title ?? 'تقرير مالي' }}</h6>
                                    <small class="text-muted">{{ $report->created_at->format('Y-m-d') }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $report->period_start->format('Y-m-d') }} إلى {{ $report->period_end->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                <span class="text-success fw-bold">{{ number_format($report->total_revenue ?? 0) }} ريال</span>
                            </td>
                            <td>
                                <span class="text-danger fw-bold">{{ number_format($report->total_expenses ?? 0) }} ريال</span>
                            </td>
                            <td>
                                <span class="text-primary fw-bold">{{ number_format(($report->total_revenue ?? 0) - ($report->total_expenses ?? 0)) }} ريال</span>
                            </td>
                            <td>
                                @if($report->status == 'completed')
                                    <span class="badge bg-success">مكتمل</span>
                                @elseif($report->status == 'generating')
                                    <span class="badge bg-warning">قيد الإنشاء</span>
                                @else
                                    <span class="badge bg-danger">فشل</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('reports.financial.show', $report->id) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('reports.financial.analytics', $report->id) }}" class="btn btn-sm btn-outline-info" title="تحليل">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
                {{ $reports->links() }}
            @else
            <div class="text-center py-8">
                <i class="fas fa-chart-line text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">لا توجد تقارير مالية متاحة</h5>
                <p class="text-muted">ابدأ بإنشاء أول تقرير مالي لك</p>
                <a href="{{ route('reports.financial.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus ms-2"></i>
                    إنشاء أول تقرير
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-lift {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.bg-gradient-to-r {
    background: linear-gradient(to right, #2563eb, #1e40af) !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge {
    font-weight: 500;
}
</style>
@endpush
