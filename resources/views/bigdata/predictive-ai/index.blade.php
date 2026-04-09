@extends('admin.layouts.admin')

@section('title', 'الذكاء الاصطناعي التنبؤي')
@section('page-title', 'الذكاء الاصطناعي التنبؤي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">إجمالي التنبؤات</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['total_predictions'] }}</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            نشط حالياً
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-chart-bar text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm font-medium">معدل الدقة</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['accuracy_rate'] }}%</p>
                        <p class="text-emerald-100 text-sm mt-2">
                            <i class="fas fa-arrow-up ml-1"></i>
                            +2.3% هذا الأسبوع
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">النماذج النشطة</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['active_models'] }}</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-brain ml-1"></i>
                            جميعها تعمل
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-brain text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">آخر تحديث</p>
                        <p class="text-3xl font-bold mt-2">{{ explode(' ', $stats['last_updated'])[0] }}</p>
                        <p class="text-amber-100 text-sm mt-2">
                            <i class="fas fa-clock ml-1"></i>
                            {{ explode(' ', $stats['last_updated'])[1] ?? 'دقيقة' }}
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Analysis Panel -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">تحليل تنبؤي جديد</h3>
                            <p class="text-sm text-gray-600">Predictive Analysis</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="fas fa-analytics text-blue-600"></i>
                        </div>
                    </div>
                    <form id="analysisForm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="dataType" class="block text-sm font-medium text-gray-700 mb-2">نوع البيانات</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="dataType" name="data_type" required>
                                    <option value="">اختر نوع البيانات</option>
                                    <option value="properties">العقارات</option>
                                    <option value="markets">الأسواق</option>
                                    <option value="investments">الاستثمارات</option>
                                </select>
                            </div>
                            <div>
                                <label for="timeRange" class="block text-sm font-medium text-gray-700 mb-2">المدة الزمنية</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="timeRange" name="time_range" required>
                                    <option value="">اختر المدة</option>
                                    <option value="1m">شهر واحد</option>
                                    <option value="3m">3 أشهر</option>
                                    <option value="6m">6 أشهر</option>
                                    <option value="1y">سنة واحدة</option>
                                    <option value="2y">سنتان</option>
                                </select>
                            </div>
                            <div>
                                <label for="region" class="block text-sm font-medium text-gray-700 mb-2">المنطقة</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="region" name="region" placeholder="اختر المنطقة (اختياري)">
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-play ml-2"></i>
                                بدء التحليل
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Results Panel -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mt-6" id="resultsPanel" style="display: none;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">نتائج التحليل</h3>
                            <p class="text-sm text-gray-600">Analysis Results</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2">
                            <i class="fas fa-chart-line text-green-600"></i>
                        </div>
                    </div>
                    <div id="analysisResults">
                        <!-- Results will be displayed here -->
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Predictions Summary -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">ملخص التنبؤات</h3>
                            <p class="text-sm text-gray-600">Predictions Summary</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-2">
                            <i class="fas fa-chart-pie text-purple-600"></i>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">أسعار العقارات</span>
                                <span class="text-sm font-bold text-blue-600">{{ $predictionsSummary['property_prices'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $total = array_sum($predictionsSummary);
                                    $percentage = $total > 0 ? ($predictionsSummary['property_prices'] / $total) * 100 : 0;
                                @endphp
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">اتجاهات السوق</span>
                                <span class="text-sm font-bold text-green-600">{{ $predictionsSummary['market_trends'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $percentage = $total > 0 ? ($predictionsSummary['market_trends'] / $total) * 100 : 0;
                                @endphp
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">عائدات الاستثمار</span>
                                <span class="text-sm font-bold text-cyan-600">{{ $predictionsSummary['investment_returns'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $percentage = $total > 0 ? ($predictionsSummary['investment_returns'] / $total) * 100 : 0;
                                @endphp
                                <div class="bg-cyan-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">تقييم المخاطر</span>
                                <span class="text-sm font-bold text-amber-600">{{ $predictionsSummary['risk_assessment'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $percentage = $total > 0 ? ($predictionsSummary['risk_assessment'] / $total) * 100 : 0;
                                @endphp
                                <div class="bg-amber-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Predictions -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">التنبؤات الأخيرة</h3>
                            <p class="text-sm text-gray-600">Recent Predictions</p>
                        </div>
                        <div class="bg-amber-100 rounded-full p-2">
                            <i class="fas fa-history text-amber-600"></i>
                        </div>
                    </div>
                    <div class="space-y-4">
                        @foreach($recentPredictions as $prediction)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h6 class="font-medium text-gray-800">{{ $prediction['type'] }}</h6>
                                <p class="text-xs text-gray-500">{{ $prediction['created_at'] }}</p>
                            </div>
                            <span class="bg-{{ $prediction['value'] >= 0 ? 'green' : 'red' }}-100 text-{{ $prediction['value'] >= 0 ? 'green' : 'red' }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $prediction['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#analysisForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("bigdata.predictive-ai.analyze") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#resultsPanel').hide();
                $('.btn[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin ml-2"></i>جاري التحليل...');
            },
            success: function(response) {
                if (response.success) {
                    displayResults(response.data);
                    $('#resultsPanel').slideDown();
                } else {
                    alert('فشل التحليل: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('حدث خطأ: ' + xhr.responseJSON?.message || 'يرجى المحاولة مرة أخرى');
            },
            complete: function() {
                $('.btn[type="submit"]').prop('disabled', false).html('<i class="fas fa-play ml-2"></i>بدء التحليل');
            }
        });
    });
});

function displayResults(data) {
    let html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                <h6 class="text-blue-800 font-medium">مستوى الثقة</h6>
                <h3 class="text-2xl font-bold text-blue-900">${data.predictions.confidence_level}%</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
                <h6 class="text-green-800 font-medium">النمو المتوقع</h6>
                <h3 class="text-2xl font-bold text-green-900">${data.predictions.predicted_growth}</h3>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4">
                <h6 class="text-amber-800 font-medium">مستوى المخاطرة</h6>
                <h3 class="text-2xl font-bold text-amber-900">${data.predictions.risk_level}</h3>
            </div>
            <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-4">
                <h6 class="text-cyan-800 font-medium">وقت التحليل</h6>
                <h3 class="text-lg font-bold text-cyan-900">${new Date(data.generated_at).toLocaleString('ar-SA')}</h3>
            </div>
        </div>
    `;
    
    $('#analysisResults').html(html);
}

function refreshData() {
    location.reload();
}

// Add action buttons
function addHeaderButtons() {
    return `
        <div class="flex space-x-4 space-x-reverse">
            <a href="{{ route('bigdata.predictive-ai.dashboard') }}" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors border border-gray-300">
                <i class="fas fa-chart-line ml-2"></i>
                لوحة التحكم
            </a>
            <button type="button" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors border border-gray-300" onclick="refreshData()">
                <i class="fas fa-sync-alt ml-2"></i>
                تحديث
            </button>
        </div>
    `;
}
</script>
@endpush
