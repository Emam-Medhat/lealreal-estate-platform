@extends('layouts.app')

@section('title', 'نظام الأمان الذكي')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">نظام الأمان الذكي</h1>
            <p class="text-muted mb-0">إدارة أنظمة الأمان والحماية للعقارات الذكية</p>
        </div>
        <div>
            <a href="{{ route('smart-security.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إعداد نظام أمان جديد
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
                            <h4 class="card-title">{{ $stats['total_systems'] }}</h4>
                            <p class="card-text">أنظمة الأمان</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shield-alt fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['armed_systems'] }}</h4>
                            <p class="card-text">أنظمة مفعلة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-lock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['active_alerts'] }}</h4>
                            <p class="card-text">تنبيهات نشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['total_cameras'] }}</h4>
                            <p class="card-text">كاميرات مراقبة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-video fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Systems Grid -->
    <div class="row mb-4">
        @forelse($recentSystems as $system)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $system->system_name }}</h6>
                    <span class="badge bg-{{ $system->is_armed ? 'success' : 'warning' }}">
                        {{ $system->is_armed ? 'مفعل' : 'غير مفعل' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-1">النوع</p>
                        <p class="mb-0"><i class="fas fa-tag"></i> {{ ucfirst($system->system_type) }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">العقار</p>
                        <p class="mb-0">
                            @if($system->property)
                                <a href="{{ route('smart-property.show', $system->property) }}">
                                    {{ $system->property->property_name }}
                                </a>
                            @else
                                غير محدد
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">الأجهزة المتصلة</p>
                        <p class="mb-0">{{ $system->devices_count ?? 0 }} جهاز</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">مستوى الأمان</p>
                        <div class="d-flex align-items-center">
                            <span class="me-2">{{ $system->security_score ?? 0 }}%</span>
                            <div class="progress" style="width: 100px; height: 10px;">
                                <div class="progress-bar bg-{{ ($system->security_score ?? 0) >= 80 ? 'success' : (($system->security_score ?? 0) >= 50 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $system->security_score ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('smart-security.show', $system) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('smart-security.edit', $system) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($system->is_armed)
                                <button class="btn btn-outline-warning" onclick="disarmSystem({{ $system->id }})">
                                    <i class="fas fa-lock-open"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-success" onclick="armSystem({{ $system->id }})">
                                    <i class="fas fa-lock"></i>
                                </button>
                            @endif
                        </div>
                        <form action="{{ route('smart-security.destroy', $system) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف نظام الأمان؟')">
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
                <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد أنظمة أمان</h5>
                <p class="text-muted">ابدأ بإعداد أول نظام أمان ذكي</p>
                <a href="{{ route('smart-security.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إعداد نظام أمان جديد
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Recent Security Alerts -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">التنبيهات الأمنية الحديثة</h5>
            <span class="badge bg-danger">{{ $securityAlerts->count() }} تنبيه</span>
        </div>
        <div class="card-body">
            @if($securityAlerts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>الخطورة</th>
                                <th>الرسالة</th>
                                <th>العقار</th>
                                <th>الوقت</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($securityAlerts as $alert)
                            <tr>
                                <td>
                                    <span class="badge bg-info">{{ $alert->alert_type }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $alert->severity == 'critical' ? 'danger' : ($alert->severity == 'high' ? 'warning' : 'secondary') }}">
                                        {{ $alert->severity }}
                                    </span>
                                </td>
                                <td>{{ $alert->message }}</td>
                                <td>
                                    @if($alert->property)
                                        <a href="{{ route('smart-property.show', $alert->property) }}">
                                            {{ $alert->property->property_name }}
                                        </a>
                                    @else
                                        غير محدد
                                    @endif
                                </td>
                                <td>{{ $alert->created_at->diffForHumans() }}</td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewAlertDetails({{ $alert->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">لا توجد تنبيهات أمنية</h5>
                    <p class="text-muted">جميع الأنظمة تعمل بشكل آمن</p>
                </div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script>
function armSystem(systemId) {
    if (confirm('هل تريد تفعيل نظام الأمان؟')) {
        fetch(`/smart-security/${systemId}/arm`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تفعيل النظام');
        });
    }
}

function disarmSystem(systemId) {
    if (confirm('هل تريد إيقاف نظام الأمان؟')) {
        fetch(`/smart-security/${systemId}/disarm`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إيقاف النظام');
        });
    }
}

function viewAlertDetails(alertId) {
    // Implementation for viewing alert details
    console.log('Viewing alert:', alertId);
}
</script>
@endsection
@endsection
