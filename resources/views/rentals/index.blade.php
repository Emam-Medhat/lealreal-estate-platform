@extends('layouts.app')

@section('title', 'نظام الإيجارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">نظام الإيجارات</h1>
                <div class="btn-group">
                    <a href="{{ route('rentals.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                    </a>
                    <a href="{{ route('rentals.properties.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> إضافة عقار
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">العقارات</h5>
                    <h2>{{ App\Models\Property::where('is_rental', true)->count() }}</h2>
                    <a href="{{ route('rentals.properties') }}" class="btn btn-sm btn-light">عرض</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">المستأجرين</h5>
                    <h2>{{ App\Models\Tenant::count() }}</h2>
                    <a href="{{ route('rentals.tenants.index') }}" class="btn btn-sm btn-light">عرض</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">العقود</h5>
                    <h2>{{ App\Models\Lease::count() }}</h2>
                    <a href="{{ route('rentals.leases.index') }}" class="btn btn-sm btn-light">عرض</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">المدفوعات</h5>
                    <h2>{{ App\Models\RentPayment::count() }}</h2>
                    <a href="{{ route('rentals.payments.index') }}" class="btn btn-sm btn-light">عرض</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Modules -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-home"></i> إدارة العقارات
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">إدارة العقارات المؤجرة، عرض الشاغر والمشغول، تحديث الإيجارات</p>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rentals.properties') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> عرض العقارات
                        </a>
                        <a href="{{ route('rentals.properties.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> إضافة عقار
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> إدارة المستأجرين
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">إضافة مستأجرين جدد، فحص المستأجرين، إدارة البيانات الشخصية</p>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rentals.tenants.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> عرض المستأجرين
                        </a>
                        <a href="{{ route('rentals.tenants.create') }}" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> إضافة مستأجر
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-contract"></i> إدارة العقود
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">إنشاء عقود إيجار، تجديد العقود، إنهاء العقود، إدارة الشروط</p>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rentals.leases.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> عرض العقود
                        </a>
                        <a href="{{ route('rentals.leases.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> إنشاء عقد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave"></i> المدفوعات
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">تتبع دفعات الإيجار، إدارة الرسوم المتأخرة، إيصالات الدفع</p>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rentals.payments.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> عرض المدفوعات
                        </a>
                        <a href="{{ route('rentals.payments.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> تسجيل دفعة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Modules -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-redo"></i> التجديدات
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">تجديدات العقود، تعديل الإيجارات، إشعارات التجديد</p>
                    <a href="{{ route('rentals.renewals.index') }}" class="btn btn-info btn-block">
                        <i class="fas fa-list"></i> عرض التجديدات
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt"></i> التأمينات
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">إدارة التأمينات، استرداد التأمينات، الخصومات</p>
                    <a href="{{ route('rentals.deposits.index') }}" class="btn btn-info btn-block">
                        <i class="fas fa-list"></i> عرض التأمينات
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check"></i> الطلبات
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">طلبات الإيجار، فحص الطلبات، الموافقات</p>
                    <a href="{{ route('rentals.applications.index') }}" class="btn btn-info btn-block">
                        <i class="fas fa-list"></i> عرض الطلبات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports and Analytics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> التقارير والتحليلات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('rentals.reports') }}" class="btn btn-outline-primary btn-block mb-2">
                                <i class="fas fa-file-alt"></i> التقارير
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('rentals.analytics') }}" class="btn btn-outline-success btn-block mb-2">
                                <i class="fas fa-chart-line"></i> التحليلات
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('rentals.performance') }}" class="btn btn-outline-info btn-block mb-2">
                                <i class="fas fa-trophy"></i> الأداء
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('rentals.occupancy') }}" class="btn btn-outline-warning btn-block mb-2">
                                <i class="fas fa-home"></i> الإشغال
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
