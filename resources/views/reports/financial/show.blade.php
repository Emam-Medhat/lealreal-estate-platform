@extends('layouts.app')

@section('title', 'عرض التقرير المالي - ' . $report->title)

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">{{ $report->title }}</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">{{ $report->description }}</p>
            <div class="mt-3">
                <a href="{{ route('reports.financial.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                    <i class="fas fa-arrow-right"></i> العودة للتقارير
                </a>
            </div>
        </div>

        <!-- Report Details -->
        <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <h5 style="color: #2c3e50; font-size: 1.3rem; font-weight: 500; margin-bottom: 20px;">
                    <i class="fas fa-file-invoice-dollar me-2" style="color: #3498db;"></i>
                    تفاصيل التقرير
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <p style="color: #7f8c8d; margin-bottom: 10px;">
                            <strong style="color: #2c3e50;">نوع التقرير:</strong> 
                            @switch($report->parameters['report_type'] ?? '')
                                @case('income_statement')
                                    بيان الدخل
                                    @break
                                @case('balance_sheet')
                                    الميزانية العمومية
                                    @break
                                @case('cash_flow')
                                    التدفق النقدي
                                    @break
                                @case('profit_loss')
                                    بيان الربح والخسارة
                                    @break
                                @case('revenue_analysis')
                                    تحليل الإيرادات
                                    @break
                                @default
                                    غير محدد
                            @endswitch
                        </p>
                        <p style="color: #7f8c8d; margin-bottom: 10px;">
                            <strong style="color: #2c3e50;">الفترة:</strong> 
                            {{ $report->parameters['date_range']['start'] ?? 'N/A' }} إلى {{ $report->parameters['date_range']['end'] ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="color: #7f8c8d; margin-bottom: 10px;">
                            <strong style="color: #2c3e50;">الحالة:</strong> 
                            <span class="badge" style="background: {{ $report->status === 'completed' ? '#27ae60' : '#f39c12' }}; color: white;">
                                {{ $report->status === 'completed' ? 'مكتمل' : 'قيد المعالجة' }}
                            </span>
                        </p>
                        <p style="color: #7f8c8d; margin-bottom: 10px;">
                            <strong style="color: #2c3e50;">التنسيق:</strong> {{ strtoupper($report->format) }}
                        </p>
                    </div>
                </div>
                
                <div class="mt-3">
                    <p style="color: #7f8c8d; margin-bottom: 10px;">
                        <strong style="color: #2c3e50;">إنشاء في:</strong> {{ $report->created_at->format('Y-m-d H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <h5 style="color: #2c3e50; font-size: 1.3rem; font-weight: 500; margin-bottom: 20px;">
                    <i class="fas fa-chart-line me-2" style="color: #3498db;"></i>
                    محتوى التقرير
                </h5>
                
                @if($report->status === 'completed')
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt" style="font-size: 4rem; color: #3498db; margin-bottom: 20px;"></i>
                        <h4 style="color: #2c3e50; margin-bottom: 15px;">التقرير جاهز للتحميل</h4>
                        <p style="color: #7f8c8d; margin-bottom: 20px;">يمكنك تحميل التقرير بالتنسيق المطلوب</p>
                        <button class="btn" style="background: #3498db; color: white; border: none; border-radius: 10px; padding: 12px 30px; font-weight: 500;">
                            <i class="fas fa-download me-2"></i>
                            تحميل التقرير
                        </button>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-spinner fa-spin" style="font-size: 4rem; color: #f39c12; margin-bottom: 20px;"></i>
                        <h4 style="color: #2c3e50; margin-bottom: 15px;">جاري إنشاء التقرير</h4>
                        <p style="color: #7f8c8d; margin-bottom: 20px;">يتم الآن معالجة بيانات التقرير. قد يستغرق هذا بعض الوقت.</p>
                        <div class="progress" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar" role="progressbar" style="width: 60%; background: #3498db;" aria-valuen="60" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
