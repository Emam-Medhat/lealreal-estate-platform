@extends('layouts.app')

@section('title', 'محلل المحفظة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">محلل المحفظة</h2>
                    <p class="text-muted">تحليل شامل لأداء محفظة العقارات</p>
                </div>
                <div>
                    <a href="{{ route('financial.portfolio.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> محفظة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">0</h4>
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
                            <h4 class="mb-0">0%</h4>
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
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">0</h4>
                            <p class="mb-0">إجمالي القيمة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <h4 class="mb-0">0</h4>
                            <p class="mb-0">التدفق النقدي</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Analysis Tools -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">أدوات تحليل المحفظة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card text-center p-3">
                                <i class="fas fa-chart-pie fa-3x text-primary mb-3"></i>
                                <h6>تحليل التنويع</h6>
                                <p class="text-muted small">حلل تنويع المحفظة</p>
                                <a href="{{ route('financial.portfolio.diversification') }}" class="btn btn-sm btn-outline-primary">
                                    ابدأ الآن
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center p-3">
                                <i class="fas fa-balance-scale fa-3x text-success mb-3"></i>
                                <h6>تحسين المحفظة</h6>
                                <p class="text-muted small">حسن أداء المحفظة</p>
                                <a href="{{ route('financial.portfolio.optimization') }}" class="btn btn-sm btn-outline-success">
                                    ابدأ الآن
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center p-3">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h6>تحليل المخاطر</h6>
                                <p class="text-muted small">قيم مخاطر المحفظة</p>
                                <a href="{{ route('financial.portfolio.risk') }}" class="btn btn-sm btn-outline-warning">
                                    ابدأ الآن
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Portfolios -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">المحافظ الأخيرة</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد محافظ بعد</h5>
                        <p class="text-muted">ابدأ بإنشاء أول محفظة عقارية</p>
                        <a href="{{ route('financial.portfolio.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus"></i> إنشاء محفظة جديدة
                        </a>
                    </div>
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
                                <h6>لوحة التحليل المالي</h6>
                                <p class="text-muted small">عرض الإحصائيات المالية</p>
                                <a href="{{ route('financial.dashboard') }}" class="btn btn-sm btn-outline-info">
                                    ابدأ الآن
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center p-3">
                                <i class="fas fa-chart-area fa-3x text-warning mb-3"></i>
                                <h6>حاسبة التقدير</h6>
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
