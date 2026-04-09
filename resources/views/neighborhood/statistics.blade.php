@extends('layouts.app')

@section('title', 'إحصائيات الأحياء')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">إحصائيات الأحياء</h1>
            <p class="text-muted mb-0">تحليل شامل للبيانات والإحصائيات</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#overviewModal">
                <i class="fas fa-chart-bar me-2"></i>نظرة عامة
            </button>
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download me-2"></i>تصدير
            </button>
            <a href="{{ route('neighborhood-statistics.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة إحصائية
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('neighborhood-statistics.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الحي</label>
                        <select name="neighborhood_id" class="form-select">
                            <option value="">جميع الأحياء</option>
                            @foreach($neighborhoods as $neighborhood)
                                <option value="{{ $neighborhood->id }}" 
                                        {{ request('neighborhood_id') == $neighborhood->id ? 'selected' : '' }}>
                                    {{ $neighborhood->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع الإحصائية</label>
                        <select name="statistic_type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            @foreach($statisticTypes as $type)
                                <option value="{{ $type }}" 
                                        {{ request('statistic_type') == $type ? 'selected' : '' }}>
                                    {{ $statisticTypeLabels[$type] ?? $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الفترة</label>
                        <select name="period" class="form-select">
                            <option value="">جميع الفترات</option>
                            <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>يومي</option>
                            <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
                            <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>شهري</option>
                            <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                            <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>سنوي</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search me-2"></i>بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white mb-0">إجمالي الإحصائيات</h6>
                            <p class="card-text text-white-50 mb-0">جميع الإحصائيات المسجلة</p>
                        </div>
                        <div class="text-end">
                            <h3 class="text-white">{{ $overview['total_statistics'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white mb-0">الأحياء النشطة</h6>
                            <p class="card-text text-white-50 mb-0">الأحياء ذات البيانات</p>
                        </div>
                        <div class="text-end">
                            <h3 class="text-white">{{ $overview['active_neighborhoods'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white mb-0">نقاطف البيانات</h6>
                            <p class="card-text text-white-50 mb-0">إجمالي نقاطف البيانات</p>
                        </div>
                        <div class="text-end">
                            <h3 class="text-white">{{ $overview['total_data_points'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white mb-0">جودة البيانات</h6>
                            <p class="card-text text-white-50 mb-0">متوسط جودة البيانات</p>
                        </div>
                        <div class="text-end">
                            <h3 class="text-white">{{ number_format($overview['data_quality_score'] * 100) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">قائمة الإحصائيات</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>الحي</th>
                            <th>النوع الإحصائية</th>
                            <th>العنوان</th>
                            <th>الفترة</th>
                            <th>تاريخ الجمع</th>
                            <th>البيانات</th>
                            <th>التحليل</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statistics as $statistic)
                            <tr>
                                <td>{{ $statistic->neighborhood->name }}</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $statisticTypeLabels[$statistic->statistic_type] ?? $statistic->statistic_type }}
                                    </span>
                                </td>
                                <td>{{ $statistic->title }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $statistic->period_label }}
                                    </span>
                                </td>
                                <td>{{ $statistic->collection_date_label }}</td>
                                <td>
                                    @if($statistic->hasAggregatedData())
                                        <small>
                                            الحدنى: {{ number_format($statistic->total_value) }}
                                            <br>
                                            المتوسط: {{ number_format($statistic->average_value) }}
                                        </small>
                                    @else
                                        <span class="text-muted">لا توجد بيانات</span>
                                    @endif
                                </td>
                                <td>
                                    @if($statistic->hasTrendAnalysis())
                                        <span class="badge bg-{{ $statistic->trend == 'increasing' ? 'success' : ($statistic->trend == 'decreasing' ? 'danger' : 'secondary') }}">
                                            {{ $statistic->trend_label }}
                                            <br>
                                            {{ $statistic->percentage_change_label }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">لا يوجد تحليل</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('neighborhood-statistics.show', $statistic) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->check())
                                            <a href="{{ route('neighborhood-statistics.edit', $statistic) }}" 
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="text-center py-4">
                                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد إحصائيات متاحة</h5>
                                        <p class="text-muted">لم يتم العثور على أي إحصائيات تطابق معايير البحث الخاصة بك.</p>
                                        <a href="{{ route('neighborhood-statistics.create') }}" class="btn btn-primary mt-3">
                                            <i class="fas fa-plus me-2"></i>إضافة إحصائية جديدة
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($statistics->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $statistics->links() }}
        </div>
    @endif
</div>

<!-- Overview Modal -->
<div class="modal fade" id="overviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">نظرة عامة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">نظرة الإحصائيات حسب النوع</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="statsByTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">نظرة الإحصائيات حسب الفترة</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="statsByPeriodChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">أحدث الإحصائيات</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="recentStatsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تصدير الإحصائيات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('neighborhood-statistics.export') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">تنسيق التصدير</label>
                            <select name="format" class="form-select">
                                <option value="csv">CSV</option>
                                <option value="xlsx">Excel</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تضمين البيانات</label>
                            <div class="form-check">
                                <input type="checkbox" name="include_data_points" class="form-check-input" checked>
                                <label class="form-check-label" for="include_data_points">
                                    تضمين نقاط البيانات
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="include_visualization" class="form-check-input">
                                <label class="form-check-label" for="include_visualization">
                                    تضمين بيانات التصور
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">فلاتر التصفية</label>
                            <div class="form-check">
                                <input type="checkbox" name="filters[neighborhood_id]" class="form-check-input">
                                <label class="form-check-label" for="filters[neighborhood_id]">
                                    تصفية حسب الحي
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="filters[statistic_type]" class="form-check-input">
                                <label class="form-check-label" for="filters[statistic_type]">
                                    تصفية حسب النوع
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>تصدير
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts when modal is shown
document.getElementById('overviewModal').addEventListener('shown.bs.modal', function () {
    // Stats by Type Chart
    const statsByTypeCtx = document.getElementById('statsByTypeChart').getContext('2d');
    new Chart(statsByTypeCtx, {
        type: 'pie',
        data: {
            labels: [
                @foreach($stats['by_type'] as $type => $statisticTypeLabels[$type] ?? $type)
            ],
            datasets: [{
                data: [
                    @foreach($stats['by_type'] as $count)
                        {{ $count }}
                    ],
                backgroundColor: [
                    '#007bff', '#28a745', '#17a2b8', '#ffc107', '#6c757d', '#343a40', '#fd7e14', '#20c997', '#6f42c8', '#5cb85c'
                ]
            }]
        }
    });

    // Stats by Period Chart
    const statsByPeriodCtx = document.getElementById('statsByPeriodChart').getContext('2d');
    new Chart(statsByPeriodCtx, {
        type: 'line',
        data: {
            labels: ['يناير', 'أمس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسط', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            datasets: [{
                label: 'عدد الإحصائيات',
                data: [
                    @foreach($stats['by_month'] as $month => $month)
                        {{ $month }}
                ],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }]
        }
    });

    // Recent Stats Chart
    const recentStatsCtx = document.getElementById('recentStatsChart').getContext('2d');
    new Chart(recentStatsCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($stats['recent'] as $statistic)
                    {{ $statistic->title }}
                ],
            datasets: [{
                label: 'عدد نقاط البيانات',
                data: [
                    @foreach($stats['recent'] as $statistic)
                        $statistic->data_points ? count($statistic->data_points) : 0
                    ],
                backgroundColor: '#28a745'
            }]
        }
    });
});

// Export functionality
document.getElementById('exportModal').addEventListener('shown.bs.modal', function () {
    // Load recent statistics for export
    fetch('{{ route('neighborhood-statistics.get-recent')}')
        .then(response => response.json())
        .then(data => {
            // Update the export form with recent statistics
            const form = document.querySelector('#exportModal form');
            const statsInput = document.createElement('input');
            statsInput.type = 'hidden';
            statsInput.name = 'recent_stats';
            statsInput.value = JSON.stringify(data);
            form.appendChild(statsInput);
        });
});

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
    
    // Auto-submit on filter change
    const filters = ['neighborhood_id', 'statistic_type', 'period'];
    filters.forEach(filterName => {
        const filter = document.querySelector(`select[name="${filterName}"]`);
        if (filter) {
            filter.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
});
</script>
@endpush
