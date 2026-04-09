@extends('layouts.dashboard')

@section('content')
    <div class="p-6 rtl text-right" dir="rtl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">📈 تحليلات الذكاء الاصطناعي</h1>
            <p class="text-gray-500 mt-2">نظرة عامة على صحة وأداء النماذج</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-gray-400 text-sm mb-1 font-medium">دقة التوقعات</h3>
                <span class="text-3xl font-bold">{{ round($analytics['prediction_accuracy'], 1) }}%</span>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center">
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.268 15c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-gray-400 text-sm mb-1 font-medium">معدل كشف الاحتيال</h3>
                <span class="text-3xl font-bold">{{ round($analytics['fraud_detection_rate'], 1) }}%</span>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center">
                <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-gray-400 text-sm mb-1 font-medium">جولات VR النشطة</h3>
                <span class="text-3xl font-bold">{{ number_format($analytics['vr_tour_usage']) }}</span>
            </div>
        </div>
    </div>
@endsection