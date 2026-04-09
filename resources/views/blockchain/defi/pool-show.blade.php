@extends('admin.layouts.admin')

@section('title', 'تفاصيل المجمع')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <button onclick="window.location.href='{{ route('blockchain.defi.pools') }}'" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $pool->name }}</h1>
                    <p class="text-gray-600">{{ $pool->token_pair }}</p>
                </div>
            </div>
            @if($pool->is_active)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                    نشط
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                    غير نشط
                </span>
            @endif
        </div>
    </div>

    <!-- Pool Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Liquidity -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي السيولة</p>
                    <p class="text-3xl font-bold">{{ number_format($pool->total_liquidity, 2) }} ETH</p>
                    <p class="text-blue-100 text-xs mt-2">${{ number_format($pool->total_liquidity_usd, 2) }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-water text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- APY -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">معدل العائد السنوي</p>
                    <p class="text-3xl font-bold">{{ number_format($pool->apy, 2) }}%</p>
                    <p class="text-green-100 text-xs mt-2">+2% هذا الأسبوع</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-percentage text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- 24h Volume -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">حجم التداول 24س</p>
                    <p class="text-3xl font-bold">${{ number_format($pool->volume_24h, 2) }}</p>
                    <p class="text-purple-100 text-xs mt-2">+15% أمس</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- 24h Fees -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">الرسوم 24س</p>
                    <p class="text-3xl font-bold">${{ number_format($pool->fees_24h, 2) }}</p>
                    <p class="text-orange-100 text-xs mt-2">0.1% من التداول</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-coins text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button onclick="window.location.href='{{ route('blockchain.defi.pool.deposit', $pool->id) }}'" class="bg-green-600 text-white py-4 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium text-lg">
                <i class="fas fa-plus ml-2"></i>
                إيداع في المجمع
            </button>
            <button onclick="window.location.href='{{ route('blockchain.defi.pool.withdraw', $pool->id) }}'" class="bg-orange-600 text-white py-4 rounded-xl hover:bg-orange-700 transition-colors duration-200 font-medium text-lg">
                <i class="fas fa-minus ml-2"></i>
                سحب من المجمع
            </button>
        </div>
    </div>

    <!-- Pool Information -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">معلومات المجمع</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">التفاصيل الأساسية</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">اسم المجمع:</span>
                            <span class="font-semibold">{{ $pool->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">زوج العملات:</span>
                            <span class="font-semibold">{{ $pool->token_pair }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">النوع:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pool->type === 'liquidity' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $pool->type === 'liquidity' ? 'سيولة' : 'تخزين' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">الحالة:</span>
                            @if($pool->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    نشط
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    غير نشط
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">الأداء</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">معدل العائد السنوي:</span>
                            <span class="font-semibold text-green-600">{{ number_format($pool->apy, 2) }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">إجمالي السيولة:</span>
                            <span class="font-semibold">{{ number_format($pool->total_liquidity, 2) }} ETH</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">قيمة السيولة:</span>
                            <span class="font-semibold">${{ number_format($pool->total_liquidity_usd, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">حجم التداول 24س:</span>
                            <span class="font-semibold">${{ number_format($pool->volume_24h, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center">
        <a href="{{ route('blockchain.defi.pools') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors duration-200 font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى المجمعات
        </a>
    </div>
</div>
@endsection
