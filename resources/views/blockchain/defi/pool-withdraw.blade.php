@extends('admin.layouts.admin')

@section('title', 'سحب من المجمع')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <button onclick="window.location.href='{{ route('blockchain.defi.pool.show', $pool->id) }}'" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">سحب من المجمع</h1>
                    <p class="text-gray-600">سحب أصولك من المجمع</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdraw Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">تفاصيل السحب</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('blockchain.defi.pool.withdraw.process', $pool->id) }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Current Position -->
                <div class="bg-blue-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">مركزك الحالي</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">المبلغ المودع:</span>
                            <span class="font-semibold">{{ $userPosition ? number_format($userPosition->amount, 4) : '0.0000' }} {{ explode('/', $pool->token_pair)[0] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">العائد المكتسب:</span>
                            <span class="font-semibold text-green-600">{{ $userPosition ? number_format($userPosition->earned_rewards, 4) : '0.0000' }} {{ explode('/', $pool->token_pair)[0] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">الإجمالي:</span>
                            <span class="font-semibold">{{ $userPosition ? number_format($userPosition->total_value, 4) : '0.0000' }} {{ explode('/', $pool->token_pair)[0] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">معدل العائد السنوي:</span>
                            <span class="font-semibold text-green-600">{{ number_format($pool->apy, 2) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Token Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اختر العملة</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option>ETH</option>
                        <option>USDC</option>
                        <option>USDT</option>
                        <option>DAI</option>
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ</label>
                    <div class="relative">
                        <input type="number" step="0.001" placeholder="0.000" class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">ETH</span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-sm text-gray-600">الرصيد المتاح: 5.592 ETH</span>
                        <button type="button" class="text-orange-600 hover:text-orange-700 text-sm font-medium">الحد الأقصى</button>
                    </div>
                </div>

                <!-- Withdraw Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع السحب</label>
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="withdrawType" value="instant" checked class="ml-3">
                            <div class="flex-1">
                                <div class="font-medium">سحب فوري</div>
                                <div class="text-sm text-gray-600">سحب فوري مع رسوم 0.5%</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="withdrawType" value="delayed" class="ml-3">
                            <div class="flex-1">
                                <div class="font-medium">سحب مؤجل</div>
                                <div class="text-sm text-gray-600">سحب خلال 24 ساعة بدون رسوم</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Fee Information -->
                <div class="bg-orange-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">معلومات الرسوم</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">رسوم السحب:</span>
                            <span class="font-semibold">0.025 ETH</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">المبلغ بعد الرسوم:</span>
                            <span class="font-semibold text-green-600">4.975 ETH</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">وقت الاستلام:</span>
                            <span class="font-semibold">فوري</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <button type="button" onclick="window.location.href='{{ route('blockchain.defi.pool.show', $pool->id) }}'" class="flex-1 bg-gray-600 text-white py-3 rounded-xl hover:bg-gray-700 transition-colors duration-200 font-medium">
                        إلغاء
                    </button>
                    <button type="submit" class="flex-1 bg-orange-600 text-white py-3 rounded-xl hover:bg-orange-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-minus ml-2"></i>
                        تأكيد السحب
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex items-start space-x-3 space-x-reverse">
            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
            <div>
                <h3 class="font-semibold text-yellow-800 mb-2">ملاحظات هامة</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• السحب الفوري يتضمن رسوم 0.5% من المبلغ</li>
                    <li>• السحب المؤجل يستغرق 24 ساعة بدون رسوم</li>
                    <li>• ستفقد أي عائد غير محقق عند السحب</li>
                    <li>• قد يؤثر السحب الكامل على سيولة المجمع</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
