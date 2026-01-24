@extends('layouts.app')

@section('title')
    تفاصيل حملة التسويق
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('marketing.index') }}">التسويق العقاري</a></li>
                    <li class="breadcrumb-item active">{{ $campaign->title }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $campaign->title }}</h1>
            <p class="text-muted mb-0">
                @if($campaign->property)
                    {{ $campaign->property->title }}
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('marketing.property-marketing.edit', $campaign) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i>
                تعديل
            </a>
            <button class="btn btn-outline-success" onclick="launchCampaign({{ $campaign->id }})" @if($campaign->status === 'scheduled')>
                <i class="fas fa-play me-1"></i>
                إطلاق الحملة
            </button>
            <button class="btn btn-outline-warning" onclick="pauseCampaign({{ $campaign->id }})" @if($campaign->status === 'active')>
                <i class="fas fa-pause me-1"></i>
                إيقاف الحملة
            </button>
            <button class="btn btn-outline-info" onclick="resumeCampaign({{ $campaign->id }})" @if($campaign->status === 'paused')>
                <i class="fas fa-play me-1"></i>
                استئناف الحملة
            </button>
            <button class="btn btn-outline-secondary" onclick="completeCampaign({{ $campaign->id }})" @if($campaign->status === 'active')>
                <i class="fas fa-check me-1"></i>
                إكمال الحملة
            </button>
        </div>
    </div>

    <!-- Campaign Details -->
    <div class="row">
        <div class="col-md-8">
        <!-- Campaign Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات الحملة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>العنوع الحملة</th>
                                <td>{{ $campaign->getCampaignTypeDisplayName() }}</td>
                            </tr>
                            <tr>
                                <th>الحالة</th>
                                <td>
                                    <span class="badge bg-{{ $campaign->getStatusColor() }}">{{ $campaign->status }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>تاريخ البدء</th>
                                <td>{{ $campaign->start_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>تاريخ الانتهاء</th>
                                <td>{{ $campaign->end_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>الميزانية</th>
                                <td>
                                    @if($campaign->budget)
                                        {{ number_format($campaign->budget, 2) }} {{ $campaign->currency }}
                                    @else
                                        غير محدد
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>المدة الحملة</th>
                                <td>{{ $campaign->getDurationAttribute() ?? 'غير محدد' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>الجمهور المستهدف</th>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($campaign->target_audience['age_groups'] ?? [] as $age => $age)
                                            <li>{{ $age }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th>القنوات التسويق</th>
                                <td>
                                    @foreach($campaign->marketing_channels ?? [] as $channel)
                                        <span class="badge bg-secondary me-1">{{ $channel }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">الأداء الحملة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4>{{ number_format($analytics['total_impressions']) }}</h4>
                            <small class="text-muted">إجمالي الانطباعات</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4>{{ number_format($analytics['total_clicks']) }}</h4>
                            <small class="text-muted">إجمالي النقرات</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4>{{ number_format($analytics['total_conversions']) }}</h4>
                            <small class="text-muted">إجمالي التحويلات</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4>{{ $analytics['conversion_rate'] }}%</h4>
                            <small class="text-muted">معدل التحويل</small>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>العائد على الإنفاق</h6>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ min(100, $analytics['roi'] ?? 0) }}%;"></div>
                            <small>{{ number_format($analytics['roi'] ?? 0) }}%</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>تكلفة الإنفاق</h6>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $campaign->getBudgetUtilization() }}%;"></div>
                            <small>{{ $campaign->getBudgetUtilization() }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Strategy -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">استراتيجية المحتوى</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>القنوات التسويق</h6>
                        <div class="list-group">
                            @foreach($campaign->marketing_channels ?? [] as $channel)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $channel }}</span>
                                    <span class="badge bg-primary">{{ $analytics['channel_performance'][$channel]['reach'] ?? 0 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>أهداف الأداء</h6>
                        <ul class="list-unstyled">
                            @foreach($campaign->performance_goals ?? [] as $goal => $goal)
                                <li>{{ $goal }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">الجدول الزمني</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @if($campaign->launched_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>بدء الحملة</h6>
                                <small>{{ $campaign->launched_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($campaign->paused_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning">
                                <i class="fas fa-pause"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>إيقاف الحملة</h6>
                                <small>{{ $campaign->paused_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($campaign->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>إكمال الحملة</h6>
                                <small>{{ $campaign->completed_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Panel -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الإجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" onclick="duplicateCampaign({{ $campaign->id }})">
                            <i class="fas fa-copy me-1"></i>
                            نسخ الحملة
                        </button>
                        <button class="btn btn-outline-info" onclick="generateReport({{ $campaign->id }})">
                            <i class="fas fa-chart-bar me-1"></i>
                            تقرير تقرير
                        </button>
                        <button class="btn btn-outline-success" onclick="downloadReport({{ $campaign->id }})">
                            <i class="fas fa-download me-1"></i>
                            تحميل تقرير
                        </button>
                        <a href="{{ route('marketing.property-marketing.analytics', $campaign) }}" class="btn btn-outline-warning">
                            <i class="fas fa-chart-line me-1"></i>
                            تحليل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Modal -->
<div class="modal fade" id="analyticsModal" tabindex="-1" aria-labelledby="analyticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="analyticsModalLabel">تحليل الأداء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>الأداء الحملة</h6>
                        <canvas id="performanceChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h6>توزيع التحويل</h6>
                        <canvas id="conversionChart"></canvas>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>تفاصيل مفصل</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <th>المقياس</th>
                                    <th>القيمة</th>
                                    <th>التغيير</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>الانطباعات</td>
                                        <td>{{ number_format($analytics['total_impressions']) }}</td>
                                        <td>+{{ number_format($analytics['impressions_change']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>النقرارات</td>
                                        <td>{{ number_format($analytics['total_clicks']) }}</td>
                                        <td>+{{ number_format($analytics['clicks_change']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>التحويلات</td>
                                        <td>{{ number_format($analytics['total_conversions']) }}</td>
                                        <td>+{{ number_format($analytics['conversions_change']) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلق</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function launchCampaign(id) {
        fetch(`/marketing/property-marketing/${id}/launch`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأء: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء أثناء إطلاق الحملة');
        });
    }

    function pauseCampaign(id) {
        fetch(`/marketing/property-marketing/${id}/pause`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأء: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء أثناء إيقاف الحملة');
        });
    }

    function resumeCampaign(id) {
        fetch(`/marketing/property-marketing/${id}/resume`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأء: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء أثناء استئناف الحملة');
        });
    }

    function completeCampaign(id) {
        fetch(`/marketing/property-marketing/${id}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأء: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء أثناء إكمال الحملة');
        });
    }

    function duplicateCampaign(id) {
        fetch(`/marketing/property-marketing/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.href = data.redirect;
            } else {
                alert('حدث خطأء: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء أثناء نسخ الحملة');
        });
    }

    function generateReport(id) {
        fetch(`/marketing/property-marketing/${id}/report`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.open(data.url, '_blank');
            } else {
                alert('حدث خطأء: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء إنشاء التقرير');
        });
    }

    function downloadReport(id) {
        fetch(`/marketing/property-marketing/${id}/download`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `campaign-${id}-report.pdf`;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => {
                document.body.removeChild(a);
            }, 0);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء تحميل التقرير');
        });
    }

    function generateReport(id) {
        const modal = new bootstrap.Modal(document.getElementById('analyticsModal'));
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const ctx2 = document.getElementById('conversionChart').getContext('2d');
        
        // Mock data for demonstration
        const performanceData = {
            labels: ['يناير', 'أسبوع', 'أيلول', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسط', 'سبتمبر', 'أكتوبر'],
            datasets: [{
                label: 'الانطباعات',
                data: [30, 50, 70, 90, 110, 130, 120, 110, 100, 80],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
            }]
        };

        const conversionData = {
            labels: ['يناير', 'أسبوع', 'أيلول', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسط', 'سبتمبر', 'أكتوبر'],
            datasets: [{
                label: 'معدل التحويلات',
                data: [5, 12, 18, 25, 35, 45, 40, 35, 30, 20],
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1,
            }]
        };

        new Chart(ctx, {
            type: 'line',
            data: performanceData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });

        new Chart(ctx2, {
            type: 'bar',
            data: conversionData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });

        modal.show();
    }
</script>
@endpush
