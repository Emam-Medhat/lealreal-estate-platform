@extends('admin.layouts.admin')

@section('title', 'تقرير أداء الوكلاء')

@push('styles')
<style>
    .agent-performance-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stats-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .stats-card.primary {
        background: linear-gradient(180deg, #f8f9ff 0%, #e8eaff 100%);
        border-left: 6px solid #667eea;
    }
    
    .stats-card.success {
        background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%);
        border-left: 6px solid #10b981;
    }
    
    .stats-card.info {
        background: linear-gradient(180deg, #ecfeff 0%, #cffafe 100%);
        border-left: 6px solid #06b6d4;
    }
    
    .stats-card.warning {
        background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%);
        border-left: 6px solid #f59e0b;
    }
    
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .table-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .agents-table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .agents-table thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        color: #495057;
        border: none;
        padding: 1rem;
        font-weight: 600;
    }
    
    .agents-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .agents-table tbody tr:hover {
        background-color: #f8f9ff;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        color: white;
        text-decoration: none;
    }
    
    .btn-action:hover {
        transform: scale(1.1);
    }
    
    .btn-action.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }
    
    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-custom.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .badge-custom.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .badge-custom.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #495057;
    }
    
    .stats-label {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .icon-circle.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .icon-circle.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .icon-circle.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .icon-circle.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .performance-chart {
        background: linear-gradient(180deg, #f8f9ff 0%, #e8eaff 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .agent-detail-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8f9ff 100%);
        border: 1px solid #e9ecef;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
    }
    
    .agent-detail-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <!-- Page Header -->
    <div class="agent-performance-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-chart-line ml-3"></i>
                    تقرير أداء الوكلاء
                </h1>
                <p class="mb-0 opacity-90">تحليل شامل لأداء الوكلاء ومقارنة الأداء بينهم</p>
            </div>
            <div class="col-md-4 text-md-left">
                <button class="btn btn-primary-custom btn-lg">
                    <i class="fas fa-download ml-2"></i> تصدير التقرير
                </button>
            </div>
        </div>
    </div>

    @if(isset($agent))
        <!-- Single Agent View -->
        <div class="row">
            <div class="col-lg-4">
                <div class="agent-detail-card">
                    <div class="text-center mb-3">
                        <div class="icon-circle primary mx-auto mb-3">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h4>{{ $agent->name }}</h4>
                        <p class="text-muted">{{ $agent->email }}</p>
                    </div>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stats-number">{{ $agent->properties_count }}</div>
                            <div class="stats-label text-primary">عقارات</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stats-number">{{ number_format($agent->total_views ?? 0) }}</div>
                            <div class="stats-label text-info">مشاهدة</div>
                        </div>
                        <div class="col-6">
                            <div class="stats-number">{{ number_format($agent->total_inquiries ?? 0) }}</div>
                            <div class="stats-label text-success">استفسار</div>
                        </div>
                        <div class="col-6">
                            <div class="stats-number">{{ number_format($conversion_rate, 1) }}%</div>
                            <div class="stats-label text-warning">تحويل</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <!-- Performance Chart -->
                <div class="performance-chart">
                    <h6 class="mb-3">
                        <i class="fas fa-chart-area ml-2"></i>
                        الأداء الشهري
                    </h6>
                    <canvas id="performanceChart" height="100"></canvas>
                </div>
                
                <!-- Recent Properties -->
                <div class="table-card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-home ml-2"></i>
                            أحدث العقارات
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($recentProperties->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>العنوان</th>
                                            <th>السعر</th>
                                            <th>المشاهدات</th>
                                            <th>الاستفسارات</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentProperties as $property)
                                            <tr>
                                                <td>{{ Str::limit($property->title, 50) }}</td>
                                                <td>{{ number_format($property->price ?? 0) }} ريال</td>
                                                <td>{{ number_format($property->views_count ?? 0) }}</td>
                                                <td>{{ number_format($property->inquiries_count ?? 0) }}</td>
                                                <td>
                                                    <span class="badge-custom success">نشط</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-home text-muted fa-3x mb-3"></i>
                                <p class="text-muted">لا توجد عقارات لهذا الوكيل</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- All Agents View -->
        <!-- Summary Stats -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card primary h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stats-label text-primary mb-1">
                                    <i class="fas fa-users ml-2"></i>إجمالي الوكلاء
                                </div>
                                <div class="stats-number">{{ $summary['total_agents'] }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle primary">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card success h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stats-label text-success mb-1">
                                    <i class="fas fa-home ml-2"></i>إجمالي العقارات
                                </div>
                                <div class="stats-number">{{ $summary['total_properties'] }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle success">
                                    <i class="fas fa-home"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card info h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stats-label text-info mb-1">
                                    <i class="fas fa-eye ml-2"></i>إجمالي المشاهدات
                                </div>
                                <div class="stats-number">{{ number_format($summary['total_views']) }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle info">
                                    <i class="fas fa-eye"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card warning h-100 py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stats-label text-warning mb-1">
                                    <i class="fas fa-percentage ml-2"></i>متوسط التحويل
                                </div>
                                <div class="stats-number">{{ number_format($summary['average_conversion_rate'], 1) }}%</div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle warning">
                                    <i class="fas fa-percentage"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performer -->
        @if($summary['top_performer'])
            <div class="row mb-4">
                <div class="col-12">
                    <div class="agent-detail-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-2">
                                    <i class="fas fa-trophy text-warning ml-2"></i>
                                    أفضل أداء
                                </h6>
                                <h5 class="mb-1">{{ $summary['top_performer']['name'] }}</h5>
                                <p class="text-muted mb-0">{{ $summary['top_performer']['email'] }}</p>
                            </div>
                            <div class="col-md-4 text-md-left">
                                <div class="text-center">
                                    <div class="stats-number">{{ number_format($summary['top_performer']['total_inquiries']) }}</div>
                                    <div class="stats-label text-success">استفسار</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Agents Table -->
        <div class="table-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-list ml-2"></i>
                    قائمة الوكلاء
                </h6>
            </div>
            <div class="card-body">
                @if($agents->count() > 0)
                    <div class="table-responsive">
                        <table class="table agents-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>
                                        <i class="fas fa-user ml-2"></i>الوكيل
                                    </th>
                                    <th>
                                        <i class="fas fa-home ml-2"></i>العقارات
                                    </th>
                                    <th>
                                        <i class="fas fa-eye ml-2"></i>المشاهدات
                                    </th>
                                    <th>
                                        <i class="fas fa-comment ml-2"></i>الاستفسارات
                                    </th>
                                    <th>
                                        <i class="fas fa-percentage ml-2"></i>معدل التحويل
                                    </th>
                                    <th>
                                        <i class="fas fa-chart-line ml-2"></i>متوسط المشاهدات
                                    </th>
                                    <th>
                                        <i class="fas fa-cogs ml-2"></i>الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agents as $agent)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $agent['name'] }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $agent['email'] }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-custom info">{{ $agent['properties_count'] }}</span>
                                        </td>
                                        <td>{{ number_format($agent['total_views']) }}</td>
                                        <td>
                                            <span class="badge-custom success">{{ number_format($agent['total_inquiries']) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-custom warning">{{ number_format($agent['conversion_rate'], 1) }}%</span>
                                        </td>
                                        <td>{{ number_format($agent['average_views_per_property'], 1) }}</td>
                                        <td>
                                            <a href="{{ route('admin.reports.agent.performance', ['agent_id' => $agent['id']]) }}" class="btn-action info" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users text-muted fa-4x mb-3"></i>
                        <h4 class="text-gray-700 mb-3">لا يوجد وكلاء حالياً</h4>
                        <p class="text-gray-600">لم يتم تسجيل أي وكلاء في النظام بعد</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@if(isset($agent) && isset($monthlyPerformance))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            const monthlyData = @json($monthlyPerformance);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'العقارات',
                        data: monthlyData.map(item => item.properties_count),
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'المشاهدات',
                        data: monthlyData.map(item => item.views),
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'الاستفسارات',
                        data: monthlyData.map(item => item.inquiries),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endif
@endsection
