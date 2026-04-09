@extends('layouts.app')

@section('title', 'أتمتة المنزل الذكي')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">أتمتة المنزل الذكي</h1>
            <p class="text-muted mb-0">إدارة قواعد الأتمتة والسيناريوهات الذكية</p>
        </div>
        <div>
            <a href="{{ route('smart-automation.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إنشاء أتمتة جديدة
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
                            <h4 class="card-title">{{ $stats['total_automations'] }}</h4>
                            <p class="card-text">قواعد الأتمتة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-robot fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['active_automations'] }}</h4>
                            <p class="card-text">أتمتة نشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-play-circle fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['executed_today'] }}</h4>
                            <p class="card-text">تم تنفيذها اليوم</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-double fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($stats['success_rate'], 1) }}%</h4>
                            <p class="card-text">معدل النجاح</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Automation Rules Grid -->
    <div class="row mb-4">
        @forelse($recentAutomations as $automation)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $automation->rule_name }}</h6>
                    <div class="d-flex gap-1">
                        <span class="badge bg-{{ $automation->is_active ? 'success' : 'secondary' }}">
                            {{ $automation->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                        <span class="badge bg-info">{{ $automation->trigger_type }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-1">الوصف</p>
                        <p class="mb-0 small">{{ $automation->description ?: 'لا يوجد وصف' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">العقار</p>
                        <p class="mb-0">
                            @if($automation->property)
                                <a href="{{ route('smart-property.show', $automation->property) }}">
                                    {{ $automation->property->property_name }}
                                </a>
                            @else
                                غير محدد
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">الشروط</p>
                        <div class="small">
                            @if(is_array($automation->trigger_conditions))
                                @foreach($automation->trigger_conditions as $condition)
                                    <span class="badge bg-light text-dark me-1">{{ $condition['field'] ?? '' }} {{ $condition['operator'] ?? '' }} {{ $condition['value'] ?? '' }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">الإجراءات</p>
                        <div class="small">
                            @if(is_array($automation->actions))
                                @foreach($automation->actions as $action)
                                    <span class="badge bg-primary me-1">{{ $action['type'] ?? '' }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">الأداء</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small>تنفيذ: {{ $automation->execution_count ?? 0 }} | نجاح: {{ $automation->successful_executions ?? 0 }}</small>
                            <div class="progress" style="width: 80px; height: 8px;">
                                @php
                                    $successRate = $automation->execution_count > 0 ? 
                                        (($automation->successful_executions ?? 0) / $automation->execution_count) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" style="width: {{ $successRate }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('smart-automation.show', $automation) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('smart-automation.edit', $automation) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-outline-info" onclick="testAutomation({{ $automation->id }})">
                                <i class="fas fa-play"></i>
                            </button>
                            @if($automation->is_active)
                                <button class="btn btn-outline-warning" onclick="toggleAutomation({{ $automation->id }}, false)">
                                    <i class="fas fa-pause"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-success" onclick="toggleAutomation({{ $automation->id }}, true)">
                                    <i class="fas fa-play"></i>
                                </button>
                            @endif
                        </div>
                        <form action="{{ route('smart-automation.destroy', $automation) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف قاعدة الأتمتة؟')">
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
                <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد قواعد أتمتة</h5>
                <p class="text-muted">ابدأ بإنشاء أول قاعدة أتمتة ذكية</p>
                <a href="{{ route('smart-automation.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إنشاء أتمتة جديدة
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Popular Triggers & Execution Trends -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">المحفزات الشائعة</h5>
                </div>
                <div class="card-body">
                    @foreach($popularTriggers as $trigger)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ ucfirst($trigger->trigger_type) }}</span>
                        <div class="d-flex align-items-center">
                            <span class="me-2">{{ $trigger->count }}</span>
                            <div class="progress" style="width: 100px; height: 10px;">
                                <div class="progress-bar bg-primary" style="width: {{ ($trigger->count / $stats['total_automations']) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">اتجاهات التنفيذ</h5>
                </div>
                <div class="card-body">
                    <canvas id="executionChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Execution Trends Chart
const ctx = document.getElementById('executionChart').getContext('2d');
const executionChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($executionTrends->pluck('date')->map(fn($date) => $date->format('M d'))),
        datasets: [{
            label: 'عدد التنفيذ',
            data: @json($executionTrends->pluck('executions')),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function testAutomation(automationId) {
    if (confirm('هل تريد اختبار قاعدة الأتمتة؟')) {
        fetch(`/smart-automation/${automationId}/test`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم اختبار الأتمتة بنجاح!');
                console.log('Test result:', data.test_result);
            } else {
                alert('حدث خطأ: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء اختبار الأتمتة');
        });
    }
}

function toggleAutomation(automationId, activate) {
    const action = activate ? 'تفعيل' : 'إيقاف';
    if (confirm(`هل تريد ${action} قاعدة الأتمتة؟`)) {
        fetch(`/smart-automation/${automationId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ activate: activate })
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
            alert('حدث خطأ أثناء ' + action + ' الأتمتة');
        });
    }
}
</script>
@endsection
@endsection
