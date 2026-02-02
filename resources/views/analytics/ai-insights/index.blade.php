@extends('admin.layouts.admin')

@section('title', 'رؤى الذكاء الاصطناعي')

@section('page-title', 'رؤى الذكاء الاصطناعي')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- AI Insights Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">رؤى الذكاء الاصطناعي</h1>
                <p class="text-gray-600">تحليل البيانات باستخدام الذكاء الاصطناعي للحصول على رؤى عميقة وتوصيات ذكية</p>
            </div>
            <button onclick="generateNewInsights()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center transition-colors">
                <i class="fas fa-magic ml-2"></i>
                توليد رؤى جديدة
            </button>
        </div>
    </div>

    <!-- AI Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Anomaly Detection -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-red-100 text-red-600 ml-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">كشف الشذوذ</h3>
            </div>
            <p class="text-gray-600 mb-4">اكتشاف الأنماط غير الطبيعية في البيانات</p>
            <button onclick="detectAnomalies()" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg font-medium transition-colors">
                تشغيل الكشف
            </button>
        </div>

        <!-- Pattern Recognition -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-green-100 text-green-600 ml-4">
                    <i class="fas fa-project-diagram text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">التعرف على الأنماط</h3>
            </div>
            <p class="text-gray-600 mb-4">تحديد الأنماط المتكررة في السلوك</p>
            <button onclick="recognizePatterns()" class="w-full bg-green-50 hover:bg-green-100 text-green-600 px-4 py-2 rounded-lg font-medium transition-colors">
                تحليل الأنماط
            </button>
        </div>

        <!-- Predictions -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 ml-4">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">التنبؤات</h3>
            </div>
            <p class="text-gray-600 mb-4">توقع الاتجاهات المستقبلية</p>
            <button onclick="getPredictions()" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-lg font-medium transition-colors">
                الحصول على التنبؤات
            </button>
        </div>

        <!-- Recommendations -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 ml-4">
                    <i class="fas fa-lightbulb text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">التوصيات</h3>
            </div>
            <p class="text-gray-600 mb-4">الحصول على اقتراحات ذكية</p>
            <button onclick="getRecommendations()" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-600 px-4 py-2 rounded-lg font-medium transition-colors">
                الحصول على توصيات
            </button>
        </div>
    </div>

    <!-- Recent Predictions -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">التنبؤات الحديثة</h2>
        </div>
        <div class="p-6">
            @if($insights->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرقم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التنبؤ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الثقة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($insights as $insight)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $insight->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $insight->type ?? 'غير محدد' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($insight->prediction ?? 'غير محدد', 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(isset($insight->confidence))
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $insight->confidence > 80 ? 'bg-green-100 text-green-800' : 
                                               ($insight->confidence > 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $insight->confidence }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $insight->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="viewDetails({{ $insight->id }})" class="text-blue-600 hover:text-blue-900 font-medium">
                                        <i class="fas fa-eye ml-1"></i>
                                        عرض
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($insights->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $insights->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-robot text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد رؤى متاحة</h3>
                    <p class="text-gray-600 mb-6">ابدأ في توليد رؤى الذكاء الاصطناعي لرؤية التنبؤات والتوصيات هنا</p>
                    <button onclick="generateNewInsights()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-magic ml-2"></i>
                        توليد أول رؤى
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Results Modal -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" id="resultsModal">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">نتائج التحليل بالذكاء الاصطناعي</h3>
            <button onclick="closeResultsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="resultsContent">
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-gray-600">جاري تحليل البيانات...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateNewInsights() {
    showResultsModal();
    
    fetch('/ai/insights/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            data_source: 'all',
            insight_type: 'prediction',
            time_range: '30d'
        })
    })
    .then(response => response.json())
    .then(data => {
        displayResults(data);
    })
    .catch(error => {
        console.error('Error:', error);
        showError('فشل في توليد الرؤى');
    });
}

function detectAnomalies() {
    showResultsModal();
    
    fetch('/ai/anomaly-detection', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        displayResults({ anomalies: data });
    })
    .catch(error => {
        console.error('Error:', error);
        showError('فشل في كشف الشذوذ');
    });
}

function recognizePatterns() {
    showResultsModal();
    
    fetch('/ai/pattern-recognition', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        displayResults({ patterns: data });
    })
    .catch(error => {
        console.error('Error:', error);
        showError('فشل في التعرف على الأنماط');
    });
}

function getPredictions() {
    showResultsModal();
    
    fetch('/ai/predictive-insights', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        displayResults({ predictions: data });
    })
    .catch(error => {
        console.error('Error:', error);
        showError('فشل في الحصول على التنبؤات');
    });
}

function getRecommendations() {
    showResultsModal();
    
    fetch('/ai/recommendations', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        displayResults({ recommendations: data });
    })
    .catch(error => {
        console.error('Error:', error);
        showError('فشل في الحصول على التوصيات');
    });
}

function viewDetails(id) {
    alert('عرض التفاصيل للرؤى رقم: ' + id);
}

function showResultsModal() {
    document.getElementById('resultsModal').classList.remove('hidden');
}

function closeResultsModal() {
    document.getElementById('resultsModal').classList.add('hidden');
}

function displayResults(data) {
    const content = document.getElementById('resultsContent');
    let html = '';
    
    if (data.anomalies) {
        html = '<div class="mb-4"><h4 class="text-lg font-semibold text-red-600 mb-3">الشذوذ المكتشف</h4>';
        if (data.anomalies.length > 0) {
            html += '<div class="space-y-2">';
            data.anomalies.forEach(anomaly => {
                html += `<div class="bg-red-50 border-r-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <strong>${anomaly.date}</strong> - شذوذ ${anomaly.type} (درجة Z: ${anomaly.z_score.toFixed(2)})
                            </p>
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
        } else {
            html += '<p class="text-gray-600">لم يتم اكتشاف أي شذوذ في الفترة المحددة</p>';
        }
        html += '</div>';
    } else if (data.patterns) {
        html = '<div class="mb-4"><h4 class="text-lg font-semibold text-green-600 mb-3">الأنماط المعترف بها</h4>';
        
        // Handle seasonal patterns
        if (data.patterns.seasonal_patterns) {
            if (typeof data.patterns.seasonal_patterns === 'object' && data.patterns.seasonal_patterns.weekly_pattern) {
                html += `<div class="mb-4">
                    <h5 class="font-medium text-gray-900 mb-3 bg-green-50 p-2 rounded">الأنماط الموسمية</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">`;
                
                Object.keys(data.patterns.seasonal_patterns).forEach(key => {
                    const title = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    html += `<div class="flex items-start text-sm text-gray-700 bg-white p-2 rounded border border-green-200">
                        <i class="fas fa-chart-line text-green-500 ml-2 mt-1"></i>
                        <div>
                            <strong>${title}:</strong>
                            <p class="mt-1">${data.patterns.seasonal_patterns[key]}</p>
                        </div>
                    </div>`;
                });
                
                html += '</div></div>';
            } else {
                html += `<div class="mb-4">
                    <h5 class="font-medium text-gray-900 mb-3 bg-green-50 p-2 rounded">الأنماط الموسمية</h5>
                    <div class="flex items-center text-sm text-gray-700 bg-white p-3 rounded border border-green-200">
                        <i class="fas fa-chart-line text-green-500 ml-2"></i>
                        <span>${data.patterns.seasonal_patterns}</span>
                    </div>
                </div>`;
            }
        }
        
        // Handle trend patterns
        if (data.patterns.trend_patterns) {
            html += `<div class="mb-4">
                <h5 class="font-medium text-gray-900 mb-3 bg-blue-50 p-2 rounded">اتجاهات النمو</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">`;
            
            Object.keys(data.patterns.trend_patterns).forEach(key => {
                const title = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                html += `<div class="flex items-start text-sm text-gray-700 bg-white p-2 rounded border border-blue-200">
                    <i class="fas fa-trending-up text-blue-500 ml-2 mt-1"></i>
                    <div>
                        <strong>${title}:</strong>
                        <p class="mt-1">${data.patterns.trend_patterns[key]}</p>
                    </div>
                </div>`;
            });
            
            html += '</div></div>';
        }
        
        // Handle behavioral patterns
        if (data.patterns.behavioral_patterns) {
            html += `<div class="mb-4">
                <h5 class="font-medium text-gray-900 mb-3 bg-purple-50 p-2 rounded">الأنماط السلوكية</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">`;
            
            Object.keys(data.patterns.behavioral_patterns).forEach(key => {
                const title = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                html += `<div class="flex items-start text-sm text-gray-700 bg-white p-2 rounded border border-purple-200">
                    <i class="fas fa-users text-purple-500 ml-2 mt-1"></i>
                    <div>
                        <strong>${title}:</strong>
                        <p class="mt-1">${data.patterns.behavioral_patterns[key]}</p>
                    </div>
                </div>`;
            });
            
            html += '</div></div>';
        }
        
        // Handle conversion patterns
        if (data.patterns.conversion_patterns) {
            html += `<div class="mb-4">
                <h5 class="font-medium text-gray-900 mb-3 bg-orange-50 p-2 rounded">أنماط التحويل</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">`;
            
            Object.keys(data.patterns.conversion_patterns).forEach(key => {
                const title = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                html += `<div class="flex items-start text-sm text-gray-700 bg-white p-2 rounded border border-orange-200">
                    <i class="fas fa-exchange-alt text-orange-500 ml-2 mt-1"></i>
                    <div>
                        <strong>${title}:</strong>
                        <p class="mt-1">${data.patterns.conversion_patterns[key]}</p>
                    </div>
                </div>`;
            });
            
            html += '</div></div>';
        }
        
        html += '</div>';
    } else if (data.predictions) {
        html = '<div class="mb-4"><h4 class="text-lg font-semibold text-blue-600 mb-3">الرؤى التنبؤية</h4>';
        Object.keys(data.predictions).forEach(key => {
            const title = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            const prediction = data.predictions[key];
            
            html += `<div class="mb-4">
                <h5 class="font-medium text-gray-900 mb-2">${title}</h5>`;
            
            if (typeof prediction === 'object' && prediction.next_7_days) {
                html += '<div class="bg-blue-50 p-3 rounded-lg mb-2">';
                html += '<p class="text-sm text-gray-700 mb-2"><strong>توقعات 7 أيام قادمة:</strong></p>';
                html += '<div class="flex flex-wrap gap-1 mb-2">';
                prediction.next_7_days.forEach((value, index) => {
                    html += `<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">يوم ${index + 1}: ${value}</span>`;
                });
                html += '</div>';
                html += `<p class="text-sm text-gray-600"><strong>توقعات 30 يوم:</strong> ${prediction.next_30_days || 'غير محدد'}</p>`;
                html += `<p class="text-sm text-gray-600"><strong>مستوى الثقة:</strong> ${prediction.confidence}%</p>`;
                html += '</div>';
            } else {
                html += `<p class="text-sm text-gray-600">${typeof prediction === 'string' ? prediction : JSON.stringify(prediction)}</p>`;
            }
            
            html += '</div>';
        });
        html += '</div>';
    } else if (data.recommendations) {
        html = '<div class="mb-4"><h4 class="text-lg font-semibold text-purple-600 mb-3">توصيات الذكاء الاصطناعي</h4>';
        Object.keys(data.recommendations).forEach(key => {
            const title = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            html += `<div class="mb-4">
                <h5 class="font-medium text-gray-900 mb-3 bg-purple-50 p-2 rounded">${title}</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">`;
            data.recommendations[key].forEach(rec => {
                html += `<div class="flex items-start text-sm text-gray-700 bg-white p-3 rounded border border-purple-200">
                    <i class="fas fa-lightbulb text-purple-500 ml-2 mt-1"></i>
                    <span>${rec}</span>
                </div>`;
            });
            html += '</div></div>';
        });
        html += '</div>';
    } else {
        html = '<div class="bg-blue-50 border-r-4 border-blue-400 p-4"><p class="text-sm text-blue-700">اكتمل التحليل بنجاح. لا توجد رؤى محددة متاحة.</p></div>';
    }
    
    content.innerHTML = html;
}

function showError(message) {
    const content = document.getElementById('resultsContent');
    content.innerHTML = `<div class="bg-red-50 border-r-4 border-red-400 p-4"><p class="text-sm text-red-700">${message}</p></div>`;
}
</script>
@endpush
