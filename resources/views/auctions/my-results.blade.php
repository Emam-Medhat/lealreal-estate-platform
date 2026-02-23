@extends('admin.layouts.admin')

@section('title', 'نتائجي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-96 h-96 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse"></div>
        <div class="absolute top-60 right-20 w-80 h-80 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-40 left-1/3 w-72 h-72 bg-gradient-to-r from-indigo-400 to-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 min-h-screen">
        <div class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h1 class="text-5xl font-black bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent mb-4">
                            <i class="fas fa-trophy ml-4"></i>
                            نتائجي
                        </h1>
                        <p class="text-xl text-gray-600">تصفح المزادات التي فزت بها والمزادات التي أنشأتها</p>
                    </div>
                    
                    <!-- Navigation Tabs -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('auctions.index') }}" class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300">
                            <i class="fas fa-hammer ml-2"></i>
                            المزادات
                        </a>
                        <a href="{{ route('auctions.my') }}" class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300">
                            <i class="fas fa-list ml-2"></i>
                            مزاداتي
                        </a>
                        <a href="{{ route('auctions.my-results') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-trophy ml-2"></i>
                            نتائجي
                        </a>
                    </div>
                </div>
            </div>

            <!-- Won Auctions Section -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-medal text-amber-500 ml-3"></i>
                    المزادات التي فزت بها
                </h2>
                
                @if($wonAuctions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($wonAuctions as $result)
                    <div class="bg-white/95 backdrop-blur-xl rounded-2xl border border-white/20 overflow-hidden hover:bg-white hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-trophy text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $result->auction->property->title ?? 'N/A' }}</h3>
                                        <p class="text-gray-600 text-sm">{{ $result->auction->property->address ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-emerald-50 rounded-xl p-4 mb-4 border border-emerald-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-700 font-medium">سعر الفوز</span>
                                    <span class="text-emerald-700 font-bold text-xl">{{ number_format($result->final_price, 2) }} ريال</span>
                                </div>
                                <div class="mt-2 text-xs text-emerald-600 text-left">
                                    {{ $result->completed_at ? $result->completed_at->format('Y-m-d H:i') : 'N/A' }}
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">حالة العقد</p>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $result->contract ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $result->contract ? $result->contract->status : 'معلق' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">البائع</p>
                                    <p class="text-gray-900 font-medium">{{ $result->auction->creator->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100">
                            <a href="{{ route('auctions.results.show', $result->id) }}" class="block w-full py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all text-center font-medium">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white/50 rounded-2xl p-8 text-center border-2 border-dashed border-gray-300">
                    <i class="fas fa-trophy text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 text-lg">لم تفز بأي مزادات حتى الآن</p>
                </div>
                @endif
            </div>

            <!-- Created Auctions Section -->
            <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-gavel text-blue-500 ml-3"></i>
                    نتائج مزاداتي
                </h2>
                
                @if($createdAuctions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($createdAuctions as $result)
                    <div class="bg-white/95 backdrop-blur-xl rounded-2xl border border-white/20 overflow-hidden hover:bg-white hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-check-circle text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $result->auction->property->title ?? 'N/A' }}</h3>
                                        <p class="text-gray-600 text-sm">{{ $result->auction->property->address ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-xl p-4 mb-4 border border-blue-100">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-blue-700 font-medium">الفائز</span>
                                    <span class="text-blue-900 font-bold">{{ $result->winner->name ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-700 font-medium">السعر</span>
                                    <span class="text-blue-900 font-bold">{{ number_format($result->final_price, 2) }} ريال</span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">تاريخ الانتهاء</p>
                                    <p class="text-gray-900 font-medium text-sm">{{ $result->completed_at ? $result->completed_at->format('Y-m-d') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">الحالة</p>
                                    <span class="px-2 py-1 rounded text-xs font-bold {{ $result->status == 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $result->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100">
                            <a href="{{ route('auctions.results.show', $result->id) }}" class="block w-full py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all text-center font-medium">
                                إدارة النتيجة
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white/50 rounded-2xl p-8 text-center border-2 border-dashed border-gray-300">
                    <i class="fas fa-box-open text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 text-lg">لا توجد نتائج لمزاداتك حتى الآن</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
