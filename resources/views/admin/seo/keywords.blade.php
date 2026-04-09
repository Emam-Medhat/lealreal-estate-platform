@extends('admin.layouts.admin')

@section('title', 'SEO Keywords Analysis')

@push('styles')
<style>
    .keywords-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .keywords-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .keywords-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .keywords-card .card-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .stats-card .card-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .tips-card .card-header {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .keyword-table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .keyword-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1rem;
        font-weight: 600;
    }
    
    .keyword-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .keyword-table tbody tr:hover {
        background-color: #f8f9ff;
    }
    
    .keyword-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        display: inline-block;
    }
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    .stats-item {
        padding: 1rem;
        border-radius: 10px;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
    }
    
    .stats-item:hover {
        transform: translateX(5px);
    }
    
    .empty-state {
        padding: 3rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border-radius: 15px;
    }
    
    .empty-state i {
        color: #667eea;
        margin-bottom: 1rem;
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
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    .alert-info {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: none;
        border-radius: 10px;
        color: #1565c0;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .keyword-tag {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.875rem;
        font-weight: 600;
        margin: 0.25rem;
        transition: transform 0.2s ease;
    }
    
    .keyword-tag:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <!-- Page Header -->
    <div class="keywords-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-chart-line ml-3"></i>
                    تحليل الكلمات المفتاحية SEO
                </h1>
                <p class="mb-0 opacity-90">مراقبة وتحليل أداء الكلمات المفتاحية لموقعك</p>
            </div>
            <div class="col-md-4 text-md-left">
                <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary-custom btn-lg ml-2">
                    <i class="fas fa-arrow-left ml-2"></i> العودة
                </a>
                <a href="{{ route('admin.seo.create') }}" class="btn btn-primary-custom btn-lg">
                    <i class="fas fa-plus ml-2"></i> إضافة وسوم
                </a>
            </div>
        </div>
    </div>

    <!-- Keywords Analysis -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card keywords-card mb-4">
                <div class="card-header">
                    <h6 class="m-0">
                        <i class="fas fa-key ml-2"></i>
                        أعلى الكلمات المفتاحية
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($keywords) > 0)
                        <div class="table-responsive">
                            <table class="table keyword-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-tag ml-2"></i>الكلمة المفتاحية</th>
                                        <th><i class="fas fa-chart-bar ml-2"></i>التكرار</th>
                                        <th><i class="fas fa-percentage ml-2"></i>الاستخدام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($keywords as $keyword => $frequency)
                                        <tr>
                                            <td>
                                                <span class="keyword-tag">{{ $keyword }}</span>
                                            </td>
                                            <td>
                                                <span class="keyword-badge">{{ $frequency }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ ($frequency / max($keywords)) * 100 }}%"
                                                         aria-valuenow="{{ $frequency }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="{{ max($keywords) }}">
                                                        {{ round(($frequency / max($keywords)) * 100) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-key fa-4x mb-3"></i>
                            <h4 class="text-gray-700 mb-3">لا توجد كلمات مفتاحية</h4>
                            <p class="text-gray-600 mb-4">ابدأ بإضافة وسوم meta مع كلمات مفتاحية لرؤية التحليل هنا.</p>
                            <a href="{{ route('admin.seo.create') }}" class="btn btn-primary-custom btn-lg">
                                <i class="fas fa-plus ml-2"></i> إضافة وسوم Meta
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Keywords Stats -->
            <div class="card keywords-card stats-card mb-4">
                <div class="card-header">
                    <h6 class="m-0">
                        <i class="fas fa-chart-pie ml-2"></i>
                        إحصائيات الكلمات المفتاحية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stats-item">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي الكلمات المفتاحية</div>
                                <div class="h4 mb-0 font-weight-bold">{{ count($keywords) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-key fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>

                    @if(count($keywords) > 0)
                        <div class="stats-item">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-trophy ml-2 text-warning"></i>
                                الأكثر استخداماً:
                            </h6>
                            <p class="mb-0">
                                <span class="keyword-tag">{{ array_key_first($keywords) }}</span>
                                <span class="keyword-badge">{{ array_values($keywords)[0] }} مرة</span>
                            </p>
                        </div>

                        <div class="stats-item">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-calculator ml-2 text-info"></i>
                                متوسط الاستخدام:
                            </h6>
                            <p class="mb-0">
                                <strong>{{ round(array_sum($keywords) / count($keywords), 1) }}</strong> مرة لكل كلمة
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SEO Tips -->
            <div class="card keywords-card tips-card mb-4">
                <div class="card-header">
                    <h6 class="m-0">
                        <i class="fas fa-lightbulb ml-2"></i>
                        نصائح SEO
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="mb-3">
                            <i class="fas fa-magic ml-2"></i>
                            تحسين الكلمات المفتاحية
                        </h6>
                        <ul class="mb-0 text-right">
                            <li class="mb-2">استخدم 5-10 كلمات مفتاحية ذات صلة لكل صفحة</li>
                            <li class="mb-2">ضع الكلمة المفتاحية الرئيسية في العنوان</li>
                            <li class="mb-2">استخدم الكلمات المفتاحية بشكل طبيعي في الوصف</li>
                            <li class="mb-2">تجنب حشو الكلمات المفتاحية</li>
                            <li class="mb-2">ركز على الكلمات المفتاحية الطويلة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
