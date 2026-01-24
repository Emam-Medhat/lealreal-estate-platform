@extends('layouts.dashboard')

@section('title', 'الرؤى التحليلية')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Insights Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">الرؤى التحليلية</h1>
                <p class="text-gray-600">رؤى ذكية مستخلصة من البيانات</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-magic ml-2"></i>
                    توليد رؤى جديدة
                </button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-cog ml-2"></i>
                    الإعدادات
                </button>
            </div>
        </div>
    </div>

    <!-- AI Insights Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-800 bg-opacity-50 p-3 rounded-full">
                    <i class="fas fa-lightbulb text-xl"></i>
                </div>
                <span class="bg-purple-800 bg-opacity-50 px-2 py-1 rounded-full text-xs">
                    AI
                </span>
            </div>
            <h3 class="text-lg font-semibold mb-2">رؤى الأداء</h3>
            <p class="text-purple-100 text-sm mb-4">تحليلات ذكية لأداء المنصة</p>
            <div class="text-2xl font-bold">{{ $performanceInsights ?? 0 }}</div>
            <div class="text-purple-100 text-xs">رؤى جديدة هذا الأسبوع</div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-800 bg-opacity-50 p-3 rounded-full">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <span class="bg-blue-800 bg-opacity-50 px-2 py-1 rounded-full text-xs">
                    AI
                </span>
            </div>
            <h3 class="text-lg font-semibold mb-2">اتجاهات السوق</h3>
            <p class="text-blue-100 text-sm mb-4">توقعات وتحليلات السوق</p>
            <div class="text-2xl font-bold">{{ $marketTrends ?? 0 }}</div>
            <div class="text-blue-100 text-xs">اتجاه محدث</div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-800 bg-opacity-50 p-3 rounded-full">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <span class="bg-green-800 bg-opacity-50 px-2 py-1 rounded-full text-xs">
                    AI
                </span>
            </div>
            <h3 class="text-lg font-semibold mb-2">سلوك المستخدمين</h3>
            <p class="text-green-100 text-sm mb-4">فهم عميق لسلوك المستخدمين</p>
            <div class="text-2xl font-bold">{{ $userBehaviorInsights ?? 0 }}</div>
            <div class="text-green-100 text-xs">نمط محدد</div>
        </div>
    </div>

    <!-- Key Insights -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">أهم الرؤى</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @if(isset($keyInsights) && $keyInsights->count() > 0)
                @foreach($keyInsights as $insight)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="bg-purple-100 text-purple-600 p-2 rounded-full ml-3">
                                <i class="fas fa-brain text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $insight->title }}</h3>
                                <p class="text-sm text-gray-500">{{ $insight->category }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                            {{ $insight->confidence }}%
                        </span>
                    </div>
                    <p class="text-gray-700 mb-3">{{ $insight->description }}</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-clock ml-1"></i>
                                {{ $insight->created_at->diffForHumans() }}
                            </span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-chart-bar ml-1"></i>
                                {{ $insight->impact }}
                            </span>
                        </div>
                        <button class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                            التفاصيل
                        </button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-span-2 text-center py-8 text-gray-500">
                    <i class="fas fa-lightbulb text-4xl mb-4"></i>
                    <p>لا توجد رؤى حالياً</p>
                    <p class="text-sm">سيتم توليد الرؤى تلقائياً عند توفر البيانات الكافية</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recommendations -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">التوصيات الذكية</h2>
        <div class="space-y-4">
            @if(isset($recommendations) && $recommendations->count() > 0)
                @foreach($recommendations as $recommendation)
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-3">
                            <i class="fas fa-robot text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $recommendation->title }}</h4>
                            <p class="text-sm text-gray-600">{{ $recommendation->description }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            {{ $recommendation->priority }}
                        </span>
                        <button class="bg-blue-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                            تطبيق
                        </button>
                        <button class="text-blue-600 hover:text-blue-800 text-sm">
                            تجاهل
                        </button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-robot text-4xl mb-4"></i>
                    <p>لا توجد توصيات حالياً</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Analytics Settings -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">إعدادات التحليلات</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-medium text-gray-900 mb-3">توليد الرؤى التلقائي</h3>
                <div class="space-y-3">
                    <label class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">توليد رؤى الأداء</span>
                        <input type="checkbox" class="form-checkbox" checked>
                    </label>
                    <label class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">تحليل اتجاهات السوق</span>
                        <input type="checkbox" class="form-checkbox" checked>
                    </label>
                    <label class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">مراقبة سلوك المستخدمين</span>
                        <input type="checkbox" class="form-checkbox">
                    </label>
                </div>
            </div>
            <div>
                <h3 class="font-medium text-gray-900 mb-3">مستوى الثقة</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-700">الحد الأدنى للثقة</label>
                        <select class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option>70%</option>
                            <option>80%</option>
                            <option>90%</option>
                            <option>95%</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-gray-700">تكرار التحليل</label>
                        <select class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option>كل ساعة</option>
                            <option>كل 6 ساعات</option>
                            <option>يومياً</option>
                            <option>أسبوعياً</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
