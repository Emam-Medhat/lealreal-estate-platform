@extends('layouts.app')

@section('title', 'مزاداتي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="relative z-10 min-h-screen">
        <div class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h1 class="text-5xl font-black bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent mb-4">
                            <i class="fas fa-list ml-4"></i>
                            مزاداتي
                        </h1>
                        <p class="text-xl text-gray-600">إدارة المزادات الخاصة بك والمشاركات</p>
                    </div>
                    
                    <!-- Navigation Tabs -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('auctions.index') }}" class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300">
                            <i class="fas fa-hammer ml-2"></i>
                            المزادات
                        </a>
                        <a href="{{ route('auctions.my') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-list ml-2"></i>
                            مزاداتي
                        </a>
                        <a href="{{ route('auctions.my-results') }}" class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300">
                            <i class="fas fa-trophy ml-2"></i>
                            نتائجي
                        </a>
                    </div>
                </div>
            </div>

            <!-- Created Auctions Section -->
            <div class="mb-12">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-gavel text-blue-500 ml-3"></i>
                        المزادات التي أنشأتها
                    </h2>
                    <a href="{{ route('auctions.create') }}" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-plus ml-2"></i> إنشاء مزاد جديد
                    </a>
                </div>
                
                @if($createdAuctions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($createdAuctions as $auction)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="relative h-48 bg-gray-200">
                            @if($auction->property->images && $auction->property->images->count() > 0)
                                <img src="{{ asset('storage/' . $auction->property->images->first()->path) }}" alt="{{ $auction->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <i class="fas fa-image text-4xl"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-bold text-blue-600">
                                {{ $auction->status }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">{{ $auction->title }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($auction->description, 100) }}</p>
                            
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <span class="text-xs text-gray-500 block">السعر الحالي</span>
                                    <span class="text-lg font-bold text-green-600">{{ number_format($auction->current_price, 2) }} ريال</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block">أعلى مزايدة</span>
                                    <span class="text-sm font-medium">{{ $auction->highestBid ? $auction->highestBid->user->name : 'لا يوجد' }}</span>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4">
                                <a href="{{ route('auctions.show', $auction->id) }}" class="block w-full py-2 text-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                    إدارة المزاد
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white/50 rounded-2xl p-8 text-center border-2 border-dashed border-gray-300">
                    <p class="text-gray-500 text-lg">لم تقم بإنشاء أي مزادات</p>
                </div>
                @endif
            </div>

            <!-- Participated Auctions Section -->
            <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-hand-paper text-amber-500 ml-3"></i>
                    المزادات المشارك بها
                </h2>
                
                @if($participatedAuctions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($participatedAuctions as $auction)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold mb-1">{{ $auction->title }}</h3>
                                    <span class="text-sm text-gray-500">{{ $auction->property->address }}</span>
                                </div>
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $auction->status }}</span>
                            </div>
                            
                            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600 text-sm">السعر الحالي</span>
                                    <span class="font-bold text-green-600">{{ number_format($auction->current_price, 2) }} ريال</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">ينتهي في</span>
                                    <span class="font-medium text-red-500">{{ \Carbon\Carbon::parse($auction->end_time)->diffForHumans() }}</span>
                                </div>
                            </div>

                            <a href="{{ route('auctions.show', $auction->id) }}" class="block w-full py-2 text-center bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                                عرض المزاد
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white/50 rounded-2xl p-8 text-center border-2 border-dashed border-gray-300">
                    <p class="text-gray-500 text-lg">لم تشارك في أي مزادات بعد</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
