@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">خطط الاشتراك</h1>
        <p class="text-gray-600">اختر الخطة المناسبة لك من بين خطط الاشتراك المتاحة</p>
    </div>

    @if($plans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                        
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-blue-600">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-gray-500">/{{ $plan->billing_cycle }}</span>
                        </div>
                        
                        @if($plan->features)
                            <div class="mb-6">
                                <h4 class="font-semibold text-gray-900 mb-2">المميزات:</h4>
                                <ul class="space-y-2">
                                    @foreach(json_decode($plan->features) as $feature)
                                        <li class="flex items-center text-gray-600">
                                            <svg class="w-4 h-4 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="flex space-x-2 space-x-reverse">
                            @if($plan->is_active)
                                <button class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                    اشترك الآن
                                </button>
                            @else
                                <button disabled class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-md cursor-not-allowed">
                                    غير متاح
                                </button>
                            @endif
                            
                            <a href="#" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $plans->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد خطط اشتراك متاحة</h3>
            <p class="text-gray-600">لم يتم إضافة أي خطط اشتراك بعد. يرجى العودة لاحقاً.</p>
        </div>
    @endif
</div>
@endsection
