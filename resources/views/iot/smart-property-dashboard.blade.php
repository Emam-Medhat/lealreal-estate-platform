@extends('layouts.app')

@section('title', 'لوحة تحكم العقارات الذكية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">العقارات الذكية</h1>
            <p class="text-muted mb-0">إدارة ومراقبة العقارات المتصلة</p>
        </div>
        <div>
            <a href="{{ route('smart-property.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة عقار ذكي
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_properties'] }}</h4>
                            <p class="card-text">إجمالي العقارات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['active_properties'] }}</h4>
                            <p class="card-text">عقارات نشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_devices'] }}</h4>
                            <p class="card-text">الأجهزة المتصلة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-microchip fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['active_alerts'] }}</h4>
                            <p class="card-text">التنبيهات النشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Properties -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">العقارات الحديثة</h5>
                    <a href="{{ route('smart-property.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>اسم العقار</th>
                                    <th>العنوان</th>
                                    <th>الحالة</th>
                                    <th>الأجهزة</th>
                                    <th>آخر تحديث</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProperties as $property)
                                <tr>
                                    <td>
                                        <a href="{{ route('smart-property.show', $property) }}">
                                            {{ $property->property_name }}
                                        </a>
                                    </td>
                                    <td>{{ $property->address }}</td>
                                    <td>
                                        <span class="badge bg-{{ $property->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $property->status == 'active' ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>{{ $property->devices_count ?? 0 }}</td>
                                    <td>{{ $property->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('smart-property.show', $property) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('smart-property.edit', $property) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('iot-device.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> إضافة جهاز جديد
                        </a>
                        <a href="{{ route('smart-automation.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-robot"></i> إنشاء أتمتة
                        </a>
                        <a href="{{ route('energy-monitoring.create') }}" class="btn btn-outline-info">
                            <i class="fas fa-bolt"></i> إعداد مراقبة الطاقة
                        </a>
                        <a href="{{ route('smart-security.create') }}" class="btn btn-outline-warning">
                            <i class="fas fa-shield-alt"></i> إعداد الأمان
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">حالة النظام</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>الأجهزة النشطة</span>
                            <span>{{ $stats['active_devices'] }}/{{ $stats['total_devices'] }}</span>
                        </div>
                        <div class="progress">
                            @php
                                $devicePercentage = $stats['total_devices'] > 0 ? 
                                    ($stats['active_devices'] / $stats['total_devices']) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" style="width: {{ $devicePercentage }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>كفاءة الطاقة</span>
                            <span>{{ $stats['energy_efficiency'] }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: {{ $stats['energy_efficiency'] }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>مستوى الأمان</span>
                            <span>{{ $stats['security_level'] }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['security_level'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
