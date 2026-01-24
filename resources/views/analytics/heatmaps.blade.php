@extends('layouts.app')

@section('title', 'الخرائط الحرارية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">الخرائط الحرارية</h1>
                <a href="{{ route('analytics.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Heatmap Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($totalHeatmaps) }}</h4>
                            <p class="card-text">إجمالي الخرائط</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-fire fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($totalInteractions) }}</h4>
                            <p class="card-text">إجمالي التفاعلات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-mouse-pointer fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($avgEngagement, 1) }}%</h4>
                            <p class="card-text">متوسط التفاعل</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($hotspotCount) }}</h4>
                            <p class="card-text">نقاطق ساخنة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-fire-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Heatmap Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إنشاء خريطة حرارية جديدة</h5>
                </div>
                <div class="card-body">
                    <form id="heatmapForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="page_url" class="form-label">رابط الصفحة</label>
                                <input type="url" class="form-control" id="page_url" name="page_url" required>
                            </div>
                            <div class="col-md-2">
                                <label for="heatmap_type" class="form-label">نوع الخريطة</label>
                                <select class="form-select" id="heatmap_type" name="heatmap_type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="click">النقرات</option>
                                    <option value="movement">حركة الماوس</option>
                                    <option value="scroll">التمرير</option>
                                    <option value="attention">الانتباه</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="time_range" class="form-label">الفترة الزمنية</label>
                                <select class="form-select" id="time_range" name="time_range" required>
                                    <option value="">اختر الفترة</option>
                                    <option value="1d">24 ساعة</option>
                                    <option value="7d">7 أيام</option>
                                    <option value="30d">30 يوم</option>
                                    <option value="90d">90 يوم</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-fire"></i> إنشاء خريطة
                                </button>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-success" onclick="generateRealtimeHeatmap()">
                                    <i class="fas fa-sync"></i> فوري
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Heatmap Display -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">عرض الخريطة الحرارية</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active" onclick="switchHeatmapType('click')">نقرات</button>
                        <button class="btn btn-outline-success" onclick="switchHeatmapType('movement')">حركة</button>
                        <button class="btn btn-outline-info" onclick="switchHeatmapType('scroll')">تمرير</button>
                        <button class="btn btn-outline-warning" onclick="switchHeatmapType('attention')">انتباه</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="heatmapContainer" style="position: relative; height: 600px; background: #f8f9fa; border: 1px solid #dee2e6;">
                        <!-- Heatmap will be rendered here -->
                        <div class="text-center pt-5">
                            <i class="fas fa-fire fa-3x text-muted mb-3"></i>
                            <p class="text-muted">اختر صفحة ونوع الخريطة لعرض البيانات</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إحصائيات الخريطة</h5>
                </div>
                <div class="card-body">
                    <div id="heatmapStats">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between">
                                <span>إجمالي التفاعلات:</span>
                                <span id="totalInteractionsStat">-</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>نقاطق ساخنة:</span>
                                <span id="hotspotsStat">-</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>نقاطق باردة:</span>
                                <span id="coldzonesStat">-</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>كثافة النقرات:</span>
                                <span id="clickDensityStat">-</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>درجة التفاعل:</span>
                                <span id="engagementScoreStat">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hotspots Analysis -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">النقاطق الساخنة</h5>
                </div>
                <div class="card-body">
                    <div id="hotspotsList">
                        <!-- Hotspots list will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">النقاطق الباردة</h5>
                </div>
                <div class="card-body">
                    <div id="coldzonesList">
                        <!-- Cold zones list will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Heatmaps -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">الخرائط الحرارية الأخيرة</h5>
                    <div class="btn-group btn-group-sm">
                        <a href="#" class="btn btn-outline-primary" onclick="exportHeatmap('csv')">
                            <i class="fas fa-download"></i> CSV
                        </a>
                        <a href="#" class="btn btn-outline-success" onclick="exportHeatmap('json')">
                            <i class="fas fa-download"></i> JSON
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الصفحة</th>
                                    <th>النوع</th>
                                    <th>الفترة</th>
                                    <th>التفاعلات</th>
                                    <th>التفاعل</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentHeatmaps as $heatmap)
                                <tr>
                                    <td>
                                        <a href="{{ $heatmap->page_url }}" target="_blank" class="text-decoration-none">
                                            {{ Str::limit($heatmap->page_url, 50) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $heatmap->isClickHeatmap() ? 'primary' : ($heatmap->isMovementHeatmap() ? 'success' : ($heatmap->isScrollHeatmap() ? 'info' : 'warning') }}">
                                            {{ $heatmap->heatmap_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $heatmap->time_range }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($heatmap->getTotalInteractionsAttribute()) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 20px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $heatmap->calculateEngagementScore() }}%"></div>
                                            </div>
                                            <small>{{ number_format($heatmap->calculateEngagementScore(), 1) }}%</small>
                                        </div>
                                    </td>
                                    <td>{{ $heatmap->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="viewHeatmap({{ $heatmap->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" onclick="compareHeatmap({{ $heatmap->id }})">
                                                <i class="fas fa-chart-bar"></i>
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
    </div>
</div>

<!-- Compare Heatmaps Modal -->
<div class="modal fade" id="compareHeatmapModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مقارنة الخرائط الحرارية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="compareForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">الخريطة الأولى</label>
                            <select class="form-select" id="heatmap1" name="heatmap1">
                                <option value="">اختر الخريطة الأولى</option>
                                @foreach($recentHeatmaps as $heatmap)
                                <option value="{{ $heatmap->id }}">{{ $heatmap->page_url }} - {{ $heatmap->heatmap_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الخريطة الثانية</label>
                            <select class="form-select" id="heatmap2" name="heatmap2">
                                <option value="">اختر الخريطة الثانية</option>
                                @foreach($recentHeatmaps as $heatmap)
                                <option value="{{ $heatmap->id }}">{{ $heatmap->page_url }} - {{ $heatmap->heatmap_type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">الفترة الأولى</label>
                            <select class="form-select" id="period1" name="period1">
                                <option value="7d">7 أيام</option>
                                <option value="30d">30 يوم</option>
                                <option value="90d">90 يوم</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الفترة الثانية</label>
                            <select class="form-select" id="period2" name="period2">
                                <option value="7d">7 أيام</option>
                                <option value="30d">30 يوم</option>
                                <option value="90d">90 يوم</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="mt-3">
                    <button type="button" class="btn btn-primary" onclick="performComparison()">
                        <i class="fas fa-chart-bar"></i> مقارنة
                    </button>
                </div>
                <div id="comparisonResults" class="mt-3">
                    <!-- Comparison results will be displayed here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentHeatmapType = 'click';
let currentHeatmapData = null;

$(document).ready(function() {
    $('#heatmapForm').on('submit', function(e) {
        e.preventDefault();
        generateHeatmap();
    });
});

function generateHeatmap() {
    const formData = $('#heatmapForm').serialize();
    
    $.post('/analytics/heatmaps/generate', formData, function(data) {
        if (data.status === 'success') {
            displayHeatmap(data.heatmap_data);
            updateHeatmapStats(data.heatmap_data);
            loadHotspotsAndColdzones(data.heatmap_data);
        } else {
            alert('حدث خطأ في إنشاء الخريطة الحرارية');
        }
    });
}

function generateRealtimeHeatmap() {
    const pageUrl = $('#page_url').val() || window.location.href;
    const heatmapType = $('#heatmap_type').val() || 'click';
    const timeRange = $('#time_range').val() || '30d';
    
    $.get('/analytics/heatmaps/realtime', {
        page_url: pageUrl,
        type: heatmapType,
        time_range: timeRange
    }, function(data) {
        displayHeatmap(data);
        updateHeatmapStats(data);
        loadHotspotsAndColdzones(data);
    });
}

function switchHeatmapType(type) {
    currentHeatmapType = type;
    
    // Update button states
    $('.btn-group button').removeClass('active');
    $(`.btn-group button:contains('${type}')`).addClass('active');
    
    // Reload heatmap with new type
    if ($('#page_url').val()) {
        generateRealtimeHeatmap();
    }
}

function displayHeatmap(data) {
    const container = document.getElementById('heatmapContainer');
    currentHeatmapData = data;
    
    if (!data || !data.intensity) {
        container.innerHTML = '<div class="text-center pt-5"><p class="text-muted">لا توجد بيانات متاحة</p></div>';
        return;
    }
    
    // Create heatmap visualization
    let html = '<div style="position: relative; width: 100%; height: 100%;">';
    
    // Add heatmap overlay
    Object.keys(data.intensity).forEach(position => {
        const [x, y] = position.split('_').map(Number);
        const intensity = data.intensity[position];
        const opacity = Math.min(intensity / 100, 1);
        
        const color = currentHeatmapType === 'click' ? 'rgba(255, 0, 0, ' + opacity + ')' :
                      currentHeatmapType === 'movement' ? 'rgba(0, 255, 0, ' + opacity + ')' :
                      currentHeatmapType === 'scroll' ? 'rgba(0, 0, 255, ' + opacity + ')' :
                      'rgba(255, 165, 0, ' + opacity + ')';
        
        html += `
            <div style="
                position: absolute;
                left: ${x * 50}px;
                top: ${y * 50}px;
                width: 50px;
                height: 50px;
                background: ${color};
                border: 1px solid rgba(0,0,0,0.1);
                cursor: pointer;
                title: 'التفاعلات: ${intensity}'
            "></div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function updateHeatmapStats(data) {
    if (!data) return;
    
    document.getElementById('totalInteractionsStat').textContent = 
        data.total_interactions || '0';
    document.getElementById('hotspotsStat').textContent = 
        Object.keys(data.hotspots || {}).length;
    document.getElementById('coldzonesStat').textContent = 
        Object.keys(data.cold_zones || {}).length;
    document.getElementById('clickDensityStat').textContent = 
        data.click_density || '0';
    document.getElementById('engagementScoreStat').textContent = 
        data.engagement_score ? number_format(data.engagement_score, 1) + '%' : '0%';
}

function loadHotspotsAndColdzones(data) {
    if (!data) return;
    
    // Update hotspots
    const hotspotsList = document.getElementById('hotspotsList');
    let hotspotsHtml = '<div class="list-group list-group-flush">';
    
    Object.keys(data.hotspots || {}).slice(0, 5).forEach(position => {
        const intensity = data.hotspots[position];
        hotspotsHtml += `
            <div class="list-group-item d-flex justify-content-between">
                <span>الموقع: ${position}</span>
                <span class="badge bg-danger">${intensity}</span>
            </div>
        `;
    });
    
    hotspotsHtml += '</div>';
    hotspotsList.innerHTML = hotspotsHtml;
    
    // Update cold zones
    const coldzonesList = document.getElementById('coldzonesList');
    let coldzonesHtml = '<div class="list-group list-group-flush">';
    
    Object.keys(data.cold_zones || {}).slice(0, 5).forEach(position => {
        const intensity = data.cold_zones[position];
        coldzonesHtml += `
            <div class="list-group-item d-flex justify-content-between">
                <span>الموقع: ${position}</span>
                <span class="badge bg-info">${intensity}</span>
            </div>
        `;
    });
    
    coldzonesHtml += '</div>';
    coldzonesList.innerHTML = coldzonesHtml;
}

function viewHeatmap(id) {
    // Load and display specific heatmap
    $.get('/analytics/heatmaps/' + id, function(data) {
        displayHeatmap(data);
        updateHeatmapStats(data);
        loadHotspotsAndColdzones(data);
    });
}

function compareHeatmap(id) {
    $('#heatmap1').val(id);
    $('#compareHeatmapModal').modal('show');
}

function performComparison() {
    const heatmap1 = $('#heatmap1').val();
    const heatmap2 = $('#heatmap2').val();
    const period1 = $('#period1').val();
    const period2 = $('#period2').val();
    
    if (!heatmap1 || !heatmap2) {
        alert('يرجى اختيار خريطتين للمقارنة');
        return;
    }
    
    $.post('/analytics/heatmaps/compare', {
        heatmap_id: heatmap1,
        compare_with: heatmap2,
        period1: period1,
        period2: period2
    }, function(data) {
        displayComparisonResults(data);
    });
}

function displayComparisonResults(data) {
    const results = document.getElementById('comparisonResults');
    
    let html = '<div class="card">';
    html += '<div class="card-header"><h6>نتائج المقارنة</h6></div>';
    html += '<div class="card-body">';
    
    if (data.steps_comparison) {
        html += '<table class="table table-sm">';
        html += '<thead><tr><th>الخطوة</th><th>الخريطة 1</th><th>الخريطة 2</th><th>التغيير</th></tr></thead>';
        html += '<tbody>';
        
        data.steps_comparison.forEach(step => {
            const changeClass = step.change > 0 ? 'text-success' : (step.change < 0 ? 'text-danger' : 'text-muted');
            html += `
                <tr>
                    <td>${step.step_name}</td>
                    <td>${step.period1_users}</td>
                    <td>${step.period2_users}</td>
                    <td class="${changeClass}">${step.change > 0 ? '+' : ''}${step.change}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
    }
    
    html += '</div></div>';
    results.innerHTML = html;
}

function exportHeatmap(format) {
    if (!currentHeatmapData) {
        alert('لا توجد خريطة حرارية متاحة للتصدير');
        return;
    }
    
    const pageUrl = $('#page_url').val() || window.location.href;
    const heatmapType = $('#heatmap_type').val() || 'click';
    
    window.open(`/analytics/heatmaps/export?format=${format}&page_url=${encodeURIComponent(pageUrl)}&type=${heatmapType}`);
}
</script>
@endpush
