@extends('layouts.app')

@section('title', 'تحليلات السوق')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">تحليلات السوق</h1>
                <a href="{{ route('analytics.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Market Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($marketSize, 2) }}</h4>
                            <p class="card-text">حجم السوق</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-pie fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($marketGrowth, 2) }}%</h4>
                            <p class="card-text">نمو السوق</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($competitorCount) }}</h4>
                            <p class="card-text">عدد المنافسين</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($ourShare, 2) }}%</h4>
                            <p class="card-text">حصتنا في السوق</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
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
                    <form method="GET" action="{{ route('analytics.market.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="category" class="form-label">الفئة</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">جميع الفئات</option>
                                    <option value="residential" {{ request('category') == 'residential' ? 'selected' : '' }}>سكني</option>
                                    <option value="commercial" {{ request('category') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                                    <option value="industrial" {{ request('category') == 'industrial' ? 'selected' : '' }}>صناعي</option>
                                    <option value="land" {{ request('category') == 'land' ? 'selected' : '' }}>أراضي</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="region" class="form-label">المنطقة</label>
                                <select class="form-select" id="region" name="region">
                                    <option value="">جميع المناطق</option>
                                    <option value="riyadh" {{ request('region') == 'riyadh' ? 'selected' : '' }}>الرياض</option>
                                    <option value="jeddah" {{ request('region') == 'jeddah' ? 'selected' : '' }}>جدة</option>
                                    <option value="dammam" {{ request('region') == 'dammam' ? 'selected' : '' }}>الدمام</option>
                                    <option value="mecca" {{ request('region') == 'mecca' ? 'selected' : '' }}>مكة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="period" class="form-label">الفترة</label>
                                <select class="form-select" id="period" name="period">
                                    <option value="7d" {{ request('period') == '7d' ? 'selected' : '' }}>7 أيام</option>
                                    <option value="30d" {{ request('period') == '30d' ? 'selected' : '' }}>30 يوم</option>
                                    <option value="90d" {{ request('period') == '90d' ? 'selected' : '' }}>90 يوم</option>
                                    <option value="1y" {{ request('period') == '1y' ? 'selected' : '' }}>سنة</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('analytics.market.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Trends Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">اتجاهات السوق</h5>
                </div>
                <div class="card-body">
                    <div id="marketTrendsChart" style="height: 400px;">
                        <!-- Chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Competitor Analysis -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تحليل المنافسين</h5>
                </div>
                <div class="card-body">
                    <div id="competitorChart" style="height: 300px;">
                        <!-- Competitor chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">مقارنة الأسعار</h5>
                </div>
                <div class="card-body">
                    <div id="priceComparisonChart" style="height: 300px;">
                        <!-- Price comparison chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Segments -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تقسيمات السوق</h5>
                </div>
                <div class="card-body">
                    <div id="marketSegmentsChart" style="height: 350px;">
                        <!-- Market segments chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الفرص السوقية</h5>
                </div>
                <div class="card-body">
                    <div id="opportunitiesList">
                        <!-- Opportunities list will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Trends -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الاتجاهات الأخيرة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الاتجاه</th>
                                    <th>النوع</th>
                                    <th>الفئة</th>
                                    <th>الاتجاه</th>
                                    <th>التغيير</th>
                                    <th>الثقة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTrends as $trend)
                                <tr>
                                    <td>{{ $trend->trend_name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $trend->getTrendLabel() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $trend->getCategoryLabel() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $trend->isIncreasing() ? 'bg-success' : ($trend->isDecreasing() ? 'bg-danger' : 'bg-warning') }}">
                                            {{ $trend->getDirectionLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $trend->change_percentage > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($trend->change_percentage, 2) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $trend->getConfidenceLevel() === 'high' ? 'success' : ($trend->getConfidenceLevel() === 'medium' ? 'warning' : 'danger') }}"
                                                 style="width: {{ $trend->confidence_score }}%">
                                                {{ number_format($trend->confidence_score, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $trend->created_at->format('Y-m-d') }}</td>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadMarketTrends();
    loadCompetitorAnalysis();
    loadPriceComparison();
    loadMarketSegments();
    loadOpportunities();
});

function loadMarketTrends() {
    $.get('/analytics/market/trends', function(data) {
        updateMarketTrendsChart(data);
    });
}

function loadCompetitorAnalysis() {
    $.get('/analytics/market/competitors', function(data) {
        updateCompetitorChart(data);
    });
}

function loadPriceComparison() {
    $.get('/analytics/market/pricing', function(data) {
        updatePriceComparisonChart(data);
    });
}

function loadMarketSegments() {
    $.get('/analytics/market/segments', function(data) {
        updateMarketSegmentsChart(data);
    });
}

function loadOpportunities() {
    $.get('/analytics/market/opportunities', function(data) {
        updateOpportunitiesList(data);
    });
}

function updateMarketTrendsChart(data) {
    const ctx = document.getElementById('marketTrendsChart');
    if (ctx && data.trends) {
        // Simple text display for now
        let html = '<div class="text-center">';
        html += '<h4>اتجاهات السوق</h4>';
        html += '<div class="row">';
        
        data.trends.slice(0, 4).forEach(trend => {
            const directionIcon = trend.isIncreasing() ? '↑' : (trend.isDecreasing() ? '↓' : '→');
            const color = trend.isIncreasing() ? 'text-success' : (trend.isDecreasing() ? 'text-danger' : 'text-warning');
            
            html += `
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="${color}">${directionIcon} ${trend.trend_name}</h6>
                            <p class="text-muted">${trend.getTrendLabel()}</p>
                            <small>${number_format(trend.value, 2)}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
        ctx.innerHTML = html;
    }
}

function updateCompetitorChart(data) {
    const ctx = document.getElementById('competitorChart');
    if (ctx && data.competitors) {
        let html = '<div class="list-group list-group-flush">';
        
        data.competitors.slice(0, 5).forEach(competitor => {
            const threatLevel = competitor.getThreatLevel();
            const threatColor = threatLevel === 'high' ? 'danger' : (threatLevel === 'medium' ? 'warning' : 'success');
            
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${competitor.name}</h6>
                        <small class="text-muted">حصة السوق: ${number_format(competitor.market_share, 2)}%</small>
                    </div>
                    <span class="badge bg-${threatColor}">${threatLevel}</span>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updatePriceComparisonChart(data) {
    const ctx = document.getElementById('priceComparisonChart');
    if (ctx && data.competitors) {
        let html = '<div class="list-group list-group-flush">';
        
        data.competitors.slice(0, 5).forEach(competitor => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${competitor.name}</span>
                        <span class="badge bg-primary">${number_format(competitor.avg_price, 2)}</span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-info" style="width: ${(competitor.avg_price / data.max_price) * 100}%"></div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateMarketSegmentsChart(data) {
    const ctx = document.getElementById('marketSegmentsChart');
    if (ctx && data.segments) {
        let html = '<div class="row">';
        
        data.segments.forEach(segment => {
            html += `
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>${segment.name}</h5>
                            <p class="text-muted">${segment.size}%</p>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: ${segment.size}%"></div>
                            </div>
                            <small>نمو: ${segment.growth}%</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateOpportunitiesList(data) {
    const ctx = document.getElementById('opportunitiesList');
    if (ctx && data.opportunities) {
        let html = '<div class="list-group list-group-flush">';
        
        data.opportunities.underserved_segments.forEach(opportunity => {
            html += `
                <div class="list-group-item">
                    <h6 class="text-primary">${opportunity}</h6>
                    <small class="text-muted">فرصة سوقية غير مستغلة</small>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}
</script>
@endpush
