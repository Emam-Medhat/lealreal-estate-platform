@extends('layouts.app')

@section('title', 'نظام الضرائب')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">نظام الضرائب</h1>
                <div class="btn-group">
                    <a href="{{ route('taxes.calculator.index') }}" class="btn btn-primary">
                        <i class="fas fa-calculator"></i> حاسبة الضرائب
                    </a>
                    <a href="{{ route('taxes.filing.create') }}" class="btn btn-success">
                        <i class="fas fa-file-alt"></i> تقديم إقرار
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
                            <h4 class="card-title">{{ number_format($totalTaxes) }}</h4>
                            <p class="card-text">إجمالي الضرائب</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-receipt fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($paidTaxes) }}</h4>
                            <p class="card-text">الضرائب المدفوعة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($pendingTaxes) }}</h4>
                            <p class="card-text">الضرائب المعلقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($overdueTaxes) }}</h4>
                            <p class="card-text">الضرائب المتأخرة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('taxes.property.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-home fa-2x mb-2"></i>
                                <span>ضرائب العقارات</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('taxes.payments.index') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                <span>المدفوعات</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('taxes.exemptions.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                <span>الإعفاءات</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('taxes.capital-gains.index') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <span>أرباح رأسمالية</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('taxes.vat.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-percentage fa-2x mb-2"></i>
                                <span>ضريبة القيمة المضافة</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('taxes.reports.index') }}" class="btn btn-outline-dark w-100 h-100 d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>التقارير</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">آخر النشاطات</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recentActivities as $activity)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $activity->title }}</h6>
                                <small class="text-muted">{{ $activity->description }}</small>
                            </div>
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">المواعيد الهامة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($upcomingDeadlines as $deadline)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $deadline->title }}</h6>
                                    <small class="text-muted">{{ $deadline->description }}</small>
                                </div>
                                <span class="badge bg-{{ $deadline->days_left <= 7 ? 'danger' : 'primary' }}">
                                    {{ $deadline->days_left }} يوم
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
