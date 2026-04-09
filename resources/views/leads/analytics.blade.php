@extends('layouts.app')

@section('title', 'تحليلات العملاء المحتملين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تحليلات العملاء المحتملين</h5>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="periodFilter">
                            <option value="7">آخر 7 أيام</option>
                            <option value="30" selected>آخر 30 يوم</option>
                            <option value="90">آخر 90 يوم</option>
                            <option value="365">آخر سنة</option>
                        </select>
                        <button class="btn btn-success" onclick="exportReport()">
                            <i class="fas fa-download"></i> تصدير
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_leads'] }}</h3>
                                    <p>إجمالي العملاء</p>
                                    <small>{{ $stats['leads_growth'] }}% نمو</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['converted_leads'] }}</h3>
                                    <p>العملاء المحولين</p>
                                    <small>{{ $stats['conversion_rate'] }}% معدل</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>${{ number_format($stats['total_value'], 0) }}</h3>
                                    <p>القيمة الإجمالية</p>
                                    <small>{{ $stats['avg_value'] }} متوسط</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['avg_response_time'] }}</h3>
                                    <p>متوسط وقت الرد</p>
                                    <small>{{ $stats['response_rate'] }}% معدل الرد</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">اتجاه العملاء المحتملين</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="leadsTrendChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">توزيع المصادر</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="sourcesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Distribution -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">توزيع الحالات</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">أداء الموظفين</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="performanceChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Tables -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">أفضل المصادر</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>المصدر</th>
                                                    <th>عدد العملاء</th>
                                                    <th>التحويلات</th>
                                                    <th>القيمة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topSources as $source)
                                                    <tr>
                                                        <td>{{ $source->name }}</td>
                                                        <td>{{ $source->leads_count }}</td>
                                                        <td>{{ $source->conversions_count }}</td>
                                                        <td>${{ number_format($source->total_value, 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">أفضل الموظفين</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>الموظف</th>
                                                    <th>العملاء</th>
                                                    <th>التحويلات</th>
                                                    <th>القيمة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topPerformers as $performer)
                                                    <tr>
                                                        <td>{{ $performer->name }}</td>
                                                        <td>{{ $performer->leads_count }}</td>
                                                        <td>{{ $performer->conversions_count }}</td>
                                                        <td>${{ number_format($performer->total_value, 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    
    document.getElementById('periodFilter').addEventListener('change', function() {
        updateAnalytics(this.value);
    });
});

function initializeCharts() {
    // Leads Trend Chart
    const leadsTrendCtx = document.getElementById('leadsTrendChart').getContext('2d');
    new Chart(leadsTrendCtx, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'عملاء جدد',
                data: @json($trendData),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'تحويلات',
                data: @json($conversionTrendData),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
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

    // Sources Chart
    const sourcesCtx = document.getElementById('sourcesChart').getContext('2d');
    new Chart(sourcesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($sourceLabels),
            datasets: [{
                data: @json($sourceData),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: @json($statusLabels),
            datasets: [{
                label: 'عدد العملاء',
                data: @json($statusData),
                backgroundColor: 'rgba(54, 162, 235, 0.8)'
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

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'horizontalBar',
        data: {
            labels: @json($performerLabels),
            datasets: [{
                label: 'قيمة المبيعات',
                data: @json($performerData),
                backgroundColor: 'rgba(75, 192, 192, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateAnalytics(period) {
    window.location.href = `{{ route('leads.analytics') }}?period=${period}`;
}

function exportReport() {
    const period = document.getElementById('periodFilter').value;
    window.location.href = `{{ route('leads.export') }}?period=${period}&format=excel`;
}
</script>
@endpush
