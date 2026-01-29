@extends('layouts.app')

@section('title', 'أنواع العقارات')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-building text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">أنواع العقارات</h1>
                        <p class="text-gray-600 mt-1">تصفح العقارات حسب النوع</p>
                    </div>
                </div>
                @if(auth()->user() && auth()->user()->isAdmin())
                <a href="{{ route('property-types.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة نوع جديد
                </a>
                @endif
            </div>
        </div>

        <!-- Property Types Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($propertyTypes as $type)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
                            <i class="fas fa-home text-white text-8xl"></i>
                        </div>
                        <div class="relative z-10 text-white">
                            <h3 class="text-xl font-bold">{{ $type->name }}</h3>
                            @if($type->description)
                                <p class="text-blue-100 text-sm mt-2">{{ Str::limit($type->description, 80) }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <!-- Properties Count -->
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-sm font-medium text-gray-700">العقارات المتاحة</span>
                            <span class="bg-blue-100 text-blue-800 text-sm font-bold px-3 py-1 rounded-full">
                                {{ $type->properties_count }} عقار
                            </span>
                        </div>
                        
                        <!-- Features -->
                        @if($type->features && count($type->features) > 0)
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">المميزات</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(array_slice($type->features, 0, 3) as $feature)
                                        <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full font-medium">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                    @if(count($type->features) > 3)
                                        <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full font-medium">
                                            +{{ count($type->features) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <a href="{{ route('properties.index', ['property_type' => $type->name]) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200 group/browse">
                                <i class="fas fa-search ml-2 group-hover/browse:scale-110 transition-transform"></i>
                                تصفح العقارات
                            </a>
                            
                            @if(auth()->user() && auth()->user()->isAdmin())
                            <div class="flex space-x-2">
                                <a href="{{ route('property-types.edit', $type) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group/edit">
                                    <i class="fas fa-edit group-hover/edit:scale-110 transition-transform"></i>
                                </a>
                                <form action="{{ route('property-types.destroy', $type) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('هل أنت متأكد من حذف هذا النوع؟')"
                                            class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 group/delete">
                                        <i class="fas fa-trash group-hover/delete:scale-110 transition-transform"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-16 text-center">
                        <div class="bg-gray-100 rounded-full w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-building text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">لا توجد أنواع عقارات</h3>
                        <p class="text-gray-500 mb-8 text-lg">ابدأ بإنشاء أول نوع عقاري</p>
                        @if(auth()->user() && auth()->user()->isAdmin())
                        <a href="{{ route('property-types.create') }}" 
                           class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-plus ml-2"></i>
                            إنشاء نوع عقاري
                        </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Statistics -->
        @if($propertyTypes->count() > 0)
        <div class="mt-12 bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center mb-6">
                <div class="bg-purple-100 rounded-lg p-3 ml-4">
                    <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">إحصائيات أنواع العقارات</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-blue-50 rounded-lg">
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ $propertyTypes->count() }}</div>
                    <div class="text-gray-700 font-medium">إجمالي الأنواع</div>
                </div>
                <div class="text-center p-6 bg-green-50 rounded-lg">
                    <div class="text-4xl font-bold text-green-600 mb-2">{{ $propertyTypes->sum('properties_count') }}</div>
                    <div class="text-gray-700 font-medium">إجمالي العقارات</div>
                </div>
                <div class="text-center p-6 bg-purple-50 rounded-lg">
                    <div class="text-4xl font-bold text-purple-600 mb-2">{{ round($propertyTypes->avg('properties_count'), 1) }}</div>
                    <div class="text-gray-700 font-medium">متوسط العقارات لكل نوع</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Custom animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-white {
    animation: slideIn 0.5s ease-out;
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Smooth transitions */
* {
    transition: all 0.2s ease;
}
</style>
@endsection
