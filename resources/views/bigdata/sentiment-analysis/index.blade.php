@extends('admin.layouts.admin')

@section('title', 'تحليل المشاعر')
@section('page-title', 'تحليل المشاعر')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-comment-dots text-blue-500 ml-3"></i>
                    تحليل المشاعر
                </h1>
                <p class="text-gray-600 mt-2">تحليل مشاعر العملاء والرأي العام</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.sentiment-analysis.dashboard') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-chart-line ml-2"></i>
                    لوحة التحكم
                </a>
                <button type="button" onclick="refreshAnalysis()" id="refreshBtn" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt ml-2" id="refreshIcon"></i>
                    <span id="refreshText">تحديث</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">إجمالي التحليلات</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_analyzed']) }}</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            نشط حالياً
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-comments text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">مشاعر إيجابي</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['positive_percentage'] }}%</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-smile ml-1"></i>
                            مشاعر إيجابية
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-smile text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">مشاعر سلبية</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['negative_percentage'] }}%</p>
                        <p class="text-red-100 text-sm mt-2">
                            <i class="fas fa-frown ml-1"></i>
                            مشاعر سلبية
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-frown text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">مشاعر محايدة</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['neutral_percentage'] }}%</p>
                        <p class="text-amber-100 text-sm mt-2">
                            <i class="fas fa-meh ml-1"></i>
                            مشاعر محايدة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-meh text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Form -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-microscope text-purple-500 ml-3"></i>
                    تحليل النص
                </h3>
                <div class="bg-purple-100 rounded-full p-2">
                    <i class="fas fa-search text-purple-600"></i>
                </div>
            </div>
            <form id="sentimentForm" class="space-y-4">
                <div>
                    <label for="analysisText" class="block text-sm font-medium text-gray-700 mb-2">النص المراد تحليله</label>
                    <textarea id="analysisText" name="text" rows="4" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="أدخل النص الذي تريد تحليل مشاعره..." required></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 mb-2">مصدر النص</label>
                        <select id="source" name="source" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">اختر المصدر</option>
                            <option value="review">مراجعة</option>
                            <option value="social_media">وسائل التواصل الاجتماعي</option>
                            <option value="news">خبر</option>
                            <option value="forum">منتدى</option>
                        </select>
                    </div>
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">لغة النص</label>
                        <select id="language" name="language" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="ar">العربية</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                </div>
                <button type="submit" id="analyzeBtn" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-play ml-2" id="analyzeIcon"></i>
                    <span id="analyzeText">بدء التحليل</span>
                </button>
            </form>
        </div>

        <!-- Analysis Results -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-chart-pie text-blue-500 ml-3"></i>
                    نتائج التحليل
                </h3>
                <div class="bg-blue-100 rounded-full p-2">
                    <i class="fas fa-chart-bar text-blue-600"></i>
                </div>
            </div>
            <div id="analysisResults" class="text-gray-500 text-center py-8">
                <!-- Results will be displayed here -->
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Sources Distribution -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-pie text-blue-500 ml-3"></i>
                        توزيع المصادر
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">مراجعات</span>
                            <span class="text-sm font-bold text-blue-600">{{ number_format($sourcesDistribution['reviews']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $total = array_sum($sourcesDistribution);
                                $percentage = $total > 0 ? ($sourcesDistribution['reviews'] / $total) * 100 : 0;
                            @endphp
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">وسائل التواصل</span>
                            <span class="text-sm font-bold text-green-600">{{ number_format($sourcesDistribution['social_media']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $total > 0 ? ($sourcesDistribution['social_media'] / $total) * 100 : 0;
                            @endphp
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">أخبار</span>
                            <span class="text-sm font-bold text-cyan-600">{{ number_format($sourcesDistribution['news']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $total > 0 ? ($sourcesDistribution['news'] / $total) * 100 : 0;
                            @endphp
                            <div class="bg-cyan-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">منتديات</span>
                            <span class="text-sm font-bold text-amber-600">{{ number_format($sourcesDistribution['forums']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $total > 0 ? ($sourcesDistribution['forums'] / $total) * 100 : 0;
                            @endphp
                            <div class="bg-amber-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-bolt text-yellow-500 ml-3"></i>
                        إجراءات سريعة
                    </h3>
                    <div class="bg-yellow-100 rounded-full p-2">
                        <i class="fas fa-lightning text-yellow-600"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('bigdata.sentiment-analysis.reviews') }}" class="block w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 text-center">
                        <i class="fas fa-star ml-2"></i>
                        تحليل المراجعات
                    </a>
                    <a href="{{ route('bigdata.sentiment-analysis.social-media') }}" class="block w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 text-center">
                        <i class="fas fa-share-alt ml-2"></i>
                        تحليل وسائل التواصل
                    </a>
                    <a href="{{ route('bigdata.sentiment-analysis.trends') }}" class="block w-full bg-gradient-to-r from-cyan-500 to-cyan-600 text-white px-4 py-3 rounded-lg hover:from-cyan-600 hover:to-cyan-700 transition-all duration-300 text-center">
                        <i class="fas fa-chart-line ml-2"></i>
                        اتجاهات المشاعر
                    </a>
                </div>
            </div>

            <!-- Recent Analyses -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-clock text-orange-500 ml-3"></i>
                        التحليلات الأخيرة
                    </h3>
                    <div class="bg-orange-100 rounded-full p-2">
                        <i class="fas fa-history text-orange-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    @foreach($recentAnalyses as $analysis)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div>
                            <h6 class="font-medium text-gray-800">{{ $analysis['type'] }}</h6>
                            <p class="text-xs text-gray-500">{{ $analysis['created_at'] }}</p>
                        </div>
                        <span class="bg-{{ $analysis['sentiment'] == 'positive' ? 'green' : ($analysis['sentiment'] == 'negative' ? 'red' : 'amber') }}-100 text-{{ $analysis['sentiment'] == 'positive' ? 'green' : ($analysis['sentiment'] == 'negative' ? 'red' : 'amber') }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $analysis['sentiment'] == 'positive' ? 'إيجابي' : ($analysis['sentiment'] == 'negative' ? 'سلبي' : 'محايد') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Trend Overview -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-chart-area text-blue-500 ml-3"></i>
                    نظرة عامة على الاتجاهات
                </h3>
                <div class="bg-blue-100 rounded-full p-2">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div>
                    <h4 class="text-2xl font-bold text-green-600">{{ $trendsOverview['overall_sentiment'] }}</h4>
                    <p class="text-sm text-gray-600 mt-2">الاتجاه العام للمشاعر</p>
                    <div class="w-full bg-gray-200 rounded-full h-3 mt-3">
                        <div class="bg-green-600 h-3 rounded-full flex items-center justify-center" style="width: {{ $stats['positive_percentage'] }}%">
                            <span class="text-xs text-white font-medium">{{ $stats['positive_percentage'] }}%</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-2xl font-bold text-cyan-600">{{ $trendsOverview['confidence_score'] }}%</h4>
                    <p class="text-sm text-gray-600 mt-2">مستوى الثقة في التحليل</p>
                    <div class="w-full bg-gray-200 rounded-full h-3 mt-3">
                        <div class="bg-cyan-600 h-3 rounded-full flex items-center justify-center" style="width: {{ $trendsOverview['confidence_score'] }}%">
                            <span class="text-xs text-white font-medium">{{ $trendsOverview['confidence_score'] }}%</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-2xl font-bold text-blue-600">{{ $trendsOverview['improvement'] }}</h4>
                    <p class="text-sm text-gray-600 mt-2">تحسن المشاعر خلال الشهر</p>
                    <div class="w-full bg-gray-200 rounded-full h-3 mt-3">
                        @php
                            $improvementValue = (float)str_replace('%', '', $trendsOverview['improvement']);
                            $displayValue = abs($improvementValue);
                        @endphp
                        <div class="bg-blue-600 h-3 rounded-full flex items-center justify-center" style="width: {{ min(100, $displayValue * 5) }}%">
                            <span class="text-xs text-white font-medium">{{ $trendsOverview['improvement'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Overview -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area ml-2"></i>
                        نظرة عامة على الاتجاهات
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="text-center">
                    <h4 class="text-2xl font-bold text-green-600">{{ $trendsOverview['overall_sentiment'] }}</h4>
                    <p class="text-sm text-gray-600 mt-2">الاتجاه العام للمشاعر</p>
                    <div class="w-full bg-gray-200 rounded-full h-3 mt-3">
                        <div class="bg-green-600 h-3 rounded-full flex items-center justify-center" style="width: {{ $stats['positive_percentage'] }}%">
                            <span class="text-xs text-white font-medium">{{ $stats['positive_percentage'] }}%</span>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <h4 class="text-2xl font-bold text-cyan-600">{{ $trendsOverview['confidence_score'] }}%</h4>
                    <p class="text-sm text-gray-600 mt-2">مستوى الثقة في التحليل</p>
                    <div class="w-full bg-gray-200 rounded-full h-3 mt-3">
                        <div class="bg-cyan-600 h-3 rounded-full flex items-center justify-center" style="width: {{ $trendsOverview['confidence_score'] }}%">
                            <span class="text-xs text-white font-medium">{{ $trendsOverview['confidence_score'] }}%</span>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <h4 class="text-2xl font-bold text-blue-600">{{ $trendsOverview['improvement'] }}</h4>
                    <p class="text-sm text-gray-600 mt-2">تحسن المشاعر خلال الشهر</p>
                    <div class="w-full bg-gray-200 rounded-full h-3 mt-3">
                        @php
                            $improvementValue = (float)str_replace('%', '', $trendsOverview['improvement']);
                            $displayValue = abs($improvementValue);
                        @endphp
                        <div class="bg-blue-600 h-3 rounded-full flex items-center justify-center" style="width: {{ min(100, $displayValue * 5) }}%">
                            <span class="text-xs text-white font-medium">{{ $trendsOverview['improvement'] }}</span>
                        </div>
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
    $('#sentimentForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("bigdata.sentiment-analysis.analyze") }}',
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
                    displayAnalysisResults(response.analysis);
                    $('#resultsPanel').slideDown();
                } else {
                    alert('فشل التحليل: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('حدث خطأ: ' + xhr.responseJSON?.message || 'يرجى المحاولة مرة أخرى');
            },
            complete: function() {
                $('.btn[type="submit"]').prop('disabled', false).html('<i class="fas fa-search ml-2"></i>تحليل المشاعر');
            }
        });
    });
});

function displayAnalysisResults(analysis) {
    const sentimentColors = {
        'positive': 'green',
        'negative': 'red',
        'neutral': 'amber'
    };
    
    const sentimentArabic = {
        'positive': 'إيجابي',
        'negative': 'سلبي',
        'neutral': 'محايد'
    };
    
    const color = sentimentColors[analysis.sentiment] || 'gray';
    const arabicSentiment = sentimentArabic[analysis.sentiment] || 'غير محدد';
    
    const html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gradient-to-r from-${color}-50 to-${color}-100 rounded-lg p-6 border border-${color}-200">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-${color}-800">النتيجة النهائية</h4>
                    <div class="bg-${color}-100 rounded-full p-2">
                        <i class="fas fa-${analysis.sentiment === 'positive' ? 'smile' : analysis.sentiment === 'negative' ? 'frown' : 'meh'} text-${color}-600"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-${color}-600 mb-2">${arabicSentiment}</h3>
                <div class="flex items-center text-sm text-${color}-600">
                    <i class="fas fa-chart-line ml-2"></i>
                    الثقة: ${(analysis.confidence * 100).toFixed(1)}%
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-blue-800">درجة المشاعر</h4>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-chart-bar text-blue-600"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-600 mb-2">${(analysis.score * 100).toFixed(1)}%</h3>
                <div class="flex items-center text-sm text-blue-600">
                    <i class="fas fa-info-circle ml-2"></i>
                    من -100% إلى +100%
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-lg font-bold text-gray-800">المشاعر المكتشفة</h4>
                    <div class="bg-purple-100 rounded-full p-2">
                        <i class="fas fa-heart text-purple-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    ${Object.entries(analysis.emotions).map(([emotion, value]) => 
                        `<div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">${emotion}</span>
                                <span class="text-sm font-bold text-purple-600">${value}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" style="width: ${value}%"></div>
                            </div>
                        </div>`
                    ).join('')}
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-lg font-bold text-gray-800">الكلمات المفتاحية</h4>
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-key text-green-600"></i>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    ${analysis.keywords.map(keyword => 
                        `<span class="bg-gradient-to-r from-green-100 to-green-200 text-green-800 px-3 py-1 rounded-full text-sm font-medium border border-green-300">${keyword}</span>`
                    ).join('')}
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-6 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-lg font-bold text-gray-800">تفاصيل التحليل</h4>
                <div class="bg-gray-100 rounded-full p-2">
                    <i class="fas fa-info text-gray-600"></i>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center space-x-reverse space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-source text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المصدر</p>
                        <p class="font-medium text-gray-800">${analysis.source}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-reverse space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-language text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">اللغة</p>
                        <p class="font-medium text-gray-800">${analysis.language === 'ar' ? 'العربية' : 'English'}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-reverse space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-purple-100 rounded-full p-2">
                        <i class="fas fa-clock text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">وقت التحليل</p>
                        <p class="font-medium text-gray-800">${new Date(analysis.analyzed_at).toLocaleString('ar-SA')}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#analysisResults').html(html);
}

function refreshAnalysis() {
    const refreshBtn = document.getElementById('refreshBtn');
    const refreshIcon = document.getElementById('refreshIcon');
    const refreshText = document.getElementById('refreshText');
    
    // Show loading state
    refreshBtn.disabled = true;
    refreshIcon.className = 'fas fa-spinner fa-spin ml-2';
    refreshText.textContent = 'جاري التحديث...';
    
    // Simulate refresh
    setTimeout(() => {
        location.reload();
    }, 1500);
}

function generateReport() {
    const reportBtn = document.getElementById('generateReportBtn');
    const reportIcon = document.getElementById('reportIcon');
    const reportText = document.getElementById('reportText');
    
    // Show loading state
    reportBtn.disabled = true;
    reportIcon.className = 'fas fa-spinner fa-spin ml-2';
    reportText.textContent = 'جاري إنشاء التقرير...';
    
    $.ajax({
        url: '{{ route("bigdata.sentiment-analysis.report") }}',
        method: 'GET',
        data: {
            period: '30d',
            format: 'pdf'
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                window.open(response.report_url, '_blank');
            }
            // Reset button state
            reportBtn.disabled = false;
            reportIcon.className = 'fas fa-file-alt ml-2';
            reportText.textContent = 'إنشاء تقرير';
        },
        error: function(xhr) {
            // Reset button state on error
            reportBtn.disabled = false;
            reportIcon.className = 'fas fa-file-alt ml-2';
            reportText.textContent = 'إنشاء تقرير';
            alert('حدث خطأثناء إنشاء التقرير');
        }
    });
}

// Form submission handler
$(document).ready(function() {
    $('#sentimentForm').on('submit', function(e) {
        e.preventDefault();
        
        const analyzeBtn = document.getElementById('analyzeBtn');
        const analyzeIcon = document.getElementById('analyzeIcon');
        const analyzeText = document.getElementById('analyzeText');
        const analysisResults = document.getElementById('analysisResults');
        
        // Show loading state
        analyzeBtn.disabled = true;
        analyzeIcon.className = 'fas fa-spinner fa-spin ml-2';
        analyzeText.textContent = 'جاري التحليل...';
        
        // Show loading in results area
        analysisResults.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-purple-500 mb-4"></i>
                <p class="text-gray-600">جاري تحليل النص...</p>
            </div>
        `;
        
        // Get form data
        const formData = {
            text: $('#analysisText').val(),
            source: $('#source').val(),
            language: $('#language').val()
        };
        
        // Submit analysis
        $.ajax({
            url: '{{ route("bigdata.sentiment-analysis.analyze") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    displayAnalysisResults(response.analysis);
                } else {
                    analysisResults.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                            <p class="text-red-600">${response.message || 'حدث خطأ في التحليل'}</p>
                        </div>
                    `;
                }
                
                // Reset button state
                analyzeBtn.disabled = false;
                analyzeIcon.className = 'fas fa-play ml-2';
                analyzeText.textContent = 'بدء التحليل';
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                analysisResults.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                        <p class="text-red-600">${error?.message || 'حدث خطأ في الاتصال بالخادم'}</p>
                    </div>
                `;
                
                // Reset button state
                analyzeBtn.disabled = false;
                analyzeIcon.className = 'fas fa-play ml-2';
                analyzeText.textContent = 'بدء التحليل';
            }
        });
    });
});
</script>
@endpush
