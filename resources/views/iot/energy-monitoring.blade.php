@extends('layouts.app')

@section('title', 'مراقبة استهلاك الطاقة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">مراقبة استهلاك الطاقة</h1>
            <p class="text-muted mb-0">تتبع وتحليل استهلاك الطاقة للعقارات الذكية</p>
        </div>
        <div>
            <a href="{{ route('energy-monitoring.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إعداد مراقبة جديدة
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
                            <p class="card-text">عقارات تحت المراقبة</p>
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
                            <h4 class="card-title">{{ number_format($stats['total_consumption'], 2) }}</h4>
                            <p class="card-text">استهلاك إجمالي (كيلوواط/ساعة)</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bolt fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($stats['average_daily'], 2) }}</h4>
                            <p class="card-text">متوسط استهلاك يومي</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="card-title">${{ number_format($stats['energy_savings'], 2) }}</h4>
                            <p class="card-text">توفير في التكاليف</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-piggy-bank fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Energy Consumption Chart -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">اتجاهات استهلاك الطاقة</h5>
                </div>
                <div class="card-body">
                    <canvas id="consumptionChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">تحليل التكاليف</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>التكلفة الشهرية الحالية</span>
                            <span>${{ number_format($costAnalysis['current_month_cost'], 2) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>التكلفة المستهدفة</span>
                            <span>${{ number_format($costAnalysis['target_monthly_cost'], 2) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>توفير محتمل</span>
                            <span class="text-success">${{ number_format($costAnalysis['savings_potential'], 2) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>كفاءة الطاقة</span>
                            <span>{{ $stats['efficiency_score'] }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $stats['efficiency_score'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Properties Under Monitoring -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">العقارات تحت المراقبة</h5>
            <a href="{{ route('energy-monitoring.index') }}" class="btn btn-sm btn-outline-primary">
                عرض الكل
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>العقار</th>
                            <th>الاستهلاك اليومي</th>
                            <th>الاستهلاك الشهري</th>
                            <th>الاستهداف</th>
                            <th>الكفاءة</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentData as $monitoring)
                        <tr>
                            <td>
                                @if($monitoring->property)
                                    <a href="{{ route('smart-property.show', $monitoring->property) }}">
                                        {{ $monitoring->property->property_name }}
                                    </a>
                                @else
                                    غير محدد
                                @endif
                            </td>
                            <td>{{ number_format($monitoring->consumption_kwh, 2) }} ك.و.س</td>
                            <td>{{ number_format($monitoring->consumption_kwh * 30, 2) }} ك.و.س</td>
                            <td>{{ number_format($monitoring->target_consumption, 2) }} ك.و.س</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $monitoring->efficiency_score ?? 0 }}%</span>
                                    <div class="progress" style="width: 100px; height: 10px;">
                                        <div class="progress-bar bg-{{ ($monitoring->efficiency_score ?? 0) >= 80 ? 'success' : (($monitoring->efficiency_score ?? 0) >= 50 ? 'warning' : 'danger') }}" 
                                             style="width: {{ $monitoring->efficiency_score ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $monitoring->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ $monitoring->status == 'active' ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('energy-monitoring.show', $monitoring) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('energy-monitoring.edit', $monitoring) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-info" onclick="optimizeUsage({{ $monitoring->id }})">
                                        <i class="fas fa-magic"></i>
                                    </button>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Consumption Chart
const ctx = document.getElementById('consumptionChart').getContext('2d');
const consumptionChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($consumptionTrends->pluck('date')->map(fn($date) => $date->format('M d'))),
        datasets: [{
            label: 'استهلاك الطاقة (ك.و.س)',
            data: @json($consumptionTrends->pluck('avg_consumption')),
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

function optimizeUsage(monitoringId) {
    if (confirm('هل تريد تشغيل تحسين استهلاك الطاقة؟')) {
        fetch(`/energy-monitoring/${monitoringId}/optimize`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم تشغيل تحسين استهلاك الطاقة بنجاح!');
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تحسين استهلاك الطاقة');
        });
    }
}
</script>
@endsection
@endsection
