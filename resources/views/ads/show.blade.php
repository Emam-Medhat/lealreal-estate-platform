@extends('layouts.app')

@section('title', $ad->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ $ad->title }}</h1>
                <div>
                    <a href="{{ route('ads.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للإعلانات
                    </a>
                </div>
            </div>

            <!-- Ad Status Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-{{ $ad->status == 'active' ? 'success' : ($ad->status == 'paused' ? 'warning' : 'secondary') }} text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-{{ $ad->status == 'active' ? 'play' : ($ad->status == 'paused' ? 'pause' : 'stop') }} fa-2x mb-2"></i>
                            <h5 class="card-title">
                                {{ $ad->status == 'active' ? 'نشط' : ($ad->status == 'paused' ? 'موقف' : ($ad->status == 'draft' ? 'مسودة' : 'غير نشط')) }}
                            </h5>
                            <p class="card-text">حالة الإعلان</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-{{ $ad->approval_status == 'approved' ? 'success' : ($ad->approval_status == 'rejected' ? 'danger' : 'warning') }} text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-{{ $ad->approval_status == 'approved' ? 'check' : ($ad->approval_status == 'rejected' ? 'times' : 'clock') }} fa-2x mb-2"></i>
                            <h5 class="card-title">
                                {{ $ad->approval_status == 'approved' ? 'موافق عليه' : ($ad->approval_status == 'rejected' ? 'مرفوض' : 'في انتظار') }}
                            </h5>
                            <p class="card-text">حالة الموافقة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar fa-2x mb-2"></i>
                            <h5 class="card-title">{{ $ad->days_remaining }}</h5>
                            <p class="card-text">أيام متبقية</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-percentage fa-2x mb-2"></i>
                            <h5 class="card-title">{{ number_format($ad->budget_utilization, 1) }}%</h5>
                            <p class="card-text">استهلاك الميزانية</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Ad Details -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">تفاصيل الإعلان</h5>
                            <div class="btn-group">
                                <a href="{{ route('ads.edit', $ad->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                                    <i class="fas fa-print"></i> طباعة
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>العنوان:</strong> {{ $ad->title }}</p>
                                    <p><strong>النوع:</strong> 
                                        <span class="badge bg-{{ $ad->type == 'banner' ? 'primary' : ($ad->type == 'video' ? 'danger' : 'info') }}">
                                            {{ $ad->type == 'banner' ? 'بانر' : ($ad->type == 'video' ? 'فيديو' : ($ad->type == 'native' ? 'أصلي' : 'منبثق')) }}
                                        </span>
                                    </p>
                                    <p><strong>الحملة:</strong> 
                                        @if($ad->campaign)
                                            <a href="{{ route('campaigns.show', $ad->campaign->id) }}">{{ $ad->campaign->name }}</a>
                                        @else
                                            <span class="text-muted">بدون حملة</span>
                                        @endif
                                    </p>
                                    <p><strong>الحجم:</strong> 
                                        @if($ad->width && $ad->height)
                                            {{ $ad->width }}x{{ $ad->height }}
                                        @else
                                            غير محدد
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>تاريخ البدء:</strong> {{ $ad->start_date->format('Y-m-d') }}</p>
                                    <p><strong>تاريخ الانتهاء:</strong> {{ $ad->end_date->format('Y-m-d') }}</p>
                                    <p><strong>الميزانية اليومية:</strong> {{ number_format($ad->daily_budget, 2) }} ريال</p>
                                    <p><strong>الميزانية الإجمالية:</strong> {{ number_format($ad->total_budget, 2) }} ريال</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p><strong>الوصف:</strong></p>
                                <p class="text-muted">{{ $ad->description }}</p>
                            </div>
                            @if($ad->image_url || $ad->video_url)
                                <div class="mt-3">
                                    <p><strong>الوسائط:</strong></p>
                                    @if($ad->image_url)
                                        <div class="mb-2">
                                            <img src="{{ $ad->image_url_full }}" alt="{{ $ad->title }}" 
                                                 class="img-thumbnail" style="max-width: 300px;">
                                        </div>
                                    @endif
                                    @if($ad->video_url)
                                        <div class="mb-2">
                                            <video controls class="img-thumbnail" style="max-width: 300px;">
                                                <source src="{{ $ad->video_url_full }}" type="video/mp4">
                                                متصفحك لا يدعم تشغيل الفيديو.
                                            </video>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="mt-3">
                                <p><strong>الرابط المستهدف:</strong></p>
                                <a href="{{ $ad->target_url }}" target="_blank" class="text-primary">{{ $ad->target_url }}</a>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">مقاييس الأداء</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary">{{ number_format($ad->impressions_count) }}</h4>
                                        <p class="mb-0">الظهور</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success">{{ number_format($ad->clicks_count) }}</h4>
                                        <p class="mb-0">النقرات</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-info">{{ number_format($ad->conversions_count) }}</h4>
                                        <p class="mb-0">التحويلات</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-warning">{{ number_format($metrics['ctr'], 2) }}%</h4>
                                        <p class="mb-0">نسبة النقر إلى الظهور</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4 text-center">
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary">{{ number_format($metrics['cpc'], 2) }}</h4>
                                        <p class="mb-0">التكلفة لكل نقرة</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success">{{ number_format($metrics['cpa'], 2) }}</h4>
                                        <p class="mb-0">التكلفة لكل تحويل</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-info">{{ number_format($metrics['cpm'], 2) }}</h4>
                                        <p class="mb-0">التكلفة لكل ألف ظهور</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-warning">{{ number_format($metrics['conversion_rate'], 2) }}%</h4>
                                        <p class="mb-0">معدل التحويل</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Performance Chart -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">الأداء اليومي (آخر 30 يوم)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">الإجراءات</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($ad->status == 'active')
                                    <form action="{{ route('ads.pause', $ad->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-pause"></i> إيقاف الإعلان
                                        </button>
                                    </form>
                                @elseif($ad->status == 'paused')
                                    <form action="{{ route('ads.resume', $ad->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-play"></i> استئناف الإعلان
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('ads.duplicate', $ad->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-copy"></i> تكرار الإعلان
                                    </button>
                                </form>

                                @if(auth()->user()->role === 'admin')
                                    @if($ad->approval_status == 'pending')
                                        <form action="{{ route('ads.approve', $ad->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> موافقة
                                            </button>
                                        </form>
                                        <form action="{{ route('ads.reject', $ad->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-times"></i> رفض
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                <a href="{{ route('ads.preview', $ad->id) }}" class="btn btn-outline-primary" target="_blank">
                                    <i class="fas fa-eye"></i> معاينة الإعلان
                                </a>

                                @if($ad->type == 'banner')
                                    <a href="{{ route('banner-ads.get-code', $ad->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-code"></i> الحصول على الكود
                                    </a>
                                @endif

                                <form action="{{ route('ads.destroy', $ad->id) }}" method="POST" 
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> حذف الإعلان
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Budget Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">حالة الميزانية</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>استهلاك الميزانية</span>
                                    <span>{{ number_format($ad->budget_utilization, 1) }}%</span>
                                </div>
                                <div class="progress">
                                    @php 
                                    $bgClass = $ad->budget_utilization > 80 ? 'danger' : ($ad->budget_utilization > 50 ? 'warning' : 'success');
                                    $width = $ad->budget_utilization;
                                @endphp
                                    <div class="progress-bar bg-{{ $bgClass }}" 
                                         style="width: {{ $width }}%;"></div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">الميزانية الإجمالية</small>
                                <p class="mb-0 fw-bold">{{ number_format($ad->total_budget, 2) }} ريال</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">المبلغ المنفق</small>
                                <p class="mb-0 fw-bold">{{ number_format($ad->total_spent, 2) }} ريال</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">المبلغ المتبقي</small>
                                <p class="mb-0 fw-bold">{{ number_format($ad->total_budget - $ad->total_spent, 2) }} ريال</p>
                            </div>
                        </div>
                    </div>

                    <!-- Placements -->
                    @if($ad->placements->isNotEmpty())
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">مواضع الإعلان</h5>
                            </div>
                            <div class="card-body">
                                @foreach($ad->placements as $placement)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $placement->name }}</span>
                                        <span class="badge bg-secondary">{{ $placement->dimensions }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Targeting -->
                    @if($ad->targeting)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">الاستهداف</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">{{ $ad->targeting->targeting_summary }}</p>
                                <p><strong>الوصول المقدر:</strong> {{ number_format($ad->targeting->estimated_reach) }} مستخدم</p>
                                <p><strong>الواقع الفعلي:</strong> {{ number_format($ad->targeting->actual_reach) }} مستخدم</p>
                                <p><strong>معدل المطابقة:</strong> {{ number_format($ad->targeting->match_rate, 1) }}%</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const performanceData = @json($dailyPerformance);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: performanceData.map(item => item.date),
            datasets: [{
                label: 'الظهور',
                data: performanceData.map(item => item.impressions),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                yAxisID: 'y',
            }, {
                label: 'النقرات',
                data: performanceData.map(item => item.clicks),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                yAxisID: 'y1',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'الظهور'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'النقرات'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                },
            }
        }
    });
});
</script>
@endsection
