@extends('layouts.app')

@section('title', 'الأجهزة الذكية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">الأجهزة الذكية</h1>
            <p class="text-muted mb-0">إدارة ومراقبة أجهزة إنترنت الأشياء</p>
        </div>
        <div>
            <a href="{{ route('iot-device.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة جهاز جديد
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
                            <h4 class="card-title">{{ $stats['total_devices'] }}</h4>
                            <p class="card-text">إجمالي الأجهزة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-microchip fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['active_devices'] }}</h4>
                            <p class="card-text">أجهزة نشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['offline_devices'] }}</h4>
                            <p class="card-text">أجهزة غير متصلة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['maintenance_devices'] }}</h4>
                            <p class="card-text">تحت الصيانة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('iot-device.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">العقار</label>
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
                        <label class="form-label">نوع الجهاز</label>
                        <select name="device_type" class="form-select">
                            <option value="">كل الأنواع</option>
                            @foreach($deviceTypes as $type)
                                <option value="{{ $type }}" {{ request('device_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">كل الحالات</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            <a href="{{ route('iot-device.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> إعادة تعيين
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Devices Grid -->
    <div class="row">
        @forelse($devices as $device)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $device->device_name }}</h6>
                    <span class="badge bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'offline' ? 'danger' : 'warning') }}">
                        {{ $device->status }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-1">النوع</p>
                        <p class="mb-0"><i class="fas fa-tag"></i> {{ ucfirst($device->device_type) }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">الموقع</p>
                        <p class="mb-0"><i class="fas fa-map-marker-alt"></i> {{ $device->location }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">العقار</p>
                        <p class="mb-0">
                            @if($device->property)
                                <a href="{{ route('smart-property.show', $device->property) }}">
                                    {{ $device->property->property_name }}
                                </a>
                            @else
                                غير محدد
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">آخر نبضة</p>
                        <p class="mb-0">
                            @if($device->last_heartbeat)
                                {{ $device->last_heartbeat->diffForHumans() }}
                            @else
                                غير متوفر
                            @endif
                        </p>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('iot-device.show', $device) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('iot-device.edit', $device) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-outline-info" onclick="getDeviceData({{ $device->id }})">
                                <i class="fas fa-sync"></i>
                            </button>
                        </div>
                        <form action="{{ route('iot-device.destroy', $device) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الجهاز؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-microchip fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد أجهزة</h5>
                <p class="text-muted">ابدأ بإضافة أول جهاز ذكي</p>
                <a href="{{ route('iot-device.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة جهاز جديد
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($devices->hasPages())
    <div class="d-flex justify-content-center">
        {{ $devices->links() }}
    </div>
    @endif
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
