@extends('layouts.app')

@section('title', 'سلوك المستخدمين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">سلوك المستخدمين</h1>
                <a href="{{ route('analytics.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Engagement Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($avgSessionDuration, 1) }}دقيقة</h4>
                            <p class="card-text">متوسط مدة الجلسة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($pagesPerSession, 1) }}</h4>
                            <p class="card-text">صفحات لكل جلسة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($bounceRate, 2) }}%</h4>
                            <p class="card-text">معدل الارتداد</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-sign-out-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($returnVisitorRate, 2) }}%</h4>
                            <p class="card-text">الزوار العائدين</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('analytics.behavior.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="period" class="form-label">الفترة</label>
                                <select class="form-select" id="period" name="period">
                                    <option value="1d" {{ request('period') == '1d' ? 'selected' : '' }}>24 ساعة</option>
                                    <option value="7d" {{ request('period') == '7d' ? 'selected' : '' }}>7 أيام</option>
                                    <option value="30d" {{ request('period') == '30d' ? 'selected' : '' }}>30 يوم</option>
                                    <option value="90d" {{ request('period') == '90d' ? 'selected' : '' }}>90 يوم</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="segment" class="form-label">القطاع</label>
                                <select class="form-select" id="segment" name="segment">
                                    <option value="">جميع القطاعات</option>
                                    <option value="new_users" {{ request('segment') == 'new_users' ? 'selected' : '' }}>مستخدمون جدد</option>
                                    <option value="returning_users" {{ request('segment') == 'returning_users' ? 'selected' : '' }}>مستخدمون عائدون</option>
                                    <option value="high_value" {{ request('segment') == 'high_value' ? 'selected' : '' }}>مستخدمون ذو قيمة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="device" class="form-label">الجهاز</label>
                                <select class="form-select" id="device" name="device">
                                    <option value="">جميع الأجهزة</option>
                                    <option value="desktop" {{ request('device') == 'desktop' ? 'selected' : '' }}>كمبيوتر</option>
                                    <option value="mobile" {{ request('device') == 'mobile' ? 'selected' : '' }}>جوال</option>
                                    <option value="tablet" {{ request('device') == 'tablet' ? 'selected' : '' }}>لوحي</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> تحليل
                                </button>
                                <a href="{{ route('analytics.behavior.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Behavior Patterns -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أنماط السلوك</h5>
                </div>
                <div class="card-body">
                    <div id="behaviorPatternsChart" style="height: 300px;">
                        <!-- Behavior patterns chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أنماط التصفح</h5>
                </div>
                <div class="card-body">
                    <div id="navigationPatternsChart" style="height: 300px;">
                        <!-- Navigation patterns chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Patterns -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أنماط الوقتية</h5>
                </div>
                <div class="card-body">
                    <div id="timePatternsChart" style="height: 300px;">
                        <!-- Time patterns chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أنماط الأجهزة</h5>
                </div>
                <div class="card-body">
                    <div id="devicePatternsChart" style="height: 300px;">
                        <!-- Device patterns chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Segments -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تقسيمات المستخدمين</h5>
                </div>
                <div class="card-body">
                    <div id="userSegmentsChart" style="height: 350px;">
                        <!-- User segments chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">التحويلات حسب القطاع</h5>
                </div>
                <div class="card-body">
                    <div id="segmentConversionsChart" style="height: 350px;">
                        <!-- Segment conversions chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Retention Analysis -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تحليل الاحتفاظ</h5>
                </div>
                <div class="card-body">
                    <div id="retentionChart" style="height: 300px;">
                        <!-- Retention chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معدل الاحتفاظ</h5>
                </div>
                <div class="card-body">
                    <div id="retentionRateChart" style="height: 300px;">
                        <!-- Retention rate chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Behavior -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">السلوك الفوري</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active" onclick="refreshRealTimeBehavior()">
                            <i class="fas fa-sync"></i> تحديث
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="realTimeBehavior">
                        <!-- Real-time behavior will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadBehaviorPatterns();
    loadNavigationPatterns();
    loadTimePatterns();
    loadDevicePatterns();
    loadUserSegments();
    loadSegmentConversions();
    loadRetentionAnalysis();
    loadRealTimeBehavior();
    
    // Auto-refresh every 30 seconds
    setInterval(loadRealTimeBehavior, 30000);
});

function loadBehaviorPatterns() {
    $.get('/analytics/behavior/patterns', function(data) {
        updateBehaviorPatternsChart(data.session_patterns);
    });
}

function loadNavigationPatterns() {
    $.get('/analytics/behavior/patterns', function(data) {
        updateNavigationPatternsChart(data.navigation_patterns);
    });
}

function loadTimePatterns() {
    $.get('/analytics/behavior/patterns', function(data) {
        updateTimePatternsChart(data.time_patterns);
    });
}

function loadDevicePatterns() {
    $.get('/analytics/behavior/patterns', function(data) {
        updateDevicePatternsChart(data.device_patterns);
    });
}

function loadUserSegments() {
    $.get('/analytics/behavior/segments', function(data) {
        updateUserSegmentsChart(data.segments);
    });
}

function loadSegmentConversions() {
    $.get('/analytics/behavior/segments', function(data) {
        updateSegmentConversionsChart(data.conversion_by_segment);
    });
}

function loadRetentionAnalysis() {
    $.get('/analytics/behavior/retention', function(data) {
        updateRetentionChart(data.daily_retention);
        updateRetentionRateChart(data.retention_trend);
    });
}

function loadRealTimeBehavior() {
    $.get('/analytics/behavior/real-time', function(data) {
        updateRealTimeBehavior(data);
    });
}

function refreshRealTimeBehavior() {
    loadRealTimeBehavior();
}

function updateBehaviorPatternsChart(data) {
    const ctx = document.getElementById('behaviorPatternsChart');
    if (ctx && data) {
        let html = '<div class="row">';
        
        Object.keys(data).forEach(key => {
            const value = data[key];
            html += `
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>${key}</h6>
                            <p class="text-muted">متوسط المدة: ${value.avg_duration} ثانية</p>
                            <p class="text-muted">متوسط الجلسات: ${value.avg_sessions}</p>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateNavigationPatternsChart(data) {
    const ctx = document.getElementById('navigationPatternsChart');
    if (ctx && data) {
        let html = '<div class="list-group list-group-flush">';
        
        data.slice(0, 5).forEach(page => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${page.page_url}</h6>
                        <small class="text-muted">المشاهدات: ${page.views}</small>
                    </div>
                    <span class="badge bg-primary">${page.views}</span>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateTimePatternsChart(data) {
    const ctx = document.getElementById('timePatternsChart');
    if (ctx && data) {
        let html = '<div class="list-group list-group-flush">';
        
        data.forEach(hour => {
            const peakHour = hour.hour >= 18 && hour.hour <= 21 ? 'bg-warning' : 'bg-info';
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${hour.hour}:00</h6>
                        <small class="text-muted">النشاطات: ${hour.events}</small>
                    </div>
                    <span class="badge ${peakHour}">${hour.events}</span>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateDevicePatternsChart(data) {
    const ctx = document.getElementById('devicePatternsChart');
    if (ctx && data) {
        let html = '<div class="list-group list-group-flush">';
        
        Object.keys(data).forEach(device => {
            const count = data[device];
            const deviceIcon = device === 'mobile' ? 'fa-mobile' : (device === 'tablet' ? 'fa-tablet-alt' : 'fa-desktop');
            
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1"><i class="fas ${deviceIcon}"></i> ${device}</h6>
                        <small class="text-muted">المشاهدات: ${count}</small>
                    </div>
                    <span class="badge bg-primary">${count}</span>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateUserSegmentsChart(data) {
    const ctx = document.getElementById('userSegmentsChart');
    if (ctx && data) {
        let html = '<div class="row">';
        
        data.forEach(segment => {
            const color = segment.name === 'new_users' ? 'primary' : 
                         segment.name === 'returning_users' ? 'success' : 
                         segment.name === 'high_value' ? 'warning' : 'info';
            
            html += `
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>${segment.name}</h6>
                            <p class="text-muted">الحجم: ${segment.size}</p>
                            <div class="progress">
                                <div class="progress-bar bg-${color}" style="width: ${segment.size}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateSegmentConversionsChart(data) {
    const ctx = document.getElementById('segmentConversionsChart');
    if (ctx && data) {
        let html = '<div class="list-group list-group-flush">';
        
        Object.keys(data).forEach(segment => {
            const conversion = data[segment];
            const rate = conversion.rate || 0;
            
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${segment}</span>
                        <span class="badge bg-success">${number_format(rate, 2)}%</span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" style="width: ${rate}%"></div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateRetentionChart(data) {
    const ctx = document.getElementById('retentionChart');
    if (ctx && data) {
        let html = '<div class="list-group list-group-flush">';
        
        Object.keys(data).forEach(day => {
            const rate = data[day];
            const color = rate >= 80 ? 'success' : (rate >= 60 ? 'warning' : 'danger');
            
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>يوم ${day}</span>
                        <span class="badge bg-${color}">${number_format(rate, 1)}%</span>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateRetentionRateChart(data) {
    const ctx = document.getElementById('retentionRateChart');
    if (ctx && data) {
        let html = '<div class="text-center">';
        html += '<h4>اتجاه الاحتفاظ</h4>';
        html += `<p class="text-muted">${data}</p>`;
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateRealTimeBehavior(data) {
    const container = document.getElementById('realTimeBehavior');
    if (container && data) {
        let html = '<div class="row">';
        
        data.active_users.slice(0, 6).forEach(user => {
            html += `
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>${user.user?.name || 'مستخدم مجهول'}</h6>
                            <p class="text-muted small">${user.current_page || 'غير محدد'}</p>
                            <small class="text-muted">الجهاز: ${user.device_type}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }
}
</script>
@endpush
