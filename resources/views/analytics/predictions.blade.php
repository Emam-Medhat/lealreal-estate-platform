@extends('layouts.app')

@section('title', 'التنبؤات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">التنبؤات</h1>
                <a href="{{ route('analytics.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Prediction Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($totalPredictions) }}</h4>
                            <p class="card-text">إجمالي التنبؤات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($avgAccuracy, 1) }}%</h4>
                            <p class="card-text">متوسط الدقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bullseye fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($highConfidence) }}</h4>
                            <p class="card-text">تنبؤات عالية الثقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shield-alt fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($pendingPredictions) }}</h4>
                            <p class="card-text">تنبؤات معلقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prediction Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إنشاء تنبؤ جديد</h5>
                </div>
                <div class="card-body">
                    <form id="predictionForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="prediction_type" class="form-label">نوع التنبؤ</label>
                                <select class="form-select" id="prediction_type" name="prediction_type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="revenue">الإيرادات</option>
                                    <option value="traffic">الزيارات</option>
                                    <option value="conversion">التحويلات</option>
                                    <option value="churn">معدل التسرب</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="time_horizon" class="form-label">الأفق الزمني</label>
                                <select class="form-select" id="time_horizon" name="time_horizon" required>
                                    <option value="">اختر الفترة</option>
                                    <option value="7d">7 أيام</option>
                                    <option value="30d">30 يوم</option>
                                    <option value="90d">90 يوم</option>
                                    <option value="1y">سنة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="model_type" class="form-label">نموذج التنبؤ</label>
                                <select class="form-select" id="model_type" name="model_type" required>
                                    <option value="">اختر النموذج</option>
                                    <option value="linear">انحدار خطي</option>
                                    <option value="regression">انحدار متعدد</option>
                                    <option value="neural_network">شبكة عصبية</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-magic"></i> إنشاء تنبؤ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Predictions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">التنبؤات الأخيرة</h5>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('analytics.predictions.export', ['format' => 'csv']) }}" class="btn btn-outline-primary">
                            <i class="fas fa-download"></i> تصدير CSV
                        </a>
                        <a href="{{ route('analytics.predictions.export', ['format' => 'json']) }}" class="btn btn-outline-success">
                            <i class="fas fa-download"></i> تصدير JSON
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>القيمة المتوقعة</th>
                                    <th>القيمة الفعلية</th>
                                    <th>الدقة</th>
                                    <th>الثقة</th>
                                    <th>النموذج</th>
                                    <th>الفترة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($predictions as $prediction)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $prediction->getPredictionLabel() }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($prediction->predicted_value, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($prediction->actual_value)
                                            <span class="{{ $prediction->getPredictionError()['percentage_error'] <= 10 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($prediction->actual_value, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">غير متوفر</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($prediction->accuracy)
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 60px; height: 20px;">
                                                    <div class="progress-bar bg-{{ $prediction->getAccuracyLevel() === 'excellent' ? 'success' : ($prediction->getAccuracyLevel() === 'good' ? 'warning' : 'danger') }}"
                                                         style="width: {{ $prediction->accuracy }}%">
                                                    </div>
                                                </div>
                                                <small>{{ number_format($prediction->accuracy, 1) }}%</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 20px;">
                                                <div class="progress-bar bg-{{ $prediction->getConfidenceLevel() === 'very_high' ? 'success' : ($prediction->getConfidenceLevel() === 'high' ? 'info' : ($prediction->getConfidenceLevel() === 'medium' ? 'warning' : 'danger')) }}"
                                                     style="width: {{ $prediction->confidence_score }}%">
                                                </div>
                                            </div>
                                            <small>{{ number_format($prediction->confidence_score, 1) }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $prediction->getModelLabel() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $prediction->getTimeHorizonLabel() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $prediction->isCompleted() ? 'success' : ($prediction->isFailed() ? 'danger' : 'warning') }}">
                                            {{ $prediction->status === 'completed' ? 'مكتمل' : ($prediction->status === 'failed' ? 'فشل' : 'معلق') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="viewPrediction({{ $prediction->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($prediction->actual_value === null)
                                                <button class="btn btn-outline-success" onclick="updateActualValue({{ $prediction->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
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

    <!-- Prediction Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">دقة التنبؤات حسب النوع</h5>
                </div>
                <div class="card-body">
                    <div id="accuracyByTypeChart" style="height: 300px;">
                        <!-- Accuracy chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">دقة التنبؤات حسب النموذج</h5>
                </div>
                <div class="card-body">
                    <div id="accuracyByModelChart" style="height: 300px;">
                        <!-- Model accuracy chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Model Performance -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أداء النماذج</h5>
                </div>
                <div class="card-body">
                    <div id="modelPerformanceChart" style="height: 400px;">
                        <!-- Model performance chart will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prediction Details Modal -->
<div class="modal fade" id="predictionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل التنبؤ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="predictionDetails">
                    <!-- Prediction details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Actual Value Modal -->
<div class="modal fade" id="updateActualValueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث القيمة الفعلية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateActualValueForm">
                    <input type="hidden" id="predictionId" name="prediction_id">
                    <div class="mb-3">
                        <label for="actual_value" class="form-label">القيمة الفعلية</label>
                        <input type="number" step="0.01" class="form-control" id="actual_value" name="actual_value" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveActualValue()">حفظ</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadAccuracyByType();
    loadAccuracyByModel();
    loadModelPerformance();
    
    $('#predictionForm').on('submit', function(e) {
        e.preventDefault();
        createPrediction();
    });
});

function createPrediction() {
    const formData = $('#predictionForm').serialize();
    
    $.post('/analytics/predictions/generate', formData, function(data) {
        if (data.status === 'success') {
            alert('تم إنشاء التنبؤ بنجاح!');
            location.reload();
        } else {
            alert('حدث خطأ في إنشاء التنبؤ');
        }
    });
}

function viewPrediction(id) {
    $.get('/analytics/predictions/' + id, function(data) {
        updatePredictionDetails(data);
        $('#predictionModal').modal('show');
    });
}

function updatePredictionDetails(data) {
    const details = data.prediction_info;
    const performance = data.performance;
    
    let html = `
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات التنبؤ</h6>
                <table class="table table-sm">
                    <tr><td>النوع:</td><td>${details.type_label}</td></tr>
                    <tr><td>الفترة:</td><td>${details.time_horizon_label}</td></tr>
                    <tr><td>النموذج:</td><td>${details.model_label}</td></tr>
                    <tr><td>الحالة:</td><td>${details.status}</td></tr>
                    <tr><td>تاريخ الإنشاء:</td><td>${details.created_at}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>بيانات التنبؤ</h6>
                <table class="table table-sm">
                    <tr><td>القيمة المتوقعة:</td><td>${details.predicted_value}</td></tr>
                    <tr><td>القيمة الفعلية:</td><td>${details.actual_value || 'غير متوفر'}</td></tr>
                    <tr><td>مستوى الثقة:</td><td>${details.confidence_level}</td></tr>
                    <tr><td>مستوى الدقة:</td><td>${details.accuracy_level || 'غير متوفر'}</td></tr>
                    <tr><td>تقييم الأداء:</td><td>${performance?.performance_rating || 'معلق'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    if (data.top_features && data.top_features.length > 0) {
        html += '<h6 class="mt-3">أهم المميزات</h6>';
        html += '<div class="list-group list-group-flush">';
        
        Object.keys(data.top_features).forEach(feature => {
            html += `
                <div class="list-group-item d-flex justify-content-between">
                    <span>${feature}</span>
                    <span class="badge bg-primary">${data.top_features[feature]}</span>
                </div>
            `;
        });
        
        html += '</div>';
    }
    
    if (data.recommendations && data.recommendations.length > 0) {
        html += '<h6 class="mt-3">التوصيات</h6>';
        html += '<ul class="list-group list-group-flush">';
        
        data.recommendations.forEach(rec => {
            html += `<li class="list-group-item">${rec}</li>`;
        });
        
        html += '</ul>';
    }
    
    document.getElementById('predictionDetails').innerHTML = html;
}

function updateActualValue(id) {
    $('#predictionId').val(id);
    $('#updateActualValueModal').modal('show');
}

function saveActualValue() {
    const id = $('#predictionId').val();
    const actualValue = $('#actual_value').val();
    
    $.post('/analytics/predictions/' + id + '/update-actual', {
        actual_value: actualValue
    }, function(data) {
        if (data.status === 'success') {
            alert('تم تحديث القيمة الفعلية بنجاح!');
            $('#updateActualValueModal').modal('hide');
            location.reload();
        } else {
            alert('حدث خطأ في تحديث القيمة الفعلية');
        }
    });
}

function loadAccuracyByType() {
    $.get('/analytics/predictions/accuracy', function(data) {
        updateAccuracyByTypeChart(data.by_type);
    });
}

function loadAccuracyByModel() {
    $.get('/analytics/predictions/accuracy', function(data) {
        updateAccuracyByModelChart(data.by_model);
    });
}

function loadModelPerformance() {
    $.get('/analytics/predictions/accuracy', function(data) {
        updateModelPerformanceChart(data);
    });
}

function updateAccuracyByTypeChart(data) {
    const ctx = document.getElementById('accuracyByTypeChart');
    if (ctx && data) {
        let html = '<div class="list-group list-group-flush">';
        
        Object.keys(data).forEach(type => {
            const accuracy = data[type];
            const color = accuracy >= 80 ? 'success' : (accuracy >= 60 ? 'warning' : 'danger');
            
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${type}</span>
                        <span class="badge bg-${color}">${number_format(accuracy, 1)}%</span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-${color}" style="width: ${accuracy}%"></div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateAccuracyByModelChart(data) {
    const ctx = document.getElementById('accuracyByModelChart');
    if (ctx && data) {
        let html = '<div class="list-group list-group-flush">';
        
        Object.keys(data).forEach(model => {
            const accuracy = data[model];
            const color = accuracy >= 80 ? 'success' : (accuracy >= 60 ? 'warning' : 'danger');
            
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${model}</span>
                        <span class="badge bg-${color}">${number_format(accuracy, 1)}%</span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-${color}" style="width: ${accuracy}%"></div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}

function updateModelPerformanceChart(data) {
    const ctx = document.getElementById('modelPerformanceChart');
    if (ctx && data) {
        let html = '<div class="text-center">';
        html += '<h4>أداء النماذج</h4>';
        html += '<p class="text-muted">متوسط الدقة الإجمالية: ' + number_format(data.overall_accuracy, 1) + '%</p>';
        
        if (data.improvement_suggestions && data.improvement_suggestions.length > 0) {
            html += '<h6>توصيات التحسين:</h6>';
            html += '<ul class="list-unstyled">';
            data.improvement_suggestions.forEach(suggestion => {
                html += `<li class="mb-2"><i class="fas fa-lightbulb text-warning"></i> ${suggestion}</li>`;
            });
            html += '</ul>';
        }
        
        html += '</div>';
        ctx.innerHTML = html;
    }
}
</script>
@endpush
