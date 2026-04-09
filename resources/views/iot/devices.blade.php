@extends('layouts.app')

@section('title', 'الأجهزة الذكية')

@push('styles')
<style>
:root {
    --primary: #0D6EFD;
    --primary-dark: #0a58ca;
    --success: #198754;
    --info: #0DCAF0;
    --warning: #FD7E14;
    --danger: #DC3545;
    --light: #F8F9FC;
    --dark: #1A1F36;
    --gray: #6B7280;
    --border: #E5E7EB;
    --shadow: 0 4px 16px rgba(0, 0, 0, .08);
    --shadow-lg: 0 8px 32px rgba(0, 0, 0, .12);
}

body {
    background: linear-gradient(135deg, #F1F3F8 0%, #EAECF3 100%);
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    direction: rtl;
}

/* Hero Header */
.hero-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 0 4rem 0;
    margin: -1.5rem -1.5rem 0 -1.5rem;
    position: relative;
    overflow: hidden;
}

.hero-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 1;
}

.hero-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: linear-gradient(to bottom, transparent, #F1F3F8);
}

.hero-header .container {
    position: relative;
    z-index: 1;
}

.hero-header h1 {
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 0.75rem;
    text-shadow: 0 4px 20px rgba(0, 0, 0, .2);
    letter-spacing: -0.5px;
}

.hero-header p {
    font-size: 1.15rem;
    opacity: 0.95;
    margin: 0;
    font-weight: 400;
}

/* Cards */
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--primary-dark));
    opacity: 0;
    transition: opacity 0.3s;
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-card.primary { border-top: 4px solid var(--primary); }
.stat-card.success { border-top: 4px solid var(--success); }
.stat-card.warning { border-top: 4px solid var(--warning); }
.stat-card.info { border-top: 4px solid var(--info); }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    transition: transform 0.3s;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
}

.stat-icon.primary { background: rgba(13, 110, 253, .1); color: var(--primary); }
.stat-icon.success { background: rgba(25, 135, 84, .1); color: var(--success); }
.stat-icon.warning { background: rgba(253, 126, 20, .1); color: var(--warning); }
.stat-icon.info { background: rgba(13, 202, 240, .1); color: var(--info); }

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.stat-label {
    color: var(--gray);
    font-size: 0.9rem;
    font-weight: 600;
}

/* Filter Card */
.filter-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 2rem;
}

.filter-card .card-body {
    padding: 1.5rem;
}

/* Device Cards */
.device-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.device-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg);
}

.device-card .card-header {
    background: var(--light);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    font-weight: 700;
    color: var(--dark);
}

.device-card .card-body {
    padding: 1.25rem;
}

.device-card .card-footer {
    background: transparent;
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--border);
}

.device-info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    color: var(--dark);
}

.device-info-item i {
    color: var(--primary);
    width: 16px;
}

.device-info-item .label {
    color: var(--gray);
    font-size: 0.85rem;
}

.device-info-item .value {
    font-weight: 600;
    margin-right: auto;
}

/* Status Badge */
.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Buttons */
.btn-custom {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s;
    border: 1px solid transparent;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
}

.btn-primary-custom {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
}

.empty-state i {
    color: var(--gray);
    margin-bottom: 1rem;
}

/* Form Styling */
.form-select, .form-control {
    border-radius: 8px;
    border: 1px solid var(--border);
    padding: 0.625rem 0.875rem;
    font-weight: 500;
}

.form-select:focus, .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(13, 110, 253, .1);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease forwards;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-header {
        padding: 2rem 1rem;
        margin: -1rem -1rem 1.5rem -1rem;
    }
    
    .hero-header h1 {
        font-size: 2rem;
    }
    
    .stat-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- Hero Header -->
    <div class="hero-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="animate-fade-in">
                        <i class="fas fa-microchip me-3"></i>الأجهزة الذكية
                    </h1>
                    <p class="animate-fade-in" style="animation-delay: 0.1s">
                        إدارة ومراقبة أجهزة إنترنت الأشياء المتصلة
                    </p>
                </div>
                <div class="col-lg-4 text-lg-start">
                    <a href="{{ route('iot-device.create') }}" class="btn-custom btn-primary-custom animate-fade-in" style="animation-delay: 0.2s">
                        <i class="fas fa-plus"></i> إضافة جهاز جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card primary animate-fade-in">
                    <div class="stat-icon primary">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="stat-value">{{ $stats['total_devices'] ?? 0 }}</div>
                    <div class="stat-label">إجمالي الأجهزة</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success animate-fade-in" style="animation-delay: 0.1s">
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['active_devices'] ?? 0 }}</div>
                    <div class="stat-label">أجهزة نشطة</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning animate-fade-in" style="animation-delay: 0.2s">
                    <div class="stat-icon warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['offline_devices'] ?? 0 }}</div>
                    <div class="stat-label">أجهزة غير متصلة</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card info animate-fade-in" style="animation-delay: 0.3s">
                    <div class="stat-icon info">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-value">{{ $stats['maintenance_devices'] ?? 0 }}</div>
                    <div class="stat-label">تحت الصيانة</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card animate-fade-in" style="animation-delay: 0.4s">
            <div class="card-body">
                <form method="GET" action="{{ route('iot-device.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">العقار</label>
                            <select name="property_id" class="form-select">
                                <option value="">كل العقارات</option>
                                @foreach($properties ?? [] as $property)
                                    <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                        {{ $property->property_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">نوع الجهاز</label>
                            <select name="device_type" class="form-select">
                                <option value="">كل الأنواع</option>
                                @foreach($deviceTypes ?? [] as $type)
                                    <option value="{{ $type }}" {{ request('device_type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                @foreach($statuses ?? [] as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('iot-device.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Devices Grid -->
        <div class="row">
            @forelse($devices ?? [] as $device)
            <div class="col-lg-4 col-md-6 mb-4 animate-fade-in">
                <div class="device-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">{{ $device->device_name }}</h6>
                        @php
                            $statusColor = match($device->status) {
                                'active' => 'success',
                                'offline' => 'danger',
                                default => 'warning'
                            };
                            $statusText = match($device->status) {
                                'active' => 'نشط',
                                'offline' => 'غير متصل',
                                default => 'صيانة'
                            };
                        @endphp
                        <span class="status-badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                            {{ $statusText }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="device-info-item">
                            <i class="fas fa-tag"></i>
                            <span class="label">النوع:</span>
                            <span class="value">{{ ucfirst($device->device_type) }}</span>
                        </div>
                        <div class="device-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="label">الموقع:</span>
                            <span class="value">{{ $device->location ?? 'غير محدد' }}</span>
                        </div>
                        <div class="device-info-item">
                            <i class="fas fa-building"></i>
                            <span class="label">العقار:</span>
                            <span class="value">
                                @if($device->property)
                                    <a href="{{ route('smart-property.show', $device->property) }}" class="text-decoration-none">
                                        {{ $device->property->property_name }}
                                    </a>
                                @else
                                    غير محدد
                                @endif
                            </span>
                        </div>
                        <div class="device-info-item">
                            <i class="fas fa-heartbeat"></i>
                            <span class="label">آخر نبضة:</span>
                            <span class="value">
                                @if($device->last_heartbeat)
                                    {{ $device->last_heartbeat->diffForHumans() }}
                                @else
                                    غير متوفر
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="{{ route('iot-device.show', $device) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('iot-device.edit', $device) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-info" onclick="getDeviceData({{ $device->id }})">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </div>
                            <form action="{{ route('iot-device.destroy', $device) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الجهاز؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state animate-fade-in">
                    <i class="fas fa-microchip fa-3x mb-3"></i>
                    <h5 class="text-muted mb-2">لا توجد أجهزة</h5>
                    <p class="text-muted mb-3">ابدأ بإضافة أول جهاز ذكي</p>
                    <a href="{{ route('iot-device.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة جهاز جديد
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($devices->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $devices->links() }}
        </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
function getDeviceData(deviceId) {
    fetch(`/iot-device/${deviceId}/data`)
        .then(response => response.json())
        .then(data => {
            console.log('Device data:', data);
            // Refresh the page or update UI
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
</script>
@endsection
@endsection
