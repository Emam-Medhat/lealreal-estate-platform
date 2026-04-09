@extends('layouts.app')

@section('title', $campaign->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8">
    <div class="max-w-7xl mx-auto px-6">

        <!-- Header -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-full p-3 mr-4">
                        <i class="fas fa-bullhorn text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800">{{ $campaign->name }}</h1>
                        <div class="flex items-center mt-2 space-x-4 space-x-reverse">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $campaign->status == 'active' ? 'bg-green-100 text-green-800' : 
                                   ($campaign->status == 'paused' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($campaign->status == 'draft' ? 'bg-slate-100 text-slate-800' : 'bg-gray-100 text-gray-800')) }}">
                                <i class="fas fa-{{ $campaign->status == 'active' ? 'play' : ($campaign->status == 'paused' ? 'pause' : ($campaign->status == 'draft' ? 'edit' : 'stop')) }} ml-1"></i>
                                {{ $campaign->status == 'active' ? 'نشطة' : ($campaign->status == 'paused' ? 'موقفة' : ($campaign->status == 'draft' ? 'مسودة' : 'منتهية')) }}
                            </span>
                            <span class="text-slate-500 text-sm">
                                <i class="fas fa-calendar-alt ml-1"></i>
                                {{ $campaign->start_date->format('Y-m-d') }} - {{ $campaign->end_date->format('Y-m-d') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('ads.campaigns.index') }}" 
                       class="bg-white border-2 border-slate-300 text-slate-700 px-6 py-3 rounded-2xl hover:bg-slate-50 transition-all font-medium flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        العودة
                    </a>
                    <a href="{{ route('ads.campaigns.edit', $campaign->id) }}" 
                       class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all font-medium flex items-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-edit mr-2"></i>
                        تعديل
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Ads -->
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-medium mb-1">إجمالي الإعلانات</p>
                        <p class="text-3xl font-bold text-slate-800">{{ $metrics['total_ads'] }}</p>
                        <p class="text-sm text-green-600 mt-1">
                            <i class="fas fa-check-circle ml-1"></i>
                            {{ $metrics['active_ads'] }} نشط
                        </p>
                    </div>
                    <div class="bg-blue-100 rounded-2xl p-4">
                        <i class="fas fa-ad text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Impressions -->
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-medium mb-1">المشاهدات</p>
                        <p class="text-3xl font-bold text-slate-800">{{ number_format($metrics['total_impressions']) }}</p>
                        <p class="text-sm text-blue-600 mt-1">
                            <i class="fas fa-eye ml-1"></i>
                            إجمالي
                        </p>
                    </div>
                    <div class="bg-purple-100 rounded-2xl p-4">
                        <i class="fas fa-eye text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Clicks -->
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-medium mb-1">النقرات</p>
                        <p class="text-3xl font-bold text-slate-800">{{ number_format($metrics['total_clicks']) }}</p>
                        <p class="text-sm text-purple-600 mt-1">
                            <i class="fas fa-mouse-pointer ml-1"></i>
                            CTR: {{ number_format($metrics['ctr'], 2) }}%
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-2xl p-4">
                        <i class="fas fa-mouse-pointer text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Budget -->
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-medium mb-1">المصروف</p>
                        <p class="text-3xl font-bold text-slate-800">{{ number_format($metrics['total_spent'], 0) }}</p>
                        <p class="text-sm text-slate-500 mt-1">
                            <i class="fas fa-money-bill ml-1"></i>
                            ريال
                        </p>
                    </div>
                    <div class="bg-amber-100 rounded-2xl p-4">
                        <i class="fas fa-money-bill-wave text-amber-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column - Campaign Info -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Campaign Details -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full p-2 mr-4">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800">معلومات الحملة</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <p class="text-slate-500 text-sm mb-1">هدف الحملة</p>
                            <p class="text-lg font-semibold text-slate-800">
                                {{ $campaign->objective == 'awareness' ? 'زيادة الوعي' : 
                                   ($campaign->objective == 'traffic' ? 'زيادة الزيارات' : 
                                   ($campaign->objective == 'conversions' ? 'زيادة التحويلات' : 'زيادة التفاعل')) }}
                            </p>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <p class="text-slate-500 text-sm mb-1">الوصف</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $campaign->description ?? 'لا يوجد وصف' }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <p class="text-slate-500 text-sm mb-1">تاريخ البدء</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $campaign->start_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <p class="text-slate-500 text-sm mb-1">تاريخ الانتهاء</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $campaign->end_date->format('Y-m-d') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ads List -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-full p-2 mr-4">
                                <i class="fas fa-ad text-white"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-800">إعلانات الحملة</h2>
                        </div>
                        <a href="{{ route('ads.create', ['campaign_id' => $campaign->id]) }}" 
                           class="bg-gradient-to-r from-green-500 to-green-600 text-white px-5 py-2.5 rounded-xl hover:from-green-600 hover:to-green-700 transition-all font-medium flex items-center shadow-md hover:shadow-lg text-sm">
                            <i class="fas fa-plus mr-2"></i>
                            إضافة إعلان
                        </a>
                    </div>

                    @if($campaign->ads->count() > 0)
                        <div class="space-y-4">
                            @foreach($campaign->ads as $ad)
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 hover:shadow-md transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-white rounded-xl p-3 shadow-sm mr-4">
                                            <i class="fas fa-image text-slate-400 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-slate-800">{{ $ad->title }}</h4>
                                            <div class="flex items-center mt-1 space-x-3 space-x-reverse">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium
                                                    {{ $ad->status == 'active' ? 'bg-green-100 text-green-800' : 
                                                       ($ad->status == 'paused' ? 'bg-yellow-100 text-yellow-800' : 'bg-slate-100 text-slate-800') }}">
                                                    {{ $ad->status == 'active' ? 'نشط' : ($ad->status == 'paused' ? 'موقف' : 'مسودة') }}
                                                </span>
                                                <span class="text-slate-500 text-xs">
                                                    <i class="fas fa-eye ml-1"></i> {{ number_format($ad->impressions_count) }}
                                                </span>
                                                <span class="text-slate-500 text-xs">
                                                    <i class="fas fa-mouse-pointer ml-1"></i> {{ number_format($ad->clicks_count) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('ads.show', $ad->id) }}" 
                                       class="bg-white border-2 border-slate-200 text-slate-600 px-4 py-2 rounded-xl hover:bg-slate-50 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-slate-50 rounded-2xl border border-slate-200 border-dashed">
                            <div class="bg-white rounded-full p-4 w-16 h-16 mx-auto mb-4 shadow-sm">
                                <i class="fas fa-ad text-slate-300 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-700 mb-2">لا توجد إعلانات</h3>
                            <p class="text-slate-500 mb-4">أضف إعلانات لبدء الحملة</p>
                            <a href="{{ route('ads.create', ['campaign_id' => $campaign->id]) }}" 
                               class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all font-medium inline-flex items-center shadow-lg hover:shadow-xl">
                                <i class="fas fa-plus mr-2"></i>
                                إضافة إعلان جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Budget & Performance -->
            <div class="space-y-8">
                
                <!-- Budget Card -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-full p-2 mr-4">
                            <i class="fas fa-wallet text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">الميزانية</h2>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-slate-500">المصروف</span>
                                <span class="font-semibold text-slate-800">{{ number_format($metrics['total_spent'], 2) }} ريال</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-slate-500">المتبقي</span>
                                <span class="font-semibold text-slate-800">{{ number_format($metrics['remaining_budget'], 2) }} ريال</span>
                            </div>
                            <div class="flex justify-between text-sm mb-3">
                                <span class="text-slate-500">الإجمالي</span>
                                <span class="font-semibold text-slate-800">{{ number_format($campaign->budget->total_budget ?? 0, 2) }} ريال</span>
                            </div>
                            
                            @php
                                $utilization = ($campaign->budget->total_budget ?? 0) > 0 
                                    ? ($metrics['total_spent'] / $campaign->budget->total_budget) * 100 
                                    : 0;
                            @endphp
                            <div class="w-full bg-slate-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ min($utilization, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 text-center">{{ number_format($utilization, 1) }}% مستهلك</p>
                        </div>

                        <div class="border-t border-slate-200 pt-4">
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div class="bg-slate-50 rounded-xl p-3">
                                    <p class="text-xs text-slate-500">الميزانية اليومية</p>
                                    <p class="font-semibold text-slate-800">{{ number_format($campaign->budget->daily_budget ?? 0, 0) }}</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3">
                                    <p class="text-xs text-slate-500">تكلفة النقرة</p>
                                    <p class="font-semibold text-slate-800">{{ number_format($metrics['cpc'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Card -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-full p-2 mr-4">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">الأداء</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-eye text-blue-500 ml-3"></i>
                                <span class="text-slate-600">المشاهدات</span>
                            </div>
                            <span class="font-semibold text-slate-800">{{ number_format($metrics['total_impressions']) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-mouse-pointer text-green-500 ml-3"></i>
                                <span class="text-slate-600">النقرات</span>
                            </div>
                            <span class="font-semibold text-slate-800">{{ number_format($metrics['total_clicks']) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-percentage text-purple-500 ml-3"></i>
                                <span class="text-slate-600">نسبة النقر</span>
                            </div>
                            <span class="font-semibold text-slate-800">{{ number_format($metrics['ctr'], 2) }}%</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-hand-pointer text-amber-500 ml-3"></i>
                                <span class="text-slate-600">تكلفة النقرة</span>
                            </div>
                            <span class="font-semibold text-slate-800">{{ number_format($metrics['cpc'], 2) }} ريال</span>
                        </div>
                    </div>
                </div>

                <!-- Targeting Summary -->
                @if($campaign->targeting)
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-r from-pink-500 to-pink-600 rounded-full p-2 mr-4">
                            <i class="fas fa-bullseye text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">الاستهداف</h2>
                    </div>

                    <div class="space-y-3">
                        @if($campaign->targeting->location_criteria)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-map-marker-alt text-slate-400 ml-2"></i>
                            <span class="text-slate-600">المواقع: {{ count($campaign->targeting->location_criteria) }}</span>
                        </div>
                        @endif
                        @if($campaign->targeting->age_range)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-user text-slate-400 ml-2"></i>
                            <span class="text-slate-600">العمر: {{ $campaign->targeting->age_range }}</span>
                        </div>
                        @endif
                        @if($campaign->targeting->gender_criteria)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-venus-mars text-slate-400 ml-2"></i>
                            <span class="text-slate-600">الجنس: {{ implode(', ', $campaign->targeting->gender_criteria) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection
