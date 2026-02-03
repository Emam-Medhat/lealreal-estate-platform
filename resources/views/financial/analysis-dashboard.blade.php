@extends('layouts.app')

@section('title', 'لوحة التحليل المالي')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">لوحة التحليل المالي</h2>
                    <p class="text-muted">تحليل شامل للأداء المالي للعقارات</p>
                </div>
                <div>
                    <a href="{{ route('financial.analyses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> تحليل جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalProperties) }}</h4>
                            <p class="mb-0">إجمالي العقارات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($totalAnalyses) }}</h4>
                            <p class="mb-0">إجمالي التحليلات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($avgRoi, 2) }}%</h4>
                            <p class="mb-0">متوسط العائد</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($totalCashFlow, 2) }}</h4>
                            <p class="mb-0">إجمالي التدفق النقدي</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Analyses -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">التحليلات الأخيرة</h5>
                </div>
                <div class="card-body">
                    @if($analyses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>العقار</th>
                                        <th>سعر الشراء</th>
                                        <th>العائد على الاستثمار</th>
                                        <th>التدفق النقدي</th>
                                        <th>تاريخ التحليل</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analyses as $analysis)
                                        <tr>
                                            <td>{{ $analysis->property->title ?? 'غير محدد' }}</td>
                                            <td>{{ number_format($analysis->purchase_price ?? 0, 2) }}</td>
                                            <td>
                                                @if($analysis->roiCalculations->isNotEmpty())
                                                    {{ number_format($analysis->roiCalculations->first()->roi_percentage ?? 0, 2) }}%
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($analysis->cashFlowProjections->isNotEmpty())
                                                    {{ number_format($analysis->cashFlowProjections->first()->total_cash_flow ?? 0, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $analysis->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="#" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-info">
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
                            {{ $analyses->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد تحليلات بعد</h5>
                            <p class="text-muted">ابدأ بإنشاء أول تحليل مالي للعقارات</p>
                            <a href="{{ route('financial.analyses.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> إنشاء تحليل جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card text-center p-3">
                                <i class="fas fa-calculator fa-3x text-primary mb-3"></i>
                                <h6>حاسبة العائد</h6>
                                <p class="text-muted small">احسب عائد الاستثمار</p>
                                <a href="{{ route('financial.roi.index') }}" class="btn btn-sm btn-outline-primary">
                                    ابدأ الآن
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center p-3">
                                <i class="fas fa-water fa-3x text-success mb-3"></i>
                                <h6>محلل التدفق النقدي</h6>
                                <p class="text-muted small">حلل التدفقات النقدية</p>
                                <a href="{{ route('financial.cash_flow.index') }}" class="btn btn-sm btn-outline-success">
                                    ابدأ الآن
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center p-3">
                                <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                                <h6>محلل المحفظة</h6>
                                <p class="text-muted small">حلل محفظة العقارات</p>
                                <a href="{{ route('financial.portfolio.index') }}" class="btn btn-sm btn-outline-info">
                                    ابدأ الآن
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center p-3">
                                <i class="fas fa-chart-area fa-3x text-warning mb-3"></i>
                                <h6">حاسبة التقدير</h6>
                                <p class="text-muted small">قدر قيمة العقار</p>
                                <a href="{{ route('financial.appreciation.index') }}" class="btn btn-sm btn-outline-warning">
                                    ابدأ الآن
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
