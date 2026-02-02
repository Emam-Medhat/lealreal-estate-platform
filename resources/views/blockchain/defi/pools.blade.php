@extends('admin.layouts.admin')

@section('title', 'المجمعات النشطة')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">المجمعات النشطة</h1>
                <p class="text-gray-600">تصفح جميع مجمعات السيولة المتاحة</p>
            </div>
            
            <!-- Navigation Tabs -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('blockchain.defi.lending') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-hand-holding-usd ml-2"></i>
                    القروض
                </a>
                <a href="{{ route('blockchain.defi.staking') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-coins ml-2"></i>
                    التخزين
                </a>
                <a href="{{ route('blockchain.defi.yield') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-chart-line ml-2"></i>
                    زراعة العوائد
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="بحث عن مجمع..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع الأنواع</option>
                    <option>سيولة</option>
                    <option>تخزين</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>ترتيب حسب</option>
                    <option>الأعلى APY</option>
                    <option>الأكبر سيولة</option>
                    <option>الأحدث</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Pools Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($activePools as $pool)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-water text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $pool->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $pool->token_pair }}</p>
                        </div>
                    </div>
                    @if($pool->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                            نشط
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            غير نشط
                        </span>
                    @endif
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">النوع:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pool->type === 'liquidity' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $pool->type === 'liquidity' ? 'سيولة' : 'تخزين' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">السيولة:</span>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">{{ number_format($pool->total_liquidity, 2) }} ETH</div>
                            <small class="text-gray-500">${{ number_format($pool->total_liquidity_usd, 2) }}</small>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">معدل العائد:</span>
                        <span class="font-bold text-green-600">{{ number_format($pool->apy, 2) }}%</span>
                    </div>
                </div>

                <div class="flex space-x-2 space-x-reverse">
                    <button onclick="window.location.href='{{ route('blockchain.defi.pool.show', $pool->id) }}'" class="flex-1 bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-eye ml-2"></i>
                        عرض
                    </button>
                    <button onclick="window.location.href='{{ route('blockchain.defi.pool.deposit', $pool->id) }}'" class="flex-1 bg-green-600 text-white py-2 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-plus ml-2"></i>
                        إيداع
                    </button>
                    <button onclick="window.location.href='{{ route('blockchain.defi.pool.withdraw', $pool->id) }}'" class="flex-1 bg-orange-600 text-white py-2 rounded-xl hover:bg-orange-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-minus ml-2"></i>
                        سحب
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Back to DeFi -->
    <div class="text-center">
        <a href="{{ route('blockchain.defi.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors duration-200 font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى DeFi
        </a>
    </div>
</div>
@endsection
